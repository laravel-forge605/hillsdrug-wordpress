<?php
/**
 * Taxonomy Settings Page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_taxonomy_settings() {

	$reg_post_types = bdp_get_post_types();
?>

<div id="bdpp-taxonomy-sett-wrp" class="post-box-container bdpp-taxonomy-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-taxonomy-sett" class="postbox bdpp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Taxonomy Settings', 'blog-designer-pack' ); ?> <a class="pro-badge" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'blog-designer-pack' ); ?></a></span>
				</h2>
			</div>

			<div class="inside">
				<div class="bdpp-prowrap-content"></div>
				<table class="form-table bdpp-taxonomy-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Select Taxonomy', 'blog-designer-pack' ); ?></label></th>
							<td>
								<?php if( ! empty( $reg_post_types ) ) {
									foreach ($reg_post_types as $post_key => $post_label) {

										$taxonomy_objects = bdp_get_taxonomies( $post_key );

										// Skip if no taxonomy
										if( empty( $taxonomy_objects ) ) {
											continue;
										}
								?>
									<div class="bdpp-term-type-wrap">
										<div class="bdpp-term-type-title"><?php echo esc_html( $post_label ); ?></div>
										<?php foreach ($taxonomy_objects as $term_key => $term_label) { ?>
										<div class="bdpp-term-type-inr-wrap">
											<label>
												<input type="checkbox" value="<?php echo esc_attr( $post_key ); ?>" name="" class="bdpp-checkbox" disabled="disabled" />
												<?php echo esc_html( $term_label ); ?>
												( <?php echo esc_html( $term_key ); ?> )
											</label>
										</div>
									<?php } ?>
									</div>
								<?php }
								} ?>
								<span class="description"><?php esc_html_e('Select checkbox to enable taxonomy support for category grid, category slider and category ticker functionality.', 'blog-designer-pack'); ?></span> <br/>
								<span class="description"><?php esc_html_e('It will enable some settings like Category Image, Category Link etc at category add / edit page.', 'blog-designer-pack'); ?></span>
							</td>
						</tr>
					</tbody>
				</table><!-- .bdpp-taxonomy-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #bdpp-taxonomy-sett-wrp -->

<?php }

// Action to add taxonomy settings
add_action( 'bdp_settings_tab_taxonomy', 'bdp_render_taxonomy_settings' );