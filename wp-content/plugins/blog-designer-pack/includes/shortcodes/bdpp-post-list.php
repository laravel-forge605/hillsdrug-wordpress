<?php
/**
 * 'bdp_post_list' Post List Shortcode
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to handle the `bdp_post_list` shortcode
 * 
 * @since 1.0
 */
function bdp_render_post_list( $atts, $content = null ) {

	// Taking some globals
	global $post, $multipage, $bdpp_layout_id;

	// Shortcode Parameters
	$atts = shortcode_atts(array(
		'design' 				=> 'design-1',
		'show_author' 			=> 'true',
		'show_date' 			=> 'true',
		'show_category' 		=> 'true',
		'show_content' 			=> 'true',
		'show_tags'				=> 'true',
		'show_comments'			=> 'true',
		'show_read_more' 		=> 'true',
		'read_more_text'		=> '',
		'media_size' 			=> 'bdpp-medium',
		'limit' 				=> 20,
		'content_words_limit' 	=> 20,
		'category' 				=> array(),
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		'pagination' 			=> 'true',
		'css_class'				=> '',
		'custom_param_1'		=> '',	// Custom Param Passed Just for Developer
		'custom_param_2'		=> '',
		), $atts, 'bdp_post_list');

	$shortcode_designs 				= bdp_post_list_designs();
	$atts['shortcode']				= 'bdp_post_list';
	$atts['layout_id']				= $bdpp_layout_id;
	$atts['limit'] 					= bdp_clean_number( $atts['limit'], 20, 'number' );
	$atts['show_author'] 			= bdp_string_to_bool( $atts['show_author'] );
	$atts['show_tags'] 				= bdp_string_to_bool( $atts['show_tags'] );
	$atts['show_comments'] 			= bdp_string_to_bool( $atts['show_comments'] );
	$atts['show_date'] 				= bdp_string_to_bool( $atts['show_date'] );
	$atts['show_category'] 			= bdp_string_to_bool( $atts['show_category'] );
	$atts['show_content'] 			= bdp_string_to_bool( $atts['show_content'] );
	$atts['pagination'] 			= bdp_string_to_bool( $atts['pagination'] );
	$atts['show_read_more'] 		= bdp_string_to_bool( $atts['show_read_more'] );
	$atts['category'] 				= bdp_maybe_explode( $atts['category'] );
	$atts['media_size'] 			= ! empty( $atts['media_size'] )			? $atts['media_size'] 					: 'large';
	$atts['content_words_limit'] 	= ! empty( $atts['content_words_limit'] ) 	? $atts['content_words_limit'] 			: 20;
	$atts['read_more_text']			= ! empty( $atts['read_more_text'] )		? $atts['read_more_text']				: __( 'Read More', 'blog-designer-pack' );
	$atts['order'] 					= ( strtolower($atts['order']) == 'asc' ) 	? 'ASC' 								: 'DESC';
	$atts['orderby'] 				= ! empty( $atts['orderby'] )				? $atts['orderby'] 						: 'date';
	$atts['design'] 				= ($atts['design'] && (array_key_exists(trim($atts['design']), $shortcode_designs))) ? trim($atts['design']) : 'design-1';
	$atts['multi_page']				= ( $multipage || is_single() ) ? 1 : 0;	
	$atts['unique'] 				= bdp_get_unique();
	$atts['css_class']				.= ( $atts['layout_id'] ) ? " bdpp-layout-{$atts['layout_id']}" : '';
	$atts['css_class']				= bdp_sanitize_html_classes( $atts['css_class'] );

	// Pagination parameter
	if( isset( $_GET['bdpp_page'] ) || $atts['multi_page'] ) {
		$atts['paged'] = isset( $_GET['bdpp_page'] ) ? $_GET['bdpp_page'] : 1;
	} elseif ( get_query_var( 'paged' ) ) {
		$atts['paged'] = get_query_var('paged');
	} elseif ( get_query_var( 'page' ) ) {
		$atts['paged'] = get_query_var( 'page' );
	} else {
		$atts['paged'] = 1;
	}

	// Taking some variables
	$atts['count'] 	= 0;

	// WP Query Parameters
	$args = array(
		'post_type'				=> BDP_POST_TYPE,
		'post_status'			=> array('publish'),
		'order'					=> $atts['order'],
		'orderby'				=> $atts['orderby'],
		'posts_per_page'		=> $atts['limit'],
		'paged'					=> ( $atts['pagination'] ) ? $atts['paged'] : 1,
		'no_found_rows'			=> ( ! $atts['pagination'] ) ? true : false,
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

	$args = apply_filters( 'bdpp_post_list_query_args', $args, $atts );

	// WP Query
	$query 					= new WP_Query( $args );
	$atts['max_num_pages'] 	= $query->max_num_pages;

	ob_start();

	// If post is there
	if ( $query->have_posts() ) {

		include( BDP_DIR . '/templates/list/loop-start.php' );

		while ( $query->have_posts() ) : $query->the_post();

			$atts['count']		= ( $atts['count'] + 1 );
			$atts['format']		= bdp_get_post_format();
			$atts['feat_img'] 	= bdp_get_post_feat_image( $post->ID,  $atts['media_size'] );
			$atts['post_link'] 	= bdp_get_post_link( $post->ID );
			$atts['cate_name'] 	= bdp_get_post_terms( $post->ID, BDP_CAT );
			$atts['tags']  		= (  $atts['show_tags'] ) ? bdp_post_meta_data( array('tag' =>  $atts['show_tags']), array('tag_taxonomy' => 'post_tag') ) : '';

			$atts['wrp_cls']	= "bdpp-post-{$post->ID} bdpp-post-{$atts['format']}";
			$atts['wrp_cls']	.= ( is_sticky( $post->ID ) ) 	? ' bdpp-sticky'	: '';
			$atts['wrp_cls'] 	.= empty($atts['feat_img'])		? ' bdpp-no-thumb'	: ' bdpp-has-thumb';

			// Include Dsign File
			include( BDP_DIR . "/templates/list/{$atts['design']}.php" );

		endwhile;

		include( BDP_DIR . "/templates/list/loop-end.php" );
	}

	wp_reset_postdata(); // Reset WP Query

	$content .= ob_get_clean();
	return $content;
}

// Post List Shortcode
add_shortcode( 'bdp_post_list', 'bdp_render_post_list' );
add_shortcode( 'pld_post_list', 'bdp_render_post_list' );