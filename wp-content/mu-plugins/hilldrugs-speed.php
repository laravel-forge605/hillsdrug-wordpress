<?php
/**
 * Plugin Name: HillDrugs Speed Tweaks
 * Description: PageSpeed fixes (front-end only, logged-out visitors only):
 *   - delays third-party / popup scripts until first user interaction (or 6s)
 *   - loads non-critical CSS without blocking render
 *   - preloads the hero image, drops duplicate Font Awesome / eicons CSS
 *   - font-display: swap for Google fonts
 * Drop-in: lives in wp-content/mu-plugins, nothing to activate.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Only touch the public front end for logged-out visitors (keeps Elementor editor/admin intact). */
function hd_speed_active() {
	static $on = null;
	if ( null === $on ) {
		$on = ! is_admin()
			&& ! is_user_logged_in()
			&& ! wp_doing_ajax()
			&& ! ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			&& empty( $_GET['elementor-preview'] )
			&& empty( $_GET['preview'] );
	}
	return $on;
}

/** Scripts delayed until first interaction (matched against src). */
function hd_speed_delayed_src_patterns() {
	return array(
		'googletagmanager.com/gtag/js',
		'accounts.google.com/gsi/client',
		'sign-in-with-google',
		'challenges.cloudflare.com/turnstile',
		'googlesitekit-events-provider-',
		'brave-popup-builder/assets/frontend/brave.js',
		'popup-maker/dist/assets/site.js',
	);
}

/** Stylesheet handles safe to load async (not needed for first paint). */
function hd_speed_async_css_handles() {
	return array(
		'contact-form-7',
		'wpcf7-redirect-script-frontend',
		'wp-show-posts',
		'wpshowposts',
		'elementor-gf-local-robotoslab',
		'widget-nested-accordion',
		'widget-divider',
		'widget-icon-box',
		'widget-social-icons',
		'e-apple-webkit',
		'elementor-icons-fa-regular',
		'elementor-icons-fa-brands',
	);
}

/* ------------------------------------------------------------------ */
/* 1. Delay heavy scripts                                              */
/* ------------------------------------------------------------------ */
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
	if ( ! hd_speed_active() || ! $src ) {
		return $tag;
	}
	foreach ( hd_speed_delayed_src_patterns() as $needle ) {
		if ( false !== strpos( $src, $needle ) ) {
			// Neutralise: browser ignores unknown type; loader below re-creates it.
			$tag = preg_replace( '/<script\b/i', '<script type="hd/delayed"', $tag, 1 );
			$tag = preg_replace( '/\s(src)=/i', ' data-hd-src=', $tag, 1 );
			$tag = preg_replace( '/\s(async|defer)(=([\'"])[^\'"]*\3)?(?=[\s>])/i', '', $tag );
			return $tag;
		}
	}
	return $tag;
}, 20, 3 );

/* Defer the remaining non-jQuery-dependent-inline scripts is intentionally NOT done (inline scripts rely on order). */

add_action( 'wp_footer', function () {
	if ( ! hd_speed_active() ) {
		return;
	}
	?>
<script id="hd-delay-loader">
(function(){
	var done=false;
	function load(){
		if(done)return;done=true;
		var list=[].slice.call(document.querySelectorAll('script[type="hd/delayed"]'));
		(function next(){
			var o=list.shift();if(!o)return;
			var s=document.createElement('script');
			for(var i=0;i<o.attributes.length;i++){
				var a=o.attributes[i];
				if(a.name==='type'||a.name==='data-hd-src')continue;
				s.setAttribute(a.name,a.value);
			}
			var src=o.getAttribute('data-hd-src');
			if(src){s.src=src;s.async=false;s.onload=s.onerror=next;}else{s.text=o.text;}
			o.parentNode.replaceChild(s,o);
			if(!src)next();
		})();
	}
	['scroll','mousemove','touchstart','keydown','click','wheel'].forEach(function(e){
		window.addEventListener(e,load,{once:true,passive:true});
	});
	setTimeout(load,6000);
})();
</script>
	<?php
}, 999 );

/* ------------------------------------------------------------------ */
/* 2. Non-render-blocking CSS                                          */
/* ------------------------------------------------------------------ */
add_filter( 'style_loader_tag', function ( $tag, $handle ) {
	if ( ! hd_speed_active() || ! in_array( $handle, hd_speed_async_css_handles(), true ) ) {
		return $tag;
	}
	$async = str_replace( "media='all'", "media='print' onload=\"this.media='all'\"", $tag );
	return $async . '<noscript>' . $tag . '</noscript>';
}, 20, 2 );

/* Duplicate (older) icon/font CSS from Header Footer Elementor; Elementor core already loads newer copies. */
add_filter( 'style_loader_tag', function ( $tag, $handle ) {
	static $dupes = array( 'hfe-elementor-icons', 'hfe-icons-list', 'hfe-social-icons', 'hfe-social-share-icons-brands', 'hfe-social-share-icons-fontawesome', 'hfe-nav-menu-icons', 'elementor-gf-local-roboto', 'elementor-gf-local-montserrat' );
	return ( hd_speed_active() && in_array( $handle, $dupes, true ) ) ? '' : $tag;
}, 10, 2 );

