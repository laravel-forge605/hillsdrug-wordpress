<?php
/**
 * Misc Settings Page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_misc_settings() { ?>

<div id="bdpp-misc-sett-wrp" class="post-box-container bdpp-misc-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-misc-sett" class="postbox bdpp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Misc Settings', 'blog-designer-pack' ); ?></span>
				</h2>
			</div>

			<div class="inside">
				<table class="form-table bdpp-misc-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label for="bdpp-post-cnt-fix"><?php esc_html_e( 'Enable Post Content Fix', 'blog-designer-pack' ); ?></label></th>
							<td>
								<input type="checkbox" name="bdpp_opts[post_content_fix]" value="1" class="bdpp-post-cnt-fix" id="bdpp-post-cnt-fix" <?php checked(1, bdp_get_option('post_content_fix')); ?>/><br/>
								<span class="description"><?php esc_html_e('Check this box to apply a fix to get text from post content when shortcodes are there.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="bdpp-dsbl-font-awsm"><?php esc_html_e( 'Disable Font Awesome CSS', 'blog-designer-pack' ); ?></label></th>
							<td>
								<input type="checkbox" name="bdpp_opts[disable_font_awsm_css]" value="1" class="bdpp-dsbl-font-awsm" id="bdpp-dsbl-font-awsm" <?php checked(1, bdp_get_option('disable_font_awsm_css', 0)); ?>/><br/>
								<span class="description"><?php esc_html_e('Check this box if your theme or any other plugins uses the Font Awesome CSS. Plugin will not use it\'s own Font Awesome CSS with respect to site speed.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<?php submit_button( __( 'Save Settings', 'blog-designer-pack' ), 'button-primary right', 'bdpp_sett_submit', false ); ?>
							</td>
						</tr>
					</tbody>
				</table><!-- .bdpp-misc-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #bdpp-misc-sett-wrp -->

<?php
}

// Action to add misc settings
add_action( 'bdp_settings_tab_misc', 'bdp_render_misc_settings' );