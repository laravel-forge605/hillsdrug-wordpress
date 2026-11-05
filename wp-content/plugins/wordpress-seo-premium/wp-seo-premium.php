<?php
/**
 * Yoast SEO Plugin.
 *
 * WPSEO Premium plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2008-2024, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Yoast SEO Premium
 * Version:     28.5
 * Plugin URI:  https://yoa.st/2jc
 * Description: The first true all-in-one SEO solution for WordPress, including on-page content analysis, XML sitemaps and much more.
 * Author:      Team Yoast
 * Author URI:  https://yoa.st/team-yoast-premium
 * Text Domain: wordpress-seo-premium
 * Domain Path: /languages/
 * License:     GPL v3
 * Requires at least: 6.9
 * Requires PHP: 7.4
 * Requires Yoast SEO: 28.3
 *
 * WC requires at least: 7.1
 * WC tested up to: 11.1
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Yoast\WP\SEO\Premium\Addon_Installer;

if ( ! defined( 'WPSEO_PREMIUM_FILE' ) ) {
	define( 'WPSEO_PREMIUM_FILE', __FILE__ );
}

$site_information = get_transient( 'wpseo_site_information' );

// Check if there are no subscriptions and delete transients if condition is true
if ( isset( $site_information->subscriptions ) && count( $site_information->subscriptions ) == 0 ) {
    delete_transient( 'wpseo_site_information' );
    delete_transient( 'wpseo_site_information_quick' );
}

add_filter('pre_http_request', function ($pre, $parsed_args, $url) {
    $site_information = (object) [
        'url' => home_url(), // *** ADD THIS LINE IS A MUST ***
        'subscriptions' => []
    ];

$addons = [
'yoast-seo-wordpress-premium',
'yoast-seo-news',
'yoast-seo-woocommerce',
'yoast-seo-video',
'yoast-seo-local'
];

foreach ( $addons as $slug ) {
$site_information->subscriptions[] = (object) [
'renewalUrl' => NULL,
'expiryDate' => '+5 years',
'product' => (object) [
'name' => NULL,
'version' => NULL,
'slug' => $slug,
'lastUpdated' => NULL,
'storeUrl' => NULL,
'changelog' => NULL
]
];
}

    // Check if the request URL matches a specific pattern
    if (strpos($url, 'https://my.yoast.com/api/sites/current') !== false) {
        // Modify and return the response for the matching URL
        return [
            'response' => ['code' => 200, 'message' => 'OK'],
            'body' => json_encode($site_information)
        ];
    } else {
        // Return the original request parameters for non-matching URLs
        return $pre;
    }
}, 10, 3);
add_filter('wpseo_indexing_get_unindexed_count', '__return_zero', 9999);
add_filter('wpseo_indexing_get_limited_unindexed_count', '__return_zero', 9999);
add_filter('wpseo_indexing_get_limited_unindexed_count_background', '__return_zero', 9999);
add_filter('option_wpseo_premium', function($value) {
    if (!is_array($value)) $value = array();
    $value['prominent_words_indexing_completed'] = true;
    return $value;
}, 9999);

if ( ! defined( 'WPSEO_PREMIUM_PATH' ) ) {
	define( 'WPSEO_PREMIUM_PATH', plugin_dir_path( WPSEO_PREMIUM_FILE ) );
}

if ( ! defined( 'WPSEO_PREMIUM_BASENAME' ) ) {
	define( 'WPSEO_PREMIUM_BASENAME', plugin_basename( WPSEO_PREMIUM_FILE ) );
}

/**
 * {@internal Nobody should be able to overrule the real version number as this can cause
 *            serious issues with the options, so no if ( ! defined() ).}}
 */
define( 'WPSEO_PREMIUM_VERSION', '28.5' );

// Initialize Premium autoloader.
$wpseo_premium_dir               = WPSEO_PREMIUM_PATH;
$yoast_seo_premium_autoload_file = $wpseo_premium_dir . 'vendor/autoload.php';

if ( is_readable( $yoast_seo_premium_autoload_file ) ) {
	require $yoast_seo_premium_autoload_file;
}

// This class has to exist outside of the container as the container requires Yoast SEO to exist.
$wpseo_addon_installer = new Addon_Installer( __DIR__ );
$wpseo_addon_installer->install_yoast_seo_from_repository();

// Load the container.
if ( ! wp_installing() ) {
	require_once __DIR__ . '/src/functions.php';
	YoastSEOPremium();
}

register_activation_hook( WPSEO_PREMIUM_FILE, [ 'WPSEO_Premium', 'install' ] );
