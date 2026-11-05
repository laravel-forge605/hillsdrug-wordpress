<?php
/**
 * Shortcode Preview 
 *
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$authenticated          = false; 
$registered_shortcodes  = bdp_registered_shortcodes();

// Getting shortcode value
if( ! empty( $_POST['bdpp_customizer_shrt'] ) ) {
	$shortcode_val = wp_unslash( $_POST['bdpp_customizer_shrt'] ); // WPCS: input var ok, CSRF ok.
} else {
	$shortcode_val = '';
}

// For authentication so no one can use page via URL
if( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
	$url_query  = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
	parse_str( $url_query, $referer );

	if( ! empty( $referer['page'] ) && ( 'bdpp-shrt-builder' == $referer['page'] || 'bdpp-layout' == $referer['page'] ) ) {
		$authenticated = true;
	}

} elseif ( is_user_logged_in() && current_user_can('manage_options') ) {
	$authenticated = true;
}

// Check Authentication else exit
if( ! $authenticated ) {
	wp_die( __('Sorry, you are not allowed to access this page.', 'blog-designer-pack') );
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta http-equiv="Imagetoolbar" content="No" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php esc_html_e("Shortcode Preview", "blog-designer-pack"); ?></title>

		<?php wp_print_styles('common'); ?>
		<link rel="stylesheet" href="<?php echo esc_url( BDP_URL."assets/css/font-awesome.min.css?ver=".BDP_VERSION ); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo esc_url( BDP_URL."assets/css/owl.carousel.min.css?ver=".BDP_VERSION ); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo esc_url( BDP_URL."assets/css/bdpp-public.css?ver=".BDP_VERSION ); ?>" type="text/css" />
		<?php do_action( 'bdpp_shortcode_preview_head', $shortcode_val ); ?>

		<style type="text/css">
			body{background: #fff; overflow-x: hidden;}
			.bdpp-customizer-container{padding:0 16px;}
			.bdpp-customizer-container a[href^="http"]{cursor:not-allowed !important;}
			a:focus, a:active{box-shadow: none; outline: none;}
			.bdpp-link-notice{display: none; position: fixed; color: #a94442; background-color: #f2dede; border:1px solid #ebccd1; max-width:300px; width: 100%; left:0; right:0; bottom:30%; margin:auto; padding:10px; text-align: center; z-index: 1050;}
		</style>
		<?php wp_print_scripts( array('jquery', 'masonry') ); ?>
	</head>
	<body>
		<div id="bdpp-customizer-container" class="bdpp-customizer-container">
			<?php if( $shortcode_val ) {
				echo do_shortcode( $shortcode_val );
			} ?>
		</div>
		<div class="bdpp-link-notice"><?php esc_html_e('Sorry, You can not visit the link in preview mode.', 'blog-designer-pack'); ?></div>

		<script type='text/javascript'> 
		/*<![CDATA[*/
		var Bdpp = <?php echo wp_json_encode(array(
												'ajax_url'			=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
												'is_mobile'			=> (wp_is_mobile()) ? 1 : 0,
												'is_rtl'			=> (is_rtl())       ? 1 : 0,
												'no_post_found_msg'	=> esc_js( __('No more post to display.', 'blog-designer-pack') ),
											)); ?>;
		/*]]>*/
		</script>
		<script type="text/javascript" src="<?php echo esc_url( BDP_URL."assets/js/owl.carousel.min.js?ver=".BDP_VERSION ); ?>"></script>
		<script type="text/javascript" src="<?php echo esc_url( BDP_URL."assets/js/bdpp-ticker.min.js?ver=".BDP_VERSION ); ?>"></script>
		<script type="text/javascript" src="<?php echo esc_url( BDP_URL."assets/js/bdpp-public.js?ver=".BDP_VERSION ); ?>"></script>
		<?php do_action( 'bdpp_shortcode_preview_footer', $shortcode_val ); ?>
		<script type="text/javascript">
		( function($) {

			/* To avoid the browser POST data resend warning when we refresh the page */
			if ( window.history.replaceState ) {
				window.history.replaceState( null, null, window.location.href );
			}

			$(document).on('click', 'a', function(event) {

				var href_val = $(this).attr('href');

				if( href_val.indexOf('javascript:') < 0 ) {
					$('.bdpp-link-notice').fadeIn();
				}
				event.preventDefault();

				setTimeout(function() {
					$(".bdpp-link-notice").fadeOut('normal');
				}, 4000 );
			});
		})( jQuery );
		</script>
	</body>
</html>