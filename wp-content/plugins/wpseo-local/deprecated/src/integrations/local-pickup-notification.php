<?php

namespace Yoast\WP\Local\Integrations;

use WPSEO_Admin_Asset_Manager;
use WPSEO_Options;
use Yoast\WP\Local\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Local_Pickup_Notification class
 *
 * @deprecated 15.4
 * @codeCoverageIgnore
 */
class Local_Pickup_Notification implements Integration_Interface {

	/**
	 * The capability helper.
	 *
	 * @var Capability_Helper
	 */
	private $capability_helper;

	/**
	 * The admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Local_Pickup_Notification constructor.
	 *
	 * @param Capability_Helper $capability_helper The capability helper.
	 */
	public function __construct( Capability_Helper $capability_helper ) {
		$this->admin_asset_manager = new WPSEO_Admin_Asset_Manager();
		$this->capability_helper   = $capability_helper;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function register_hooks() {
		\_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
	}

	/**
	 * Shows a notice if Local store pickup option is enabled and it's not being dismissed before.
	 *
	 * @return void
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function local_pickup_notice() {
		\_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
	}

	/**
	 * Dismisses the notice.
	 *
	 * @return bool
	 * @deprecated 15.4
	 * @codeCoverageIgnore
	 */
	public function dismiss_local_pickup_notice() {
		\_deprecated_function( __METHOD__, 'Yoast Local SEO 15.4' );
		return WPSEO_Options::set( 'dismiss_local_pickup_notice', true );
	}
}
