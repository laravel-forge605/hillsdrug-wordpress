<?php
/**
 * Loop Start - Grid Template
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="bdpp-wrap bdpp-lite bdpp-post-grid-wrap bdpp-post-data-wrap bdpp-<?php echo esc_attr( $atts['design'] ); ?> bdpp-grid-<?php echo esc_attr( $atts['grid'] .' '. $atts['css_class'] ); ?> bdpp-clearfix" id="bdpp-post-grid-<?php echo esc_attr( $atts['unique'] ); ?>">
	<div class="bdpp-post-grid-inr-wrap bdpp-post-data-inr-wrap bdpp-clearfix">