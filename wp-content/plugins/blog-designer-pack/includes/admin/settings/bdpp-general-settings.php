<?php
/**
 * General Settings Page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_general_settings() {

	$reg_post_types 		= bdp_get_post_types();
	$saved_post_types 		= bdp_get_option( 'post_types', array() );
	$post_default_feat_img	= bdp_get_option( 'post_default_feat_img' );
?>

<div id="bdpp-general-sett-wrp" class="post-box-container bdpp-general-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-general-sett" class="postbox bdpp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'General Settings', 'blog-designer-pack' ); ?></span>
				</h2>
			</div>

			<div class="inside">
				<table class="form-table bdpp-general-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Select Post Type', 'blog-designer-pack' ); ?></label></th>
							<td>
								<div class="bdpp-post-type-wrap">
									<label>
										<input type="checkbox" value="<?php echo esc_attr( BDP_POST_TYPE ); ?>" name="bdpp_opts[post_types][]" class="bdpp-checkbox" <?php checked( in_array(BDP_POST_TYPE, $saved_post_types), true ); ?> disabled="disabled" />
										<?php echo isset( $reg_post_types[ BDP_POST_TYPE ] ) ? esc_html( $reg_post_types[ BDP_POST_TYPE ] ) : BDP_POST_TYPE; ?>
											( <?php echo esc_html__('Post Type', 'blog-designer-pack').' : '.esc_html( BDP_POST_TYPE );

											$taxonomy_objects = bdp_get_taxonomies( BDP_POST_TYPE, 'list' );

											if( ! empty( $taxonomy_objects ) ) {
												echo ' | '.esc_html__('Taxonomy', 'blog-designer-pack').' : '.esc_html( $taxonomy_objects );
											} ?>
											)
									</label>
								</div>

								<?php if( ! empty( $reg_post_types ) ) { ?>
									<div class="bdpp-other-post-type-wrap">
										<div class="bdpp-pro-features"><i class="dashicons dashicons-lock"></i> <?php esc_html_e('Premium Features', 'blog-designer-pack'); ?>  </div>
										<span class="description"><?php esc_html_e('Bellow are custom post types(CPTs) and custom Taxonomies.', 'blog-designer-pack'); ?> <a href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><?php esc_html_e('Unlock Custom Post Types & Taxonomies!', 'blog-designer-pack'); ?></a></span>
										<?php foreach ($reg_post_types as $post_key => $post_label) {

											if( BDP_POST_TYPE == $post_key ) {
												continue;
											}

											$taxonomy_objects = bdp_get_taxonomies( $post_key, 'list' );
										?>
											<div class="bdpp-post-type-wrap">
												<label>
													<input type="checkbox" value="<?php echo esc_attr( $post_key ); ?>" name="bdpp_opts[post_types][]" class="bdpp-checkbox" <?php checked( in_array($post_key, $saved_post_types), true ); ?> disabled="disabled" />
													<?php echo esc_html( $post_label ); ?>
														( <?php echo esc_html__('Post Type', 'blog-designer-pack').' : '.esc_html( $post_key );

														if( ! empty( $taxonomy_objects ) ) {
															echo ' | '.esc_html__('Taxonomy', 'blog-designer-pack').' : '.esc_html( $taxonomy_objects );
														} ?>
														)
												</label>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
								<span class="description"><?php esc_html_e('Note: `post` will be remain enabled by default.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>

						<tr>
							<th colspan="2">
								<div class="bdpp-sett-sub-title"><?php esc_html_e( 'General Settings', 'blog-designer-pack' ); ?></div>
							</th>
						</tr>
						<tr>
							<th><label for="bdpp-enable-post-first-img"><?php esc_html_e( 'First Image From Post Content', 'blog-designer-pack' ); ?></label></th>
							<td>
								<input type="checkbox" name="bdpp_opts[post_first_img]" value="1" class="bdpp-checkbox bdpp-enable-post-first-img" id="bdpp-enable-post-first-img" <?php checked(1, bdp_get_option('post_first_img')); ?>/><br/>
								<span class="description"><?php esc_html_e('Check this box to take the first image from post content when the post featured image is not available.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="bdpp-default-post-feat-img"><?php esc_html_e('Post Default Featured Image', 'blog-designer-pack'); ?></label>
							</th>
							<td>
								<input type="text" name="bdpp_opts[post_default_feat_img]" value="<?php echo esc_url( $post_default_feat_img ); ?>" class="regular-text bdpp-default-post-feat-img bdpp-img-upload-input" />
								<input type="button" id="bdpp-default-post-feat-img" class="button button-secondary bdpp-img-upload bdpp-default-post-feat-img" value="<?php esc_html_e( 'Choose', 'blog-designer-pack'); ?>" />
								<input type="button" class="button button-secondary bdpp-default-post-feat-img-clear bdpp-image-clear" value="<?php esc_html_e( 'Clear', 'blog-designer-pack'); ?>" />
								<p class="description"><?php esc_html_e( 'Upload / Choose default post featured image.', 'blog-designer-pack' ); ?></p>
								
								<div class="bdpp-img-preview bdpp-img-view">
									<?php if( ! empty( $post_default_feat_img ) ) { ?>
									<img src="<?php echo esc_url( $post_default_feat_img ); ?>" alt="" />
									<?php } ?>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php submit_button( __( 'Save Settings', 'blog-designer-pack' ), 'button-primary right', 'bdpp_sett_submit', false ); ?>
							</td>
						</tr>
					</tbody>
				</table><!-- .bdpp-general-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #bdpp-general-sett-wrp -->

<?php }

// Action to add general settings
add_action( 'bdp_settings_tab_general', 'bdp_render_general_settings' );