<?php
/**
 * Widget Class
 * Widget related functions and widget registration.
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include widget classes.
require_once( BDP_DIR . '/includes/widgets/class-bdpp-post-widget.php' );
require_once( BDP_DIR . '/includes/widgets/class-bdpp-post-scrolling-widget.php' );

/**
 * Register Widgets.
 *
 * @since 1.0
 */
function bdp_register_widgets() {
	register_widget( 'BDP_Post_Widget' );
	register_widget( 'BDP_Post_Scrolling_Widget' );
}
add_action( 'widgets_init', 'bdp_register_widgets' );