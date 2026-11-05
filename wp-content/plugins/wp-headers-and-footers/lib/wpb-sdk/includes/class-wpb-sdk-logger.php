<?php
/**
 * WP Brigade SDK: telemetry logger (schedule, collect, POST to API).
 *
 * @package wpbrigade_sdk
 * @subpackage WPB_SDK
 */

/**
 * Registers hooks, builds payloads, and sends telemetry for WP Brigade products.
 *
 * Product behavior is driven by each plugin's wpb_sdk_dynamic_init() module array.
 */
class WPBRIGADE_Logger {

	/**
	 * Product slug for this logger instance.
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * Singleton instances keyed by product slug.
	 *
	 * @var array<string, self>
	 */
	private static $instances = array();

	/**
	 * Module config from wpb_init(), keyed by slug.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private static $product_data = array();

	/**
	 * Slugs that already registered WordPress hooks via hooks().
	 *
	 * @var array<string, bool>
	 */
	private static $hooked_slugs = array();

	/**
	 * Slugs that already registered wp_wpb_sdk_after_uninstall lifecycle cleanup.
	 *
	 * @var array<string, bool>
	 */
	private static $lifecycle_uninstall_hooked = array();

	/**
	 * Last module id passed to instance() (used by uninstall static entry).
	 *
	 * @var mixed
	 */
	private static $module_id;

	/**
	 * Slug set on the instance used for uninstall telemetry.
	 *
	 * @var string|null
	 */
	private static $current_uninstall_slug = null;

	/**
	 * In-flight verification email dispatch (activate log with token).
	 *
	 * @var array{slug: string, force: bool}|null
	 */
	private static $verification_email_dispatch = null;

	/**
	 * Constructor for the Logger class.
	 *
	 * @param mixed $module_id Module id.
	 * @param mixed $slug      Product slug.
	 * @param bool  $is_init   Whether this is an init call.
	 */
	private function __construct( $module_id = null, $slug = false, $is_init = false ) {
		if ( ! empty( $slug ) ) {
			$this->slug = (string) $slug;
		}
	}

	/**
	 * Create or retrieve a Logger instance (singleton per slug).
	 *
	 * @param mixed $module_id Module id.
	 * @param mixed $slug      Product slug.
	 * @param bool  $is_init   Whether this is an init call.
	 * @return self|false
	 */
	public static function instance( $module_id, $slug = false, $is_init = false ) {
		if ( empty( $module_id ) ) {
			return false;
		}
		if ( ! $is_init && true === $slug ) {
			$is_init = true;
		}

		if ( ! isset( self::$instances[ $slug ] ) ) {
			self::$instances[ $slug ] = new WPBRIGADE_Logger( $module_id, $slug, $is_init );
		}

		return self::$instances[ $slug ];
	}


	/**
	 * Store module config and register WordPress hooks for this product.
	 *
	 * @param array<string, mixed> $module Module definition (slug, keys, settings, etc.).
	 * @return void
	 */
	public function wpb_init( array $module ) {
		if ( empty( $module['plugin_file'] ) || ! is_string( $module['plugin_file'] ) ) {
			return;
		}
		if ( ! file_exists( $module['plugin_file'] ) ) {
			return;
		}
		$key                        = $module['slug'];
		self::$product_data[ $key ] = array_merge(
			self::$product_data[ $key ] ?? array(),
			array(
				'module' => $module,
			)
		);
		if ( function_exists( 'wpb_sdk_store_uninstall_cleanup_manifest' ) ) {
			wpb_sdk_store_uninstall_cleanup_manifest( $module );
		}
		$this->hooks( $module['slug'] );
	}

