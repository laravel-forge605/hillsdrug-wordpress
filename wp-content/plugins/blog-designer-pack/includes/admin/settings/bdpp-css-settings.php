<?php
/**
 * CSS - JS Settings Page
 * 
 * The code for the plugins css settings page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_css_settings() { ?>

<div id="bdpp-css-sett-wrp" class="post-box-container bdpp-css-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-css-sett" class="postbox bdpp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Custom CSS Settings', 'blog-designer-pack' ); ?></span>
				</h2>
			</div>

			<div class="inside">
				<table class="form-table bdpp-css-sett-tbl">
					<tbody>
						<tr>
							<th scope="row">
								<label for="bdpp-cust-css"><?php esc_html_e( 'Custom CSS', 'blog-designer-pack' ); ?></label>
							</th>
							<td>
								<textarea name="bdpp_opts[custom_css]" id="bdpp-cust-css" rows="18" class="large-text bdpp-cust-css bdpp-code-editor"><?php echo esc_textarea( bdp_get_option('custom_css') ); ?></textarea>
								<span class="description"><?php esc_html_e( 'Here you can enter your custom CSS for the plugin. The CSS will automatically be inserted to the header of theme, when you save it.', 'blog-designer-pack' ); ?></span><br/>
								<span class="description"><?php esc_html_e( 'Note: Do not add `style` tag.', 'blog-designer-pack' ); ?></span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php submit_button( __( 'Save Settings', 'blog-designer-pack' ), 'button-primary right', 'bdpp_sett_submit', false ); ?>
							</td>
						</tr>
					</tbody>
				</table><!-- .bdpp-css-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- end .metabox-holder -->
</div><!-- #bdpp-css-sett-wrp -->

<?php }

add_action( 'bdp_settings_tab_css', 'bdp_render_css_settings' );