/* Icon fonts: font-display swap instead of block (same files, just redeclared). */
add_action( 'wp_head', function () {
	if ( ! hd_speed_active() ) {
		return;
	}
	$b = content_url( '/plugins/elementor/assets/lib' );
	echo '<style id="hd-font-swap">'
		. '@font-face{font-family:"Font Awesome 5 Free";font-style:normal;font-weight:900;font-display:swap;src:url(' . $b . '/font-awesome/webfonts/fa-solid-900.woff2) format("woff2")}'
		. '@font-face{font-family:"Font Awesome 5 Free";font-style:normal;font-weight:400;font-display:swap;src:url(' . $b . '/font-awesome/webfonts/fa-regular-400.woff2) format("woff2")}'
		. '@font-face{font-family:"Font Awesome 5 Brands";font-style:normal;font-weight:400;font-display:swap;src:url(' . $b . '/font-awesome/webfonts/fa-brands-400.woff2) format("woff2")}'
		. '@font-face{font-family:eicons;font-display:swap;src:url(' . $b . '/eicons/fonts/eicons.woff2) format("woff2")}'
		. hd_speed_text_fonts_css()
		. '</style>' . "
";
}, 50 );

/* Roboto (body) + Montserrat (header): one variable latin file each. Inline faces + preload replace
 * Elementor's 2 big local font CSS files (~150 @font-face rules) so text paints in the right font
 * immediately and doesn't reflow (fixes the layout shift). */
function hd_speed_text_fonts() {
	return array(
		'Roboto'     => '/uploads/elementor/google-fonts/fonts/roboto-kfo7cnqeu92fr1me7ksn66agldtyluama3yuba.woff2',
		'Montserrat' => '/uploads/elementor/google-fonts/fonts/montserrat-jtusjig1_i6t8kchkm459wrhyzbi.woff2',
	);
}
function hd_speed_text_fonts_css() {
	$css = '';
	foreach ( hd_speed_text_fonts() as $family => $rel ) {
		if ( ! file_exists( WP_CONTENT_DIR . $rel ) ) {
			continue;
		}
		$css .= '@font-face{font-family:"' . $family . '";font-style:normal;font-weight:100 900;font-display:swap;src:url(' . content_url( $rel ) . ') format("woff2");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}';
	}
	return $css;
}
add_action( 'wp_head', function () {
	if ( ! hd_speed_active() ) {
		return;
	}
	foreach ( hd_speed_text_fonts() as $rel ) {
		if ( file_exists( WP_CONTENT_DIR . $rel ) ) {
			echo '<link rel="preload" as="font" type="font/woff2" href="' . esc_url( content_url( $rel ) ) . '" crossorigin>' . "\n";
		}
	}
}, 2 );

/* Drop the emoji stylesheet. */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! hd_speed_active() ) {
		return;
	}
	wp_dequeue_style( 'wp-emoji' );
}, 100 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/* ------------------------------------------------------------------ */
/* 3. Hero image preload + priority                                    */
/* ------------------------------------------------------------------ */
add_action( 'wp_head', function () {
	if ( ! hd_speed_active() || ! is_front_page() ) {
		return;
	}
	$u = content_url( '/uploads/2024/09/h2-2.webp' );
	echo '<link rel="preload" as="image" href="' . esc_url( $u ) . '" fetchpriority="high">' . "\n";
	echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>' . "\n";
}, 1 );

/* Hero slider's first slide: fetchpriority high, never lazy. */
add_filter( 'the_content', function ( $html ) {
	return hd_speed_hero_img( $html );
}, 99 );
add_filter( 'elementor/frontend/the_content', function ( $html ) {
	return hd_speed_hero_img( $html );
}, 99 );
function hd_speed_hero_img( $html ) {
	if ( ! hd_speed_active() || false === strpos( $html, 'h2-2.webp' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'/<img\b[^>]*h2-2\.webp[^>]*>/i',
		function ( $m ) {
			$t = str_replace( 'loading="lazy"', '', $m[0] );
			return false === strpos( $t, 'fetchpriority' ) ? preg_replace( '/<img\b/i', '<img fetchpriority="high"', $t, 1 ) : $t;
		},
		$html,
		1
	);
}

/* ------------------------------------------------------------------ */
/* 4. Fonts: swap                                                      */
/* ------------------------------------------------------------------ */
add_filter( 'style_loader_src', function ( $src ) {
	if ( false !== strpos( $src, 'fonts.googleapis.com/css' ) && false === strpos( $src, 'display=' ) ) {
		$src = add_query_arg( 'display', 'swap', $src );
	}
	return $src;
} );
add_filter( 'pre_option_elementor_font_display', function () {
	return 'swap';
} );