	/**
	 * Restore in-memory module config when init was short-circuited but static data was lost.
	 *
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	public static function wpb_sdk_store_module_if_missing( array $module ) {
		$key = isset( $module['slug'] ) ? (string) $module['slug'] : '';
		if ( '' === $key ) {
			return;
		}
		self::$product_data[ $key ] = array_merge(
			self::$product_data[ $key ] ?? array(),
			array(
				'module' => $module,
			)
		);

		if ( function_exists( 'wpb_sdk_store_module_in_registry' ) ) {
			wpb_sdk_store_module_in_registry( $module );
		}
		if ( function_exists( 'wpb_sdk_store_uninstall_cleanup_manifest' ) ) {
			wpb_sdk_store_uninstall_cleanup_manifest( $module );
		}
	}

	/**
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	private static function get_module_config( $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return array();
		}
		if ( isset( self::$product_data[ $slug ]['module'] ) && is_array( self::$product_data[ $slug ]['module'] ) ) {
			return self::$product_data[ $slug ]['module'];
		}
		$module = function_exists( 'wpb_sdk_get_registered_module' ) ? wpb_sdk_get_registered_module( $slug ) : null;
		return ( is_array( $module ) && ! empty( $module ) ) ? $module : array();
	}

	/**
	 * Lets the host plugin run wpb_sdk_dynamic_init() when the SDK needs module config early.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function bootstrap_module_if_needed( $slug ) {
		if ( ! empty( self::$product_data[ $slug ]['module'] ) ) {
			return;
		}
		$main = wpb_sdk_get_plugin_path( $slug );
		if ( '' !== $main && file_exists( $main ) ) {
			include_once $main;
		}

		if ( empty( self::$product_data[ $slug ]['module'] ) ) {
			do_action( 'wpb_sdk_bootstrap_module', $slug );
			if ( function_exists( 'wpb_sdk_restore_module_for_lifecycle' ) ) {
				$restored = wpb_sdk_restore_module_for_lifecycle( $slug );
				if ( is_array( $restored ) && ! empty( $restored ) ) {
					self::$product_data[ $slug ] = array_merge(
						self::$product_data[ $slug ] ?? array(),
						array(
							'module' => $restored,
						)
					);
				}
			}
		}
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return array<string, mixed>
	 */
	private static function get_telemetry_config( array $module ) {
		$telemetry = isset( $module['telemetry'] ) && is_array( $module['telemetry'] )
			? $module['telemetry']
			: array();

		return wp_parse_args(
			$telemetry,
			array(
				'user_info_format'            => 'legacy',
				'optout_submit_key'           => '',
				'verification_token_ttl_days' => 14,
			)
		);
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return bool
	 */
	private static function uses_laravel_user_info( array $module ) {
		return 'laravel' === self::get_telemetry_config( $module )['user_info_format'];
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return bool
	 */
	private static function is_optout_form_submission( array $module ) {
		$key = (string) self::get_telemetry_config( $module )['optout_submit_key'];
		if ( '' === $key ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Product opt-out form uses its own nonce.
		return isset( $_POST[ $key ] );
	}

	/**
	 * Product slug accessor.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Extract product slug and module definition from dynamic init module config.
	 *
	 * @param array $module Module config.
	 * @return array{0: string, 1: array<string, mixed>}
	 */
	public static function extract_slug_and_module_from_init( array $module ) {
		$slug = isset( $module['slug'] ) ? sanitize_key( (string) $module['slug'] ) : '';
		if ( '' === $slug && isset( $module['id'] ) ) {
			$slug = sanitize_key( (string) $module['id'] );
		}

		$config = $module;
		if ( '' !== $slug && empty( $config['slug'] ) ) {
			$config['slug'] = $slug;
		}

		return array( $slug, $config );
	}

	/**
	 * Instantiate lifecycle class using args count.
	 *
	 * @param string $class_name Class name.
	 * @param array  $args       Constructor arguments.
	 * @return object
	 */
	private static function instantiate_lifecycle_class( $class_name, array $args ) {
		$count = count( $args );
		if ( 0 === $count ) {
			return new $class_name();
		}
		if ( 1 === $count ) {
			return new $class_name( $args[0] );
		}
		$ref = new ReflectionClass( $class_name );
		return $ref->newInstanceArgs( $args );
	}

	/**
	 * Prepare and verify environment before invoking product lifecycle class.
	 *
	 * @param string $plugin_dir Directory of the plugin.
	 * @param array  $lifecycle  Lifecycle config.
	 * @param string $which      activate|deactivate|uninstall.
	 * @return bool
	 */
	private static function prepare_lifecycle_environment( $plugin_dir, array $lifecycle, $which ) {
		foreach ( (array) ( $lifecycle['bootstrap_files'] ?? array() ) as $relative ) {
			$path = $plugin_dir . ltrim( (string) $relative, '/\\' );
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}

		if ( 'uninstall' === $which && ! empty( $lifecycle['uninstall_requires'] ) ) {
			$required = $plugin_dir . ltrim( (string) $lifecycle['uninstall_requires'], '/\\' );
			if ( ! is_readable( $required ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Optional product lifecycle class (activate / deactivate / uninstall) from module config.
	 *
	 * @param string $slug  Product slug.
	 * @param string $which activate|deactivate|uninstall.
	 * @return void
	 */
	private static function run_module_lifecycle( $slug, $which ) {
		if ( ! in_array( $which, array( 'activate', 'deactivate', 'uninstall' ), true ) ) {
			return;
		}

		$module    = self::get_module_config( $slug );
		$lifecycle = isset( $module['lifecycle'] ) && is_array( $module['lifecycle'] ) ? $module['lifecycle'] : array();
		$class     = isset( $lifecycle['class'] ) ? (string) $lifecycle['class'] : '';
		$main_file = wpb_sdk_get_plugin_path( $slug );

		if ( '' === $class || ! file_exists( $main_file ) ) {
			return;
		}

		$plugin_dir = trailingslashit( dirname( $main_file ) );
		if ( ! self::prepare_lifecycle_environment( $plugin_dir, $lifecycle, $which ) || ! class_exists( $class, false ) ) {
			return;
		}

		$args     = isset( $lifecycle['constructor_args'] ) ? (array) $lifecycle['constructor_args'] : array( null );
		$instance = self::instantiate_lifecycle_class( $class, $args );

		if ( is_object( $instance ) && is_callable( array( $instance, $which ) ) ) {
			call_user_func( array( $instance, $which ) );
		}
	}

	/**
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function register_lifecycle_uninstall_hook( $slug ) {
		$module = self::get_module_config( $slug );
		if ( empty( $module['lifecycle']['class'] ) || ! empty( self::$lifecycle_uninstall_hooked[ $slug ] ) ) {
			return;
		}

		self::$lifecycle_uninstall_hooked[ $slug ] = true;
		add_action(
			'wp_wpb_sdk_after_uninstall',
			static function ( $uninstall_slug ) use ( $slug ) {
				if ( $uninstall_slug !== $slug ) {
					return;
				}
				self::run_module_lifecycle( $slug, 'uninstall' );
			},
			5
		);
	}

	/**
	 * Singleton logger for a slug.
	 *
	 * @param string $slug Product slug.
	 * @return self|false
	 */
	private static function logger_for_slug( $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return false;
		}
		self::bootstrap_module_if_needed( $slug );
		$mid = isset( self::$product_data[ $slug ]['module']['id'] )
			? self::$product_data[ $slug ]['module']['id']
			: '1';
		if ( empty( $mid ) ) {
			$mid = '1';
		}
		return self::instance( $mid, $slug, true );
	}

	/**
	 * Activation hook entry (static so the callback survives any instance edge cases).
	 *
	 * @param bool $network_wide Whether the plugin is network-activated.
	 * @return void
	 */
	public static function telemetry_handle_activation( $network_wide = false, $slug = '' ) {
		unset( $network_wide );
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}
		self::ensure_module_registered_for_slug( $slug );
		// First opt-in yes/skip is sent from Opt_Manager AJAX; re-activation sends for opted-in users only.
		if ( function_exists( 'wpb_sdk_allows_ongoing_telemetry' ) && wpb_sdk_allows_ongoing_telemetry( $slug ) ) {
			$logger = self::logger_for_slug( $slug );
			if ( $logger ) {
				$logger->log_activation( $slug );
			}
		}
		self::run_module_lifecycle( $slug, 'activate' );
	}

	/**
	 * Deactivation hook entry (static so the callback survives any instance edge cases).
	 *
	 * @param bool $network_wide Whether the plugin is network-activated.
	 * @return void
	 */
	public static function telemetry_handle_deactivation( $network_wide = false, $slug = '' ) {
		unset( $network_wide );
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}
		self::ensure_module_registered_for_slug( $slug );
		$logger = self::logger_for_slug( $slug );
		if ( ! $logger ) {
			return;
		}
		$logger->product_deactivation( $slug );
		self::run_module_lifecycle( $slug, 'deactivate' );
	}


	/**
	 * Register optional verification and opt manager subsystems for a product module.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function register_subsystem_modules( $slug ) {
		$module = isset( self::$product_data[ $slug ]['module'] ) && is_array( self::$product_data[ $slug ]['module'] )
			? self::$product_data[ $slug ]['module']
			: array();

		if ( class_exists( 'WPBRIGADE_Optin_Verification' )
			&& ! empty( $module['optin_user_meta']['token'] )
			&& function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
			&& '' !== wpb_sdk_email_verified_meta_key_from_module( $module )
		) {
			WPBRIGADE_Optin_Verification::register_module( $module );
		}

		if ( class_exists( 'WPBRIGADE_Opt_Manager' ) && ! empty( $module['sdk_views_dir'] ) ) {
			WPBRIGADE_Opt_Manager::register_module( $module );
		}
	}

	/**
	 * Register cron, AJAX, activation/deactivation, and uninstall hooks.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function hooks( $slug ) {
		if ( ! empty( self::$hooked_slugs[ $slug ] ) ) {
			return;
		}
		self::$hooked_slugs[ $slug ] = true;

		$plugin_path = wpb_sdk_get_plugin_path( $slug );
		if ( empty( $plugin_path ) || ! file_exists( $plugin_path ) ) {
			return;
		}

		add_action(
			'init',
			function () use ( $slug ) {
				$this->set_logs_schedule( $slug );
			}
		);

		// Daily log plugin execution.
		add_action(
			'wpb_data_sync_' . $slug,
			function () use ( $slug ) {
				$this->daily_log_plugin( $slug );
			}
		);

		// Deactivation survey: load after footer scripts so jQuery exists on plugins.php.
		add_action(
			'admin_print_footer_scripts',
			function () use ( $slug ) {
				$this->deactivation_model( $slug );
			},
			100
		);

		// Some admin screens defer jQuery; modal script needs it before our inline block runs.
		add_action(
			'admin_enqueue_scripts',
			static function ( $hook_suffix ) {
				if ( 'plugins.php' !== $hook_suffix ) {
					return;
				}
				wp_enqueue_script( 'jquery' );
			}
		);

		// AJAX handler for plugin deactivation feedback (per-slug action avoids cross-plugin collision).
		add_action(
			'wp_ajax_wpb_sdk_' . $slug . '_deactivation',
			function () use ( $slug ) {
				$this->ajax_deactivation( $slug );
			}
		);

		// Real global callbacks only — PHP 8+ rejects __callStatic names in call_user_func_array().
		register_activation_hook( $plugin_path, 'wpb_sdk_run_product_activation' );
		register_deactivation_hook( $plugin_path, 'wpb_sdk_run_product_deactivation' );

		if ( function_exists( 'wpb_sdk_persist_lifecycle_basename_map' ) ) {
			wpb_sdk_persist_lifecycle_basename_map( $slug, $plugin_path );
		}
		register_uninstall_hook( $plugin_path, 'wpb_sdk_run_product_uninstall' );

		self::register_lifecycle_uninstall_hook( $slug );
		self::register_subsystem_modules( $slug );
	}

	/**
	 * On init: optionally log opt-out submission, then ensure daily cron is scheduled.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function set_logs_schedule( $slug ) {
		$module = self::get_module_config( $slug );
		if ( function_exists( 'wpb_sdk_user_skipped_optin' ) && wpb_sdk_user_skipped_optin( $slug ) ) {
			self::remove_logs_schedule( $slug );
			if ( ! self::is_optout_form_submission( $module ) ) {
				return;
			}
		}
		if ( self::is_optout_form_submission( $module ) ) {
			$this->log_activation( $slug );
		}

		// Remove legacy cron hook names if present.
		wp_clear_scheduled_hook( 'wpb_logger_cron_' . $slug );
		wp_clear_scheduled_hook( 'wpb_daily_sync_cron_' . $slug );

		// Schedule the next daily sync if not already scheduled.
		$daily_start_time = strtotime( '+1 day' );

		if ( ! wp_next_scheduled( 'wpb_data_sync_' . $slug ) ) {
			wp_schedule_event( $daily_start_time, 'daily', 'wpb_data_sync_' . $slug );
		}
	}

	/**
	 * Reset daily sync cron hooks for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public static function reset_logs_schedule( $slug ) {
		wp_clear_scheduled_hook( 'wpb_logger_cron_' . $slug );
		wp_clear_scheduled_hook( 'wpb_daily_sync_cron_' . $slug );

		$daily_start_time = strtotime( '+1 day' );

		if ( ! wp_next_scheduled( 'wpb_data_sync_' . $slug ) ) {
			wp_schedule_event( $daily_start_time, 'daily', 'wpb_data_sync_' . $slug );
		}
	}

	/**
	 * Remove the daily sync event for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public static function remove_logs_schedule( $slug ) {
		wp_clear_scheduled_hook( 'wpb_data_sync_' . $slug );
	}


	/**
	 * Cron callback: send daily telemetry when SDK toggles allow it.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function daily_log_plugin( $slug ) {
		$logs_data = self::get_logs_data( $slug, 'daily' );

		if ( ! empty( $logs_data ) ) {
			$this->send_telemetry_with_explicit(
				$slug,
				$logs_data,
				array(
					'action' => 'daily',
				)
			);
		}
	}


	/**
	 * Send activation telemetry (used by register_activation_hook and opt-in flows).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function log_activation( $slug ) {
		if ( ! self::may_send_telemetry( $slug, 'activate' ) ) {
			return;
		}
		$logs_data = self::get_logs_data( $slug, 'activate' );
		$this->send_telemetry_with_explicit(
			$slug,
			$logs_data,
			array(
				'action' => 'activate',
			)
		);
	}

	/**
	 * Send activation telemetry with a verification token (initial or resend).
	 *
	 * @param string $slug            Product slug.
	 * @param bool   $force_new_token Replace any existing pending token first.
	 * @return void
	 */
	public function log_verification_email_request( $slug, $force_new_token = false ) {
		self::$verification_email_dispatch = array(
			'slug'  => (string) $slug,
			'force' => (bool) $force_new_token,
		);

		$this->log_activation( $slug );

		self::$verification_email_dispatch = null;
	}

	/**
	 * Basename used in plugins.php `plugin=` param (from active_plugins), not assumed `slug/slug.php`.
	 *
	 * @param string $slug Telemetry slug.
	 * @return string
	 */
	private static function resolve_plugin_basename_for_list_table( $slug ) {
		if ( function_exists( 'wpb_sdk_resolve_plugin_basename' ) ) {
			return wpb_sdk_resolve_plugin_basename( $slug );
		}

		$path = wpb_sdk_get_plugin_path( $slug );
		return file_exists( $path ) ? plugin_basename( $path ) : '';
	}

	/**
	 * Output the deactivation survey modal on the plugins screen.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function deactivation_model( $slug ) {
		global $pagenow;

		$on_plugins_screen = is_string( $pagenow ) && 'plugins.php' === $pagenow;
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen ) {
				$on_plugins_screen = $on_plugins_screen
					|| ( 'plugins.php' === $screen->parent_file )
					|| ( 'plugins' === $screen->id )
					|| ( 'plugins-network' === $screen->id );
			}
		}
		if ( ! $on_plugins_screen ) {
			return;
		}
		$plugin_data = wpb_sdk_get_plugin_details( $slug );
		if ( empty( $plugin_data ) || ! is_array( $plugin_data ) ) {
			return;
		}
		$deactivate_vars = array(
			'product_name'         => $plugin_data['Name'],
			'product_slug'         => $slug,
			'plugin_file_basename' => self::resolve_plugin_basename_for_list_table( $slug ),
		);
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Extracting view variables for template inclusion.
		extract( $deactivate_vars, EXTR_SKIP );
		include dirname( __DIR__ ) . '/views/wpb-sdk-deactivate-form.php';
	}


	/**
	 * AJAX: verify nonce, then log deactivation telemetry.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function ajax_deactivation( $slug ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified below.
		if ( ! isset( $_POST['nonce'] ) || '' === $_POST['nonce'] ) {
			wp_die( -1, '', array( 'response' => 403 ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1, '', array( 'response' => 403 ) );
		}

		$plugin_file  = self::resolve_plugin_basename_for_list_table( $slug );
		if ( ! check_ajax_referer( 'deactivate-plugin_' . $plugin_file, 'nonce', false ) ) {
			wp_die( -1, '', array( 'response' => 403 ) );
		}

		$this->log_deactivation( $slug );
		set_transient( self::deactivation_logged_transient_key( $slug ), 1, MINUTE_IN_SECONDS );

		wp_die();
	}


	/**
	 * Core deactivation hook: log telemetry and clear daily cron (no POST body).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function product_deactivation( $slug ) {
		if ( get_transient( self::deactivation_logged_transient_key( $slug ) ) ) {
			delete_transient( self::deactivation_logged_transient_key( $slug ) );
		} else {
			$this->log_deactivation( $slug );
		}
		wp_clear_scheduled_hook( 'wpb_data_sync_' . $slug );
	}

	/**
	 * Transient key: modal AJAX already sent deactivate telemetry for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private static function deactivation_logged_transient_key( $slug ) {
		return 'wpb_sdk_deact_logged_' . sanitize_key( $slug );
	}


	/**
	 * Build deactivation payload; reads optional reason fields when the modal POSTed them.
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	public function log_deactivation( $slug ) {
		if ( ! self::may_send_telemetry( $slug, 'deactivate' ) ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce checked in ajax_deactivation(); core hook has no POST.
		$reason        = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['reason'] ) ) : '';
		$reason_detail = isset( $_POST['reason_detail'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['reason_detail'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		$logs_data = self::get_logs_data( $slug, 'deactivate' );
		if ( ! empty( $logs_data ) ) {
			$this->send_telemetry_with_explicit(
				$slug,
				$logs_data,
				array(
					'action'        => 'deactivate',
					'reason'        => sanitize_text_field( wp_unslash( $reason ) ),
					'reason_detail' => sanitize_text_field( wp_unslash( $reason_detail ) ),
				)
			);
		}
	}


	/**
	 * Resolve product slug from the plugin basename in the current delete request.
	 *
	 * @return string Product slug or empty string.
	 */
	private static function resolve_uninstall_slug_from_request() {
		if ( ! function_exists( 'wpb_sdk_resolve_product_slug_for_lifecycle' ) ) {
			return '';
		}

		$slug = wpb_sdk_resolve_product_slug_for_lifecycle();

		return is_string( $slug ) ? $slug : '';
	}

	/**
	 * Static uninstall callback for a specific product slug.
	 *
	 * @param string $slug Product slug being uninstalled.
	 * @return void
	 */
	public static function log_uninstallation_for_slug( $slug ) {
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}
		self::$current_uninstall_slug = $slug;
		self::log_uninstallation();
	}

	/**
	 * Static uninstall callback: send uninstall telemetry then fire after-uninstall action.
	 *
	 * @param mixed $_unused Reserved for WordPress uninstall hook signature compatibility.
	 * @return void
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- WordPress uninstall hook signature.
	public static function log_uninstallation( $_unused = '' ) {
		unset( $_unused );
		$slug = self::$current_uninstall_slug;
		if ( ! is_string( $slug ) || '' === $slug ) {
			$slug = self::resolve_uninstall_slug_from_request();
		}
		if ( ! is_string( $slug ) || '' === $slug ) {
			return;
		}

		$mid = ! empty( self::$product_data[ $slug ]['module']['id'] )
			? (string) self::$product_data[ $slug ]['module']['id']
			: '';
		if ( '' === $mid && function_exists( 'wpb_sdk_get_registered_module' ) ) {
			$reg_module = wpb_sdk_get_registered_module( $slug );
			if ( ! empty( $reg_module['id'] ) ) {
				$mid = (string) $reg_module['id'];
			}
		}
		if ( '' === $mid ) {
			$mid = '1';
		}

		self::$current_uninstall_slug = $slug;

		self::bootstrap_module_if_needed( $slug );

		$logger = self::instance( $mid, $slug, true );
		if ( ! $logger ) {
			do_action( 'wp_wpb_sdk_after_uninstall', $slug );
			return;
		}

		if ( ! self::may_send_telemetry( $slug, 'uninstall' ) ) {
			do_action( 'wp_wpb_sdk_after_uninstall', $slug );
			return;
		}

		$logs_data = self::get_logs_data( $slug, 'uninstall' );

		if ( ! empty( $logs_data ) ) {
			$logger->send_telemetry_with_explicit(
				$slug,
				$logs_data,
				array(
					'action' => 'uninstall',
				)
			);
		}

		// Allow products to remove options after uninstall telemetry.
		do_action( 'wp_wpb_sdk_after_uninstall', $slug );
	}


	/**
	 * Restore module config on lifecycle hooks (e.g. plugin re-activated after deactivate).
	 *
	 * @param string $slug Product slug.
	 * @return void
	 */
	private static function ensure_module_registered_for_slug( $slug ) {
		self::bootstrap_module_if_needed( $slug );

		$module = array();
		if ( ! empty( self::$product_data[ $slug ]['module'] ) ) {
			$module = self::$product_data[ $slug ]['module'];
		} elseif ( function_exists( 'wpb_sdk_get_registered_module' ) ) {
			$module = wpb_sdk_get_registered_module( $slug );
			if ( ! empty( $module ) && function_exists( 'wpb_sdk_store_module_if_missing_compat' ) ) {
				wpb_sdk_store_module_if_missing_compat( $module );
			}
		}

		if ( empty( $module ) ) {
			return;
		}

		$module_id = isset( $module['id'] ) ? $module['id'] : '1';
		$logger    = self::instance( $module_id, $slug, true );
		if ( $logger ) {
			$logger->wpb_init( $module );
		}
	}

	/**
	 * Only the first opt-in "activate" may include a token (one verification email).
	 *
	 * @param string               $slug    Product slug.
	 * @param string               $action  Telemetry action.
	 * @param array<string, mixed> $module  Module definition.
	 * @param int                  $user_id Admin user ID.
	 * @return bool
	 */
	/**
	 * Opt-in initiator when still valid; otherwise primary administrator.
	 *
	 * @param string $slug Product slug.
	 * @return WP_User|null
	 */
	private static function resolve_telemetry_contact_user( $slug ) {
		if ( function_exists( 'wpb_sdk_get_telemetry_contact_user' ) ) {
			$user = wpb_sdk_get_telemetry_contact_user( $slug );
			if ( $user instanceof WP_User ) {
				return $user;
			}
		}

		return null;
	}

	private static function should_include_verification_token( $slug, $action, array $module, $user_id = 0 ) {
		if ( empty( $module['optin_user_meta']['token'] ) || 'activate' !== $action ) {
			return false;
		}
		if ( ( function_exists( 'wpb_sdk_user_skipped_optin' ) && wpb_sdk_user_skipped_optin( $slug ) )
			|| ( function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $user_id ) )
		) {
			return false;
		}

		$is_initial_or_dispatch = self::is_verification_email_dispatch( $slug ) || '1' !== (string) get_option( self::wpb_sdk_initial_log_option_key( $slug ), '' );

		return $is_initial_or_dispatch || ( function_exists( 'wpb_sdk_is_verification_token_expired' ) && (bool) wpb_sdk_is_verification_token_expired( $slug, $user_id ) );
	}

	/**
	 * Retrieve or create fallback verification token for non-user contexts.
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private static function get_or_create_fallback_verification_token( $slug ) {
		$opt_key = 'wpb_sdk_' . $slug . '_fallback_verify_token';
		$token   = (string) get_option( $opt_key, '' );
		if ( '' === $token ) {
			$token = function_exists( 'wpb_sdk_generate_verification_token' )
				? wpb_sdk_generate_verification_token()
				: wp_generate_password( 32, false, false );
			update_option( $opt_key, $token, false );
		}
		return $token;
	}

	/**
	 * Resolves telemetry display name using WP_User display_name/user_login, first/last name, or email user part.
	 *
	 * @param mixed  $admin_obj WP_User or null.
	 * @param string $first     First name.
	 * @param string $last      Last name.
	 * @param string $email     Resolved email.
	 * @return string
	 */
	private static function resolve_telemetry_display_name( $admin_obj, $first, $last, $email ) {
		$name = '';
		if ( $admin_obj instanceof WP_User ) {
			$name = sanitize_text_field( (string) $admin_obj->display_name );
			if ( '' === $name ) {
				$name = sanitize_text_field( (string) $admin_obj->user_login );
			}
		}
		if ( '' === $name ) {
			$name = trim( $first . ' ' . $last );
		}
		if ( '' === $name && '' !== $email ) {
			$local = strstr( $email, '@', true );
			$name  = false !== $local ? $local : '';
		}
		return $name;
	}

	/**
	 * Retrieve or create verification token for a specific user ID.
	 *
	 * @param int                  $user_id    User ID.
	 * @param string               $token_meta Meta key.
	 * @param string               $slug       Product slug.
	 * @param array<string, mixed> $module     Module definition.
	 * @return string
	 */
	private static function ensure_user_verification_token( $user_id, $token_meta, $slug, array $module ) {
		$expires_meta = function_exists( 'wpb_sdk_verification_token_expires_meta_key' )
			? wpb_sdk_verification_token_expires_meta_key( $module )
			: $token_meta . '_expires';
		$existing     = get_user_meta( $user_id, $token_meta, true );
		$expires_at   = function_exists( 'wpb_sdk_ensure_verification_token_expiry_meta' )
			? wpb_sdk_ensure_verification_token_expiry_meta( $slug, $user_id )
			: (int) get_user_meta( $user_id, $expires_meta, true );
		$is_expired   = $expires_at > 0 && time() > $expires_at;

		$force_new = self::is_verification_email_dispatch( $slug )
			&& ! empty( self::$verification_email_dispatch['force'] );

		if ( ! $force_new && is_string( $existing ) && '' !== $existing && ! $is_expired ) {
			return $existing;
		}

		$token = wp_generate_password( 40, false, false );
		update_user_meta( $user_id, $token_meta, $token );
		if ( function_exists( 'wpb_sdk_verification_token_expires_at' ) ) {
			update_user_meta( $user_id, $expires_meta, wpb_sdk_verification_token_expires_at( $module ) );
		}

		return $token;
	}

	/**
	 * Unverified: token + empty email_verification string. Verified: email_verification "yes", no token.
	 *
	 * @param int                  $user_id WordPress user ID for the telemetry contact, or 0 during non-interactive sends.
	 * @param string               $slug    Product slug.
	 * @param array<string, mixed> $module  Module definition.
	 * @return string Non-empty token, or empty string on failure.
	 */
	private static function ensure_verification_token( $user_id, $slug, array $module ) {
		$user_id    = (int) $user_id;
		$token_meta = ! empty( $module['optin_user_meta']['token'] )
			? (string) $module['optin_user_meta']['token']
			: '';

		if ( $user_id > 0 && '' !== $token_meta ) {
			return self::ensure_user_verification_token( $user_id, $token_meta, $slug, $module );
		}

		return self::get_or_create_fallback_verification_token( $slug );
	}

	/**
	 * Best-effort MySQL server version string for telemetry (may use mysqli handle).
	 *
	 * @param \wpdb $wpdb WordPress database object.
	 * @return string
	 */
	private static function wpb_sdk_get_mysql_server_version( $wpdb ) {
		if ( ! method_exists( $wpdb, 'db_version' ) ) {
			return 'unknown';
		}
		if ( function_exists( 'mysqli_get_server_info' ) && isset( $wpdb->dbh ) && $wpdb->dbh instanceof mysqli ) {
			// phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysqli_get_server_info,WordPress.DB.DirectDatabaseQuery.DirectQuery -- Telemetry diagnostic only.
			return mysqli_get_server_info( $wpdb->dbh );
		}
		return $wpdb->db_version();
	}

	/**
	 * Get HTTP blocking diagnostic string.
	 *
	 * @return string
	 */
	private static function get_http_blocked_status() {
		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL ) {
			return 'none';
		}
		return defined( 'WP_ACCESSIBLE_HOSTS' )
			? 'partially (accessible hosts: ' . esc_html( WP_ACCESSIBLE_HOSTS ) . ')'
			: 'all';
	}

	/**
	 * Get cURL version string.
	 *
	 * @return string
	 */
	private static function get_curl_version_string() {
		if ( ! function_exists( 'curl_init' ) || ! function_exists( 'curl_version' ) ) {
			return '';
		}
		$curl = curl_version();
		return '(' . $curl['version'] . ' ' . $curl['ssl_version'] . ')';
	}

	/**
	 * Build diagnostic metadata for telemetry payload.
	 *
	 * @param \wpdb       $wpdb Database instance.
	 * @param \WP_Theme   $theme_data Active theme.
	 * @param string      $ip Client/server IP.
	 * @param mixed       $location Location details.
	 * @param array|false $multisites Result from get_multisites().
	 * @return array{site_meta_info: array, location_details: mixed}
	 */
	private static function build_site_info_diagnostics( $wpdb, $theme_data, $ip, $location, $multisites ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$users_count = '';
		if ( function_exists( 'count_users' ) ) {
			$uc          = count_users();
			$users_count = isset( $uc['total_users'] ) ? (int) $uc['total_users'] : '';
		}

		$server = isset( $_SERVER['SERVER_SOFTWARE'] )
			? sanitize_text_field( wp_unslash( (string) $_SERVER['SERVER_SOFTWARE'] ) )
			: '';

		$site_meta_info = array(
			'is_multisite'          => is_multisite(),
			'multisites'            => $multisites,
			'php_version'           => phpversion(),
			'wp_version'            => get_bloginfo( 'version' ),
			'server'                => $server,
			'timezoneoffset'        => gmdate( 'P' ),
			'ext/mysqli'            => isset( $wpdb->use_mysqli ) && ! empty( $wpdb->use_mysqli ),
			'mysql_version'         => self::wpb_sdk_get_mysql_server_version( $wpdb ),
			'memory_limit'          => defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : ini_get( 'memory_limit' ),
			'external_http_blocked' => self::get_http_blocked_status(),
			'wp_locale'             => get_locale(),
			'db_charset'            => defined( 'DB_CHARSET' ) ? DB_CHARSET : '',
			'debug_mode'            => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'wp_max_upload'         => size_format( wp_max_upload_size() ),
			'php_time_limit'        => function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : '',
			'php_error_log'         => function_exists( 'ini_get' ) ? ini_get( 'error_log' ) : '',
			'fsockopen'             => function_exists( 'fsockopen' ),
			'open_ssl'              => defined( 'OPENSSL_VERSION_TEXT' ) ? OPENSSL_VERSION_TEXT : '',
			'curl'                  => self::get_curl_version_string(),
			'ip'                    => $ip,
			'user_count'            => $users_count,
			'admin_email'           => sanitize_email( get_bloginfo( 'admin_email' ) ),
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- WP_Theme API uses Name and Version.
			'theme_name'            => sanitize_text_field( $theme_data->Name ),
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- WP_Theme API uses Name and Version.
			'theme_version'         => sanitize_text_field( $theme_data->Version ),
		);

		return array(
			'site_meta_info'   => $site_meta_info,
			'location_details' => null !== $location ? $location : '',
		);
	}

	/**
	 * Shared entry: cron/activation call this statically.
	 *
	 * @param string $slug Product slug.
	 * @param string $action Telemetry action.
	 * @return array
	 */
	public static function get_logs_data( $slug, $action = '' ) {
		self::bootstrap_module_if_needed( $slug );
		if ( empty( self::$product_data[ $slug ]['module'] ) ) {
			return array();
		}
		$mid = self::$product_data[ $slug ]['module']['id'] ?? '1';
		if ( empty( $mid ) ) {
			$mid = 1;
		}
		$inst = self::instance( $mid, $slug, true );
		return $inst->collect_logs_data( $slug, $action );
	}

	/**
	 * Forward for SDK views that still call $logger->get_logs_data().
	 *
	 * @param string $name Method name.
	 * @param array  $args Arguments.
	 * @return mixed
	 */
	public function __call( $name, $args ) {
		if ( 'get_logs_data' === $name ) {
			return self::get_logs_data( isset( $args[0] ) ? $args[0] : '', isset( $args[1] ) ? $args[1] : '' );
		}
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Intentional guard for unsupported magic calls.
		trigger_error( 'Call to undefined method ' . __CLASS__ . '::' . esc_html( $name ), E_USER_WARNING );
	}

	/**
	 * Populate verification fields in user info payload.
	 *
	 * @param array<string, mixed> $user_info        Reference to user info array.
	 * @param string               $slug             Product slug.
	 * @param string               $action           Telemetry action.
	 * @param array<string, mixed> $module           Module definition.
	 * @param bool                 $user_skipped     Whether user skipped opt-in.
	 * @param int                  $user_id          User ID.
	 * @param bool                 $payload_verified Whether user is already verified.
	 * @return void
	 */
	private static function populate_verification_payload( array &$user_info, $slug, $action, array $module, $user_skipped, $user_id, $payload_verified ) {
		if ( $user_skipped ) {
			$user_info['email_verification'] = 'skip';
		} elseif ( $payload_verified ) {
			$user_info['email_verification'] = 'yes';
		} elseif ( self::should_include_verification_token( $slug, $action, $module, $user_id ) ) {
			$user_info['email_verification'] = '';
			$token                           = $user_id > 0 ? self::ensure_verification_token( $user_id, $slug, $module ) : '';
			$user_info['token']              = is_string( $token ) ? $token : '';
		} else {
			$user_info['email_verification'] = 'pending';
		}
	}

	/**
	 * Resolve admin contact email.
	 *
	 * @param mixed  $admin_obj User object or null.
	 * @param string $slug      Product slug.
	 * @return string
	 */
	private static function resolve_telemetry_email( $admin_obj, $slug ) {
		if ( $admin_obj instanceof WP_User && '' !== $admin_obj->user_email ) {
			return sanitize_email( $admin_obj->user_email );
		}
		if ( function_exists( 'wpb_sdk_resolve_optin_admin_user_id' ) ) {
			$fallback_id = wpb_sdk_resolve_optin_admin_user_id( $slug, 0 );
			if ( $fallback_id > 0 ) {
				$fallback_user = get_user_by( 'id', $fallback_id );
				if ( $fallback_user instanceof WP_User && '' !== $fallback_user->user_email ) {
					return sanitize_email( $fallback_user->user_email );
				}
			}
		}
		return sanitize_email( (string) get_bloginfo( 'admin_email' ) );
	}



	/**
	 * Build user info payload for Laravel telemetry format.
	 *
	 * @param string               $slug         Product slug.
	 * @param string               $action       Telemetry action.
	 * @param array<string, mixed> $module       Module definition.
	 * @param bool                 $user_skipped Whether user skipped opt-in.
	 * @return array<string, mixed>
	 */
	private static function build_laravel_user_info_payload( $slug, $action, array $module, $user_skipped ) {
		$admin_user_id = 0;
		$admin_obj     = self::resolve_telemetry_contact_user( $slug );
		$admin_meta    = array();

		if ( $admin_obj instanceof WP_User ) {
			$admin_user_id = (int) $admin_obj->ID;
			$admin_meta    = get_user_meta( $admin_user_id );
		}

		$first        = isset( $admin_meta['first_name'][0] ) ? sanitize_text_field( (string) $admin_meta['first_name'][0] ) : '';
		$last         = isset( $admin_meta['last_name'][0] ) ? sanitize_text_field( (string) $admin_meta['last_name'][0] ) : '';
		$email        = self::resolve_telemetry_email( $admin_obj, $slug );
		$display_name = self::resolve_telemetry_display_name( $admin_obj, $first, $last, $email );

		$payload_verified = function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $admin_user_id );

		$user_info = array(
			'user_email' => $email,
			'name'       => $display_name,
		);

		self::populate_verification_payload( $user_info, $slug, $action, $module, $user_skipped, $admin_user_id, $payload_verified );
		return $user_info;
	}

