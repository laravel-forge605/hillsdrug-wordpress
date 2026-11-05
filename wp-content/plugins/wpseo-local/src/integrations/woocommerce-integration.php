<?php

namespace Yoast\WP\Local\Integrations;

use Yoast\WP\Local\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Woocommerce_Integration.
 */
class Woocommerce_Integration implements Integration_Interface {

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'woocommerce_order_formatted_shipping_address', [ $this, 'order_formatted_shipping_address' ], 10, 2 );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [ WooCommerce_Conditional::class ];
	}

	/**
	 * @param array<string> $shipping_address The current shipping address.
	 * @param WC_Order      $order            The WC order object.
	 *
	 * @return array<string> The address array
	 */
	public function order_formatted_shipping_address( $shipping_address, $order ) {
		// Get the specs of the current shipping method.
		$order_shipping_methods = $order->get_shipping_methods();
		$order_shipping_method  = \array_shift( $order_shipping_methods );

		$location_id = (int) \str_replace( 'yoast_wcseo_local_pickup_', '', $order_shipping_method['method_id'] );
		// Only alter the shipping address when local shipping has been selected.

		if ( \strstr( $order_shipping_method['method_id'], 'yoast_wcseo_local_pickup' ) === false ) {
			return $shipping_address;
		}
		// Get the shipping method address.
		$_wpseo_business_name          = $order_shipping_method['name'];
		$_wpseo_business_address       = \get_post_meta( $location_id, '_wpseo_business_address', true );
		$_wpseo_business_city          = \get_post_meta( $location_id, '_wpseo_business_city', true );
		$_wpseo_business_zipcode       = \get_post_meta( $location_id, '_wpseo_business_zipcode', true );
		$_wpseo_business_state         = \get_post_meta( $location_id, '_wpseo_business_state', true );
		$_wpseo_business_country       = \get_post_meta( $location_id, '_wpseo_business_country', true );    // Store the shipping method address.
		$shipping_address['company']   = $_wpseo_business_name;
		$shipping_address['address_1'] = $_wpseo_business_address;
		$shipping_address['city']      = $_wpseo_business_city;
		$shipping_address['postcode']  = $_wpseo_business_zipcode;
		$shipping_address['state']     = $_wpseo_business_state;
		$shipping_address['country']   = $_wpseo_business_country;

		return $shipping_address;
	}
}
