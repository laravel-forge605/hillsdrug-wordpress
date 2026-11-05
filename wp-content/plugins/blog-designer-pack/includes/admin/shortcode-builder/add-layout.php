<?php
/**
 * Featured and Trending Post Pro Shortcode Mapper Page 
 *
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_version;

$wrap_cls				= '';
$valid					= true;
$meta_prefix			= BDP_META_PREFIX;
$registered_shortcodes 	= bdp_registered_shortcodes();
$shortcodes_arr 		= bdp_registered_shortcodes( false );
$allowed_reg_shortcodes	= bdp_allowed_reg_shortcodes();
$preview_shortcode 		= ! empty( $_GET['shortcode'] ) ? $_GET['shortcode'] : apply_filters('bdpp_default_preview_shortcode', 'bdp_post' );
$action					= ( ! empty( $_GET['action'] ) && 'edit' == $_GET['action'] ) ? 'edit' : 'add';
$layout_id				= ( ! empty( $_GET['id'] ) && 'edit' == $action ) ? bdp_clean_number( $_GET['id'] ) : false;
$preview_url 			= add_query_arg( array('page' => 'bdpp-shortcode-preview', 'shortcode' => $preview_shortcode), admin_url('admin.php') );
$page_url				= add_query_arg( array('page' => 'bdpp-layout'), admin_url('admin.php') );

// Version 7 compatibility
if ( version_compare( $wp_version, '7.0', '>=' ) ) {
	$wrap_cls = 'bdpp-layout-wrap-v7';
}

// Instantiate the shortcode builder
if( ! class_exists( 'BDPP_Shortcode_Builder' ) ) {
	include_once( BDP_DIR . '/includes/admin/shortcode-builder/class-bdpp-shortcode-builder.php' );
}

// Getting layout temp data when we change the shortcode
if( isset( $_COOKIE['bdpp_layout_temp_data'] ) ) {
	$layout_temp_data = json_decode( wp_unslash( $_COOKIE['bdpp_layout_temp_data'] ), true );
}

$shortcode_val		= "[{$preview_shortcode}]";
$layout_title		= '';
$layout_desc		= '';
$layout_enable		= 1;
$shortcode_fields 	= array();
$shortcode_sanitize = str_replace('-', '_', $preview_shortcode);
$page_title			= __( 'Add New Layout', 'blog-designer-pack' );
$save_btn_text		= __('Publish', 'blog-designer-pack');

if( 'edit' == $action ) {
	$page_title		= __( 'Edit Layout', 'blog-designer-pack' );
	$save_btn_text	= __('Update', 'blog-designer-pack');
	$trash_url		= add_query_arg( array('page' => 'bdpp-layouts', 'action' => 'delete', 'bdpp_layout' => $layout_id, '_wpnonce' => wp_create_nonce('bulk-bdpp_layouts') ), admin_url('admin.php') );
	$duplicate_url	= add_query_arg( array('page' => 'bdpp-layouts', 'shortcode' => $preview_shortcode, 'action' => 'duplicate_layout', 'id' => $layout_id, '_wpnonce' => wp_create_nonce("bdpp-duplicate-layout-{$layout_id}") ), admin_url('admin.php') );
}
?>
<div class="wrap bdpp-layout-wrap <?php echo esc_attr( $wrap_cls ); ?>">

	<h1 class="wp-heading-inline"><?php echo esc_html( $page_title ); ?></h1>
	<?php if( 'edit' == $action ) { ?>
	<a href="<?php echo esc_url( $page_url ); ?>" class="page-title-action"><?php esc_html_e('Add Layout', 'blog-designer-pack'); ?></a>
	<?php } ?>
	<hr class="wp-header-end">

	<?php
	// If invalid shortcode or data is passed then simply return
	if( ( ! empty( $_GET['shortcode'] ) && ! isset( $registered_shortcodes[ $_GET['shortcode'] ] ) )
		|| ( 'edit' == $action && ( empty( $layout_id ) || empty( $_GET['shortcode'] ) ) )
		|| ( 'add' == $action && isset( $_GET['id'] ) )
	) {

		$valid = false;

		echo '<div id="message" class="error notice">
				<p><strong>'.__('Sorry, Something happened wrong.', 'blog-designer-pack').'</strong></p>
			 </div>';
	}

	// Check valid shortcode template is there
	if( ! empty( $layout_id ) ) {

		// Get layout post
		$layout_data = get_post( $layout_id );

		if( $layout_data && isset( $layout_data->post_type ) && $layout_data->post_type == BDP_LAYOUT_POST_TYPE ) {

			$layout_title		= $layout_data->post_title;
			$layout_desc		= $layout_data->post_content;
			$layout_enable		= ( 'publish' == $layout_data->post_status ) ? 1 : 0;
			$layout_shrt_type	= get_post_meta( $layout_id, $meta_prefix.'layout_shrt_type', true );

			if( $preview_shortcode == $layout_shrt_type ) {
				$shortcode_val = get_post_meta( $layout_id, $meta_prefix.'layout_shrt', true );
			}

		} else {

			$valid = false;

			echo '<div id="message" class="error notice">
					<p><strong>'.__('Sorry, No shortcode layout found.', 'blog-designer-pack').'</strong></p>
				</div>';
		}
	}

	// Set layout temp data if it is there
	$layout_title	= isset( $layout_temp_data['title'] )		? $layout_temp_data['title']		: $layout_title;
	$layout_desc	= isset( $layout_temp_data['description'] ) ? $layout_temp_data['description']	: $layout_desc;
	$layout_enable	= ! empty( $layout_temp_data['enable'] )	? 1									: $layout_enable;

	// Messages
	if( isset( $_GET['message'] ) && 1 == $_GET['message'] ) {
		
		echo '<div class="notice-success notice bdpp-notice is-dismissible">
				<p><strong>'.__('Layout saved successfully.', 'blog-designer-pack').'</strong></p>
			 </div>';

	} else if( isset( $_GET['message'] ) && 0 == $_GET['message'] ) {
		
		echo '<div class="notice-error notice bdpp-notice is-dismissible">
				<p><strong>'.__('Sorry, Something happened wrong. Layout data has not been saved.', 'blog-designer-pack').'</strong></p>
			 </div>';

	} else if( isset( $_GET['message'] ) && 2 == $_GET['message'] ) {
		
		echo '<div class="notice-success notice bdpp-notice is-dismissible">
				<p><strong>'.__('Layout data copied successfully.', 'blog-designer-pack').'</strong></p>
			 </div>';
	}

	if( $valid ) : ?>

	<form action="" method="post" class="bdpp-layout-submit-form" id="bdpp-layout-submit-form">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<table class="form-table">
						<tr>
							<th><label for="bdpp-layout-title"><?php esc_html_e('Layout Name', 'blog-designer-pack'); ?></label></th>
							<td><input type="text" id="bdpp-layout-title" name="bdpp_layout_title" class="large-text bdpp-layout-title" value="<?php echo esc_attr( $layout_title ); ?>" spellcheck="true" autocomplete="off" /></td>
						</tr>
						<tr>
							<th><label for="bdpp-layout-desc"><?php esc_html_e('Layout Description', 'blog-designer-pack'); ?></label></th>
							<td>
								<textarea name="bdpp_layout_desc" id="bdpp-layout-desc" class="large-text bdpp-layout-desc"><?php echo esc_textarea( $layout_desc ); ?></textarea>
								<span class="description"><?php esc_html_e('Enter layout description. This is just for administrator purpose.'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="bdpp-shrt-switcher"><?php esc_html_e('Choose Layout', 'blog-designer-pack'); ?></label></th>
							<td>
								<?php if( ! empty( $registered_shortcodes ) ) { ?>
									<select class="regular-text bdpp-shrt-switcher" id="bdpp-shrt-switcher" name="bdpp_layout_shrt_type">
										<option value=""><?php esc_html_e('-- Choose Layout --', 'blog-designer-pack'); ?></option>
										<?php foreach ($shortcodes_arr as $shrt_grp_key => $shrt_grp_val) {

											// Creating OPT group
											if( is_array( $shrt_grp_val ) && ! empty( $shrt_grp_val['shortcodes'] ) ) {

												$option_grp_name = !empty( $shrt_grp_val['name'] ) ? $shrt_grp_val['name'] : __('General', 'blog-designer-pack');
										?>
												<optgroup label="<?php echo esc_attr( $option_grp_name ); ?>">
												<?php foreach ($shrt_grp_val['shortcodes'] as $shrt_key => $shrt_val) {

													if( empty($shrt_key) ) {
														continue;
													}

													$shrt_val 		= ! empty( $shrt_val ) ? $shrt_val : $shrt_key;
													$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_key, 'action' => $action, 'id' => $layout_id), $page_url );
												?>
													<option value="<?php echo esc_attr( $shrt_key ); ?>" <?php disabled( ! in_array( $shrt_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_val ); ?></option>
												<?php } ?>
												</optgroup>

											<?php } else { 
													$shrt_val 		= ! empty( $shrt_grp_val ) ? $shrt_grp_val : $shrt_grp_key;
													$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_grp_key, 'action' => $action, 'id' => $layout_id), $page_url );
											?>
												<option value="<?php echo esc_attr( $shrt_grp_key ); ?>" <?php disabled( ! in_array( $shrt_grp_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_grp_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_grp_val ); ?></option>
										<?php } // End of else
										} ?>
									</select>
								<?php } ?>
							</td>
						</tr>
						<?php if( $layout_id ) { ?>
						<tr>
							<th><label><?php esc_html_e('Shortcode', 'blog-designer-pack'); ?></label></th>
							<td>
								<div class="bdpp-layout-shrt-preview-wrap">
									<?php esc_html_e('Kindly add below shortcode to any page or post to get the output.', 'blog-designer-pack'); ?>
									<div class="bdpp-layout-shrt-preview">[bdpp_tmpl layout_id="<?php echo esc_attr( $layout_id ); ?>"]
										<span class="bdpp-copy bdpp-layout-shrt-copy" title="<?php esc_attr_e('Copy', 'blog-designer-pack'); ?>" data-clipboard-text="<?php echo esc_attr( '[bdpp_tmpl layout_id="'.$layout_id.'"]' ); ?>">
											<i class="dashicons dashicons-admin-page"></i>
											<span class="bdpp-copy-success bdpp-hide"><?php esc_html_e('Copied!', 'blog-designer-pack'); ?></span>
										</span>
									</div>
								</div>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables">
						<div id="submitdiv" class="postbox">
							<div class="postbox-header"><h2 class="hndle"><?php esc_html_e('Publish', 'blog-designer-pack'); ?></h2></div>
							<div class="inside">
								<div id="submitpost" class="submitbox">
									<div id="misc-publishing-actions">
										<?php if( 'edit' == $action ) { ?>
											<div class="misc-pub-section">
												<input type="checkbox" name="bdpp_layout_enable" id="bdpp-layout-enable" class="bdpp-layout-enable" value="1" <?php checked( $layout_enable, 1 ); ?> />
												<label for="bdpp-layout-enable"><?php esc_html_e('Enable Layout', 'blog-designer-pack'); ?></label>
												<p><?php esc_html_e('Note: If layout is not enabled then no result will be displayed at front.', 'blog-designer-pack'); ?></p>
											</div>
										<?php } else { ?>
											<div class="misc-pub-section">
												<p><?php esc_html_e('Choose your desired layout and check various parameters from left panel.', 'blog-designer-pack'); ?></p>
												<input type="hidden" name="bdpp_layout_enable" id="bdpp-layout-enable" class="bdpp-layout-enable" value="1" />
											</div>
										<?php } ?>
									</div>
									<div id="major-publishing-actions" class="bdpp-major-publishing-actions">
										<?php if( 'edit' == $action ) { ?>
										<div id="duplicate-action"><a class="submitduplicate duplication bdpp-confirm" href="<?php echo esc_url( $duplicate_url ); ?>"><?php esc_html_e('Copy to a new layout', 'blog-designer-pack'); ?></a></div>
										<div id="delete-action"><a class="submitdelete deletion bdpp-confirm" href="<?php echo esc_url( $trash_url ); ?>"><?php esc_html_e('Delete Permanently', 'blog-designer-pack'); ?></a></div>
										<?php } ?>

										<div id="publishing-action">
											<span class="spinner"></span>
											<input type="submit" name="bdpp_layout_save" class="button button-primary button-large bdpp-layout-save" value="<?php echo esc_html( $save_btn_text ); ?>" />
											<input type="hidden" name="bdpp_layout_save_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bdpp-layout-save-nonce' ) ); ?>" />
											<input type="hidden" name="bdpp_layout_shrt_val" class="bdpp-layout-shrt-val" value="" />
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>	
						<div class="bdpp-spc-offer">🎉 <?php esc_html_e('Limited Time Special Offer!', 'blog-designer-pack'); ?></div>
						<a class="bdpp-pro-inline-button" target="_blank" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e('Unlock Premium Features', 'blog-designer-pack'); ?></a>
						
					</div>
				</div><!-- end .postbox-container-1 -->
			</div><!-- end .metabox-holder -->
			<br class="clear">
		</div><!-- end #poststuff -->
	</form>

	<div class="bdpp-shrt-invalid-param-notice bdpp-shrt-alert bdpp-shrt-alert-error bdpp-hide">
		<p><?php esc_html_e('Sorry, The shortcode contains some invalid parameters. These parameters may be missing, obsolete, or incompatible with the current selection. Please verify and correct the parameters to ensure the shortcode functions correctly.', 'blog-designer-pack'); ?></p>
		<p><?php esc_html_e('The parameters are:', 'blog-designer-pack'); ?> <span class="bdpp-shrt-invalid-params"></span></p>
	</div>

	<div class="bdpp-customizer bdpp-clearfix" data-shortcode="<?php echo esc_attr( $preview_shortcode ); ?>" data-template="<?php echo esc_attr( $layout_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('bdpp-shortcode-builder') ); ?>">
		<div class="bdpp-shrt-fields-panel bdpp-clearfix">
			<div class="bdpp-shrt-heading"><?php esc_html_e('Layout Options', 'blog-designer-pack'); ?></div>
			<div class="bdpp-shrt-accordion-wrap">
				<?php
					if ( function_exists( $shortcode_sanitize.'_lite_shortcode_fields' ) ) {
						$shortcode_fields = call_user_func( $shortcode_sanitize.'_lite_shortcode_fields', $preview_shortcode );
					}

					$shortcode_mapper = new BDPP_Shortcode_Builder();
					$shortcode_mapper->render( $shortcode_fields );
				?>
				<div class="bdpp-shrt-loader"></div>
			</div>
		</div>

		<div class="bdpp-shrt-preview-wrap bdpp-clearfix">
			<div class="bdpp-shrt-box-wrp bdpp-hide">
				<div class="bdpp-shrt-heading">
					<?php esc_html_e('Shortcode', 'blog-designer-pack'); ?> <span class="bdpp-cust-heading-info bdpp-tooltip" title="<?php esc_attr_e("Paste below shortcode to any page or post to get output as preview. \n\nYou can paste shortcode to below and press Generate button to preview so each and every time you do not have to choose each parameters!!!", "blog-designer-pack"); ?>">[?]</span>
					<div class="bdpp-shrt-tool-wrap">
						<button type="button" class="button button-secondary button-small bdpp-cust-shrt-generate"><?php esc_html_e('Generate', 'blog-designer-pack') ?></button>
					</div>
				 </div>
				<form action="<?php echo esc_url( $preview_url ); ?>" method="post" class="bdpp-customizer-shrt-form" id="bdpp-customizer-shrt-form" target="bdpp_shortcode_preview_frame">
					<textarea name="bdpp_customizer_shrt" class="bdpp-shrt-box" id="bdpp-shrt-box" placeholder="<?php esc_attr_e('Copy or Paste Shortcode', 'blog-designer-pack'); ?>"><?php echo esc_textarea( $shortcode_val ); ?></textarea>
					<input type="hidden" class="bdpp-customizer-old-shrt" name="bdpp_customizer_old_shrt" value="<?php echo esc_attr( $shortcode_val ); ?>" />
				</form>
			</div>
			<div class="bdpp-shrt-heading">
				<?php esc_html_e('Preview Window', 'blog-designer-pack'); ?> <span class="bdpp-cust-heading-info bdpp-tooltip" title="<?php esc_attr_e('Preview will be displayed according to responsive layout mode. You can check with `Full Preview` mode for better visualization.', 'blog-designer-pack'); ?>">[?]</span>
				<div class="bdpp-shrt-tool-wrap">
					<i title="<?php esc_attr_e('Full Preview Mode', 'blog-designer-pack'); ?>" class="bdpp-tooltip bdpp-shrt-dwp dashicons dashicons-editor-expand"></i>
					<a class="bdpp-layout-debug bdpp-layout-debug-js" href="#" title="<?php esc_html_e('Want to debug layout which parameters are being used?', 'blog-designer-pack'); ?>"><?php esc_html_e('Debug?', 'blog-designer-pack') ?></a>
				</div>
			</div>
			<div class="bdpp-shrt-preview-window">
				<iframe class="bdpp-shrt-preview-frame" name="bdpp_shortcode_preview_frame" src="<?php echo esc_url( $preview_url ); ?>" scrolling="auto" frameborder="0"></iframe>
				<div class="bdpp-shrt-loader"></div>
				<div class="bdpp-shrt-error"><?php esc_html_e('Sorry, Something happened wrong.', 'blog-designer-pack'); ?></div>
			</div>
		</div>
	</div><!-- bdpp-customizer -->

	<br/>
	<div class="bdpp-cust-footer-note"><span class="description"><?php esc_html_e('Note: Preview will be displayed according to responsive layout mode. Live preview may display differently when added to your page based on inheritance from some styles.', 'blog-designer-pack'); ?></span></div>
	<?php endif ?>

</div><!-- end .wrap -->