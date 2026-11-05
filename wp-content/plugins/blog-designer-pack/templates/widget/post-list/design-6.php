<?php
/**
 * Post List Widget Template 6
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
?>
<div class="bdpp-post-widget-main <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
	<div class="bdpp-widget-content bdpp-clearfix">
		<div class="bdpp-col-s-12 bdpp-columns">
		<?php if ( $atts['feat_img'] ) { ?>
			<div class="bdpp-post-img-bg">
				<a href="<?php echo esc_url( $atts['post_link'] ); ?>">
					<img src="<?php echo esc_url( $atts['feat_img'] ); ?>" alt="<?php the_title_attribute(); ?>" />
				</a>
			</div>
			<?php }

			if( $atts['show_category'] && $atts['cate_name'] ) { ?>
				<div class="bdpp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
			<?php } ?>

			<h4 class="bdpp-post-title">
				<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
			</h4>

			<?php if( $atts['show_date'] || $atts['show_author'] ) { ?>
				<div class="bdpp-post-meta">
					<?php echo bdp_post_meta_data( array( 'author' => $atts['show_author'], 'post_date' => $atts['show_date']) ); // WPCS: XSS ok. ?>
				</div>
			<?php }

			if( $atts['show_content'] ) { ?>
			<div class="bdpp-post-desc"><?php echo bdp_get_post_excerpt( $post->ID, get_the_content(), $atts['content_words_limit'] ); ?></div>
			<?php } ?>
		</div>
	</div>
</div>