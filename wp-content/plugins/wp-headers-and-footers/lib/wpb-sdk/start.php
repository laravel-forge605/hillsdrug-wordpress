<?php
/**
 * WPBrigade Telemetry SDK bootstrap broker.
 *
 * File-scope SDK selection (DB + reorder + redirect) always runs so a newer
 * bundle can still win after an older runtime loaded earlier in the request.
 *
 * @package wpbrigade_sdk
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * On-disk version for THIS bundle only (never use global WPBRIGADE_SDK_VERSION
 * for provider advertising — that constant is first-writer-wins across plugins).
 */
$wpb_sdk_version      = '3.4.0';
$wpb_sdk_version_file = __DIR__ . '/version.php';
if ( is_readable( $wpb_sdk_version_file ) ) {
	$wpb_sdk_loaded = include $wpb_sdk_version_file;
	if ( is_string( $wpb_sdk_loaded ) && '' !== $wpb_sdk_loaded ) {
		$wpb_sdk_version = $wpb_sdk_loaded;
	}
}

// Load SDK broker helpers when missing (older 3.3.x start.php never defined them).
if ( ! function_exists( 'wpb_sdk_get_active_plugins_option' ) ) {
	$wpb_sdk_broker_file = __DIR__ . '/includes/wpb-sdk-broker.php';
	if ( is_readable( $wpb_sdk_broker_file ) ) {
		require_once $wpb_sdk_broker_file;
	}
}

#region File-scope SDK selection (always runs) ---------------------------------

$wpb_this_sdk_relative_path = function_exists( 'wpb_sdk_sdk_relative_path' )
	? wpb_sdk_sdk_relative_path( __DIR__ )
	: ltrim( str_replace( '\\', '/', str_replace( WP_PLUGIN_DIR, '', __DIR__ ) ), '/' );

$wpb_this_plugin_file_abs = function_exists( 'wpb_sdk_find_caller_plugin_file' )
	? wpb_sdk_find_caller_plugin_file( __FILE__ )
	: __FILE__;

$wpb_this_plugin_basename = ( defined( 'WP_PLUGIN_DIR' ) && 0 === strpos(
	function_exists( 'wpb_sdk_normalize_path' ) ? wpb_sdk_normalize_path( $wpb_this_plugin_file_abs ) : $wpb_this_plugin_file_abs,
	function_exists( 'wpb_sdk_normalize_path' ) ? wpb_sdk_normalize_path( WP_PLUGIN_DIR ) : WP_PLUGIN_DIR
) )
	? plugin_basename( $wpb_this_plugin_file_abs )
	: plugin_basename( $wpb_this_plugin_file_abs );

