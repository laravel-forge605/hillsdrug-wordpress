<?php
/**
 * `bdp_ticker` Post Ticker Shortcode
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_post_ticker( $atts, $content = null ) {

	// Taking some globals
	global $post, $bdpp_layout_id;

	// Shortcode Parameters
	$atts = shortcode_atts(array(
		'ticker_title'			=> __('Latest Post', 'blog-designer-pack'),
		'theme_color'			=> '#2096cd',
		'heading_font_color'	=> '#fff',
		'font_color'			=> '#2096cd',
		'font_style'			=> 'normal',
		'ticker_effect'			=> 'slide-up',
		'autoplay'				=> 'true',
		'speed'					=> 3000,
		'limit' 				=> 20,
		'category' 				=> array(),
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		'css_class'				=> '',
		'custom_param_1'		=> '',	// Custom Param Passed Just for Developer
		'custom_param_2'		=> '',
	), $atts, 'bdp_ticker');

	$atts['shortcode']				= 'bdp_ticker';
	$atts['layout_id']				= $bdpp_layout_id;
	$atts['theme_color']			= ! empty( $atts['theme_color'] )			? $atts['theme_color']						: '#2096cd';
	$atts['font_color']				= ! empty( $atts['font_color'] )			? $atts['font_color']						: '#2096cd';
	$atts['heading_font_color']		= ! empty( $atts['heading_font_color'] )	? $atts['heading_font_color']				: '#fff';
	$atts['ticker_effect']			= ! empty( $atts['ticker_effect'] )			? $atts['ticker_effect']					: 'slide-up';
	$atts['autoplay']				= bdp_string_to_bool( $atts['autoplay'] );
	$atts['speed']					= bdp_clean_number( $atts['speed'], 3000 );
	$atts['order'] 				= ( strtolower($atts['order']) == 'asc' ) 	? 'ASC' 					: 'DESC';
	$atts['orderby'] 			= ! empty( $atts['orderby'] )				? $atts['orderby'] 			: 'date';
	$atts['limit'] 				= bdp_clean_number( $atts['limit'], 20, 'number' );
	$atts['category'] 			= bdp_maybe_explode( $atts['category'] );
	$atts['unique'] 			= bdp_get_unique();
	$atts['css_class']			.= ( $atts['layout_id'] ) ? " bdpp-layout-{$atts['layout_id']}"	: '';
	$atts['css_class']			= bdp_sanitize_html_classes( $atts['css_class'] );

	// Enqueue required scripts
	wp_enqueue_script( 'bdpp-ticker-script' );
	wp_enqueue_script( 'bdpp-public-script' );
	bdp_enqueue_script();

	// Taking some variables
	$atts['ticker_conf'] = array('ticker_effect' => $atts['ticker_effect'], 'autoplay' => $atts['autoplay'], 'speed' => $atts['speed']);

	// WP Query Parameters
	$args = array(
		'post_type'				=> BDP_POST_TYPE,
		'post_status'			=> array('publish'),
		'order'					=> $atts['order'],
		'orderby'				=> $atts['orderby'],
		'posts_per_page'		=> $atts['limit'],
		'no_found_rows'			=> true,
		'ignore_sticky_posts'	=> true,
	);

	// Category Parameter
	if( $atts['category'] ) {

		$args['tax_query'] = array(
								array( 
									'taxonomy'	=> BDP_CAT,
									'terms'		=> $atts['category'],
									'field'		=> ( isset($atts['category'][0]) && is_numeric($atts['category'][0]) ) ? 'term_id' : 'slug',
								));
	}

	$args = apply_filters( 'bdpp_ticker_query_args', $args, $atts );

	// WP Query
	$query = new WP_Query( $args );

	ob_start();

	// If post is there
	if ( $query->have_posts() ) {

		include( BDP_DIR . "/templates/ticker/loop-start.php" );

		while ( $query->have_posts() ) : $query->the_post();

			$atts['format']		= bdp_get_post_format();
			$atts['post_link']	= bdp_get_post_link( $post->ID );
			$atts['wrp_cls']	= "bdpp-post-{$post->ID} bdpp-post-{$atts['format']}";
			$atts['wrp_cls']	.= ( is_sticky( $post->ID ) ) ? ' bdpp-sticky' : '';

			// Include shortcode html file
			include( BDP_DIR . "/templates/ticker/design-1.php" );

		endwhile;

		include( BDP_DIR . "/templates/ticker/loop-end.php" );
	}

	wp_reset_postdata(); // Reset WP Query

	$content .= ob_get_clean();
	return $content;
}

// Post Ticker Shortcode
add_shortcode( 'bdp_ticker', 'bdp_render_post_ticker' );