<?php
/**
 * Public Class
 *
 * Handles the public side functionality of plugin
 *
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class BDP_Public {

	function __construct() {

		// Load More Post via Ajax
		add_action( 'wp_ajax_bdp_load_more_posts', array($this, 'bdp_load_more_posts') );
		add_action( 'wp_ajax_nopriv_bdp_load_more_posts', array($this, 'bdp_load_more_posts') );
	}

	/**
	 * Load More Posts via Ajax
	 * 
	 * @since 1.0
	 */
	function bdp_load_more_posts() {

		// Taking the shortocde parameters
		$atts = json_decode( wp_unslash($_POST['shrt_param']), true );
		extract( $atts );

		$result = array(
					'status'	=> 0,
					'msg'		=> esc_html__( 'Sorry, Something happened wrong.', 'blog-designer-pack' ),
				);
		$paged				= isset( $_POST['paged'] )				? bdp_clean_number( $_POST['paged'] )	: 1;
		$href				= isset( $_POST['href'] )				? bdp_clean_url( $_POST['href'] )		: '';
		$count				= isset( $count )						? $count					: 0;
		$count				= isset( $_POST['count'] )				? $_POST['count']			: $count;
		$pagination_type	= isset( $atts['pagination_type'] )		? $atts['pagination_type']	: '';
		$query_shrt			= str_replace('bdp_', 'bdpp_', $shortcode);
		$shortcode_designs 	= bdp_post_masonry_designs();
		$atts['design'] 	= ( $atts['design'] && (array_key_exists(trim($atts['design']), $shortcode_designs)) ) ? trim( $atts['design'] ) : 'design-1';
		$atts['loop_count'] = 0;

		// If valid data found
		if( ! empty( $atts ) ) {

			// Taking some globals
			global $post;

			// WP Query Parameters
			$args = array(
				'post_type'      		=> BDP_POST_TYPE,
				'post_status' 			=> array('publish'),
				'order'					=> $order,
				'orderby'		 		=> $orderby,
				'posts_per_page' 		=> $limit,
				'paged'					=> $paged,
				'ignore_sticky_posts'	=> true,
			);

		    // Category Parameter
			if( $category ) {

				$args['tax_query'] = array(
										array( 
											'taxonomy' 	=> BDP_CAT,
											'terms' 	=> $category,
											'field' 	=> ( isset($category[0]) && is_numeric($category[0]) ) ? 'term_id' : 'slug',
										));
			}

			$args = apply_filters( $query_shrt.'_query_args', $args, $atts );

			// WP Query
			$query 					= new WP_Query( $args );
			$atts['post_count']		= $query->post_count;
			$atts['max_num_pages'] 	= $query->max_num_pages;
			$atts['paged']			= $paged;

			ob_start();

			// If post is there
			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) : $query->the_post();

					$count++;
					$atts['count'] 		= $count;
					$atts['loop_count']++;

					$atts['format']		= bdp_get_post_format();
					$atts['feat_img'] 	= bdp_get_post_feat_image( $post->ID, $media_size );
					$atts['post_link'] 	= bdp_get_post_link( $post->ID );
					$atts['cate_name'] 	= bdp_get_post_terms( $post->ID, BDP_CAT );
					$atts['tags']  		= isset( $show_tags ) ? bdp_post_meta_data( array('tag' => $show_tags), array('tag_taxonomy' => 'post_tag') ) : '';

					$atts['wrp_cls']	= "bdpp-post-{$post->ID} bdpp-post-{$atts['format']}";
					$atts['wrp_cls']	.= ( is_sticky( $post->ID ) ) 	? ' bdpp-sticky'	: '';
					$atts['wrp_cls'] 	.= empty( $atts['feat_img'] )	? ' bdpp-no-thumb'	: ' bdpp-has-thumb';
					$atts['wrp_cls']	.= " bdpp-col-{$grid} bdpp-columns";

					// Include Dsign File
					include( BDP_DIR . "/templates/masonry/{$atts['design']}.php" );

				endwhile;

			} // end of have_post()

			wp_reset_postdata(); // Reset WP Query

			$content = ob_get_clean();

			$result['status']			= 1;
			$result['shortcode']		= $shortcode;
			$result['count']			= $count;
			$result['data']				= $content;
			$result['last_page']		= ( $paged >= $atts['max_num_pages'] ) ? 1 : 0;
			$result['msg']				= esc_html__('Success', 'blog-designer-pack');
		}

		wp_send_json( $result );
	}
}

$bdp_public = new BDP_Public();