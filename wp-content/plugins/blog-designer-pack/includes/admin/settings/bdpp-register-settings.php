<?php
/**
 * Register Settings
 *
 * @package Blog Designer Pack
 * @since 4.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin register settings
 * Handles to register plugin settings
 *
 * @since 4.0
 */
function bdp_register_settings() {
	register_setting( 'bdpp_settings', 'bdpp_opts', 'bdp_validate_settings' );
}

// Action to register settings
add_action( 'admin_init', 'bdp_register_settings' );

/**
 * Handles to validate plugin settings before updation
 *
 * @since 4.0
 */
function bdp_validate_settings( $input ) {
	
	global $bdpp_options;
	
	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}
	
	$input = $input ? $input : array();
	
	parse_str( $_POST['_wp_http_referer'], $referrer );
	$tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

	// Sanitize filter according to tab
	if( $tab ) {
		$input = apply_filters( 'bdpp_validate_settings_' . $tab, $input );
	}
	
	// General sanitize filter
	$input = apply_filters( 'bdpp_validate_settings', $input );
	
	// Merge our new settings with the existing
	$output = array_merge( $bdpp_options, $input );

	return $output;
}

/**
 * Plugin Settings Tab array
 *
 * @since 4.0
 */
function bdp_settings_tab() {
	
	$result_arr 	= array();
	$settings_arr	= array(
							'welcome'	=> __('Welcome', 'blog-designer-pack'),
							'general'	=> __('General', 'blog-designer-pack'),
							'trending'	=> __('Trending Post', 'blog-designer-pack'),
							'taxonomy'	=> __('Taxonomy', 'blog-designer-pack'),
							'sharing'	=> __('Sharing', 'blog-designer-pack'),
							'css'		=> __('CSS', 'blog-designer-pack'),
							'misc'		=> __('Misc', 'blog-designer-pack'),
							'pro'		=> __('Premium Features', 'blog-designer-pack'),
						);

	foreach ( $settings_arr as $sett_key => $sett_val ) {
		if( ! empty( $sett_key ) && ! empty( $sett_val ) ) {
			$result_arr[trim($sett_key)] = trim($sett_val);
		}
	}

	return $result_arr;
}

/**
 * Plugin default settings
 *
 * @since 4.0
 */
function bdp_default_settings() {
	
	$bdpp_options = array(
					'post_types'			=> array( 0 => 'post' ),
					'post_first_img'		=> 1,
					'post_default_feat_img'	=> '',
					'custom_css'			=> '',
					'post_content_fix'		=> 1,
					'disable_font_awsm_css'	=> 0,
				);

	return $bdpp_options;
}

/**
 * Plugin Setup On First Time Activation
 *
 * Does the initial setup when plugin is going to activate first time,
 * set default values for the plugin options.
 *
 * @since 4.0
 */
function bdp_set_default_settings() {

	global $bdpp_options;

	// Plugin default settings
	$bdpp_options = bdp_default_settings();

	// Update default options
	update_option( 'bdpp_opts', $bdpp_options );
}

/**
 * Get Settings From Option Page Handles to return all settings value
 * 
 * @since 4.0
 */
function bdp_get_settings() {
	
	$options = get_option('bdpp_opts');
	
	$settings = (is_array($options)) ? $options : array();
	
	return $settings;
}

/**
 * Get an option
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 4.0
 */
function bdp_get_option( $key = '', $default = false ) {

	global $bdpp_options;

	$default_setting = bdp_default_settings();

	if( ! isset( $bdpp_options[ $key ] ) && isset( $default_setting[ $key ] ) && ! $default ) {
		$value = $default_setting[ $key ];
	} else {
		$value = ! empty( $bdpp_options[ $key ] ) ? $bdpp_options[ $key ] : $default;
	}

	return $value;
}

/**
 * Handles to validate General tab settings
 *
 * @since 4.0
 */
function bdp_validate_general_settings( $input ) {

	$input['post_types']			= array( 0 => 'post' );
	$input['post_first_img']		= isset( $input['post_first_img'] ) 		? 1 : 0;
	$input['post_default_feat_img']	= isset( $input['post_default_feat_img'] )	? bdp_clean_url( $input['post_default_feat_img'] ) : '';

	return $input;
}
add_filter( 'bdpp_validate_settings_general', 'bdp_validate_general_settings', 9, 1 );

/**
 * Handles to validate CSS tab settings
 *
 * @since 4.0
 */
function bdp_validate_css_settings( $input ) {

	$input['custom_css'] = isset( $input['custom_css'] ) ? sanitize_textarea_field( $input['custom_css'] ) : '';

	return $input;
}
add_filter( 'bdpp_validate_settings_css', 'bdp_validate_css_settings', 9, 1 );

/**
 * Handles to validate Misc tab settings
 *
 * @since 4.0
 */
function bdp_validate_misc_settings( $input ) {

	$input['post_content_fix']		= ! empty( $input['post_content_fix'] )			? 1 : 0;
	$input['disable_font_awsm_css'] = ! empty( $input['disable_font_awsm_css'] ) 	? 1 : 0;

	return $input;
}
add_filter( 'bdpp_validate_settings_misc', 'bdp_validate_misc_settings', 9, 1 );