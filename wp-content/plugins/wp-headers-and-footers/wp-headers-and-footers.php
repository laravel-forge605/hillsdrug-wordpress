<?php

/**
 * Plugin Name: WP Headers And Footers
 * Plugin URI: https://www.WPBrigade.com/wordpress/plugins/wp-headers-and-footers/?utm_source=wp-headers-and-footers&utm_medium=author-uri-link
 * Description: Allows you to insert code or text in the header or footer of your WordPress site.
 * Version: 3.2.0
 * Author: WPBrigade
 * Author URI: https://wpbrigade.com/?utm_source=wp-headers-and-footers&utm_medium=author-uri-link
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.0.0
 * Text Domain: wp-headers-and-footers
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WPBrigade/wp-headers-and-footers
 *
 * @package wp-headers-and-footers
 * @category Core
 * @author WPBrigade
 */

if ( ! function_exists( 'whaf_wpb56690683' ) ) {
	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return mixed
	 */
	function whaf_wpb56690683() {
		global $whaf_wpb56690683;

		if ( ! isset( $whaf_wpb56690683 ) || ! is_array( $whaf_wpb56690683 ) ) {
			require_once __DIR__ . '/lib/wpb-sdk/start.php';

			/**
			 * Initialize WPB SDK.
			 *
			 * @phpstan-ignore-next-line
			 */
			$whaf_wpb56690683 = wpb_sdk_dynamic_init(
				array(
					'id'              => '2',
					'slug'            => 'wp-headers-and-footers',
					'type'            => 'plugin',
					'plugin_file'     => __FILE__,
					'sdk_views_dir'   => __DIR__ . '/lib/wpb-sdk/views',
					'public_key'      => '1|4aOA8EuyIN4pi2miMvC23LLpnHbBZFNki9R9pVmwd673d3c8',
					'secret_key'      => 'sk_b36c525848fee035', // NOSONAR
					'is_premium'      => false,
					'has_addons'      => false,
					'has_paid_plans'  => false,
					'optin_user_meta' => array(
						'token'          => '_wpheaderandfooter_verification_token',
						'email_verified' => '_wpheaderandfooter_email_verified',
					),
					'optin'           => array(
						'option_name'         => '_wpheaderandfooter_optin',
						'optin_page'          => 'wpheadersandfooters-optin',
						'settings_page'       => 'wp-headers-and-footers',
						'verify_query_args'   => array(
							'wp-headers-and-footers_optin_verify',
							'wp_headers_and_footers_optin_verify',
						),
						'ajax_prefix'         => 'whaf',
						'product_name'        => 'WP Headers And Footers',
						'logo_path'           => 'asset/img/logo.svg',
						'settings_admin_path' => 'options-general.php?page=wp-headers-and-footers',
					),
					'telemetry'       => array(
						'optout_submit_key' => 'whaf-submit-optout',
					),
					'menu'            => array(
						'slug'    => 'wp-headers-and-footers',
						'account' => false,
						'support' => false,
					),
					'settings'        => array(
						'wpheaderandfooter_settings'                         => '',
						'wpheaderandfooter_basics'                           => '',
						'_wpheaderandfooter_optin'                           => '',
						'wpb_sdk_wp-headers-and-footers'                     => '',
						'wpb_sdk_wp-headers-and-footers_fallback_verify_token' => '',
						'wpb_sdk_wp-headers-and-footers_initial_log_sent'      => '',
					),
				)
			);
		}

		return $whaf_wpb56690683;
	}

	whaf_wpb56690683();
	do_action( 'whaf_wpb56690683_loaded' );
}

