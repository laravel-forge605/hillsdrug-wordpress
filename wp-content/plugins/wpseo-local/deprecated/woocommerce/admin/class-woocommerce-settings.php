<?php
/**
 * Yoast SEO: Local for WooCommerce plugin file.
 *
 * @since   4.0
 * @package YoastSEO_Local_WooCommerce
 */

if ( ! defined( 'WPSEO_LOCAL_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists( 'WPSEO_Local_Admin_Woocommerce_Settings' ) ) {

	/**
	 * WPSEO_Local_Admin_API_Settings class.
	 *
	 * Build the WPSEO Local admin form.
	 *
	 * @since      4.1
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	class WPSEO_Local_Admin_Woocommerce_Settings {

		/**
		 * Holds the slug for this settings tab.
		 *
		 * @var string
		 */
		private $slug = 'woocommerce';

		/**
		 * WPSEO_Local_Admin_API_Settings constructor.
		 *
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function __construct() {
			_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
		}

		/**
		 * Adds the WooCommerce Settings tab in the WPSEO local admin panel.
		 *
		 * @param array $tabs Array holding the tabs.
		 *
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function create_tab( $tabs ) {
			_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
			$tabs[ $this->slug ] = [
				/* translators: 1: expands to 'WooCommerce'. */
				'tab_title'     => sprintf( __( '%1$s settings', 'yoast-local-seo' ), 'WooCommerce' ),
				/* translators: 1: expands to 'Local SEO for WooCommerce'. */
				'content_title' => sprintf( esc_html__( '%1$s settings', 'yoast-local-seo' ), 'Local SEO for WooCommerce' ),
			];

			return $tabs;
		}

		/**
		 * Create tab content for API Settings.
		 *
		 * @return void
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function tab_content() {
			_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
			echo '<p>';
			printf(
			/* translators: 1: expands to '<a>'; 2: expands to '</a>' */
				esc_html__( '%1$sClick here%2$s for the specific WooCommerce settings', 'yoast-local-seo' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=yoast_wcseo_local_pickup' ) ) . '">',
				'</a>'
			);
			echo '</p>';
		}
	}
}
