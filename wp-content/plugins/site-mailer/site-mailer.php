<?php
/**
 * Plugin Name: Email Deliverability - SMTP Replacement, Email API Deliverability & Email Log
 * Description: Effortlessly manage transactional emails with Email Deliverability. High deliverability, logs and statistics, and no SMTP plugins needed.
 * Plugin URI: https://elementor.com/
 * Version: 1.4.7
 * Author: Elementor.com
 * Author URI: https://go.elementor.com/author-url-sm/
 * Text Domain: site-mailer
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Email Deliverability is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Email Deliverability is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'SITE_MAILER_VERSION', '1.4.7' );
define( 'SITE_MAILER_FILE', __FILE__ );
define( 'SITE_MAILER_PATH', plugin_dir_path( SITE_MAILER_FILE ) );
define( 'SITE_MAILER_URL', plugins_url( '/', SITE_MAILER_FILE ) );
define( 'SITE_MAILER_ASSETS_PATH', SITE_MAILER_PATH . 'assets/' );
define( 'SITE_MAILER_ASSETS_URL', SITE_MAILER_URL . 'assets/' );

/**
 *  SiteMailer Class
 *
 */
final class SiteMailer {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		// Load Composer autoloader
		require_once SITE_MAILER_PATH . 'vendor/autoload.php';

		// Init Plugin
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin
	 *
	 * Do your Validations here:
	 * for example checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {
		// Once we get here, We have passed all validation checks, so we can safely include our plugin
		require_once 'plugin.php';
	}
}
// Instantiate SiteMailer..
new SiteMailer();
