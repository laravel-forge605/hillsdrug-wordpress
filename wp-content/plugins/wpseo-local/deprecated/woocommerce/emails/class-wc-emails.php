<?php
/**
 * Yoast SEO: Local for WooCommerce plugin file.
 *
 * @package YoastSEO_Local_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Class: Yoast_WCSEO_Local_Emails.
 *
 * @deprecated 15.4
 * @codeCoverageIgnore
 */
class Yoast_WCSEO_Local_Emails {

	/**
	 * Constructor.
	 *
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
	}

	/**
	 * @param $hooks
	 *
	 * @return mixed
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function add_email_actions( $hooks ) {
		_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
		$hooks[] = 'woocommerce_order_status_processing_to_transporting';
		$hooks[] = 'woocommerce_order_status_transporting_to_ready-for-pickup';

		return $hooks;
	}

	/**
	 * @param $email_classes
	 *
	 * @return mixed
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function add_email_classes( $email_classes ) {
		_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
		// Include our custom email class.
		require_once WPSEO_LOCAL_PATH . 'woocommerce/emails/class-wc-email-transporting.php';
		require_once WPSEO_LOCAL_PATH . 'woocommerce/emails/class-wc-email-readyforpickup-order.php';

		// Add the email class to the list of email classes that WooCommerce loads.
		$email_classes['WC_Email_Transporting_Order']   = new WC_Email_Transporting_Order();
		$email_classes['WC_Email_ReadyForPickup_Order'] = new WC_Email_ReadyForPickup_Order();

		return $email_classes;
	}
}

new Yoast_WCSEO_Local_Emails();
