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

require_once WPSEO_LOCAL_PATH . 'woocommerce/emails/abstract-wc-email.php';

/**
 * A custom Transporting Order WooCommerce Email class.
 *
 * @since 0.1
 * @deprecated 15.4
 * @codeCoverageIgnore
 */
class WC_Email_Transporting_Order extends WPSEO_Local_WooCommerce_Email {

	/**
	 * Set email defaults.
	 *
	 * @since 0.1
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
		// Set ID, this simply needs to be a unique name.
		$this->id = 'wc_transporting_order';

		// This is the title in WooCommerce Email settings.
		$this->title = __( 'Transporting order', 'yoast-local-seo' );

		// This is the description in WooCommerce email settings.
		$this->description = __( 'Transporting order notification emails are sent when an order has been sent to the local pickup store', 'yoast-local-seo' );

		// These are the default heading and subject lines that can be overridden using the settings.
		/* translators: Transporting shipping order = the title and subject for an email-notification that is being sent to the customer when an order is being shipped to the local store */
		$this->heading = __( 'Transporting shipping order', 'yoast-local-seo' );
		$this->subject = __( 'Transporting shipping order', 'yoast-local-seo' );

		/*
		 * These define the locations of the templates that this email should use,
		 * we'll just use the new order template since this email is similar.
		 */
		$this->template_html  = 'emails/customer-transporting-order.php';
		$this->template_plain = 'emails/plain/customer-transporting-order.php';

		// Call parent constructor to load any other defaults not explicity defined here.
		parent::__construct();
	}
}
