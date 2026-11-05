<?php
/**
 * Shortcode Builder Class
 * Handles shortcode builder admin side functionality
 *
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDP_Shortcode_Generator {

	function __construct() {
		
		// Ajax action to get shortcode parameters data
		add_action( 'wp_ajax_bdpp_get_shrt_params_data', array( $this, 'bdpp_get_shrt_params_data' ) );

		// Ajax action to get categories based on search
		add_action( 'wp_ajax_bdpp_category_sugg', array( $this, 'bdpp_category_sugg' ) );

		// Ajax action to get categories based on search
		add_action( 'wp_ajax_bdpp_post_sugg', array( $this, 'bdpp_post_sugg' ) );
	}

	/**
	 * Get shortcode parameters data
	 * 
	 * @since 3.4.2
	 */
	function bdpp_get_shrt_params_data() {

		// Taking some defaults
		$result		= array(
							'success'			=> 0,
							'msg'				=> esc_js( __('Sorry, Something happened wrong.', 'blog-designer-pack') ),
							'data'				=> array(),
							'invalid_params'	=> array(),
						);
		$params				= isset( $_POST['params'] )				? bdp_clean( $_POST['params'] )				: '';
		$predefined_params	= isset( $_POST['predefined_params'] )	? bdp_clean( $_POST['predefined_params'] )	: '';
		$bdpp_nonce			= isset( $_POST['nonce'] )				? bdp_clean( $_POST['nonce'] )				: '';

		if( ! wp_verify_nonce( $bdpp_nonce, 'bdpp-shortcode-builder' ) ) {
			wp_send_json( $result );	
		}

		// Taking shortcode arguments
		$taxonomy = BDP_CAT;

		/***** Category / Exclude Category *****/
		$category_params = array( 'category' );

		foreach( $category_params as $param ) {

			$result['data'][$param] = '';

			/* Append the predefined data */
			if( ! empty( $predefined_params[$param] ) ) {

				foreach( $predefined_params[$param] as $predefined_param_key => $predefined_param_data ) {

					if( ! isset( $predefined_param_data['id'] ) || ! isset( $predefined_param_data['text'] ) ) {
						continue;
					}

					$result['data'][$param] .= '<option value="'.esc_attr( $predefined_param_data['id'] ).'" selected="selected">'.esc_html( $predefined_param_data['text'] ).'</option>';
				}
			}

			if( empty( $params[$param] ) ) {
				continue;
			}

			$result['data'][$param] = isset( $result['data'][$param] ) ? $result['data'][$param] : '';
			$param_data				= bdp_clean( explode( ',', $params[$param] ) );
			$processed_data			= array();

			if( $taxonomy ) {
				$terms_args = array(
								'taxonomy'		=> $taxonomy,
								'orderby'		=> 'name',
								'order'			=> 'ASC',
								'number'		=> 0,
								'fields'		=> 'id=>name',
								'hide_empty'	=> false,
							);

				// Compatibility with slug
				if( is_numeric( $param_data[0] ) ) {
					$terms_args['include'] = $param_data;
				} else {
					$terms_args['slug'] = $param_data;
				}

				$terms = get_terms( $terms_args );

				if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term_id => $term ) {
						
						$term_title	= ( $term != '' ) ? $term : __('Category', 'blog-designer-pack');
						$term_title	= $term_title . " - (#{$term_id})";

						$result['data'][$param] .= '<option value="'.esc_attr( $term_id ).'" selected="selected">'.esc_html( $term_title ).'</option>';

						// Store temp data
						$processed_data[ $term_id ] = $term_id;
					}
				}
			}

			/**
			 * Loop of params data so we can identify the missing values due to delete or not available with current selection
			 */
			foreach( $param_data as $param_data_key => $param_data_val ) {
				if( ! isset( $processed_data[ $param_data_val ] ) && $param_data_val != '' ) {
					$result['data'][$param] .= '<option value="'.esc_attr( $param_data_val ).'" selected="selected">'.esc_html( "Term - (#{$param_data_val}) (Not Available)" ).'</option>';

					// Set invalid parameters
					$result['invalid_params'][$param][] = $param_data_val;
				}
			}
		}

		$result['success']	= 1;
		$result['msg']		= esc_js( __('Success', 'blog-designer-pack') );

		$result = apply_filters( 'bdpp_shrt_builder_params_data', $result, $params );

		wp_send_json( $result );
	}

	/**
	 * Get Category Suggestion
	 * 
	 * @since 3.4.2
	 */
	function bdpp_category_sugg() {

		// Taking some defaults
		$result		= array();
		$taxonomy	= isset( $_GET['taxonomy'] )	? bdp_clean( $_GET['taxonomy'] )	: '';
		$search		= isset( $_GET['search'] )		? bdp_clean( $_GET['search'] )		: '';
		$bdpp_nonce	= isset( $_GET['nonce'] )		? bdp_clean( $_GET['nonce'] )		: '';

		if( ! empty( $taxonomy ) && $search && wp_verify_nonce( $bdpp_nonce, 'bdpp-shortcode-builder' ) ) {

			$terms_args = array(
									'taxonomy'		=> $taxonomy,
									'orderby'		=> 'name',
									'order'			=> 'ASC',
									'number'		=> 25,
									'fields'		=> 'id=>name',
									'hide_empty'	=> false,
								);

			if( ctype_digit( $search ) ) {
				$terms_args['include'] = $search;
			} else {
				$terms_args['search'] = $search;
			}

			$terms = get_terms( $terms_args );

			if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term_id => $term ) {
					
					$term_title	= ( $term != '' ) ? $term : __('Category', 'blog-designer-pack');
					$term_title	= $term_title . " - (#{$term_id})";

					$result[]	= array( $term_id, $term_title );
				}
			}

			$result = apply_filters( 'bdpp_shrt_builder_category_sugg', $result );
		}

		wp_send_json( $result );
	}

	/**
	 * Get Post Type Suggetions
	 * 
	 * @since 3.4.2
	 */
	function bdpp_post_sugg() {

		// Taking some defaults
		$result			= array();
		$post_type		= isset( $_GET['post_type'] )	? bdp_clean( $_GET['post_type'] )		: '';
		$search			= isset( $_GET['search'] )		? bdp_clean( $_GET['search'] )			: '';
		$bdpp_nonce		= isset( $_GET['nonce'] )		? bdp_clean( $_GET['nonce'] )			: '';
		$post_status	= isset( $_GET['post_status'] )	? bdp_clean( $_GET['post_status'] )	: '';
		$post_statuses	= ! empty( $post_status ) 		? explode(',', $post_status) 			: array( 'publish' );

		if( ! empty( $post_type ) && wp_verify_nonce( $bdpp_nonce, 'bdpp-shortcode-builder' ) ) {

			$posts_args = array(
									'post_type'				=> $post_type,
									'post_status'			=> $post_statuses,
									'order'					=> 'ASC',
									'orderby'				=> 'title',
									'limit'					=> 25,
									'no_found_rows'			=> true,
									'ignore_sticky_posts'	=> true,
								);

			if( ctype_digit( $search ) ) {
				$posts_args['post__in'] = explode( ',', $search );
			} else {
				$posts_args['s'] = $search;
			}

			$search_query = get_posts( $posts_args );

			if( $search_query ) {
				foreach ( $search_query as $search_data ) {

					$post_status	= ( ! empty( $search_data->post_status ) && 'publish' != $search_data->post_status ) ? ' - '.ucfirst( $search_data->post_status ) : '';
					$post_title		= ! empty( $search_data->post_title ) ? $search_data->post_title : __('Post', 'blog-designer-pack');
					$post_title		= $post_title . " - (#{$search_data->ID}{$post_status})";

					$result[]	= array( $search_data->ID, $post_title );
				}
			}

			$result = apply_filters( 'bdpp_shrt_builder_post_sugg', $result );
		}

		wp_send_json( $result );
	}
}

$bdp_shortcode_generator = new BDP_Shortcode_Generator();