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
$registered_shortcodes 	= bdp_registered_shortcodes();
$shortcodes_arr 		= bdp_registered_shortcodes( false );
$allowed_reg_shortcodes	= bdp_allowed_reg_shortcodes();
$preview_shortcode 		= ! empty( $_GET['shortcode'] ) ? $_GET['shortcode'] : apply_filters('bdpp_default_preview_shortcode', 'bdp_post' );
$preview_url 			= add_query_arg( array( 'page' => 'bdpp-shortcode-preview', 'shortcode' => $preview_shortcode), admin_url('admin.php') );
$shrt_builder_url 		= add_query_arg( array('page' => 'bdpp-shrt-builder'), admin_url('admin.php') );

// Version 7 compatibility
if ( version_compare( $wp_version, '7.0', '>=' ) ) {
	$wrap_cls = 'bdpp-layout-wrap-v7';
}

// Instantiate the shortcode builder
if( ! class_exists( 'BDPP_Shortcode_Builder' ) ) {
	include_once( BDP_DIR . '/includes/admin/shortcode-builder/class-bdpp-shortcode-builder.php' );
}

$shortcode_val		= "[{$preview_shortcode}]";
$shortcode_fields 	= array();
$shortcode_sanitize = str_replace('-', '_', $preview_shortcode);
?>
<div class="wrap bdpp-customizer-settings <?php echo esc_attr( $wrap_cls ); ?>">
	<div class="bdpp-pro-main-wrap bdpp-clearfix" style="text-align:left; margin:0 -15px 20px -15px;">
		<div class="bdpp-cnt-grid-8 bdpp-columns">
			<h2><?php esc_html_e( 'Shortcode Builder (Alternate Option For Layouts)', 'blog-designer-pack' ); ?></h2>
			<p><?php esc_html_e( 'Shortcode builder is an alternate option for those who do not want to create the layout & store the data in to database. It will help you to understand all the parameters in the details.', 'blog-designer-pack' ); ?></p>
		</div>
		<div class="bdpp-cnt-grid-4 bdpp-columns" style="text-align:right; padding-top:20px;">
			<div class="bdpp-spc-offer" style="text-align:right;">🎉 <?php esc_html_e('Limited Time Special Offer!', 'blog-designer-pack'); ?></div>
			<a class="pro-badge" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'blog-designer-pack' ); ?></a>
		</div>
	</div>
	<?php
	// If invalid shortcode is passed then simply return
	if( ! empty( $_GET['shortcode'] ) && ! isset( $registered_shortcodes[ $_GET['shortcode'] ] ) ) {

		$valid = false;

		echo '<div id="message" class="error notice">
				<p><strong>'.__('Sorry, Something happened wrong.', 'blog-designer-pack').'</strong></p>
			 </div>';
	}

	if( $valid ) : ?>

	<div class="bdpp-shrt-invalid-param-notice bdpp-shrt-alert bdpp-shrt-alert-error bdpp-hide">
		<p><?php esc_html_e('Sorry, The shortcode contains some invalid parameters. These parameters may be missing, obsolete, or incompatible with the current selection. Please verify and correct the parameters to ensure the shortcode functions correctly.', 'blog-designer-pack'); ?></p>
		<p><?php esc_html_e('The parameters are:', 'blog-designer-pack'); ?> <span class="bdpp-shrt-invalid-params"></span></p>
	</div>

	<div class="bdpp-shrt-toolbar">
		<div class="bdpp-shrt-heading"><?php esc_html_e('Choose Shortcode', 'blog-designer-pack'); ?></div>
		<?php if( ! empty( $registered_shortcodes ) ) { ?>
			<select class="bdpp-shrt-switcher" id="bdpp-shrt-switcher">
				<option value=""><?php esc_html_e('-- Choose Shortcode --', 'blog-designer-pack'); ?></option>
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

							$shrt_val 		= !empty($shrt_val) ? $shrt_val : $shrt_key;
							$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_key), $shrt_builder_url );
						?>
							<option value="<?php echo esc_attr( $shrt_key ); ?>" <?php disabled( ! in_array( $shrt_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_val ); ?></option>
						<?php } ?>
						</optgroup>

					<?php } else {
							$shrt_val 		= !empty($shrt_grp_val) ? $shrt_grp_val : $shrt_grp_key;
							$shortcode_url 	= add_query_arg( array('shortcode' => $shrt_grp_key), $shrt_builder_url );
					?>
						<option value="<?php echo esc_attr( $shrt_grp_key ); ?>" <?php disabled( ! in_array( $shrt_grp_key, $allowed_reg_shortcodes ), true ); ?> <?php selected( $preview_shortcode, $shrt_grp_key); ?> data-url="<?php echo esc_url( $shortcode_url ); ?>"><?php echo esc_html( $shrt_grp_val ); ?></option>
				<?php } // End of else
				} ?>
			</select>
		<?php } ?>

		<span class="bdpp-shrt-generate-help bdpp-tooltip" title="<?php esc_attr_e("The shortcode builder allows you to preview plugin shortcode. You can choose your desired shortcode from the dropdown and check various parameters from left panel. \n\nYou can paste shortcode to below textarea and press Generate button to preview so each and every time you do not have to choose each parameters!!!", 'blog-designer-pack'); ?>"><i class="dashicons dashicons-editor-help"></i></span>
	</div><!-- end .bdpp-shrt-toolbar -->

	<div class="bdpp-customizer bdpp-clearfix" data-shortcode="<?php echo esc_attr( $preview_shortcode ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('bdpp-shortcode-builder') ); ?>" data-template="">
		<div class="bdpp-shrt-fields-panel bdpp-clearfix">
			<div class="bdpp-shrt-heading"><?php esc_html_e('Shortcode Parameters', 'blog-designer-pack'); ?></div>
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
			<div class="bdpp-shrt-box-wrp">
				<div class="bdpp-shrt-heading"><?php esc_html_e('Shortcode', 'blog-designer-pack'); ?> <span class="bdpp-cust-heading-info bdpp-tooltip" title="<?php esc_attr_e('Paste below shortcode to any page or post to get output as preview.', 'blog-designer-pack'); ?>">[?]</span>
					<div class="bdpp-shrt-tool-wrap">
						<button type="button" class="button button-primary button-small bdpp-cust-shrt-generate"><?php esc_html_e('Generate', 'blog-designer-pack') ?></button>
				 		<i title="<?php esc_attr_e('Full Preview Mode', 'blog-designer-pack'); ?>" class="bdpp-tooltip bdpp-shrt-dwp dashicons dashicons-editor-expand"></i>
				 	</div>
				 </div>
				<form action="<?php echo esc_url($preview_url); ?>" method="post" class="bdpp-customizer-shrt-form" id="bdpp-customizer-shrt-form" target="bdpp_shortcode_preview_frame">
					<textarea name="bdpp_customizer_shrt" class="bdpp-shrt-box" id="bdpp-shrt-box" placeholder="<?php esc_attr_e('Copy or Paste Shortcode', 'blog-designer-pack'); ?>"><?php echo esc_textarea( $shortcode_val ); ?></textarea>
					<input type="hidden" class="bdpp-customizer-old-shrt" name="bdpp_customizer_old_shrt" value="<?php echo esc_attr( $shortcode_val ); ?>" />
				</form>
			</div>
			<div class="bdpp-shrt-heading"><?php esc_html_e('Preview Window', 'blog-designer-pack'); ?> <span class="bdpp-cust-heading-info bdpp-tooltip" title="<?php esc_attr_e('Preview will be displayed according to responsive layout mode. You can check with `Full Preview` mode for better visualization.', 'blog-designer-pack'); ?>">[?]</span></div>
			<div class="bdpp-shrt-preview-window">
				<iframe class="bdpp-shrt-preview-frame" name="bdpp_shortcode_preview_frame" src="<?php echo esc_url($preview_url); ?>" scrolling="auto" frameborder="0"></iframe>
				<div class="bdpp-shrt-loader"></div>
				<div class="bdpp-shrt-error"><?php esc_html_e('Sorry, Something happened wrong.', 'blog-designer-pack'); ?></div>
			</div>
		</div>
	</div><!-- bdpp-customizer -->

	<br/>
	<div class="bdpp-cust-footer-note"><span class="description"><?php esc_html_e('Note: Preview will be displayed according to responsive layout mode. Live preview may display differently when added to your page based on inheritance from some styles.', 'blog-designer-pack'); ?></span></div>
	<?php endif ?>

</div><!-- end .wrap -->