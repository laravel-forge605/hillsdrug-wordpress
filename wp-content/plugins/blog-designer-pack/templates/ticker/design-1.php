<?php
/**
 * Ticker Template 1
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<li class="bdpp-ticker-ele <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
	<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
</li>