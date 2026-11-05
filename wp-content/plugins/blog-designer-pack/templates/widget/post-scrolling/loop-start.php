<?php
/**
 * Loop Start - Vertical Post Scrolling Widget Template
 * 
 * @package Blog Designer Pack
 * @since 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="bdpp-wrap bdpp-lite bdpp-post-scroling-wdgt bdpp-post-scroling-wdgt-js inf-post-scroling-wdgt bdpp-post-widget-wrap bdpp-<?php echo esc_attr( $atts['design'] .' '. $atts['css_class'] ); ?>" id="bdpp-post-scroling-wdgt-<?php echo esc_attr( $atts['unique'] ); ?>" data-conf="<?php echo htmlspecialchars(json_encode( $atts['slider_conf'] )); ?>">
	<div class="bdpp-vticker-scroling-wdgt bdpp-vticker-scroling-wdgt-js bdpp-clearfix">
		<ul class="bdpp-vscroll-wdgt-wrap">