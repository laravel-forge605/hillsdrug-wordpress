<?php
/**
 * Plugin Name: HillDrugs Page Cache
 * Description: Lightweight full-page HTML cache for logged-out visitors. Serves a saved copy of the page
 *              before WordPress/plugins/Elementor run, cutting TTFB from seconds to milliseconds.
 *              Cache lives in wp-content/cache/hd-pagecache (git-ignored). Purges automatically when
 *              content, menus, widgets, theme or Elementor data change. TTL 6 hours.
 *
 * Manual purge: log in as admin and open  https://hilldrugs.com/?hd_purge_cache=1
 * Disable:      define( 'HD_PAGECACHE_DISABLE', true ); in wp-config.php, or delete this file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HD_PC_DIR', WP_CONTENT_DIR . '/cache/hd-pagecache/' );
define( 'HD_PC_TTL', 6 * HOUR_IN_SECONDS );

/** Can this request be served from / stored in the cache? */
function hd_pc_cacheable() {
	static $ok = null;
	if ( null !== $ok ) {
		return $ok;
	}
	$ok = false;

	if ( defined( 'HD_PAGECACHE_DISABLE' ) && HD_PAGECACHE_DISABLE ) {
		return $ok;
	}
	if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		return $ok;
	}
	if ( defined( 'DOING_AJAX' ) || defined( 'DOING_CRON' ) || defined( 'REST_REQUEST' ) || defined( 'XMLRPC_REQUEST' ) || defined( 'WP_CLI' ) ) {
		return $ok;
	}

	$uri  = $_SERVER['REQUEST_URI'] ?? '/';
	$path = (string) parse_url( $uri, PHP_URL_PATH );
	if ( preg_match( '#^/(wp-admin|wp-login|wp-json|wp-cron|xmlrpc|wp-content|wp-includes)#', $path ) || preg_match( '#/(feed|embed)/?$#', $path ) || preg_match( '#\.(php|xml|txt|json|css|js)$#i', $path ) ) {
		return $ok;
	}

	// Any non-tracking query string (search, preview, elementor, etc.) bypasses the cache.
	$qs = (string) parse_url( $uri, PHP_URL_QUERY );
	if ( '' !== $qs ) {
		parse_str( $qs, $params );
		foreach ( array_keys( $params ) as $k ) {
			if ( ! preg_match( '/^(utm_[a-z_]+|gclid|fbclid|msclkid|gbraid|wbraid|_ga|_gl)$/i', $k ) ) {
				return $ok;
			}
		}
	}

	// Logged-in users, password-protected posts, commenters: never cached.
	foreach ( array_keys( $_COOKIE ) as $c ) {
		if ( 0 === strpos( $c, 'wordpress_logged_in' ) || 0 === strpos( $c, 'wp-postpass' ) || 0 === strpos( $c, 'comment_author' ) || 0 === strpos( $c, 'woocommerce_' ) || 0 === strpos( $c, 'wordpress_no_cache' ) ) {
			return $ok;
		}
	}

	$ok = true;
	return $ok;
}

function hd_pc_file() {
	$path = (string) parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
	$path = '/' . trim( $path, '/' );
	return HD_PC_DIR . md5( strtolower( ( $_SERVER['HTTP_HOST'] ?? '' ) . $path ) ) . '.html';
}

function hd_pc_purge_all() {
	foreach ( glob( HD_PC_DIR . '*.html' ) ?: array() as $f ) {
		@unlink( $f );
	}
}

/* ---------------- Serve from cache (very early) ---------------- */
if ( hd_pc_cacheable() ) {
	$hd_file = hd_pc_file();
	if ( is_file( $hd_file ) && ( time() - filemtime( $hd_file ) ) < HD_PC_TTL ) {
		header( 'Content-Type: text/html; charset=UTF-8' );
		header( 'X-HD-Cache: HIT' );
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0' ); // browsers revalidate; nginx/CDN may still cache.
		readfile( $hd_file );
		exit;
	}
}

/* ---------------- Store on a miss ---------------- */
add_action( 'template_redirect', function () {
	if ( ! hd_pc_cacheable() || is_user_logged_in() || is_404() || is_search() || is_feed() || is_preview() || is_customize_preview() || post_password_required() ) {
		return;
	}
	if ( function_exists( 'is_checkout' ) || function_exists( 'is_cart' ) ) {
		return; // never cache shop pages if WooCommerce ever appears
	}
	header( 'X-HD-Cache: MISS' );
	ob_start( function ( $html ) {
		if ( 200 !== http_response_code() || strlen( $html ) < 1500 || false === stripos( $html, '</html>' ) ) {
			return $html;
		}
		if ( ! is_dir( HD_PC_DIR ) ) {
			@wp_mkdir_p( HD_PC_DIR );
		}
		if ( is_dir( HD_PC_DIR ) && is_writable( HD_PC_DIR ) ) {
			$tmp = HD_PC_DIR . uniqid( 'tmp', true );
			if ( false !== @file_put_contents( $tmp, $html . "\n<!-- cached " . gmdate( 'Y-m-d H:i:s' ) . " UTC by hd-pagecache -->" ) ) {
				@rename( $tmp, hd_pc_file() );
			}
		}
		return $html;
	} );
}, 0 );

/* ---------------- Invalidate ---------------- */
foreach ( array(
	'save_post', 'deleted_post', 'trashed_post', 'edit_comment', 'wp_insert_comment', 'transition_comment_status',
	'wp_update_nav_menu', 'customize_save_after', 'switch_theme', 'activated_plugin', 'deactivated_plugin',
	'elementor/editor/after_save', 'elementor/core/files/clear_cache', 'wpseo_save_compare_data',
	'update_option_sidebars_widgets', 'update_option_widget_block', 'update_option_blogname', 'update_option_page_on_front',
	'created_term', 'edited_term', 'delete_term', 'upgrader_process_complete',
) as $hd_hook ) {
	add_action( $hd_hook, 'hd_pc_purge_all', 99 );
}

/* Manual purge for admins. */
add_action( 'init', function () {
	if ( isset( $_GET['hd_purge_cache'] ) && current_user_can( 'manage_options' ) ) {
		hd_pc_purge_all();
		wp_die( 'HillDrugs page cache cleared. <a href="' . esc_url( home_url( '/' ) ) . '">Back to site</a>', 'Cache cleared', array( 'response' => 200 ) );
	}
} );

/* Admin-bar button. */
add_action( 'admin_bar_menu', function ( $bar ) {
	if ( current_user_can( 'manage_options' ) ) {
		$bar->add_node( array( 'id' => 'hd-purge', 'title' => 'Clear Page Cache', 'href' => esc_url( home_url( '/?hd_purge_cache=1' ) ) ) );
	}
}, 100 );
