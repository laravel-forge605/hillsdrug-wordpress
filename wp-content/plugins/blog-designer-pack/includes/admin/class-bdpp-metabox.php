<?php
/**
 * Metabox Class
 *
 * Handles the admin side functionality of plugin
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDP_MetaBox {

	function __construct() {

		// Action to register admin menu
		add_action( 'add_meta_boxes', array( $this, 'bdp_add_meta_box' ) );
	}

	/**
	 * Register all the meta boxes for the post type
	 * 
	 * @version	4.0
	 */
	function bdp_add_meta_box() {

		// Allowed Post Types
		$allowed_post_types = bdp_allowed_post_types();

		// Post settings metabox
		add_meta_box( 'bdpp_post_sett', __( 'Blog Designer Pack - Settings', 'blog-designer-pack' ),  array( $this, 'bdp_render_post_sett_meta_box' ), $allowed_post_types, 'normal', 'high' );
	}

	/**
	 * Post Setting MetaBox
	 * 
	 * @version	4.0
	 */
	function bdp_render_post_sett_meta_box() {
		include_once( BDP_DIR . '/includes/admin/metabox/post-settings.php' );
	}
}

$bdp_metabox = new BDP_MetaBox();