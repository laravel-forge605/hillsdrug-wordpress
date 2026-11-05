<?php
/**
 * Shortcode Template Generator
 * `bdpp_tmpl` Shortcode
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_shortcode_template( $atts, $content = null ) {

	global $bdpp_layout_id;

	/* Only for page builder preview - Start */
	if ( ( function_exists('vc_is_page_editable') && vc_is_page_editable() && ! empty( $atts['layout_id'] ) && isset( $atts['bdp_layout_preview'] ) && 'no' == $atts['bdp_layout_preview'] ) 
		|| ( is_admin() && empty( $atts['bdp_layout_preview'] ) && ( isset( $_GET['elementor-preview'] ) || ( isset( $_POST['action'] ) && 'elementor_ajax' === $_POST['action'] ) || ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) ) )
	) {

		return '<div class="bdpp-pb-shrt-prev-wrap">
				<div class="bdpp-pb-shrt-title"><span>Blog Designer Pack - Layout</span></div>
				[<span>bdpp_tmpl layout_id="'.esc_attr( $atts['layout_id'] ).'"</span>]
			</div>';
	}
	/* Only for page builder preview - Ends */


	// Shortcode Parameters
	$atts = shortcode_atts(array(
		'layout_id'	=> '',
		'id'		=> '',
	), $atts, 'bdpp_tmpl');

	// Taking some variables
	$layout_id		= bdp_clean_number( $atts['layout_id'] );
	$template_id	= bdp_clean_number( $atts['id'] );

	/* Template ID - Old Method */
	if( $template_id ) {

		$bdpp_shortcode_tmpl = get_option( 'bdpp_shrt_tmpl' );

		if( ! empty( $bdpp_shortcode_tmpl[ $template_id ] ) && ! empty( $bdpp_shortcode_tmpl[ $template_id ]['shortcode'] ) ) {
			$template_enable	= ! empty( $bdpp_shortcode_tmpl[ $template_id ]['enable'] ) ? 1 : 0;
			$template_shortcode	= $bdpp_shortcode_tmpl[ $template_id ]['shortcode'];
		}

	} elseif( $layout_id ) { /* Layout ID - New Method */

		// Set Global Layout ID
		$bdpp_layout_id	= $layout_id;

		$meta_prefix	= BDP_META_PREFIX;
		$layout_data	= get_post( $layout_id );

		if( $layout_data && isset( $layout_data->post_type ) && BDP_LAYOUT_POST_TYPE == $layout_data->post_type ) {
			$template_enable	= ( isset( $layout_data->post_status ) && 'publish' == $layout_data->post_status ) ? 1 : 0;
			$template_shortcode	= get_post_meta( $layout_id, $meta_prefix.'layout_shrt', true );
		}
	}

	ob_start();

	// If template exist
	if( ! empty( $template_shortcode ) ) {

		if( ! empty( $template_enable ) ) {
			echo do_shortcode( $template_shortcode );
		}

	} else {
		esc_html_e( 'Sorry, layout does not exist.', 'blog-designer-pack' );
	}

	// Reset global layout id
	$bdpp_layout_id = '';

	$content .= ob_get_clean();
	return $content;
}

// Layout Template Shortcode
add_shortcode( 'bdpp_tmpl', 'bdp_render_shortcode_template' );