	/**
	 * Build user info payload for classic telemetry format.
	 *
	 * @param string               $slug         Product slug.
	 * @param string               $action       Telemetry action.
	 * @param array<string, mixed> $module       Module definition.
	 * @param bool                 $user_skipped Whether user skipped opt-in.
	 * @return array<string, mixed>
	 */
	private static function build_classic_user_info_payload( $slug, $action, array $module, $user_skipped ) {
		$contact_user = self::resolve_telemetry_contact_user( $slug );
		$admin        = ( $contact_user instanceof WP_User ) ? $contact_user->data : null;
		$admin_meta   = ( $contact_user instanceof WP_User ) ? get_user_meta( $contact_user->ID ) : array();

		$user_info = array(
			'user_email'     => ! empty( $admin ) ? sanitize_email( $admin->user_email ) : '',
			'user_nickname'  => ! empty( $admin ) ? sanitize_text_field( $admin->user_nicename ) : '',
			'user_firstname' => isset( $admin_meta['first_name'][0] ) ? sanitize_text_field( (string) $admin_meta['first_name'][0] ) : '',
			'user_lastname'  => isset( $admin_meta['last_name'][0] ) ? sanitize_text_field( (string) $admin_meta['last_name'][0] ) : '',
		);

		$has_verification = ! empty( $module['optin_user_meta']['token'] )
			&& function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
			&& '' !== wpb_sdk_email_verified_meta_key_from_module( $module );

		if ( ! $has_verification ) {
			return $user_info;
		}

		$token_uid = ! empty( $admin ) ? (int) $admin->ID : 0;
		if ( $token_uid < 1 && function_exists( 'wpb_sdk_resolve_telemetry_contact_user_id' ) ) {
			$token_uid = wpb_sdk_resolve_telemetry_contact_user_id( $slug );
		}
		$payload_verified = ( $token_uid > 0 ) && function_exists( 'wpb_sdk_is_user_verified' ) && wpb_sdk_is_user_verified( $slug, $token_uid );

		self::populate_verification_payload( $user_info, $slug, $action, $module, $user_skipped, $token_uid, $payload_verified );
		return $user_info;
	}

