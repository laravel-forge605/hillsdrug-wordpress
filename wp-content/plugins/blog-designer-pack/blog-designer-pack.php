<?php
/**
 * Plugin Name: Blog Designer Pack
 * Plugin URI: https://infornweb.com/news-blog-designer-pack-pro/
 * Description: Display blog posts on your website with 6 blog layouts (2 designs for each blog layout) plus 1 Ticker and 2 Widgets
 * Text Domain: blog-designer-pack
 * Domain Path: /languages/
 * Author: InfornWeb
 * Author URI: https://infornweb.com
 * Version: 4.0.14
 * Requires at least: 5.8
 * Requires PHP: 5.4
 * 
 * @package Blog Designer Pack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( function_exists( 'bdp_fs' ) ) {
	bdp_fs()->set_basename( true, __FILE__ );
}

if ( ! class_exists( 'Blog_Designer_Pack_Lite' ) )  :

	/**
	 * Main Class
	 * @package Blog Designer Pack
	 * @version	1.0
	 */
	final class Blog_Designer_Pack_Lite {

		// Instance
		private static $instance;
		
		/**
		 * Script Object.
		 *
	 	 * @version	1.0
		 */
		public $scripts;

		/**
		 * Main Blog Designer Pack Lite Instance.
		 * Ensures only one instance of Blog_Designer_Pack_Lite is loaded or can be loaded.
		 *
	 	 * @version	1.0
		 */
		public static function instance() {
			
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Blog_Designer_Pack_Lite ) ) {
				self::$instance = new Blog_Designer_Pack_Lite();
				self::$instance->setup_constants();

				self::$instance->includes(); // Including required files
				self::$instance->init_hooks();

				self::$instance->scripts = new BDP_Scripts(); // Script Class
			}
			return self::$instance;
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string      $name  Constant name.
		 * @param string|bool $value Constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Setup plugin constants
		 * Basic plugin definitions
		 * 
		 * @since 1.0
		 */
		private function setup_constants() {

			$this->define( 'BDP_VERSION', '4.0.14' ); // Version of plugin
			$this->define( 'BDP_FILE', __FILE__ );
			$this->define( 'BDP_DIR', dirname( __FILE__ ) );
			$this->define( 'BDP_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'BDP_BASENAME', basename( BDP_DIR ) );
			$this->define( 'BDP_META_PREFIX', '_bdp_' );
			$this->define( 'BDP_POST_TYPE', 'post' );
			$this->define( 'BDP_CAT', 'category' );
			$this->define( 'BDP_LAYOUT_POST_TYPE', 'bdpp_layout' );
			$this->define( 'BDP_SETTING_PAGE_URL', add_query_arg( array('page' => 'bdpp-settings', 'tab' => 'general'), 'admin.php' ) );
			$this->define( 'BDP_PRO_TAB_URL', add_query_arg( array('page' => 'bdpp-settings', 'tab' => 'pro'), 'admin.php' ) );
			$this->define( 'BDP_UPGRADE_URL', add_query_arg( array('page' => 'bdpp-layouts-pricing'), 'admin.php' ) );
		}

		/**
		 * Load Localisation files
		 *
		 * @since 1.0
		 */
		public function bdp_load_textdomain() {
			
			// Set filter for plugin's languages directory.
			$bdp_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$bdp_lang_dir = apply_filters( 'bdpp_languages_directory', $bdp_lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale	= apply_filters( 'plugin_locale', get_user_locale(), 'blog-designer-pack' );
			$mofile	= sprintf( '%1$s-%2$s.mo', 'blog-designer-pack', $locale );
			
			// Setup paths to current locale file
			$mofile_global	= WP_LANG_DIR . '/plugins/' . BDP_BASENAME . '/' . $mofile;
			
			if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/blog-designer-pack-pro folder
				
				load_textdomain( 'blog-designer-pack', $mofile_global );
				
			} else { // Load the default language files
				load_plugin_textdomain( 'blog-designer-pack', false, $bdp_lang_dir );
			}
		}

		/**
		 * Include required files
		 *
		 * @since 1.0
		 */
		private function includes() {

			global $bdpp_options;

			// Including freemius file
			include_once( BDP_DIR . '/freemius.php' );

			// Register Post Type
			require_once( BDP_DIR . '/includes/bdpp-post-types.php' );

			// Including common functions file
			include_once( BDP_DIR . '/includes/bdpp-functions.php' );

			// Plugin Settings
			require_once( BDP_DIR . '/includes/admin/settings/bdpp-register-settings.php' );
			$bdpp_options = bdp_get_settings(); // Gettings plugin settings

			// Class Script
			require_once( BDP_DIR . '/includes/class-bdpp-scripts.php' );

			// Class Public
			require_once( BDP_DIR . '/includes/class-bdpp-public.php' );

			// Class Admin
			require_once( BDP_DIR . '/includes/admin/class-bdpp-admin.php' );

			// Class Metabox
			require_once( BDP_DIR . '/includes/admin/class-bdpp-metabox.php' );

			// Plugin shortcodes
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-grid.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-list.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-gridbox.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-slider.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-carousel.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-masonry.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-post-ticker.php' );
			require_once( BDP_DIR . '/includes/shortcodes/bdpp-shrt-tmpl.php' );

			// Shortcode Supports
			include_once( BDP_DIR . '/includes/admin/shortcode-support/shortcode-fields.php' );

			// Widget Class
			require_once( BDP_DIR . '/includes/widgets/class-bdpp-widgets.php' );

			// Blocks
			require_once( BDP_DIR . '/includes/integrations/blocks/bdpp-blocks.php' );

			// For Admin Side Only
			if ( is_admin() ) {

				// Class Shortcode Builder
				require_once( BDP_DIR . '/includes/admin/shortcode-builder/class-bdpp-shortcode-generator.php' );

				include_once( BDP_DIR . '/includes/admin/settings/bdpp-welcome-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-general-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-trending-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-taxonomy-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-sharing-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-css-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-misc-settings.php' );
				include_once( BDP_DIR . '/includes/admin/settings/bdpp-pro-settings.php' );
			}

			// Plugin installation file
			require_once BDP_DIR . '/includes/class-bdpp-install.php';
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0
		 */
		private function init_hooks() {
			
			register_activation_hook( BDP_FILE, array( 'BDP_Install', 'install' ) );
			
			add_action( 'after_setup_theme', array( $this, 'bdp_setup_environment' ) );
			add_action( 'plugins_loaded', array( $this, 'bdp_plugins_loaded' ) );
			add_action( 'init', array( $this, 'bdp_init_processes' ) );
		}

		/**
		 * Ensure theme and server variable compatibility and setup image sizes.
		 *
		 * @since 1.0
		 */
		public function bdp_setup_environment() {

			// Support Post Thumbnails
			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
			add_post_type_support( 'post', array( 'thumbnail', 'page-attributes' ) );
		}

		/**
		 * Do stuff once all the plugin has been loaded
		 *
		 * @since 1.0
		 */
		public function bdp_plugins_loaded() {

			// Visual Composer Page Builder Support
			if( class_exists('Vc_Manager') ) {
				include_once( BDP_DIR . '/includes/integrations/wpbakery/wpbakery.php' );
			}

			// If Elementor Page Builder is Installed
			if( defined('ELEMENTOR_PLUGIN_BASE') ) {
				require_once( BDP_DIR . '/includes/integrations/elementor/select2-ajax-control.php' );
				require_once( BDP_DIR . '/includes/integrations/elementor/elementor.php' );
			}
		}

		/**
		 * Prior Init Processes
		 *
		 * @since 1.0
		 */
		public function bdp_init_processes() {
			
			// Set up localisation.
			$this->bdp_load_textdomain();

			/*
			 * Plugin Menu Name just to check the screen ID to load condition based assets
			 * This var is not going to be echo anywhere. `sanitize_title` will take care of string.
			 */
			$this->define( 'BDPP_SCREEN_ID', sanitize_title(__('Blog Designer Pack', 'blog-designer-pack')) );

			// Add BDP Image sizes to WP.
			add_image_size( 'bdpp-medium', 640, 480, true );
		}
	}

endif; // End if class_exists check.

/**
 * The main function for that returns Blog_Designer_Pack_Lite
 *
 * Example: $bdp = BDP_Lite();
 *
 * @since 1.0
 * @return object|Blog_Designer_Pack_Lite The one true Blog_Designer_Pack_Lite Instance.
 */
function BDP_Lite() {
	return Blog_Designer_Pack_Lite::instance();
}

// Get Plugin Running
BDP_Lite();