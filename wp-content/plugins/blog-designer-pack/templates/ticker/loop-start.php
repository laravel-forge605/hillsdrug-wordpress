<?php
/**
 * Loop Start - Ticker Template
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<style type="text/css">
#bdpp-ticker-<?php echo esc_attr( $atts['unique'] ); ?>{border-color:<?php echo esc_attr( $atts['theme_color'] ); ?>;}
#bdpp-ticker-<?php echo esc_attr( $atts['unique'] ); ?> .bdpp-label{background:<?php echo esc_attr( $atts['theme_color'] ); ?>; color: <?php echo esc_attr( $atts['heading_font_color'] ); ?>}
#bdpp-ticker-<?php echo esc_attr( $atts['unique'] ); ?> ul li a:hover, #bdpp-ticker-<?php echo esc_attr( $atts['unique'] ); ?> ul li a{color:<?php echo esc_attr( $atts['font_color'] ); ?>; font-style:<?php echo esc_attr( $atts['font_style'] ); ?>;}
</style>

<div class="bdpp-wrap bdpp-lite bdpp-post-ticker-wrp bdpp-ticker-wrp inf-news-ticker <?php echo esc_attr( $atts['css_class'] ); ?>" id="bdpp-ticker-<?php echo esc_attr( $atts['unique'] ); ?>" data-conf="<?php echo htmlspecialchars(json_encode($atts['ticker_conf'])); ?>">
	<?php if( $atts['ticker_title'] ) { ?>
	 <div class="inf-label bdpp-label"><?php echo wp_kses_post( $atts['ticker_title'] ); ?></div>
	<?php } ?>
	<div class="inf-ticker bdpp-ticker">
		<ul class="bdpp-ticker-cnt">