	/**
	 * Build telemetry payload from module config (user_info format, opt-out key, etc.).
	 *
	 * @param string $slug Product slug.
	 * @param string $action Telemetry action.
	 * @return array
	 */
	private function collect_logs_data( $slug, $action = '' ) {
		global $wpdb;

		if ( empty( self::$product_data[ $slug ]['module'] ) ) {
			return array();
		}

		$module         = self::$product_data[ $slug ]['module'];
		$laravel_format = self::uses_laravel_user_info( $module );
		$data           = array();
		$theme_data     = wp_get_theme();

		$sdk_data = json_decode( get_option( 'wpb_sdk_' . $slug ), true );
		if ( ! is_array( $sdk_data ) ) {
			$sdk_data = array();
		}
		$send_wpb_sdk_communication   = isset( $sdk_data['communication'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['communication'] );
		$send_wpb_sdk_diagnostic_info = isset( $sdk_data['diagnostic_info'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['diagnostic_info'] );
		$send_wpb_sdk_extensions      = isset( $sdk_data['extensions'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['extensions'] );
		$user_skipped                 = isset( $sdk_data['user_skip'] ) && wpb_sdk_sdk_option_is_enabled( $sdk_data['user_skip'] );
		$is_initial_skip_activate     = $user_skipped && 'activate' === $action && self::may_send_telemetry( $slug, 'activate' );

		if (
			! $send_wpb_sdk_communication
			&& ! $send_wpb_sdk_diagnostic_info
			&& ! $send_wpb_sdk_extensions
			&& ! $is_initial_skip_activate
		) {
			self::remove_logs_schedule( $slug );
			return array();
		}

		if ( ! $user_skipped ) {
			self::reset_logs_schedule( $slug );
		} else {
			self::remove_logs_schedule( $slug );
		}

		$data['authentication']['public_key'] = $module['public_key'];

		if ( $send_wpb_sdk_communication || $is_initial_skip_activate ) {
			$data['user_info'] = $laravel_format
				? self::build_laravel_user_info_payload( $slug, $action, $module, $user_skipped )
				: self::build_classic_user_info_payload( $slug, $action, $module, $user_skipped );
		}

		$data['product_info']                = $this->get_product_data( $slug );
		$data['product_info']['sdk_version'] = WP_WPBRIGADE_SDK_VERSION;

		if ( function_exists( 'wpb_sdk_get_client_info' ) ) {
			$data['client_info'] = wpb_sdk_get_client_info();
		}

		if ( $send_wpb_sdk_diagnostic_info || $is_initial_skip_activate ) {
			$data['site_info'] = array(
				'site_url' => site_url(),
				'home_url' => home_url(),
			);

			$data['product_info']['product_settings'] = $this->get_product_settings( $slug );

			$ip                                    = $this->get_ip();
			$location                              = $this->get_location_details( $ip );
			$diag                                  = self::build_site_info_diagnostics(
				$wpdb,
				$theme_data,
				$ip,
				$location,
				$this->get_multisites()
			);
			$data['site_info']['site_meta_info']   = $diag['site_meta_info'];
			$data['site_info']['location_details'] = $diag['location_details'];
		}

		if ( $send_wpb_sdk_extensions || $is_initial_skip_activate ) {
			$data['site_plugins'] = $this->get_all_plugins( $slug, $action );
		}

		return $data;
	}



	/**
	 * Retrieve plugin settings related to the product.
	 *
	 * @param string $slug The slug of the product.
	 * @return array
	 */
	private function get_product_settings( $slug ) {
		$product_data   = self::$product_data[ $slug ]['module'] ?? array();
		$plugin_options = array();

		// Opt-in flag may live network-wide (wp_sitemeta) on Multisite.
		$optin           = $product_data['optin'] ?? array();
		$site_optin_name = ! empty( $optin['use_site_option'] ) && ! empty( $optin['option_name'] ) ? (string) $optin['option_name'] : '';

		// Pull settings data from db.
		foreach ( $product_data['settings'] as $option_name => $default_value ) {
			$get_option       = $option_name === $site_optin_name ? get_site_option( $option_name ) : get_option( $option_name );
			$plugin_options[] = array(
				'option' => $option_name,
				'value'  => ! empty( $get_option ) ? wp_json_encode( $get_option ) : $default_value,
			);
		}

		return $plugin_options;
	}


	/**
	 * Collect multisite summary (no other blogs' names or URLs are sent).
	 *
	 * @return array|false
	 */
	private function get_multisites() {
		if ( ! is_multisite() ) {
			return false;
		}

		return array(
			'network_id'  => hash( 'sha256', untrailingslashit( strtolower( network_home_url() ) ) ),
			'sites_count' => (int) get_sites( array( 'count' => true ) ),
			'blog_id'     => get_current_blog_id(),
			'is_main'     => is_main_site(),
		);
	}


	/**
	 * Get user IP information.
	 *
	 * @return string|null
	 */
	private function get_ip() {
		if ( function_exists( 'wpb_sdk_get_client_ip' ) ) {
			$ip = wpb_sdk_get_client_ip();
			return '' !== $ip ? $ip : null;
		}

		$fields = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $fields as $ip_field ) {
			if ( ! empty( $_SERVER[ $ip_field ] ) ) {
				return sanitize_text_field( wp_unslash( (string) $_SERVER[ $ip_field ] ) );
			}
		}

		return null;
	}

	/**
	 * Collect plugins information: Active/Inactive plugins.
	 *
	 * @param string $slug   Product slug.
	 * @param string $action Telemetry action.
	 * @return array
	 */
	private function get_all_plugins( $slug, $action ) {
		$all_plugins      = array_keys( get_plugins() );
		$active_plugins   = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		// WordPress fires activate_/deactivate_ hooks before it saves the active list,
		// so report this product's own state from the action instead.
		$self = plugin_basename( wpb_sdk_get_plugin_path( $slug ) );
		if ( 'activate' === $action && in_array( $self, $all_plugins, true ) ) {
			$active_plugins[] = $self;
		} elseif ( in_array( $action, array( 'deactivate', 'uninstall' ), true ) ) {
			$active_plugins = array_diff( $active_plugins, array( $self ) );
		}

		$active_plugins = array_values( array_unique( $active_plugins ) );

		$inactive_plugins = array_diff( $all_plugins, $active_plugins );

		return array(
			'active'   => $active_plugins,
			'inactive' => $inactive_plugins,
		);
	}

	/**
	 * Get location details based on IP.
	 *
	 * @param string|null $ip IP address or null.
	 * @return array<string, mixed>
	 */
	private function get_location_details( $ip ) {
		$location_details = array();
		if ( ! $ip ) {
			return array(
				'response_code' => '400',
				'message'       => 'Error: IP address is required.',
			);
		}

		try {
			// phpcs:disable WordPress.WP.AlternativeFunctions.curl_curl_init,WordPress.WP.AlternativeFunctions.curl_curl_setopt,WordPress.WP.AlternativeFunctions.curl_curl_exec -- Third-party geo API.
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "https://api.iplocation.net/?ip={$ip}" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$execute = curl_exec( $ch );
			// phpcs:enable WordPress.WP.AlternativeFunctions.curl_curl_init,WordPress.WP.AlternativeFunctions.curl_curl_setopt,WordPress.WP.AlternativeFunctions.curl_curl_exec

			$result = json_decode( $execute );

			if ( $result && '200' === (string) $result->response_code ) {
				if ( '-' !== $result->country_name && '-' !== $result->country_code2 ) {
					$location_details = array(
						'response_code' => $result->response_code,
						'message'       => 'Success',
						'data'          => array(
							'country_name' => $result->country_name,
							'country_code' => $result->country_code2,
						),
					);
				} else {
					$missing_info = array();
					if ( '-' === $result->country_name ) {
						$missing_info[] = 'country_name';
					}
					if ( '-' === $result->country_code2 ) {
						$missing_info[] = 'country_code';
					}
					$location_details = array(
						'response_code' => '400',
						'message'       => 'Error: Missing information for ' . implode( ', ', $missing_info ) . ' for the IP Address: ' . $ip,
					);
				}
			} else {
				$location_details = array(
					'response_code' => '400',
					'message'       => 'Error: Invalid response code or data for the IP Address: ' . $ip,
				);
			}

			return $location_details;
		} catch ( \Exception $e ) {
			return array(
				'response_code' => '400',
				'message'       => 'Error: ' . $e->getMessage(),
			);
		}
	}

	/**
	 * Get product data.
	 *
	 * @param string $slug Product slug.
	 * @return array<string, mixed>
	 */
	private function get_product_data( $slug ) {
		$plugin_data = wpb_sdk_get_plugin_details( $slug );
		$mod         = self::$product_data[ $slug ]['module'] ?? array();
		$pid         = $mod['id'] ?? '1';
		return array(
			'name'    => $plugin_data['Name'] ?? $plugin_data['Title'],
			'slug'    => $slug,
			'id'      => $pid,
			'type'    => 'Plugin',
			'path'    => wpb_sdk_get_plugin_path( $slug ),
			'version' => $plugin_data['Version'],
		);
	}

	/**
	 * Option: first successful logger payload for this slug (per site).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private static function wpb_sdk_initial_log_option_key( $slug ) {
		return 'wpb_sdk_' . $slug . '_initial_log_sent';
	}

	/**
	 * Whether an explicit verification-email dispatch is in progress for this slug.
	 *
	 * @param string $slug Product slug.
	 * @return bool
	 */
	private static function is_verification_email_dispatch( $slug ) {
		return is_array( self::$verification_email_dispatch )
			&& (string) self::$verification_email_dispatch['slug'] === (string) $slug;
	}

	/**
	 * Whether telemetry may be sent for this product and action.
	 *
	 * @param string $slug   Product slug.
	 * @param string $action activate|deactivate|uninstall|daily|''.
	 * @return bool
	 */
	private static function may_send_telemetry( $slug, $action = '' ) {
		if ( function_exists( 'wpb_sdk_may_send_telemetry_action' ) ) {
			return wpb_sdk_may_send_telemetry_action( $slug, $action );
		}

		if ( function_exists( 'wpb_sdk_has_telemetry_consent' ) ) {
			return wpb_sdk_has_telemetry_consent( $slug );
		}

		return true;
	}

	/**
	 * Merge explicit_logs into payload and send (no-op when logs_data is empty).
	 *
	 * @param string               $slug Product slug.
	 * @param array<string, mixed> $logs_data Collected payload.
	 * @param array<string, mixed> $explicit_logs Keys for explicit_logs (must include action).
	 * @return void
	 */
	private function send_telemetry_with_explicit( $slug, $logs_data, array $explicit_logs ) {
		$action = isset( $explicit_logs['action'] ) ? (string) $explicit_logs['action'] : '';
		if ( empty( $logs_data ) || ! self::may_send_telemetry( $slug, $action ) ) {
			return;
		}
		$this->send(
			$slug,
			array_merge(
				$logs_data,
				array(
					'explicit_logs' => $explicit_logs,
				)
			)
		);
	}

	/**
	 * Send log data to the API.
	 *
	 * @param string $slug The product slug.
	 * @param array  $payload The log data payload.
	 */
	private function send( $slug, $payload ) {
		$payload['sent_at'] = current_time( 'mysql', 1 );

		$initial_key           = self::wpb_sdk_initial_log_option_key( $slug );
		$first_done            = get_option( $initial_key, '' );
		$payload['log_status'] = ( '1' === (string) $first_done ) ? 'returning' : 'new';

		$this->send_data_to_api( $slug, $payload );
	}


	/**
	 * API base URL for a product (each bundled copy may use a different endpoint).
	 *
	 * @param string $slug Product slug.
	 * @return string
	 */
	private function resolve_api_endpoint_for_slug( $slug ) {
		$module = self::get_module_config( $slug );
		if ( ! empty( $module['api_endpoint'] ) ) {
			$endpoint = (string) $module['api_endpoint'];
		} elseif ( function_exists( 'wpb_sdk_get_provider_for_slug' ) ) {
			$provider = wpb_sdk_get_provider_for_slug( $slug );
			$endpoint = ! empty( $provider['api_endpoint'] ) ? (string) $provider['api_endpoint'] : '';
		} else {
			$endpoint = '';
		}

		if ( '' === $endpoint && defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
			$endpoint = (string) WPBRIGADE_SDK_API_ENDPOINT;
		}

		$endpoint = rtrim( $endpoint, '/' );

		/**
		 * Filter telemetry API base URL for a product.
		 *
		 * @param string $endpoint Base URL (no trailing slash).
		 * @param string $slug     Product slug.
		 */
		return (string) apply_filters( 'wpb_sdk_api_endpoint', $endpoint, $slug );
	}

	private function send_data_to_api( $slug, $data ) {
		$token = self::$product_data[ $slug ]['module']['public_key'] ?? null;

		if ( ! $token ) {
			return;
		}

		$api_endpoint = $this->resolve_api_endpoint_for_slug( $slug );
		if ( '' === $api_endpoint ) {
			return;
		}

		$response = wp_remote_post(
			$api_endpoint . '/logger',
			array(
				'method'  => 'POST',
				'body'    => wp_json_encode( $data ),
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Content-Type'  => 'application/json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code >= 200 && $code < 300 && isset( $data['log_status'] ) && 'new' === $data['log_status'] ) {
			update_option( self::wpb_sdk_initial_log_option_key( $slug ), '1', false );
		}
		if (
			$code >= 200
			&& $code < 300
			&& function_exists( 'wpb_sdk_user_skipped_optin' )
			&& wpb_sdk_user_skipped_optin( $slug )
		) {
			self::remove_logs_schedule( $slug );
		}
	}
}
