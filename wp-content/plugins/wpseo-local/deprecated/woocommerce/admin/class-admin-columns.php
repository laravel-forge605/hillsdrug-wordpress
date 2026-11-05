<?php
/**
 * Yoast SEO: Local for WooCommerce plugin file.
 *
 * @package YoastSEO_Local_WooCommerce
 */

if ( ! class_exists( 'Yoast_WCSEO_Local_Admin_Columns' ) ) {

	/**
	 * Class: Yoast_WCSEO_Local_Admin_Columns.
	 *
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	class Yoast_WCSEO_Local_Admin_Columns {

		/**
		 * Pickup settings.
		 *
		 * @var array
		 */
		private $settings = null;

		/**
		 * Constructor.
		 *
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function __construct() {

			$this->settings = get_option( 'woocommerce_yoast_wcseo_local_pickup_settings' );
		}

		/**
		 * @return void
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function init() {
			_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
		}

		/**
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function columns_head( $defaults ) {
			_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
			// Add our custom column head.
			$defaults['local_pickup_allowed'] = __( 'Local Pickup allowed?', 'yoast-local-seo' );

			return $defaults;
		}

		/**
		 * @deprecated 15.4
		 * @codeCoverageIgnore
		 */
		public function columns_content( $column_name, $post_id ) {
			_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
			// Create custom column content.
			if ( $column_name === 'local_pickup_allowed' ) {

				// First we check if this Location has been enabled via Location Specific settings.
				if ( isset( $this->settings['location_specific'][ $post_id ]['allowed'] ) && ( $this->settings['location_specific'][ $post_id ]['allowed'] === 'yes' ) ) {
					esc_html_e( 'Yes', 'yoast-local-seo' );
					return;
				}

				// First we check if this Location has been enabled via Location Specific settings.
				if ( isset( $this->settings['location_specific'][ $post_id ] ) && ( ! isset( $this->settings['location_specific'][ $post_id ]['allowed'] ) ) ) {
					esc_html_e( 'No', 'yoast-local-seo' );
					return;
				}

				// Otherwise check for an allowed category.
				$terms = get_the_terms( $post_id, 'wpseo_locations_category' );
				if ( $terms && ! is_wp_error( $terms ) ) {

					foreach ( $terms as $term ) {

						if ( isset( $this->settings['category_specific'][ $term->term_id ]['allowed'] ) && ( $this->settings['category_specific'][ $term->term_id ]['allowed'] === 'yes' ) ) {
							esc_html_e( 'Yes', 'yoast-local-seo' );
							return;
						}
					}
				}

				// Echo a negative if nothing has been found.
				esc_html_e( 'No', 'yoast-local-seo' );
			}
		}
	}
}
