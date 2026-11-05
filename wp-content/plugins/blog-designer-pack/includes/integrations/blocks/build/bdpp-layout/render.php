<?php
/**
 * Block - BDP Layout Render
 *
 * @package Blog Designer Pack
 * @since 4.0.10
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$layout_id 		= isset( $attributes['layout_id'] ) ? intval( $attributes['layout_id'] )	: 0;
$layout_class	= isset( $attributes['align'] )		? "align{$attributes['align']}"			: '';

if ( $layout_id > 0 ) {
	echo '<div class="bdpp-block-wrap '.esc_attr( $layout_class ).'">' . do_shortcode( '[bdpp_tmpl layout_id="'.esc_attr( $layout_id ).'"]' ) . '</div>';
}