<?php
/**
 * Development-only SDK account diagnostics.
 *
 * @package WPBrigade\SDK
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
	wp_die(
		esc_html__( 'You do not have permission to access this page.', 'wpbrigade-sdk' ),
		'',
		array( 'response' => 403 )
	);
}

if ( ! defined( 'WPBRIGADE_SDK__DEV_MODE' ) || true !== WPBRIGADE_SDK__DEV_MODE ) {
	wp_die(
		esc_html__( 'Debug mode is not enabled.', 'wpbrigade-sdk' ),
		'',
		array( 'response' => 403 )
	);
}

$resolved = function_exists( 'wpb_sdk_dev_view_resolve_product' )
	? wpb_sdk_dev_view_resolve_product()
	: array( 'slug' => '' );
$slug     = isset( $resolved['slug'] ) ? (string) $resolved['slug'] : '';
$data     = function_exists( 'wpb_sdk_dev_view_load_logs_data' )
	? wpb_sdk_dev_view_load_logs_data( $slug )
	: array();

if ( empty( $data ) && function_exists( 'wpb_sdk_dev_view_default_logs_data' ) ) {
	$data = wpb_sdk_dev_view_default_logs_data();
}

$user_info    = isset( $data['user_info'] ) && is_array( $data['user_info'] ) ? $data['user_info'] : array();
$product_info = isset( $data['product_info'] ) && is_array( $data['product_info'] ) ? $data['product_info'] : array();
$auth         = isset( $data['authentication'] ) && is_array( $data['authentication'] ) ? $data['authentication'] : array();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'WPB SDK Account Diagnostics', 'wpbrigade-sdk' ); ?></h1>
	<table class="widefat striped" style="max-width: 800px;">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Name', 'wpbrigade-sdk' ); ?></th>
				<td><?php echo esc_html( $user_info['user_nickname'] ?? '' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Email', 'wpbrigade-sdk' ); ?></th>
				<td><?php echo esc_html( $user_info['user_email'] ?? '' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Plugin', 'wpbrigade-sdk' ); ?></th>
				<td><?php echo esc_html( $product_info['name'] ?? '' ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Public Key', 'wpbrigade-sdk' ); ?></th>
				<td><code><?php echo esc_html( $auth['public_key'] ?? '' ); ?></code></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Version', 'wpbrigade-sdk' ); ?></th>
				<td><?php echo esc_html( $product_info['version'] ?? '' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
