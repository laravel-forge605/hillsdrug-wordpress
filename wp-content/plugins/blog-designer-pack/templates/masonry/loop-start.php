<?php
/**
 * Loop Start - Masonry Template
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="bdpp-post-masonry-<?php echo esc_attr( $atts['unique'] ); ?>" class="bdpp-wrap bdpp-lite bdpp-post-masonry-wrap bdpp-post-grid-wrap bdpp-post-data-wrap <?php echo esc_attr( "bdpp-{$atts['design']} bdpp-{$atts['effect']} bdpp-grid-{$atts['grid']} {$atts['css_class']}" ); ?> bdpp-clearfix">
	<div class="bdpp-post-masonry-inr-wrap bdpp-post-data-inr-wrap bdpp-clearfix">