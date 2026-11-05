<?php
/**
 * GridBox Template 1
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

// Post Meta Data
$meta_data = array(
				'author'	=> $atts['show_author'],
				'post_date'	=> $atts['show_date'],
				'comments' 	=> $atts['show_comments'],
			);

if( $atts['count'] == 1 ) { ?>
	<div class="bdpp-post-gridbox bdpp-post-gridbox-left bdpp-col-2 bdpp-columns <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
		<div class="bdpp-post-grid-content">
			<?php if ( $atts['feat_img'] ) { ?>	
			<div class="bdpp-post-img-link">	
				<a class="bdpp-post-linkoverlay" href="<?php echo esc_url( $atts['post_link'] ); ?>"></a>
				<div class="bdpp-post-img-bg" style="height:500px; background-image:url(<?php echo esc_url( $atts['feat_img'] ); ?>);"></div>
				<?php if( $atts['format'] == 'video' ) { echo bdp_post_format_html( $atts['format'] ); } ?>
			</div>
			<?php }

			if( $atts['show_category'] && $atts['cate_name'] ) { ?>
			<div class="bdpp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
			<?php } ?>

			<div class="bdpp-post-content-overlay">
				<h2 class="bdpp-post-title">
					<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
				</h2>

				<?php if( $atts['show_date'] || $atts['show_author'] || $atts['show_comments'] ) { ?>
				<div class="bdpp-post-meta bdpp-post-meta-up">
					<?php echo bdp_post_meta_data( $meta_data ); ?>
				</div>
				<?php }

				if( $atts['show_content'] ) { ?>
				<div class="bdpp-post-content">
					<div class="bdpp-post-desc"><?php echo bdp_get_post_excerpt( $post->ID, get_the_content(), $atts['content_words_limit'] ); ?></div>
					<?php if( $atts['show_read_more'] ) { ?>
						<a href="<?php echo esc_url( $atts['post_link'] ); ?>" class="bdpp-rdmr-btn"><?php echo wp_kses_post( $atts['read_more_text'] ); ?></a>
					<?php } ?>
				</div>
				<?php }

				if( $atts['show_tags'] && $atts['tags'] ) { ?>
				<div class="bdpp-post-meta bdpp-post-meta-down"><?php echo wp_kses_post( $atts['tags'] ); ?></div>
				<?php }	?>
			</div>
		</div>
	</div>
<?php } else {

$image_thumb = bdp_get_post_feat_image( $post->ID, 'thumbnail' ); ?>

<div class="bdpp-post-gridbox bdpp-post-gridbox-right bdpp-col-2 bdpp-columns <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
	<div class="bdpp-post-grid-content bdpp-clearfix">
		<?php if ( $image_thumb ) { ?>
		<div class="bdpp-col-s-4 bdpp-columns">
			<div class="bdpp-post-img-bg">
				<a href="<?php echo esc_url( $atts['post_link'] ); ?>">
					<img src="<?php echo esc_url( $image_thumb ); ?>" class="bdpp-img bdpp-post-feat-img" alt="<?php the_title_attribute(); ?>" />
					<?php if( $atts['format'] == 'video' ) { echo bdp_post_format_html( $atts['format'] ); } ?>
				</a>
			</div>
		</div>
		<?php } ?>

		<div class="<?php if ( ! $image_thumb ) { echo 'bdpp-col-s-12'; } else { echo 'bdpp-col-s-8'; } ?> bdpp-columns">
			<?php if( $atts['show_category'] && $atts['cate_name'] ) { ?>
			<div class="bdpp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
			<?php } ?>

			<div class="bdpp-post-content-overlay">
				<h4 class="bdpp-post-title">
					<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
				</h4>

				<?php if( $atts['show_date'] || $atts['show_author'] || $atts['show_comments'] ) { ?>
				<div class="bdpp-post-meta bdpp-post-meta-up">
					<?php echo bdp_post_meta_data( $meta_data ); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>