if ( ! class_exists( 'WPHeaderAndFooter' ) ) :

	/**
	 * The class WPHeaderAndFooter
	 */
	final class WPHeaderAndFooter {

		/**
		 * The single instance of the class.
		 *
		 * @var string $version
		 */
		public $version = '3.2.0';

		/**
		 * The single instance of the class.
		 *
		 * @var object $instance
		 */
		protected static $instance = null;

		/**
		 * WPHeaderAndFooter Class constructor
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();
			$this->maybe_migrate_slashed_scripts();
			$this->hooks();
		}

		/**
		 * One-time migration for scripts saved before 3.2.0.
		 *
		 * Older versions ran wp_unslash() on output, so the frontend printed
		 * a different string than the one stored. Unslash the stored scripts
		 * once so the frontend keeps printing exactly what it printed before
		 * and the settings screen shows what is actually printed.
		 *
		 * @since 3.2.0
		 * @return void
		 */
		private function maybe_migrate_slashed_scripts() {

			if ( false !== get_option( 'wpheaderandfooter_unslash_migrated' ) ) {
				return;
			}

			// add_option() only succeeds for the first request, so concurrent requests can't migrate twice.
			if ( ! add_option( 'wpheaderandfooter_unslash_migrated', '1' ) ) {
				return;
			}

			$basics = get_option( 'wpheaderandfooter_basics' );
			if ( ! is_array( $basics ) ) {
				return;
			}

			foreach ( array( 'wp_header_textarea', 'wp_body_textarea', 'wp_footer_textarea' ) as $key ) {
				if ( isset( $basics[ $key ] ) && is_string( $basics[ $key ] ) ) {
					$basics[ $key ] = wp_unslash( $basics[ $key ] );
				}
			}

			update_option( 'wpheaderandfooter_basics', $basics );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 3.1.3
		 */
		public function includes() {

			include_once WPHEADERANDFOOTER_DIR_PATH . 'classes/class-setup.php';
			include_once WPHEADERANDFOOTER_DIR_PATH . 'classes/plugin-meta.php';
			include_once WPHEADERANDFOOTER_DIR_PATH . 'classes/class-notifications.php';
			include_once WPHEADERANDFOOTER_DIR_PATH . 'classes/class-diagnostics-log.php';

			// set the logger settings option if was not set before.
			if ( ! get_option( 'wpheaderandfooter_basics_logger' ) ) {

				$setting = get_option( 'wpheaderandfooter_basics' );
				if ( ! is_array( $setting ) ) {
					$setting = array();
				}

				$header = isset( $setting['wp_header_textarea'] ) ? (string) $setting['wp_header_textarea'] : '';
				$body   = isset( $setting['wp_body_textarea'] ) ? (string) $setting['wp_body_textarea'] : '';
				$footer = isset( $setting['wp_footer_textarea'] ) ? (string) $setting['wp_footer_textarea'] : '';

				$logger_value = array(
					'is_using_wp_header_textarea' => '' !== trim( $header ),
					'is_using_wp_body_textarea'   => '' !== trim( $body ),
					'is_using_wp_footer_textarea' => '' !== trim( $footer ),
				);

				update_option( 'wpheaderandfooter_basics_logger', $logger_value );
			}
		}


		/**
		 * Hook into actions and filters
		 *
		 * @since  1.0.0
		 * @version 2.1.0
		 */
		private function hooks() {

			$head_priority   = $this->hnf_option( 'wpheaderandfooter_settings', 'wp_header_priority' );
			$body_priority   = $this->hnf_option( 'wpheaderandfooter_settings', 'wp_body_priority' );
			$footer_priority = $this->hnf_option( 'wpheaderandfooter_settings', 'wp_footer_priority' );

			add_action( 'init', array( $this, 'textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'wp_print_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_init', array( $this, 'redirect_optin' ) );

			if ( ! empty( $head_priority ) ) {
				add_action( 'wp_head', array( $this, 'frontend_header' ), $head_priority );
			} else {
				add_action( 'wp_head', array( $this, 'frontend_header' ) );
			}

			if ( function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' ) ) {
				if ( ! empty( $body_priority ) ) {
					add_action( 'wp_body_open', array( $this, 'frontend_body' ), $body_priority );
				} else {
					add_action( 'wp_body_open', array( $this, 'frontend_body' ) );
				}
			}

			if ( ! empty( $footer_priority ) ) {
				add_action( 'wp_footer', array( $this, 'frontend_footer' ), $footer_priority );
			} else {
				add_action( 'wp_footer', array( $this, 'frontend_footer' ) );
			}

			add_action( 'wp_ajax_wpheadersandfooters_log_download', array( $this, 'wp_headers_and_footers_log_download' ) );
			add_action( 'wp_wpb_sdk_after_uninstall', array( $this, 'plugin_uninstallation' ) );
			add_action( 'admin_menu', array( $this, 'register_wpheaders_optin_page' ), 20 );

		}

		/**
		 * Summary of register_wpheaders_optin_page
		 *
		 * @since 2.2.3
		 */
		/**
		 * Hidden opt-in page (registered after Settings → WP Headers and Footers menu).
		 *
		 * @since 2.2.3
		 */
		public function register_wpheaders_optin_page() {
			add_menu_page(
				__( 'Activate', 'wp-headers-and-footers' ),
				' ',
				'manage_options',
				'wpheadersandfooters-optin',
				array( $this, 'wpheadersandfooters_render_optin_page' ),
				'',
				null
			);
			remove_menu_page( 'wpheadersandfooters-optin' );
		}

		/**
		 * Opt-in screen.
		 *
		 * @since 2.2.3
		 */
		public function wpheadersandfooters_render_optin_page() {
			if ( function_exists( 'wpb_sdk_render_optin_form' ) ) {
				wpb_sdk_render_optin_form( 'wp-headers-and-footers' );
			}
		}

		/**
		 * Redirect to opt-in before settings; leave opt-in when already decided.
		 *
		 * @since 1.6.3
		 */
		public function redirect_optin() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['page'] ) ) : '';
			$decision = function_exists( 'wpb_sdk_get_optin_decision' )
				? wpb_sdk_get_optin_decision( 'wp-headers-and-footers' )
				: (string) get_option( '_wpheaderandfooter_optin', '' );

			if (
				$page
				&& in_array( $page, array( 'wp-headers-and-footers', 'abw' ), true )
				&& '' === $decision
			) {
				wp_safe_redirect(
					admin_url(
						'admin.php?page=wpheadersandfooters-optin&redirect-page=' . rawurlencode( $page )
					)
				);
				exit;
			}

			if (
				(
					function_exists( 'wpb_sdk_should_redirect_from_optin_page' )
						? wpb_sdk_should_redirect_from_optin_page( 'wp-headers-and-footers' )
						: ( 'yes' === $decision )
				)
				&& 'wpheadersandfooters-optin' === $page
			) {
				wp_safe_redirect( admin_url( 'options-general.php?page=wp-headers-and-footers' ) );
				exit;
			}
		}

		/**
		 * Plugin Un-installation
		 *
		 * @since 2.2.2
		 * @version 2.2.3
		 */
		public function plugin_uninstallation( $slug = '' ) {
			if ( 'wp-headers-and-footers' !== $slug ) {
				return;
			}
			include_once WPHEADERANDFOOTER_DIR_PATH . 'inc/uninstall.php';
		}

		/**
		 * Define WP Header and Footer Constants
		 */
		private function define_constants() {

			$this->define( 'WPHEADERANDFOOTER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WPHEADERANDFOOTER_DIR_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'WPHEADERANDFOOTER_DIR_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WPHEADERANDFOOTER_ROOT_PATH', __DIR__ . '/' );
			$this->define( 'WPHEADERANDFOOTER_VERSION', $this->version );
			$this->define( 'WPHEADERANDFOOTER_FEEDBACK_SERVER', 'https://wpbrigade.com/' );
			$this->define( 'WPHEADERANDFOOTER_WPB_SDK_VIEWS_DIR', WPHEADERANDFOOTER_DIR_PATH . 'lib/wpb-sdk/views/' );
		}

		/**
		 * Get plugin options and apply proper check on their sub items.
		 *
		 * @param string     $option_name Name of the option.
		 * @param string     $index Name of the sub option.
		 * @param string|int $default default value of the option.
		 *
		 * @since 2.0.0
		 */
		public function hnf_option( $option_name, $index, $default = '' ) {

			$option = get_option( $option_name );
			if ( is_array( $option ) && isset( $option[ $index ] ) && ! empty( $option[ $index ] ) ) {
				return $option[ $index ];
			}
			return $default;
		}

		/**
		 * Admin Scripts
		 *
		 * @param string $page The page slug.
		 * @version 2.0.0
		 */
		public function admin_scripts( $page ) {

			if ( 'settings_page_wp-headers-and-footers' === $page ) {

				wp_enqueue_style( 'wpheaderandfooter_style', plugins_url( 'asset/css/style.css', __FILE__ ), array(), WPHEADERANDFOOTER_VERSION );

				wp_enqueue_style( 'wpheaderandfooter_admin_style', plugins_url( 'asset/css/admin-style.css', __FILE__ ), array(), WPHEADERANDFOOTER_VERSION );

				$editor_args = array( 'type' => 'text/html' );

				if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) {
					$editor_args['codemirror']['readOnly'] = true;
				}

				// Enqueue code editor and settings for manipulating HTML.
				$settings = wp_enqueue_code_editor( $editor_args );

				// Bail if user disabled CodeMirror.
				if ( false === $settings ) {
					return;
				}

				wp_enqueue_script( 'wpheaderandfooter_script', plugins_url( 'asset/js/script.js', __FILE__ ), array( 'jquery' ), WPHEADERANDFOOTER_VERSION, false );

				// Create an array for localize.
				$wp_headers_and_footers_localize = array(
					'plugin_url' => plugins_url(),
					'help_nonce' => wp_create_nonce( 'wp-headers-and-footers-log-nonce' ),
				);
				wp_localize_script( 'wpheaderandfooter_script', 'wpheadersandfooters_log', $wp_headers_and_footers_localize );
			}
		}

		/**
		 * Define constant if not already set
		 *
		 * @param string      $name The name of the variable.
		 * @param string|bool $value The value of the variable.
		 */
		private function define( $name, $value ) {

			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Main Instance
		 *
		 * @since 1.0.0
		 * @static
		 * @see wpheaderandfooter_loader()
		 * @return Main instance
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}


		/**
		 * Load Languages
		 *
		 * @since 1.0.0
		 */
		public function textdomain() {

			$plugin_dir = dirname( plugin_basename( __FILE__ ) );
			load_plugin_textdomain( 'wp-headers-and-footers', false, $plugin_dir . '/languages/' );
		}

		/**
		 * Outputs script / style to the header
		 *
		 * @since 1.0.0
		 * @version 1.3.2
		 */
		public function frontend_header() {

			/**
			 * Filter to add or exclude scripts to and from the frontend header.
			 *
			 * @since 1.3.2
			 */
			if ( apply_filters( 'wp_hnf_header_script', true ) ) {
				$this->wp_hnf_output( 'wp_header_textarea' );
			}
		}

		/**
		 * Outputs script / style to the frontend below opening body
		 *
		 * @since 1.0.0
		 * @version 1.3.2
		 */
		public function frontend_body() {

			/**
			 * Filter to add or exclude scripts to and from the frontend body.
			 *
			 * @since 1.3.2
			 */
			if ( apply_filters( 'wp_hnf_body_script', true ) ) {
				$this->wp_hnf_output( 'wp_body_textarea' );
			}
		}

		/**
		 * Outputs script / style to the footer
		 *
		 * @since 1.0.0
		 * @version 1.3.2
		 */
		public function frontend_footer() {

			/**
			 * Filter to add or exclude scripts to and from the frontend footer.
			 *
			 * @since 1.3.2
			 */
			if ( apply_filters( 'wp_hnf_footer_script', true ) ) {
				$this->wp_hnf_output( 'wp_footer_textarea' );
			}
		}

		/**
		 * Outputs the given setting, if conditions are met
		 *
		 * @param string $script Setting Name.
		 *
		 * @version 2.0.0
		 * @return output
		 */
		public function wp_hnf_output( $script ) {

			// Ignore admin, feed, robots or track backs.
			if ( is_admin() || is_feed() || is_robots() || is_trackback() ) :
				return;
			endif;

			// Get meta.
			$meta = $this->hnf_option( 'wpheaderandfooter_basics', $script, false );

			if ( false === $meta || '' === trim( (string) $meta ) ) :
				return;
			endif;

			// Output.
			echo $meta . PHP_EOL;  // @codingStandardsIgnoreLine.
		}

		/**
		 * The Ajax function callback to download the diagnostics.
		 *
		 * @since 2.1.0
		 * @version 3.1.3
		 * @return void
		 */
		public function wp_headers_and_footers_log_download() {

			check_ajax_referer( 'wp-headers-and-footers-log-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}
			$diagnostics = new WPHeadersAndFooters_Diagnostics_Log();
			echo $diagnostics->wp_headers_and_footers_get_sysinfo( false );

			wp_die();
		}

	}

endif;

/**
 * Returns the main instance of WP to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WPHeaderAndFooter
 */
function wpheaderandfooter_loader() {
	return WPHeaderAndFooter::instance();
}

// Call the function.
wpheaderandfooter_loader();
$wp_header_and_footer_setting = new WPHeaderAndFooter_Setting();
