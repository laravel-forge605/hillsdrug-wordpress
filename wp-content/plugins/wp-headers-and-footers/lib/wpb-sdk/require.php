<?php
/**
 * Loads WPBrigade SDK core classes (single runtime — winning provider only).
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/config.php';

$wpb_sdk_incoming_version = '3.4.0';
$wpb_sdk_version_file     = __DIR__ . '/version.php';
if ( is_readable( $wpb_sdk_version_file ) ) {
	$wpb_sdk_loaded = include $wpb_sdk_version_file;
	if ( is_string( $wpb_sdk_loaded ) && '' !== $wpb_sdk_loaded ) {
		$wpb_sdk_incoming_version = $wpb_sdk_loaded;
	}
}

if ( ! defined( 'WP_WPBRIGADE_SDK_VERSION' ) ) {
	define( 'WP_WPBRIGADE_SDK_VERSION', $wpb_sdk_incoming_version );
}

// Path constants belong to the winning runtime only (this file's directory).
if ( ! defined( 'WPBRIGADE_SDK_DIR' ) ) {
	define( 'WPBRIGADE_SDK_DIR', __DIR__ );
}
if ( ! defined( 'WPBRIGADE_SDK_DIR_PATH' ) ) {
	define( 'WPBRIGADE_SDK_DIR_PATH', __DIR__ . '/' );
}
if ( ! defined( 'WPBRIGADE_SDK_DIR_INCLUDES' ) ) {
	define( 'WPBRIGADE_SDK_DIR_INCLUDES', WPBRIGADE_SDK_DIR . '/includes' );
}
if ( ! defined( 'WPBRIGADE_SDK_DIR_SDK' ) ) {
	define( 'WPBRIGADE_SDK_DIR_SDK', WPBRIGADE_SDK_DIR_INCLUDES );
}

$wpb_sdk_runtime_includes = __DIR__ . '/includes';

// Always merge helpers from this bundle (function_exists guards).
require_once $wpb_sdk_runtime_includes . '/wpb-sdk-essential-functions.php';

if ( ! class_exists( 'WPBRIGADE_Optin_Verification', false ) ) {
	require_once $wpb_sdk_runtime_includes . '/class-wpb-sdk-optin-verification.php';
}

if ( ! class_exists( 'WPBRIGADE_Opt_Manager', false ) ) {
	require_once $wpb_sdk_runtime_includes . '/class-wpb-opt-manager.php';
}

if ( class_exists( 'WPBRIGADE_Logger', false ) ) {
	$wpb_sdk_loaded_version = defined( 'WP_WPBRIGADE_SDK_VERSION' ) ? WP_WPBRIGADE_SDK_VERSION : '0';
	if (
		version_compare( $wpb_sdk_loaded_version, $wpb_sdk_incoming_version, '<' )
		|| version_compare( $wpb_sdk_loaded_version, '3.2.0', '<' )
	) {
		if ( ! isset( $GLOBALS['wpb_sdk_registry'] ) || ! is_array( $GLOBALS['wpb_sdk_registry'] ) ) {
			$GLOBALS['wpb_sdk_registry'] = array();
		}
		$GLOBALS['wpb_sdk_registry']['legacy_logger_conflict'] = true;
	}
	return;
}

require_once $wpb_sdk_runtime_includes . '/class-wpb-sdk-logger.php';
