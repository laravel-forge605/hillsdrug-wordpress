<?php
/**
 * Loop Start - Slider Template
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="bdpp-wrap bdpp-lite bdpp-slider-wrap-main">
	<div id="bdpp-post-slider-wrap-<?php echo esc_attr( $atts['unique'] ); ?>" class="bdpp-post-slider-wrap owl-carousel <?php echo esc_attr("bdpp-{$atts['design']} {$atts['css_class']}"); ?> bdpp-clearfix" data-conf="<?php echo htmlspecialchars(json_encode( $atts['slider_conf'] )); ?>">