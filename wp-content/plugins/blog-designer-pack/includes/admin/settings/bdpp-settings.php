<?php
/**
 * Settings Page HTML
 * 
 * The code for the plugins main settings page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Reset all settings
if( ! empty( $_POST['bdpp_reset_settings'] ) && check_admin_referer( 'bdpp_reset_settings', 'bdpp_reset_setting_nonce' ) ) {
	bdp_set_default_settings();
}

$settings_tab_arr 	= bdp_settings_tab();
$bdpp_active_tab 	= ( !empty($_GET['tab']) && array_key_exists( $_GET['tab'], $settings_tab_arr ) ) ? bdp_clean( $_GET['tab'] ) : 'welcome';
?>
<div class="wrap">

	<h2><?php esc_html_e( 'Blog Designer Pack Settings', 'blog-designer-pack' ); ?></h2>

	<?php
	// Save Setting Message
	if( ! empty( $_GET['settings-updated'] ) ) {
		echo '<div id="message" class="updated fade notice is-dismissible"><p><strong>' . __( 'Changes Saved.', 'blog-designer-pack') . '</strong></p></div>'; 
	}

	// Reset Setting Message
	if( ! empty( $_POST['bdpp_reset_settings'] ) ) {
		echo '<div id="message" class="updated fade notice is-dismissible"><p><strong>' . __( 'Settings reset successfully.', 'blog-designer-pack') . '</strong></p></div>'; 
	}
	?>

	<!-- Reset settings form -->
	<form action="" method="post" id="bdpp-reset-sett-form" class="bdpp-right bdpp-reset-sett-form">
		<div class="bdpp-reset-settings bdpp-clearfix">
			<input type="submit" value="<?php esc_html_e('Reset All Settings', 'blog-designer-pack'); ?>" name="bdpp_reset_settings" id="bdpp_reset_settings" class="button button-primary bdpp-reset-button" />
			<?php wp_nonce_field( 'bdpp_reset_settings', 'bdpp_reset_setting_nonce' ); ?>
		</div>
	</form>

	<div class="bdpp-sett-wrp">

		<form action="options.php" method="POST" id="bdpp-settings-form">

			<?php
			settings_fields( 'bdpp_settings' );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

			global $bdpp_options;
			?>

			<!-- Save Button -->
			<div class="textright bdpp-clearfix">
				<input type="submit" name="bdpp_sett_submit" class="button button-primary bdpp-sett-submit" value="<?php esc_html_e('Save Settings', 'blog-designer-pack'); ?>" />
			</div>

			<h2 class="nav-tab-wrapper bdpp-nav-tab-wrapper bdpp-h2">
				<?php
				if( ! empty( $settings_tab_arr ) ) {
					foreach ($settings_tab_arr as $sett_key => $sett_val) {
						$selected_nav_cls 	= ($bdpp_active_tab == $sett_key) ? 'nav-tab-active' : '';
						$tab_url 			= add_query_arg( array( 'page' => 'bdpp-settings', 'tab' => $sett_key), admin_url('admin.php') );
				?>
						<a class="nav-tab <?php echo esc_attr( $selected_nav_cls ); ?> bdpp-nav-tab-<?php echo esc_attr( $sett_key ); ?>" href="<?php echo esc_url( $tab_url ); ?>">
							<?php echo esc_html( $sett_val );

							if( 'pro' == $sett_key ) { ?>
							<span class="bdpp-nav-tab-offer"><i class="dashicons dashicons-bell"></i> <?php esc_html_e('Limited Time Offer', 'blog-designer-pack'); ?></span>
							<?php } ?>
						</a>
				<?php
					} // End of for each
				} // End of if
				?>
			</h2>
			
			<div class="bdpp-sett-content-wrp bdpp-sett-tab-cnt bdpp-<?php echo esc_attr( $bdpp_active_tab ); ?>-sett-cnt-wrp">
				<?php do_action( 'bdp_settings_tab_' . $bdpp_active_tab ); ?>
			</div><!-- end .bdpp-sett-content-wrp -->

		</form><!-- end #bdpp-settings-form -->

	</div><!-- end .bdpp-sett-wrp -->
</div><!-- end .wrap -->