if ( function_exists( 'wpb_sdk_get_active_plugins_option' ) ) {
	global $wpb_sdk_active_plugins;

if ( is_admin() ) {
	wpb_sdk_prune_active_plugins_option();
}
	$wpb_sdk_active_plugins = wpb_sdk_get_active_plugins_option();

	// Register / refresh this SDK in the DB option.
	if (
		! isset( $wpb_sdk_active_plugins->plugins[ $wpb_this_sdk_relative_path ] )
		|| (string) $wpb_sdk_active_plugins->plugins[ $wpb_this_sdk_relative_path ]->version !== (string) $wpb_sdk_version
	) {
		$wpb_sdk_active_plugins->plugins[ $wpb_this_sdk_relative_path ] = (object) array(
			'version'     => $wpb_sdk_version,
			'type'        => 'plugin',
			'timestamp'   => time(),
			'plugin_path' => $wpb_this_plugin_basename,
		);
		wpb_sdk_save_active_plugins_option();
	}

	$wpb_is_current_sdk_newest = isset( $wpb_sdk_active_plugins->newest )
		&& ( $wpb_this_sdk_relative_path === $wpb_sdk_active_plugins->newest->sdk_path );

	if ( ! isset( $wpb_sdk_active_plugins->newest ) ) {
		wpb_sdk_update_newest_version( $wpb_this_sdk_relative_path, $wpb_this_plugin_basename );
		$wpb_is_current_sdk_newest = true;
	} elseif ( version_compare( (string) $wpb_sdk_active_plugins->newest->version, (string) $wpb_sdk_version, '<' ) ) {
		// This bundle is newer than the stored newest.
		wpb_sdk_update_newest_version( $wpb_this_sdk_relative_path, $wpb_this_plugin_basename );
		$wpb_is_current_sdk_newest = true;

		if ( function_exists( 'wpb_sdk_is_older_runtime_loaded' )
			&& wpb_sdk_is_older_runtime_loaded( $wpb_sdk_version )
		) {
			$wpb_sdk_order_changed = false;
			if ( empty( $wpb_sdk_active_plugins->newest->in_activation ) ) {
				$wpb_sdk_order_changed = wpb_sdk_newest_sdk_plugin_first();
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reload current URL after reorder.
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : admin_url();
			if ( $wpb_sdk_order_changed && function_exists( 'wpb_sdk_maybe_redirect_for_sdk_switch' ) ) {
				wpb_sdk_maybe_redirect_for_sdk_switch( $request_uri );
			}
		}
	} else {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$wpb_newest_entry = isset( $wpb_sdk_active_plugins->plugins[ $wpb_sdk_active_plugins->newest->sdk_path ] )
			? $wpb_sdk_active_plugins->plugins[ $wpb_sdk_active_plugins->newest->sdk_path ]
			: null;

		$wpb_is_newest_module_active = false;
		if ( $wpb_newest_entry && ! empty( $wpb_newest_entry->plugin_path ) ) {
			$wpb_is_newest_module_active = is_plugin_active( (string) $wpb_newest_entry->plugin_path );
		}

		if ( $wpb_is_current_sdk_newest
			&& ! $wpb_is_newest_module_active
			&& empty( $wpb_sdk_active_plugins->newest->in_activation )
		) {
			$wpb_sdk_active_plugins->newest->in_activation = true;
			wpb_sdk_save_active_plugins_option();
		}

		$wpb_this_starter = function_exists( 'wpb_sdk_normalize_path' )
			? wpb_sdk_normalize_path( __DIR__ . '/start.php' )
			: __DIR__ . '/start.php';

		$wpb_newest_starter = function_exists( 'wpb_sdk_resolve_newest_starter_path' )
			? wpb_sdk_resolve_newest_starter_path()
			: '';

		$wpb_newest_path_valid = (
			( $wpb_is_newest_module_active || ! empty( $wpb_sdk_active_plugins->newest->in_activation ) )
			&& '' !== $wpb_newest_starter
		);

		if ( ! $wpb_newest_path_valid && ! $wpb_is_current_sdk_newest ) {
			unset( $wpb_sdk_active_plugins->plugins[ $wpb_sdk_active_plugins->newest->sdk_path ] );
		}

		if (
			! ( $wpb_is_newest_module_active || ! empty( $wpb_sdk_active_plugins->newest->in_activation ) )
			|| ! $wpb_newest_path_valid
			|| (
				$wpb_this_sdk_relative_path === $wpb_sdk_active_plugins->newest->sdk_path
				&& version_compare( (string) $wpb_sdk_active_plugins->newest->version, (string) $wpb_sdk_version, '>' )
			)
		) {
			wpb_sdk_fallback_to_newest_active_sdk();
		} elseif (
			$wpb_is_newest_module_active
			&& $wpb_this_sdk_relative_path === $wpb_sdk_active_plugins->newest->sdk_path
			&& (
				! empty( $wpb_sdk_active_plugins->newest->in_activation )
				|| (
					class_exists( 'WPBRIGADE_Logger', false )
					&& (
						! defined( 'WP_WPBRIGADE_SDK_VERSION' )
						|| version_compare( (string) WP_WPBRIGADE_SDK_VERSION, (string) $wpb_sdk_version, '<' )
					)
				)
			)
		) {
			if ( ! empty( $wpb_sdk_active_plugins->newest->in_activation ) ) {
				$wpb_sdk_active_plugins->newest->in_activation = false;
				wpb_sdk_save_active_plugins_option();
			}

			if ( wpb_sdk_newest_sdk_plugin_first() && class_exists( 'WPBRIGADE_Logger', false ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : admin_url();
				if ( function_exists( 'wpb_sdk_maybe_redirect_for_sdk_switch' ) ) {
					wpb_sdk_maybe_redirect_for_sdk_switch( $request_uri );
				}
			}
		}

		// This copy is older than stored newest — load newest starter instead.
		if (
			isset( $wpb_sdk_active_plugins->newest )
			&& version_compare( (string) $wpb_sdk_version, (string) $wpb_sdk_active_plugins->newest->version, '<' )
		) {
			$wpb_newest_starter = function_exists( 'wpb_sdk_resolve_newest_starter_path' )
				? wpb_sdk_resolve_newest_starter_path()
				: '';
			if ( '' !== $wpb_newest_starter
				&& wpb_sdk_normalize_path( $wpb_newest_starter ) !== wpb_sdk_normalize_path( __FILE__ )
			) {
				wpb_sdk_newest_sdk_plugin_first();
				require_once $wpb_newest_starter;
				return;
			}
		}
	}
}

#endregion File-scope SDK selection --------------------------------------------

if ( ! function_exists( 'wpb_sdk_ensure_registry' ) ) {
	/**
	 * Ensure the shared in-memory SDK registry exists.
	 *
	 * @return array<string, mixed>
	 */
	function wpb_sdk_ensure_registry() {
		if ( ! isset( $GLOBALS['wpb_sdk_registry'] ) || ! is_array( $GLOBALS['wpb_sdk_registry'] ) ) {
			$GLOBALS['wpb_sdk_registry'] = array();
		}

		$defaults = array(
			'providers'           => array(),
			'active_provider'     => null,
			'runtime_loaded'      => false,
			'modules_booted'      => false,
			'initialized_modules' => array(),
			'modules'             => array(),
			'pending_modules'     => array(),
		);

		foreach ( $defaults as $key => $value ) {
			if ( ! isset( $GLOBALS['wpb_sdk_registry'][ $key ] ) ) {
				$GLOBALS['wpb_sdk_registry'][ $key ] = $value;
			}
		}

		return $GLOBALS['wpb_sdk_registry'];
	}
}

if ( ! function_exists( 'wpb_sdk_register_provider' ) ) {
	/**
	 * Register an SDK provider from a plugin bundle (in-memory).
	 *
	 * @param array $provider Provider payload.
	 * @return void
	 */
	function wpb_sdk_register_provider( $provider ) {
		wpb_sdk_ensure_registry();

		$key = isset( $provider['provider_key'] ) ? $provider['provider_key'] : md5( wp_json_encode( $provider ) );
		$GLOBALS['wpb_sdk_registry']['providers'][ $key ] = $provider;
	}
}

if ( ! function_exists( 'wpb_sdk_select_provider' ) ) {
	/**
	 * Select the active runtime provider (DB newest preferred, else highest in-memory).
	 *
	 * @return array|null
	 */
	function wpb_sdk_select_provider() {
		$registry = wpb_sdk_ensure_registry();

		if ( ! empty( $registry['active_provider'] ) ) {
			return $registry['active_provider'];
		}

		if ( function_exists( 'wpb_sdk_provider_from_db_newest' ) ) {
			$db_provider = wpb_sdk_provider_from_db_newest();
			if ( is_array( $db_provider ) && ! empty( $db_provider['runtime_file'] ) ) {
				$GLOBALS['wpb_sdk_registry']['active_provider'] = $db_provider;
				return $db_provider;
			}
		}

		if ( empty( $registry['providers'] ) ) {
			return null;
		}

		$providers = array_values( $registry['providers'] );
		usort(
			$providers,
			function ( $left, $right ) {
				$left_ver  = isset( $left['sdk_version'] ) ? (string) $left['sdk_version'] : '0';
				$right_ver = isset( $right['sdk_version'] ) ? (string) $right['sdk_version'] : '0';
				$by_ver    = version_compare( $right_ver, $left_ver );
				if ( 0 !== $by_ver ) {
					return $by_ver;
				}
				$left_key  = isset( $left['provider_key'] ) ? (string) $left['provider_key'] : '';
				$right_key = isset( $right['provider_key'] ) ? (string) $right['provider_key'] : '';
				return strcmp( $left_key, $right_key );
			}
		);

		$GLOBALS['wpb_sdk_registry']['active_provider'] = $providers[0];
		return $providers[0];
	}
}

if ( ! function_exists( 'wpb_sdk_ensure_runtime_loaded' ) ) {
	/**
	 * Load the selected runtime exactly once.
	 *
	 * @return bool
	 */
	function wpb_sdk_ensure_runtime_loaded() {
		if ( function_exists( 'wpb_sdk_runtime_is_complete' ) && wpb_sdk_runtime_is_complete() ) {
			wpb_sdk_ensure_registry();
			$GLOBALS['wpb_sdk_registry']['runtime_loaded'] = true;
			return true;
		}

		$provider = wpb_sdk_select_provider();
		if ( empty( $provider ) || empty( $provider['runtime_file'] ) ) {
			return false;
		}

		if ( ! defined( 'WP_WPBRIGADE_SDK_VERSION' ) && ! empty( $provider['sdk_version'] ) ) {
			define( 'WP_WPBRIGADE_SDK_VERSION', (string) $provider['sdk_version'] );
		}

		require_once $provider['runtime_file'];

		if ( ! defined( 'WP_WPBRIGADE_SDK_VERSION' ) ) {
			$runtime_version = ! empty( $provider['sdk_version'] )
				? (string) $provider['sdk_version']
				: '3.4.0';
			define( 'WP_WPBRIGADE_SDK_VERSION', $runtime_version );
		}

		$complete = function_exists( 'wpb_sdk_runtime_is_complete' ) && wpb_sdk_runtime_is_complete();
		if ( $complete ) {
			wpb_sdk_ensure_registry();
			$GLOBALS['wpb_sdk_registry']['runtime_loaded'] = true;
		}

		return $complete;
	}
}

if ( ! function_exists( 'wpb_sdk_sync_module_post_init' ) ) {
	/**
	 * Run shared post-init hooks and backfills for a module.
	 *
	 * @param array<string, mixed> $module Module configuration.
	 * @param string               $slug   Module slug.
	 * @return void
	 */
	function wpb_sdk_sync_module_post_init( $module, $slug ) {
		if ( class_exists( 'WPBRIGADE_Optin_Verification' ) ) {
			WPBRIGADE_Optin_Verification::register_module( $module );
		}
		if ( function_exists( 'wpb_sdk_register_opt_manager_for_module' ) ) {
			wpb_sdk_register_opt_manager_for_module( $module );
		}
		if ( function_exists( 'wpb_sdk_enqueue_optin_initiator_backfill' ) ) {
			wpb_sdk_enqueue_optin_initiator_backfill( $slug );
		}
		if ( function_exists( 'wpb_sdk_maybe_normalize_legacy_sdk_sharing_option' ) ) {
			wpb_sdk_maybe_normalize_legacy_sdk_sharing_option( $slug );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_should_boot_immediately' ) ) {
	/**
	 * Whether runtime + modules must boot now (cannot wait for plugins_loaded).
	 *
	 * @return bool
	 */
	function wpb_sdk_should_boot_immediately() {
		if ( did_action( 'plugins_loaded' ) > 0 ) {
			return true;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Context detection only.
		$is_uninstall = ( defined( 'WP_UNINSTALL_PLUGIN' ) && WP_UNINSTALL_PLUGIN )
			|| (
				defined( 'DOING_AJAX' )
				&& DOING_AJAX
				&& isset( $_REQUEST['action'] )
				&& 'delete-plugin' === $_REQUEST['action']
			);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return (bool) $is_uninstall;
	}
}

if ( ! function_exists( 'wpb_sdk_init_registered_module' ) ) {
	/**
	 * Initialize one queued module against the loaded runtime.
	 *
	 * @param array<string, mixed> $module Module configuration.
	 * @return array{logger: mixed, slug: string, id: mixed}|false
	 */
	function wpb_sdk_init_registered_module( $module ) {
		if ( ! is_array( $module ) || empty( $module['slug'] ) || empty( $module['id'] ) ) {
			return false;
		}

		if ( ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return false;
		}

		if ( function_exists( 'wpb_sdk_apply_module_defaults' ) ) {
			$module = wpb_sdk_apply_module_defaults( $module );
		}

		$slug = (string) $module['slug'];
		wpb_sdk_ensure_registry();
		$GLOBALS['wpb_sdk_registry']['modules'][ $slug ] = $module;

		$is_already_init = ! empty( $GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ] );
		if ( $is_already_init ) {
			if ( function_exists( 'wpb_sdk_store_module_if_missing_compat' ) ) {
				wpb_sdk_store_module_if_missing_compat( $module );
			} elseif ( method_exists( 'WPBRIGADE_Logger', 'wpb_sdk_store_module_if_missing' ) ) {
				WPBRIGADE_Logger::wpb_sdk_store_module_if_missing( $module );
			}
		} else {
			$logger = WPBRIGADE_Logger::instance( $module['id'], $slug, true );
			$logger->wpb_init( $module );
			$GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ] = true;
		}

		wpb_sdk_sync_module_post_init( $module, $slug );

		unset( $GLOBALS['wpb_sdk_registry']['pending_modules'][ $slug ] );

		$logger = WPBRIGADE_Logger::instance( $module['id'], $slug, true );
		return array(
			'logger' => $logger,
			'slug'   => $slug,
			'id'     => $module['id'],
		);
	}
}

if ( ! function_exists( 'wpb_sdk_boot_deferred_runtime' ) ) {
	/**
	 * Select newest provider, load runtime once, init all pending modules.
	 *
	 * @return bool
	 */
	function wpb_sdk_boot_deferred_runtime() {
		$registry = wpb_sdk_ensure_registry();

		if ( ! empty( $registry['modules_booted'] )
			&& function_exists( 'wpb_sdk_runtime_is_complete' )
			&& wpb_sdk_runtime_is_complete()
			&& empty( $registry['pending_modules'] )
		) {
			return true;
		}

		if ( ! wpb_sdk_ensure_runtime_loaded() || ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return false;
		}

		$pending = isset( $GLOBALS['wpb_sdk_registry']['pending_modules'] )
			&& is_array( $GLOBALS['wpb_sdk_registry']['pending_modules'] )
			? $GLOBALS['wpb_sdk_registry']['pending_modules']
			: array();

		foreach ( $pending as $slug => $module ) {
			wpb_sdk_init_registered_module( $module );
		}

		$modules = isset( $GLOBALS['wpb_sdk_registry']['modules'] )
			&& is_array( $GLOBALS['wpb_sdk_registry']['modules'] )
			? $GLOBALS['wpb_sdk_registry']['modules']
			: array();

		foreach ( $modules as $slug => $module ) {
			if ( ! empty( $GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ] ) ) {
				continue;
			}
			wpb_sdk_init_registered_module( $module );
		}

		$GLOBALS['wpb_sdk_registry']['modules_booted'] = true;
		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_schedule_deferred_boot' ) ) {
	/**
	 * Schedule a one-time boot on plugins_loaded priority 0.
	 *
	 * @return void
	 */
	function wpb_sdk_schedule_deferred_boot() {
		static $scheduled = false;

		if ( $scheduled ) {
			return;
		}

		$scheduled = true;

		if ( wpb_sdk_should_boot_immediately() ) {
			wpb_sdk_boot_deferred_runtime();
			return;
		}

		add_action( 'plugins_loaded', 'wpb_sdk_boot_deferred_runtime', 0 );
	}
}

if ( ! function_exists( 'wpb_sdk_dynamic_init' ) ) {
	/**
	 * Shared dynamic initializer used by all bundled SDK copies.
	 *
	 * @param array<string, mixed> $module Module configuration.
	 * @return array{logger: mixed, slug: string, id: mixed}|false
	 */
	function wpb_sdk_dynamic_init( $module ) {
		if ( ! is_array( $module ) || empty( $module['slug'] ) || empty( $module['id'] ) ) {
			return false;
		}

		wpb_sdk_ensure_registry();

		$slug = (string) $module['slug'];
		$GLOBALS['wpb_sdk_registry']['modules'][ $slug ]         = $module;
		$GLOBALS['wpb_sdk_registry']['pending_modules'][ $slug ] = $module;

		wpb_sdk_schedule_deferred_boot();

		if ( wpb_sdk_should_boot_immediately()
			|| (
				function_exists( 'wpb_sdk_runtime_is_complete' )
				&& wpb_sdk_runtime_is_complete()
				&& ! empty( $GLOBALS['wpb_sdk_registry']['modules_booted'] )
			)
		) {
			wpb_sdk_boot_deferred_runtime();
			$result = wpb_sdk_init_registered_module(
				isset( $GLOBALS['wpb_sdk_registry']['modules'][ $slug ] )
					? $GLOBALS['wpb_sdk_registry']['modules'][ $slug ]
					: $module
			);
			if ( false !== $result ) {
				return $result;
			}
		}

		$logger = null;
		if ( class_exists( 'WPBRIGADE_Logger', false )
			&& ! empty( $GLOBALS['wpb_sdk_registry']['initialized_modules'][ $slug ] )
		) {
			$logger = WPBRIGADE_Logger::instance( $module['id'], $slug, true );
		}

		return array(
			'logger' => $logger,
			'slug'   => $slug,
			'id'     => $module['id'],
		);
	}
}

if ( ! function_exists( 'wpb_sdk_detect_provider_key_from_path' ) ) {
	/**
	 * Derive provider_key from where this start.php lives on disk.
	 *
	 * @param string $start_dir Directory containing start.php (__DIR__).
	 * @return string
	 */
	function wpb_sdk_detect_provider_key_from_path( $start_dir ) {
		$start_dir = function_exists( 'wpb_sdk_normalize_path' )
			? wpb_sdk_normalize_path( $start_dir )
			: str_replace( '\\', '/', $start_dir );

		if ( preg_match( '#/lib/wpb-sdk$#', $start_dir ) ) {
			$plugin_root = dirname( dirname( $start_dir ) );
			$folder      = basename( $plugin_root );

			if ( '' !== $folder && '.' !== $folder && '..' !== $folder ) {
				return sanitize_key( $folder );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'wpb_sdk_parse_bundle_api_endpoint' ) ) {
	/**
	 * Resolve the telemetry API endpoint from WPBRIGADE_SDK_API_ENDPOINT.
	 *
	 * @param string $sdk_dir Path to lib/wpb-sdk.
	 * @return string API base URL without trailing slash.
	 */
	function wpb_sdk_parse_bundle_api_endpoint( $sdk_dir ) {
		if ( ! defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
			$path = trailingslashit( $sdk_dir ) . 'config.php';
			if ( is_readable( $path ) ) {
				require_once $path;
			}
		}

		if ( defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
			return rtrim( (string) WPBRIGADE_SDK_API_ENDPOINT, '/' );
		}

		return 'https://app.telemetry.wpbrigade.com/api/v2';
	}
}

wpb_sdk_register_provider(
	array(
		'provider_key' => wpb_sdk_detect_provider_key_from_path( __DIR__ ),
		'sdk_version'  => $wpb_sdk_version,
		'runtime_file' => __DIR__ . '/require.php',
		'sdk_dir'      => __DIR__,
		'api_endpoint' => wpb_sdk_parse_bundle_api_endpoint( __DIR__ ),
		'plugin_path'  => isset( $wpb_this_plugin_basename ) ? $wpb_this_plugin_basename : '',
		'sdk_path'     => isset( $wpb_this_sdk_relative_path ) ? $wpb_this_sdk_relative_path : '',
	)
);

if ( ! function_exists( 'wpb_sdk_dynamic_init' ) && function_exists( 'wpb_dynamic_init' ) ) {
	/**
	 * Map legacy init helper to the current SDK entry point.
	 *
	 * @param array $module Module configuration.
	 * @return array|false
	 */
	function wpb_sdk_dynamic_init( $module ) {
		return wpb_dynamic_init( $module );
	}
}

if ( ! function_exists( 'wpb_dynamic_init' ) ) {
	/**
	 * Backward-compatible init proxy.
	 *
	 * @param array $module Module configuration.
	 * @return array|false
	 */
	function wpb_dynamic_init( $module ) {
		return wpb_sdk_dynamic_init( $module );
	}
}

if ( ! function_exists( 'wpb_sdk_run_product_uninstall' ) ) {
	/**
	 * Parse-time uninstall entry.
	 *
	 * @return void
	 */
	function wpb_sdk_run_product_uninstall() {
		if ( function_exists( 'wpb_sdk_ensure_runtime_loaded' ) ) {
			wpb_sdk_ensure_runtime_loaded();
		}
		if ( function_exists( 'wpb_sdk_boot_deferred_runtime' ) ) {
			wpb_sdk_boot_deferred_runtime();
		}
		if ( function_exists( 'wpb_sdk_migrate_legacy_lifecycle_callbacks' ) ) {
			wpb_sdk_migrate_legacy_lifecycle_callbacks();
		}
		if ( function_exists( 'wpb_sdk_invoke_product_uninstall' ) ) {
			wpb_sdk_invoke_product_uninstall();
		} elseif ( class_exists( 'WPBRIGADE_Logger', false ) && method_exists( 'WPBRIGADE_Logger', 'log_uninstallation' ) ) {
			WPBRIGADE_Logger::log_uninstallation();
		}

		// Clean up SDK-managed DB options if no other WPBrigade plugin remains.
		if ( function_exists( 'wpb_sdk_uninstall_cleanup' ) ) {
			$plugin_file = '';
			if ( function_exists( 'wpb_sdk_resolve_product_slug_for_lifecycle' ) ) {
				$slug = wpb_sdk_resolve_product_slug_for_lifecycle();
				if ( '' !== $slug && function_exists( 'wpb_sdk_restore_module_for_lifecycle' ) ) {
					wpb_sdk_restore_module_for_lifecycle( $slug );
				}
				if ( '' !== $slug && function_exists( 'wpb_sdk_get_registered_module' ) ) {
					$module = wpb_sdk_get_registered_module( $slug );
					if ( is_array( $module ) && ! empty( $module['plugin_file'] ) ) {
						$plugin_file = (string) $module['plugin_file'];
					}
				}
			}
			wpb_sdk_uninstall_cleanup( $plugin_file );
		}
	}
}

if ( ! function_exists( 'wpb_sdk_bootstrap_runtime_for_uninstall' ) ) {
	/**
	 * Load Logger before WordPress runs the stored uninstall_plugins callback.
	 *
	 * @return void
	 */
	function wpb_sdk_bootstrap_runtime_for_uninstall() {
		if ( ! function_exists( 'wpb_sdk_ensure_runtime_loaded' ) ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Bootstrap check for uninstall AJAX.
		$should_bootstrap = ( defined( 'WP_UNINSTALL_PLUGIN' ) && WP_UNINSTALL_PLUGIN )
			|| ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && 'delete-plugin' === $_REQUEST['action'] );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( $should_bootstrap ) {
			wpb_sdk_ensure_runtime_loaded();
			if ( function_exists( 'wpb_sdk_boot_deferred_runtime' ) ) {
				wpb_sdk_boot_deferred_runtime();
			}
		}
	}
}
wpb_sdk_bootstrap_runtime_for_uninstall();
