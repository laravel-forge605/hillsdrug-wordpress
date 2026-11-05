<?php
/**
 * Post Scrolling Widget Template 1
 * 
 * @package Blog Designer Pack
 * @since 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
?>
<li class="bdpp-post-li <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
	<div class="bdpp-post-list-content bdpp-clearfix">
		<?php if ( $atts['feat_img'] && $atts['show_image'] ) { ?>
		<div class="bdpp-post-list-left bdpp-col-s-5 bdpp-columns">
			<div class="bdpp-post-img-bg">
				<a href="<?php echo esc_url( $atts['post_link'] ); ?>">
					<img src="<?php echo esc_url( $atts['feat_img'] ); ?>" class="bdpp-img bdpp-post-feat-img" alt="<?php the_title_attribute(); ?>" />
				</a>
			</div>
		</div>
		<?php } ?>

		<div class="bdpp-post-list-right <?php if ( $atts['feat_img'] && $atts['show_image'] ) { echo 'bdpp-col-s-7'; } else { echo 'bdpp-col-s-12'; } ?> bdpp-columns">
			<?php if( $atts['show_category'] && $atts['cate_name'] ) { ?>
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
</li>