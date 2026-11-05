<?php
/**
 * Style Manager
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wrap bdpp-style-form-wrp">

	<h1 class="wp-heading-inline"><?php esc_html_e('Style Manager', 'blog-designer-pack'); ?></h1>
	
	<p><?php esc_html_e('A Style Manager is a tool that allows you to manage the design elements of any layout. It helps you control font sizes, colors, button backgrounds, and more. By streamlining style decisions, it enhances efficiency, ensures consistency, and improves overall visual appeal. You can create multiple styles for different layouts, allowing for greater flexibility and customization.', 'blog-designer-pack'); ?></p>	
	<p><a style="margin-left:0px;" class="pro-badge" href="<?php echo esc_url( BDP_PRO_TAB_URL ); ?>"><i class="dashicons dashicons-unlock bdpp-shrt-acc-header-pro-icon"></i> <?php esc_html_e( 'Unlock Premium Features', 'blog-designer-pack' ); ?></a></p>
	
	<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/style-manager.png" ); ?>" alt="Style Manager" />
	
</div><!-- end .wrap -->