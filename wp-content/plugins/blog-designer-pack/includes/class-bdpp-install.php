<?php
/**
 * Installation Class
 *
 * Handles to manage front end process of plugin
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDP_Install {

	/**
	 * Plugin Setup (On Activation)
	 * Does the initial setup.
	 * 
	 * @since 1.0
	 */
	public static function install() {

		// Registered Post Types
		bdp_register_post_type();

		// Get plugin settings
		$bdpp_opts = get_option('bdpp_opts');

		// Update plugin settings if they are not set
		if( empty( $bdpp_opts ) ) {
			bdp_set_default_settings();

			update_option( 'bdp_version', '1.0' );
		}

		// Upgrade to premium notice
		$notice_transient = get_transient( 'bdp_pro_buy_notice' );

		if ( $notice_transient == false ) {
			set_transient( 'bdp_pro_buy_notice', 1, (15 * MINUTE_IN_SECONDS) );
		}

		// Deactivate Pro Plugin
		if( is_plugin_active('blog-designer-pack-pro/blog-designer-pack-pro.php') ) {
			add_action( 'update_option_active_plugins', array( 'BDP_Install', 'bdp_deactivate_pro_version' ) );
		}

		// Clear the permalinks
		flush_rewrite_rules();
	}

	/**
	 * Deactivate Pro Plugin
	 * 
	 * @since 1.0.6
	 */
	public static function bdp_deactivate_pro_version() {
		deactivate_plugins('blog-designer-pack-pro/blog-designer-pack-pro.php', true);
	}
}