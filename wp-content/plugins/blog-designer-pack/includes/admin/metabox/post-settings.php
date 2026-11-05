<?php
/**
 * Post Settings MetaBox HTML
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="meta-pro-badge">
	<h3><?php esc_html_e( 'Unlock all settings by upgrading to the Pro version.', 'blog-designer-pack' ); ?></h3>
	<a class="pro-badge" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'blog-designer-pack' ); ?></a>
</div>
<div class="bdpp-prowrap-content"></div>
<div class="bdpp-post-sett-mb-wrp">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="bdpp-disable-sharing"><?php esc_html_e( 'Disable Social Sharing', 'blog-designer-pack' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="" value="" class="bdpp-checkbox" id="bdpp-disable-sharing" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Check this box to disable social sharing for this post.', 'blog-designer-pack'); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="bdpp-feat-post"><?php esc_html_e( 'Featured Post', 'blog-designer-pack' ); ?></label>
				</th>
				<td>
					<input type="checkbox" name="" value="" class="bdpp-checkbox" id="bdpp-feat-post" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Check this box to mark this post as a featured post.', 'blog-designer-pack'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="bdpp-sub-title"><?php esc_html_e( 'Post Sub Title', 'blog-designer-pack' ); ?></label>
				</th>
				<td>
					<input type="text" name="" value="" class="large-text" id="bdpp-sub-title" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Enter post sub title.', 'blog-designer-pack'); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="bdpp-read-more-link"><?php esc_html_e( 'Read More Link', 'blog-designer-pack' ); ?></label>
				</th>
				<td>
					<input type="text" name="" value="" class="large-text" id="bdpp-read-more-link" disabled="disabled" /><br/>
					<span class="description"><?php esc_html_e('Enter custom read more link. Leave empty for default post permalink.', 'blog-designer-pack'); ?></span>
				</td>
			</tr>

			<tr>
				<th colspan="2">
					<div class="bdpp-sett-sub-title"><?php esc_html_e( 'Trending Post Settings', 'blog-designer-pack' ); ?></div>
				</th>
			</tr>

			<tr>
				<th scope="row"><label><?php esc_html_e( 'Post View Count', 'blog-designer-pack' ); ?></label></th>
				<td>
					<?php $post_view_count = "1008"; ?>
					
					<span class="bdpp-post-count-view"><?php echo esc_html( $post_view_count ); ?></span>
					
					<?php if( $post_view_count ) { ?>
					<input type="button" name="" value="<?php esc_html_e('Reset Post Count', 'blog-designer-pack'); ?>" class="button button-secondary" disabled="disabled" />
					<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
</div><!-- end .bdpp-post-sett-mb-wrp -->