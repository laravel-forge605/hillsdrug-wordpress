<?php
/**
 * Register Plugin Blocks
 *
 * @package Blog Designer Pack
 * @since 4.0.10
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued through the block editor in the corresponding context.
 */
function bdp_block_init() {

	// Registers block.json
	$layout_block = register_block_type( __DIR__ . '/build/bdpp-layout/' );

	// Localize the auto-registered editor script handle
	if ( $layout_block && ! empty( $layout_block->editor_script_handles ) ) {
		wp_localize_script(
			$layout_block->editor_script_handles[0], // main editor script handle
			'BdppLayoutBlock',
			[
				'layout_page_url' => add_query_arg( array('page' => 'bdpp-layouts'), admin_url('admin.php') )
			]
		);
	}
}
add_action( 'init', 'bdp_block_init' );

/**
 * Register REST API endpoint for layout search
 */
function bdp_layout_register_rest_route() {
	register_rest_route(
		'bdpp-layout-selector/v1',
		'/search-layouts',
		array(
			'methods'				=> 'GET',
			'callback'				=> 'bdp_layout_search_posts',
			'permission_callback'	=> function() {
				return current_user_can( 'edit_posts' );
			},
			'args' => array(
				'search' => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'bdp_layout_register_rest_route' );

/**
 * Search posts by ID or title
 *
 * @param WP_REST_Request $request The REST request object.
 * @return array The search results.
 */
function bdp_layout_search_posts( $request ) {

	global $post;

	$search_term = $request->get_param( 'search' );

	$args = array(
		'post_type'			=> BDP_LAYOUT_POST_TYPE,
		'post_status'		=> array('publish', 'pending'),
		'posts_per_page'	=> 20,
		'orderby'			=> 'date',
		'order'				=> 'ASC',
	);

	// Check if search term is numeric (Post ID search)
	if ( ! empty( $search_term ) && is_numeric( $search_term ) ) {
		$args['p'] = intval( $search_term );
	} elseif ( ! empty( $search_term ) ) {
		$args['s'] = $search_term;
	}

	$results	= array();
	$query		= new WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$layout_status	= get_post_status( $post );
			$layout_title 	= get_the_title();
			$layout_title	= ( 'pending' == $layout_status ) ? $layout_title ." &mdash; ". ucfirst( $layout_status ) : $layout_title;

			$results[] = array(
				'id'    => get_the_ID(),
				'title' => html_entity_decode( $layout_title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
			);
		}
		wp_reset_postdata();
	}

	return $results;
}