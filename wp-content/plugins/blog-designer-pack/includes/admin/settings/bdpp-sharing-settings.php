<?php
/**
 * Social Sharing Settings Page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_sharing_settings() {

	$reg_post_types 	= bdp_get_post_types();
	$general_post_types = bdp_get_option( 'post_types', array() );
?>

<div id="bdpp-sharing-sett-wrp" class="post-box-container bdpp-sharing-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-sharing-sett" class="postbox bdpp-postbox">
			
			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Social Sharing Settings', 'blog-designer-pack' ); ?> <a class="pro-badge" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'blog-designer-pack' ); ?></a></span>
				</h2>
			</div>

			<div class="inside">
				<div class="bdpp-prowrap-content"></div>
				<table class="form-table bdpp-sharing-sett-tbl">
					<tbody>
						<tr>
							<th><label for="bdpp-enable-sharing"><?php esc_html_e( 'Enable Social Sharing', 'blog-designer-pack' ); ?></label></th>
							<td>
								<input type="checkbox" name="" value="" class="bdpp-checkbox bdpp-enable-sharing" id="bdpp-enable-sharing"/><br/>
								<span class="description"><?php esc_html_e('Check this box to enable social sharing.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label><?php esc_html_e( 'Social Services', 'blog-designer-pack' ); ?></label></th>
							<td>
								<div class="bdpp-social-service-wrap">
									<div class="bdpp-social-service-row">
										<select name="" class="bdpp-select bdpp-social-service">
											<option value=""><?php esc_html_e('Facebook', 'blog-designer-pack'); ?></option>
										</select>
										<span class="bdpp-social-service-act">
											<button type="button" class="button button-secondary bdpp-social-service-btn bdpp-social-service-add"><?php esc_html_e('Add', 'blog-designer-pack'); ?></button>
											<button type="button" class="button button-secondary bdpp-social-service-btn bdpp-social-service-del"><?php esc_html_e('Remove', 'blog-designer-pack'); ?></button>
										</span>
									</div>
									<div class="bdpp-social-service-row">
										<select name="" class="bdpp-select bdpp-social-service">
											<option value=""><?php esc_html_e('Twitter', 'blog-designer-pack'); ?></option>
										</select>
										<span class="bdpp-social-service-act">
											<button type="button" class="button button-secondary bdpp-social-service-btn bdpp-social-service-add"><?php esc_html_e('Add', 'blog-designer-pack'); ?></button>
											<button type="button" class="button button-secondary bdpp-social-service-btn bdpp-social-service-del"><?php esc_html_e('Remove', 'blog-designer-pack'); ?></button>
										</span>
									</div>
									<div class="bdpp-social-service-row">
										<select name="" class="bdpp-select bdpp-social-service">
											<option value=""><?php esc_html_e('WhatsApp', 'blog-designer-pack'); ?></option>
										</select>
										<span class="bdpp-social-service-act">
											<button type="button" class="button button-secondary bdpp-social-service-btn bdpp-social-service-add"><?php esc_html_e('Add', 'blog-designer-pack'); ?></button>
											<button type="button" class="button button-secondary bdpp-social-service-btn bdpp-social-service-del"><?php esc_html_e('Remove', 'blog-designer-pack'); ?></button>
										</span>
									</div>
								</div>
								<span class="description"><?php esc_html_e('Choose social sharing service. Social sharing will be displayed in same order in which you save.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<div class="bdpp-sett-sub-title"><?php esc_html_e( 'Social Sharing on Single Post Pages', 'blog-designer-pack' ); ?></div>
							</th>
						</tr>
						<tr>
							<th><label for="bdpp-sharing-lbl"><?php esc_html_e( 'Sharing Label', 'blog-designer-pack' ); ?></label></th>
							<td>
								<input type="text" name="" value="" class="regular-text bdpp-sharing-label" id="bdpp-sharing-lbl" /><br/>
								<span class="description"><?php esc_html_e('Enter sharing label.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="bdpp-sharing-design"><?php esc_html_e( 'Sharing Theme', 'blog-designer-pack' ); ?></label></th>
							<td>
								<select name="" class="bdpp-select bdpp-sharing-design" id="bdpp-sharing-design">
									<option value=""><?php esc_html_e('Theme 1', 'blog-designer-pack'); ?></option>
								</select><br/>
								<span class="description"><?php esc_html_e('Choose sharing theme.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label><?php esc_html_e( 'Select Post Type', 'blog-designer-pack' ); ?></label></th>
							<td>
								<?php if( ! empty($general_post_types) ) {
									foreach ($general_post_types as $post_key => $post_val) {

										// If saved post type is not in general post type
										if( ! array_key_exists( $post_val, $reg_post_types ) ) {
											continue;
										}

										$post_label = isset( $reg_post_types[ $post_val ] ) ? $reg_post_types[ $post_val ] : '--';
								?>
									<div class="bdpp-post-type-wrap">
										<label>
											<input type="checkbox" value="" class="bdpp-checkbox" />
											<?php echo esc_html( $post_label ); ?>
											( <?php echo esc_html__('Post Type', 'blog-designer-pack').' : '. esc_html( $post_val ); ?> )
										</label>
									</div>
								<?php }
								} ?>
								<span class="description"><?php esc_html_e('Select post type box to enable social sharing on single post pages. Did not find the post type? Make sure you have enabled it from general setting.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
					</tbody>
				</table><!-- .bdpp-sharing-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #bdpp-sharing-sett-wrp -->

<?php }

// Action to add social sharing settings
add_action( 'bdp_settings_tab_sharing', 'bdp_render_sharing_settings' );