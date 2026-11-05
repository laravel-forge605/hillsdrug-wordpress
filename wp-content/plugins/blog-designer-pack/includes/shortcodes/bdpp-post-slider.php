<?php
/**
 * 'bdp_post_slider' Post Slider Shortcode
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to handle the `bdp_post_slider` shortcode
 * 
 * @since 1.0
 */
function bdp_render_post_slider( $atts, $content = null ) {

	global $post, $bdpp_layout_id;

	// Shortcode Parameters
	$atts = shortcode_atts(array(
		'limit' 				=> 20,
		'design' 				=> 'design-1',
		'show_author' 			=> 'true',
		'show_date' 			=> 'true',
		'show_category' 		=> 'true',
		'show_content' 			=> 'false',
		'show_tags'				=> 'false',
		'show_comments'			=> 'true',
		'show_read_more' 		=> 'false',
		'read_more_text'		=> '',
		'category' 				=> array(),
		'content_words_limit' 	=> 20,
		'media_size' 			=> 'large',
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		
		'dots' 					=> 'true',
		'arrows'				=> 'true',
		'autoplay'				=> 'true',
		'autoplay_interval'		=> 5000,
		'speed' 				=> 'false',
		'loop'					=> 'true',
		'css_class'				=> '',
		'custom_param_1'		=> '',	// Custom Param Passed Just for Developer
		'custom_param_2'		=> '',
		), $atts, 'bdp_post_slider');

	$shortcode_designs 				= bdp_post_slider_designs();
	$atts['shortcode']				= 'bdp_post_slider';
	$atts['layout_id']				= $bdpp_layout_id;
	$atts['limit'] 					= bdp_clean_number( $atts['limit'], 20, 'number' );
	$atts['show_author'] 			= bdp_string_to_bool( $atts['show_author'] );
	$atts['show_tags'] 				= bdp_string_to_bool( $atts['show_tags'] );
	$atts['show_comments'] 			= bdp_string_to_bool( $atts['show_comments'] );
	$atts['show_date'] 				= bdp_string_to_bool( $atts['show_date'] );
	$atts['show_category'] 			= bdp_string_to_bool( $atts['show_category'] );
	$atts['show_content'] 			= bdp_string_to_bool( $atts['show_content'] );
	$atts['show_read_more'] 		= bdp_string_to_bool( $atts['show_read_more'] );
	$atts['category'] 				= bdp_maybe_explode( $atts['category'] );
	$atts['media_size'] 			= ! empty( $atts['media_size'] )			? $atts['media_size'] 			: 'large';
	$atts['content_words_limit'] 	= ! empty( $atts['content_words_limit'] ) 	? $atts['content_words_limit'] 	: 20;
	$atts['read_more_text']			= ! empty( $atts['read_more_text'] )		? $atts['read_more_text']		: __( 'Read More', 'blog-designer-pack' );
	$atts['order'] 					= ( strtolower($atts['order']) == 'asc' ) 	? 'ASC' 						: 'DESC';
	$atts['orderby'] 				= ! empty( $atts['orderby'] )				? $atts['orderby'] 				: 'date';

	$atts['loop']					= bdp_string_to_bool( $atts['loop'] );
	$atts['arrows']					= bdp_string_to_bool( $atts['arrows'] );
	$atts['dots']					= bdp_string_to_bool( $atts['dots'] );
	$atts['autoplay']				= bdp_string_to_bool( $atts['autoplay'] );
	$atts['autoplay_interval']		= bdp_clean_number( $atts['autoplay_interval'], 5000 );
	$atts['speed']					= is_numeric( $atts['speed'] ) ? bdp_clean_number( $atts['speed'], 0 ) : bdp_string_to_bool( $atts['speed'] );
	$atts['design'] 				= ($atts['design'] && (array_key_exists(trim($atts['design']), $shortcode_designs))) ? trim($atts['design']) : 'design-1';
	$atts['unique']					= bdp_get_unique();
	$atts['css_class']				.= ( $atts['layout_id'] ) ? " bdpp-layout-{$atts['layout_id']}" : '';
	$atts['css_class']				= bdp_sanitize_html_classes( $atts['css_class'] );

	// Taking some variables
	$atts['count'] 			= 0;
	$atts['slider_conf'] 	= array('loop' => $atts['loop'], 'arrows' => $atts['arrows'], 'dots' => $atts['dots'], 'autoplay' => $atts['autoplay'], 'autoplay_interval' => $atts['autoplay_interval'], 'speed' => $atts['speed']);

	// Enqueue required scripts
	wp_enqueue_script( 'jquery-owl-carousel' );
	wp_enqueue_script( 'bdpp-public-script' );
	bdp_enqueue_script();

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

	$args = apply_filters( 'bdpp_post_slider_query_args', $args, $atts );

	// WP Query
	$query = new WP_Query( $args );

	ob_start();

	// If post is there
	if ( $query->have_posts() ) {

		include( BDP_DIR . '/templates/slider/loop-start.php' );

		while ( $query->have_posts() ) : $query->the_post();

			$atts['count'] 			= ( $atts['count'] + 1 );
			$atts['format']			= bdp_get_post_format();
			$atts['feat_img'] 		= bdp_get_post_feat_image( $post->ID, $atts['media_size'] );
			$atts['post_link'] 		= bdp_get_post_link( $post->ID );
			$atts['cate_name'] 		= bdp_get_post_terms( $post->ID, BDP_CAT );
			$atts['tags']  			= ( $atts['show_tags'] ) ? bdp_post_meta_data( array('tag' => $atts['show_tags']), array('tag_taxonomy' => 'post_tag') ) : '';

			$atts['wrp_cls']		= "bdpp-post-{$post->ID} bdpp-post-{$atts['format']}";
			$atts['wrp_cls']		.= ( is_sticky( $post->ID ) ) 	? ' bdpp-sticky'	: '';
			$atts['wrp_cls'] 		.= empty($atts['feat_img'])		? ' bdpp-no-thumb'	: ' bdpp-has-thumb';

			// Creating image style
			if( $atts['feat_img'] ) {
				$atts['image_style'] = 'background-image:url('.esc_url( $atts['feat_img'] ).');';
			} else {
				$atts['image_style'] = '';
			}

			// Include shortcode html file
			include( BDP_DIR . "/templates/slider/{$atts['design']}.php" );

		endwhile;

		include( BDP_DIR . "/templates/slider/loop-end.php" );
	}

	wp_reset_postdata(); // Reset WP Query

	$content .= ob_get_clean();
	return $content;
}

// Post Slider Shortcode
add_shortcode( 'bdp_post_slider', 'bdp_render_post_slider' );