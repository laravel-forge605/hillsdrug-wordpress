<?php
/**
 * Authorized command handler for resetting plugin settings.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Keeps state mutation out of the settings-page view/controller.
 */
class Settings_Reset_Controller {

	/**
	 * Plugin metadata and admin URLs.
	 *
	 * @var object
	 */
	private $custom_plugin_data;

	/**
	 * Register reset command hooks.
	 *
	 * @param object $custom_plugin_data Plugin metadata and admin URLs.
	 */
	public function __construct( $custom_plugin_data ) {
		$this->custom_plugin_data = $custom_plugin_data;

		add_action( 'admin_post_simple_sitemap_reset_options', array( $this, 'reset_plugin_options' ) );
		add_action( 'admin_notices', array( $this, 'render_reset_notice' ) );
	}

	/**
	 * Reset settings after an authorized admin request.
	 */
	public function reset_plugin_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to reset these settings.', 'simple-sitemap' ) );
		}

		check_admin_referer( 'simple_sitemap_reset_action', 'simple_sitemap_reset_nonce' );
		update_option( 'simple_sitemap_options', Settings_Repository::get_defaults() );

		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = $this->custom_plugin_data->main_settings_url;
		}

		wp_safe_redirect( add_query_arg( 'simple-sitemap-reset', 'success', $redirect ) );
		exit;
	}

	/**
	 * Show the settings-reset confirmation after the redirect.
	 */
	public function render_reset_notice() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only success message after an authorized redirect.
		$reset_status = isset( $_GET['simple-sitemap-reset'] ) ? sanitize_key( wp_unslash( $_GET['simple-sitemap-reset'] ) ) : '';
		if ( 'success' !== $reset_status ) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Plugin settings reset to defaults.', 'simple-sitemap' ); ?></p>
		</div>
		<?php
	}
}
