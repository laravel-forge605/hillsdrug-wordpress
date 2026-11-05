<?php
/**
 * Loop Start - Carousel Template
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="bdpp-post-carousel-wrap-<?php echo esc_attr( $atts['unique'] ); ?>" class="bdpp-wrap bdpp-lite bdpp-post-carousel-wrap owl-carousel bdpp-<?php echo esc_attr( $atts['design'] .' '. $atts['css_class'] ); ?> bdpp-clearfix" data-conf="<?php echo htmlspecialchars(json_encode( $atts['slider_conf'] )); ?>">