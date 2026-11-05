<?php
/**
 * Trending Settings Page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_trending_settings() {

	$reg_post_types = bdp_get_post_types();
?>

<div id="bdpp-trending-sett-wrp" class="post-box-container bdpp-trending-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-trending-sett" class="postbox bdpp-postbox">
			
			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Trending Post Type Settings', 'blog-designer-pack' ); ?> <a class="pro-badge" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'blog-designer-pack' ); ?></a></span>
				</h2>
			</div>

			<div class="inside">
				<div class="bdpp-prowrap-content"></div>
				<table class="form-table bdpp-trending-sett-tbl">
					<tbody>
						<tr>
							<th scope="row"><label><?php esc_html_e( 'Select Post Type', 'blog-designer-pack' ); ?></label></th>
							<td>
								<?php if( ! empty( $reg_post_types ) ) {
									foreach ( $reg_post_types as $post_key => $post_label ) {
										$taxonomy_objects = bdp_get_taxonomies( $post_key, 'list' );
								?>
									<div class="bdpp-post-type-wrap">
										<label>
											<input type="checkbox" value="<?php echo esc_attr( $post_key ); ?>" name="" class="bdpp-checkbox" disabled="disabled" />
											<?php echo esc_html( $post_label ); ?>
												( <?php echo esc_html__('Post Type', 'blog-designer-pack').' : '. esc_html( $post_key );

												if( ! empty( $taxonomy_objects ) ) {
													echo ' | '.esc_html__('Taxonomy', 'blog-designer-pack').' : '. esc_html( $taxonomy_objects );
												} ?>
												)
										</label>
									</div>
								<?php }
								} ?>
								<span class="description"><?php esc_html_e('Select post type box to enable trending post functionality. When someone visits a single page then its visit will be counted. Based on the number of visits you can display the posts. e.g.', 'blog-designer-pack'); ?> [bdp_post post_type="post" type="trending"]</span> <br/>
								<span class="description"><?php esc_html_e('You can see the total visit count and reset it at post meta settings once you enable it.', 'blog-designer-pack'); ?></span> <br/>
							</td>
						</tr>
					</tbody>
				</table><!-- .bdpp-trending-sett-tbl -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #bdpp-trending-sett-wrp -->

<?php }

// Action to add trending settings
add_action( 'bdp_settings_tab_trending', 'bdp_render_trending_settings' );