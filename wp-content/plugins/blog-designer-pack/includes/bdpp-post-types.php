<?php
/**
 * Register Post Type and Taxonomy
 *
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to register post type
 * 
 * @since 4.0
 */
function bdp_register_post_type() {

	$bdp_layout_labels = array(
		'name'						=> __( 'Layouts', 'blog-designer-pack' ),
		'singular_name'				=> __( 'Layout', 'blog-designer-pack' ),
		'add_new'					=> __( 'Add Layout', 'blog-designer-pack' ),
		'add_new_item'				=> __( 'Add New Layout', 'blog-designer-pack' ),
		'edit_item'					=> __( 'Edit Layout', 'blog-designer-pack' ),
		'new_item'					=> __( 'New Layout', 'blog-designer-pack' ),
		'all_items'					=> __( 'All Layout', 'blog-designer-pack' ),
		'view_item'					=> __( 'View Layout', 'blog-designer-pack' ),
		'search_items'				=> __( 'Search Layout', 'blog-designer-pack' ),
		'not_found'					=> __( 'No layout found', 'blog-designer-pack' ),
		'not_found_in_trash'		=> __( 'No layout found in trash', 'blog-designer-pack' ),
		'menu_name'					=> __( 'BDP Layout', 'blog-designer-pack' ),
		'parent_item_colon'			=> '',
		'items_list'				=> __( 'Layout list.', 'blog-designer-pack' ),
		'item_published'			=> __( 'Layout published.', 'blog-designer-pack' ),
		'item_published_privately'	=> __( 'Layout published privately.', 'blog-designer-pack' ),
		'item_reverted_to_draft'	=> __( 'Layout reverted to draft.', 'blog-designer-pack' ),
		'item_scheduled'			=> __( 'Layout scheduled.', 'blog-designer-pack' ),
		'item_updated'				=> __( 'Layout updated.', 'blog-designer-pack' ),
		'item_link'					=> __( 'Layout Link', 'blog-designer-pack' ),
		'item_link_description'		=> __( 'A link to a layout.', 'blog-designer-pack' ),
	);

	$bdpp_layout_args = array(
		'labels'				=> $bdp_layout_labels,
		'show_in_rest'			=> 'false',
		'public'				=> false,
		'hierarchical'			=> false,
		'publicly_queryable'	=> false,
		'exclude_from_search'	=> true,
		'show_ui'				=> false,
		'query_var'				=> true,
		'rewrite'				=> false,
		'supports'				=> array('title', 'editor', 'author')
	);

	// Register layout post type
	register_post_type( BDP_LAYOUT_POST_TYPE, apply_filters( 'bdpp_layout_registered_post_type_args', $bdpp_layout_args ) );
}
add_action( 'init', 'bdp_register_post_type' );