<?php
/**
 * Script Class
 *
 * Handles the script and style functionality of plugin
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDP_Scripts {

	function __construct() {

		// Action for admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'bdpp_admin_style_script' ) );
		
		// Action for public scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'bdpp_public_style_script' ) );

		// Action to add custom CSS in head
		add_action( 'wp_head', array($this, 'bdpp_render_custom_css'), 20 );

		// Action to add admin script and style when edit with elementor at front side
		add_action( 'elementor/editor/after_enqueue_scripts', array($this, 'bdpp_admin_elementor_script_style') );
	}

	/**
	 * Registring and enqueing admin sctipts and styles
	 *
 	 * @since 1.0
	 */
	public function bdpp_admin_style_script( $hook_suffix ) {

		global $post_type;

		$suffix				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$allowed_post_types = bdp_allowed_post_types();

		$pages_arr = array( BDPP_SCREEN_ID.'_page_bdpp-settings', 'toplevel_page_bdpp-layouts', BDPP_SCREEN_ID.'_page_bdpp-styles' );

		// Select2 Style
		if( ! wp_style_is( 'select2', 'registered' ) ) {
			wp_register_style( 'select2', BDP_URL.'assets/css/select2.min.css', array(), '4.0.3' );
		}

		// FS Pricing CSS
		if( BDPP_SCREEN_ID.'_page_bdpp-layouts-pricing' == $hook_suffix ) {
			wp_register_style( 'bdpp-fs-pricing', BDP_URL . 'assets/css/fs-pricing.css', array(), BDP_VERSION );
			wp_enqueue_style( 'bdpp-fs-pricing' );
		}

		/* Styles */
		wp_register_style( 'bdpp-admin-style', BDP_URL . "assets/css/bdpp-admin{$suffix}.css", array(), BDP_VERSION );


		/* Scripts */
		// Select2 JS
		if( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2', BDP_URL.'assets/js/select2.full.min.js', array('jquery'), '4.0.3', true );
		}

		// Shortcode Generator
		wp_register_script( 'bdpp-shrt-generator', BDP_URL . "assets/js/bdpp-shortcode-generator.min.js", array( 'jquery' ), BDP_VERSION, true );
		wp_localize_script( 'bdpp-shrt-generator', 'Bdpp_Shrt_Generator', array(
																'shortcode_err'				=> esc_js( __('Sorry, Something happened wrong. Kindly please be sure that you have choosen relevant shortcode from the dropdown.', 'blog-designer-pack') ),
																'select2_input_too_short'	=> esc_js( __( 'Please enter 1 or more characters', 'blog-designer-pack' ) ),
																'select2_remove_all_items'	=> esc_js( __( 'Remove all items', 'blog-designer-pack' ) ),
																'select2_remove_item'		=> esc_js( __( 'Remove item', 'blog-designer-pack' ) ),
																'select2_searching'			=> esc_js( __( 'Searching…', 'blog-designer-pack' ) ),
																'select2_placeholder'		=> esc_js( __( 'Select Data', 'blog-designer-pack' ) ),
															));

		// Admin JS
		wp_register_script( 'bdpp-admin-script', BDP_URL . "assets/js/bdpp-admin{$suffix}.js", array( 'jquery' ), BDP_VERSION, true );
		wp_localize_script( 'bdpp-admin-script', 'BdppAdmin', array(
																	'syntax_highlighting'	=> ( 'false' === wp_get_current_user()->syntax_highlighting )	? 0 : 1,
																	'confirm_msg'			=> esc_js( __('Are you sure you want to do this?', 'blog-designer-pack') ),
																	'reset_msg'				=> esc_js( __('Click OK to reset all options. All settings will be lost!', 'blog-designer-pack') ),
																	'reset_post_view_msg'	=> esc_js( __('Click OK to reset post view count. This process can not be undone!', 'blog-designer-pack') ),
																	'wait_msg'				=> esc_js( __('Please Wait...', 'blog-designer-pack') ),
																));

		// Post Screen, Taxonomy Screen and Widget Screen
		if( ($hook_suffix == 'widgets.php') || (in_array( $post_type, $allowed_post_types ) && ( $hook_suffix == 'edit.php' || $hook_suffix == 'post.php' || $hook_suffix == 'post-new.php' )) ) {
			wp_enqueue_style( 'bdpp-admin-style' ); 	// Admin Styles
			wp_enqueue_script( 'bdpp-admin-script' );	// Admin Script
			wp_enqueue_media();
		}

		// Plugin Setting Page
		if( $hook_suffix == BDPP_SCREEN_ID.'_page_bdpp-settings' ) {
			wp_enqueue_media();
		}

		// All Layouts Page
		if( 'toplevel_page_bdpp-layouts' == $hook_suffix ) {
			wp_enqueue_script( 'clipboard' );
		}

		if( in_array( $hook_suffix, $pages_arr ) ) {

			// Admin Styles
			wp_enqueue_style( 'bdpp-admin-style' );

			/* --------------------------------- */

			// Admin Scripts
			if( ! empty( $_GET['tab'] ) && $_GET['tab'] == 'css' ) {
				wp_enqueue_code_editor( array(
					'type' 			=> 'text/css',
					'codemirror' 	=> array(
						'indentUnit' 	=> 2,
						'tabSize'		=> 2,
					),
				) );
			}

			wp_enqueue_script( 'bdpp-admin-script' );
		}

		// Shortcode Builder and Add New Layout Screen
		if( BDPP_SCREEN_ID.'_page_bdpp-shrt-builder' == $hook_suffix || BDPP_SCREEN_ID.'_page_bdpp-layout' == $hook_suffix ) {
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'bdpp-admin-style' );

			wp_enqueue_script( 'clipboard' );
			wp_enqueue_script( 'shortcode' );
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'bdpp-admin-script' );
			wp_enqueue_script( 'bdpp-shrt-generator' );
		}

		// For VC Front End Page Editing
		if( function_exists('vc_is_frontend_editor') && vc_is_frontend_editor() ) {
			wp_register_script( 'bdpp-vc-frontend', BDP_URL . 'assets/js/vc/bdpp-vc-frontend.js', array(), BDP_VERSION, true );
			wp_enqueue_script( 'bdpp-vc-frontend' );
		}
	}

	/**
	 * Registring and enqueing public scripts
	 *
 	 * @since 1.0
	 */
	public  function bdpp_public_style_script() {

		global $post, $inf_plugin_identifier_data;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/* Styles */
		// Registring and enqueing slick slider css
		if( ! wp_style_is( 'owl-carousel', 'registered' ) && bdp_get_option('disable_owl_css') == 0 ) {
			wp_register_style( 'owl-carousel', BDP_URL.'assets/css/owl.carousel.min.css', array(), BDP_VERSION );
		}

		// Registring and enqueing font awesome css
		if( ! wp_style_is( 'inf-font-awesome', 'registered' ) && bdp_get_option('disable_font_awsm_css') == 0 ) {
			wp_register_style( 'inf-font-awesome', BDP_URL . 'assets/css/font-awesome.min.css', array(), BDP_VERSION );
		}

		// Registring and enqueing public script
		wp_register_style( 'bdpp-public-style', BDP_URL . "assets/css/bdpp-public{$suffix}.css", array(), BDP_VERSION );
		
		wp_enqueue_style( 'inf-font-awesome' );
		wp_enqueue_style( 'owl-carousel' );
		wp_enqueue_style( 'bdpp-public-style' );


		/* Scripts */

		// Taking post id
		$post_id = isset($post->ID) ? $post->ID : '';

		// Registring slick slider script
		if( ! wp_script_is( 'jquery-owl-carousel', 'registered' ) ) {
			wp_register_script( 'jquery-owl-carousel', BDP_URL. 'assets/js/owl.carousel.min.js', array('jquery'), BDP_VERSION, true);
		}

		// Registring vertical slider script
		if( ! wp_script_is( 'jquery-vticker', 'registered' ) ) {
			wp_register_script( 'jquery-vticker', BDP_URL. "assets/js/jquery-vticker.min.js", array('jquery'), BDP_VERSION, true);
		}

		// Registring ticker script
		if( ! wp_script_is( 'bdpp-ticker-script', 'registered' ) ) {
			wp_register_script( 'bdpp-ticker-script', BDP_URL . 'assets/js/bdpp-ticker.min.js', array('jquery'), BDP_VERSION, true );
		}

		// Admin Script (Do not forgot to update for elementor script action also)
		wp_register_script( 'bdpp-public-script', BDP_URL . "assets/js/bdpp-public{$suffix}.js", array( 'jquery' ), BDP_VERSION, true );
		wp_localize_script( 'bdpp-public-script', 'Bdpp', array( 
																'ajax_url' 			=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
																'is_mobile'			=> wp_is_mobile(),
																'is_rtl' 			=> ( is_rtl() ) ? 1 : 0,
																'no_post_found_msg'	=> esc_js( __('No more post to display.', 'blog-designer-pack') ),
																'vc_page_edit'		=> ( function_exists('vc_is_page_editable') && vc_is_page_editable() ) ? 1 : 0,
															));

		/*===== Page Builder Scripts =====*/
		// VC Front End Page Editing
		if ( function_exists('vc_is_page_editable') && vc_is_page_editable() ) {
			wp_enqueue_script( 'masonry' );
			wp_enqueue_script( 'jquery-owl-carousel' );
			wp_enqueue_script( 'bdpp-ticker-script' );
			wp_enqueue_script( 'bdpp-public-script' );
		}

		// Elementor Frontend Editing
		if ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_GET['elementor-preview'] ) && $post_id == (int) $_GET['elementor-preview'] ) {
			wp_register_script( 'bdpp-elementor-script', BDP_URL . 'assets/js/elementor/bdpp-elementor.js', array(), BDP_VERSION, true );

			wp_enqueue_script( 'masonry' );
			wp_enqueue_script( 'jquery-owl-carousel' );
			wp_enqueue_script( 'bdpp-ticker-script' );
			wp_enqueue_script( 'bdpp-public-script' );
			wp_enqueue_script( 'bdpp-elementor-script' );
		}
	}

	/**
	 * Add custom CSS to head
	 * 
	 * @since 1.0
	 */
	function bdpp_render_custom_css() {

		// Custom CSS
		$custom_css = bdp_get_option( 'custom_css' );

		if ( ! empty( $custom_css ) ) {
			echo '<style type="text/css">' . "\n" .
					wp_strip_all_tags( $custom_css )
				 . "\n" . '</style>' . "\n";
		}
	}

	/**
	 * Add admin script and style when edit with elementor at front side
	 * 
	 * @since 1.0
	 */
	function bdpp_admin_elementor_script_style() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Admin Style
		wp_register_style( 'bdpp-admin-style', BDP_URL . "assets/css/bdpp-admin{$suffix}.css", array(), BDP_VERSION );

		// Admin Script
		wp_register_script( 'bdpp-admin-script', BDP_URL . "assets/js/bdpp-admin{$suffix}.js", array( 'jquery' ), BDP_VERSION, true );
		wp_localize_script( 'bdpp-admin-script', 'BdppAdmin', array(
																	'syntax_highlighting'	=> ( 'false' === wp_get_current_user()->syntax_highlighting ) ? 0 : 1,
																	'confirm_msg'			=> esc_js( __('Are you sure you want to do this?', 'blog-designer-pack') ),
																	'reset_msg'				=> esc_js( __('Click OK to reset all options. All settings will be lost!', 'blog-designer-pack') ),
																	'reset_post_view_msg'	=> esc_js( __('Click OK to reset post view count. This process can not be undone!', 'blog-designer-pack') ),
																	'wait_msg'				=> esc_js( __('Please Wait...', 'blog-designer-pack') ),
																));

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style( 'bdpp-admin-style' ); 	// Admin Styles
		
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script( 'bdpp-admin-script' );	// Admin Script
	}
}