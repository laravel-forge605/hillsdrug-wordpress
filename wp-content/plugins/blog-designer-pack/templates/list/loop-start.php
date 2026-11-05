<?php
/**
 * Loop Start - List Template
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="bdpp-post-list-<?php echo esc_attr( $atts['unique'] ); ?>" class="bdpp-wrap bdpp-lite bdpp-post-list-wrap bdpp-post-data-wrap <?php echo esc_attr( "bdpp-{$atts['design']} {$atts['css_class']}" ); ?> bdpp-clearfix">
	<div class="bdpp-post-list-inr-wrap bdpp-post-data-inr-wrap bdpp-clearfix">