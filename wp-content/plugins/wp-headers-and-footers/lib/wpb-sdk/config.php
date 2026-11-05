<?php
/**
 * WPBrigade SDK shared constants (version + API).
 *
 * Path constants (WPBRIGADE_SDK_DIR*) are defined by the winning runtime in
 * require.php so an older bundle loaded first cannot poison paths.
 *
 * @package wpbrigade_sdk
 */

$wpb_sdk_bundle_version = '3.4.0';
$wpb_sdk_version_file   = __DIR__ . '/version.php';
if ( is_readable( $wpb_sdk_version_file ) ) {
	$wpb_sdk_loaded = include $wpb_sdk_version_file;
	if ( is_string( $wpb_sdk_loaded ) && '' !== $wpb_sdk_loaded ) {
		$wpb_sdk_bundle_version = $wpb_sdk_loaded;
	}
}

if ( ! defined( 'WPBRIGADE_SDK_VERSION' ) ) {
	define( 'WPBRIGADE_SDK_VERSION', $wpb_sdk_bundle_version );
}

if ( ! defined( 'WPBRIGADE_SDK_API_ENDPOINT' ) ) {
	define( 'WPBRIGADE_SDK_API_ENDPOINT', 'https://app.telemetry.wpbrigade.com/api/v2' );
}
