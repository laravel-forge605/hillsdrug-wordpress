<?php
/**
 * Blog Designer Pack Elementor Widget
 *
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Register Custom Controls.
 * 
 * @since 4.0
 */
function bdpp_register_elementor_custom_controls() {

	$controls_manager = \Elementor\Plugin::$instance->controls_manager;
	$controls_manager->register( new BDPP_Elementor_Select2_Ajax_Control() );
}
add_action( 'elementor/controls/controls_registered', 'bdpp_register_elementor_custom_controls' );

/**
 * Register elementor widget.
 * 
 * @since 4.0
 */
function bdpp_register_elementor_widgets( $widgets_manager ) {

	require_once( BDP_DIR . '/includes/integrations/elementor/bdpp-layout-elementor-wdgt.php' );
	$widgets_manager->register( new \BDPP_Layout_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'bdpp_register_elementor_widgets' );