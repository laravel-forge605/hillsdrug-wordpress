<?php

/*
Plugin Name: Simple Sitemap
Plugin URI: https://wordpress.org/plugins/simple-sitemap/
Description: HTML sitemap to display content as a single linked list of posts, pages, or custom post types. You can even display posts in groups sorted by taxonomy!
Version: 3.9.2
Requires PHP: 7.4
Author: David Gwyer
Author URI: https://wpgoplugins.com
License: GPLv2 or later
Text Domain: simple-sitemap
*/
/*
	Copyright 2019 David Gwyer (email: david@wpgoplugins.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
namespace WPGO_Plugins\Simple_Sitemap;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Allow advanced installs to select the supported Freemius navigation mode.
if ( !defined( 'SITEMAP_FREEMIUS_NAVIGATION' ) ) {
    $navigation = (string) apply_filters( 'simple_sitemap_freemius_navigation', 'menu' );
    $navigation = ( in_array( $navigation, array('menu', 'tabs'), true ) ? $navigation : 'menu' );
    define( 'SITEMAP_FREEMIUS_NAVIGATION', $navigation );
}
if ( function_exists( __NAMESPACE__ . '\\ss_fs' ) ) {
    ss_fs()->set_basename( false, __FILE__ );
} else {
    /**
     * Create a helper function for easy SDK access.
     *
     * @return \Freemius Freemius object instance.
     */
    function ss_fs() {
        global $ss_fs;
        if ( !isset( $ss_fs ) ) {
            // Composer may have loaded the SDK before WordPress defined ABSPATH,
            // causing its entry file to return before registering this function.
            if ( !function_exists( 'fs_dynamic_init' ) ) {
                require __DIR__ . '/vendor/freemius/wordpress-sdk/start.php';
            }
            $ss_fs = fs_dynamic_init( array(
                'id'               => '4087',
                'slug'             => 'simple-sitemap',
                'premium_slug'     => 'simple-sitemap-pro',
                'type'             => 'plugin',
                'public_key'       => 'pk_d7776ef9a819e02b17ef810b17551',
                'is_premium'       => false,
                'navigation'       => SITEMAP_FREEMIUS_NAVIGATION,
                'premium_suffix'   => 'Pro',
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'menu'             => array(
                    'slug'       => 'simple-sitemap-menu',
                    'first-path' => 'admin.php?page=simple-sitemap-menu-welcome',
                ),
                'is_live'          => true,
                'is_org_compliant' => true,
            ) );
        }
        return $ss_fs;
    }

    // Init Freemius.
    ss_fs();
    // Signal that SDK was initiated.
    do_action( 'ss_fs_loaded' );
    /**
     * Main class constructor.
     *
     * @param array $module_roots Root plugin path/dir.
     */
    class Main {
        /**
         * Common root paths/directories.
         *
         * @var array<string, string>
         */
        public static $module_roots;

        /**
         * Initialize class.
         */
        public static function init() {
            self::$module_roots = array(
                'dir'  => plugin_dir_path( __FILE__ ),
                'pdir' => plugin_dir_url( __FILE__ ),
                'uri'  => plugins_url( '', __FILE__ ),
                'file' => __FILE__,
            );
            $root = self::$module_roots['dir'];
            require_once $root . 'lib/classes/bootstrap.php';
            new BootStrap();
        }

    }

    Main::init();
}