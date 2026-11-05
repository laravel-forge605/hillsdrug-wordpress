( function($) {

	'use strict';

	/* Slider */
	bdpp_init_post_slider();

	/* Carousel Slider */
	bdpp_init_post_carousel();

	/* Ticker */
	bdpp_init_post_ticker();

	/* Masonry */
	bdpp_init_post_masonry();
	
	/* Widget Scrolling */
	bdpp_init_vertical_scrolling_wdgt();

	/* Load More */
	bdpp_load_more_pagi();

})( jQuery );

/* Slider */
function bdpp_init_post_slider() {
	jQuery( '.bdpp-post-slider-wrap' ).each(function( index ) {

		if( jQuery(this).hasClass('owl-loaded') ) {
			return;
		}

		var slider_id 	= jQuery(this).attr('id');
		var slider		= jQuery('#'+slider_id);
		var conf 		= JSON.parse( jQuery(this).attr('data-conf') );

		slider.owlCarousel({
				loop 			: conf.loop,
				items 			: 1,
				nav 			: conf.arrows,
				dots 			: conf.dots,
				autoplay 		: conf.autoplay,
				autoplayTimeout	: parseInt( conf.autoplay_interval ),
				autoplaySpeed	: (conf.speed == 'false') ? false : parseInt( conf.speed ),
				navElement 		: 'span',
				rtl				: ( Bdpp.is_rtl == 1 ) ? true : false,
		});
	});
}

/* Carousel Slider */
function bdpp_init_post_carousel() {
	jQuery( '.bdpp-post-carousel-wrap' ).each(function( index ) {

		if( jQuery(this).hasClass('owl-loaded') ) {
			return;
		}

		var carousel_id 	= jQuery(this).attr('id');
		var conf 			= JSON.parse( jQuery(this).attr('data-conf') );
		var items			= parseInt( conf.slide_show );
		var slide_scroll	= parseInt( conf.slide_scroll );

		jQuery('#'+carousel_id).owlCarousel({
			items 			: items,
			loop 			: conf.loop,
			slideBy 		: slide_scroll,
			margin 			: 20,
			nav 			: conf.arrows,
			dots 			: conf.dots,
			autoplay 		: conf.autoplay,
			autoplayTimeout	: parseInt( conf.autoplay_interval ),
			autoplaySpeed	: (conf.speed == 'false') ? false : parseInt( conf.speed ),
			navElement 		: 'span',
			rtl				: ( Bdpp.is_rtl == 1 ) ? true : false,
			responsiveClass : true,
			responsive:{
				0:{
					items 	: 1,
					slideBy : 1,
					stagePadding: 0,
				},
				568:{
					slideBy	 	: ( slide_scroll >= 2 ) ? 2 : slide_scroll,
					items 		: ( items >= 2 ) ? 2 : items,
					stagePadding: 0,
				},
				768:{
					slideBy	: ( slide_scroll >= 2 ) ? 2 : slide_scroll,
					items	: ( items >= 2 ) ? 2 : items,
				},
				1024:{
					slideBy	: ( slide_scroll >= 3 ) ? 3 : slide_scroll,
					items	: ( items >= 3 ) ? 3 : items,
				},
				1100:{
					slideBy	: slide_scroll,
					items	: items,
				}
			}
		});
	});
}

/* Ticker */
function bdpp_init_post_ticker() {
	jQuery( '.bdpp-ticker-wrp' ).each(function( index ) {

		if( jQuery(this).hasClass('bdpp-ticker-initialized') ) {
			return;
		}

		var ticker_id   = jQuery(this).attr('id');
		var ticker_conf = JSON.parse( jQuery(this).attr('data-conf') );

		if( typeof(ticker_id) != 'undefined' && ticker_id != '' ) {
			jQuery("#"+ticker_id).breakingNews({
				effect			: ticker_conf.ticker_effect,
				play			: ticker_conf.autoplay,
				delayTimer		: parseInt(ticker_conf.speed),
				borderWidth		: 2,
				radius			: '0px',
				direction		: ( Bdpp.is_rtl == 1 ) ? "rtl" : "ltr",
			});
			jQuery("#"+ticker_id).addClass('bdpp-ticker-initialized');
		}
	});
}

/* Masonry */
function bdpp_init_post_masonry() {
	jQuery('.bdpp-post-masonry-wrap').each(function( index ) {

		if( jQuery(this).hasClass('bdpp-masonry-loaded') ) {
			return;
		}

		var obj_id		= jQuery(this).attr('id');
		var msnry_id	= jQuery('#'+obj_id+' .bdpp-post-masonry-inr-wrap');

		/* Creating object */
		var masonry_param_obj = {itemSelector: '.bdpp-post-grid'};

		if( !jQuery(this).hasClass('bdpp-effect-1') ) {
			masonry_param_obj['transitionDuration'] = 0;
		}

		msnry_id.imagesLoaded(function() {
			msnry_id.masonry(masonry_param_obj);
			jQuery('#'+obj_id).addClass('bdpp-masonry-loaded');
		});
	});
}

/* Vertical Scrolling Widget */
function bdpp_init_vertical_scrolling_wdgt() {
	jQuery( '.bdpp-post-scroling-wdgt-js' ).each(function( index ) {

		var ticker_id	= jQuery(this).attr('id');
		var conf		= JSON.parse( jQuery(this).attr('data-conf') );

		if( typeof(ticker_id) != 'undefined' && ticker_id != '' ) {

			var ticker = jQuery('#'+ticker_id+' .bdpp-vticker-scroling-wdgt-js').easyTicker({
				easing		: 'swing',
				height		: conf.height,
				speed		: parseInt(conf.speed),
				interval	: parseInt(conf.pause),
				mousePause	: false,
				autoplay	: true,
			});
		}
	});
}

/* Load More */
function bdpp_load_more_pagi() {
	jQuery( '.bdpp-post-load-more' ).each(function( index ) {

		if( jQuery(this).hasClass('bdpp-pagi-initialized') ) {
			return;
		}

		var current_obj	= jQuery(this);
		var cls_ele		= current_obj.closest('.bdpp-post-data-wrap');
		var cnt_ele		= cls_ele.find('.bdpp-post-data-inr-wrap');
		var shrt_param	= current_obj.attr('data-conf');
		var paged		= current_obj.attr('data-paged');

		if( cls_ele.hasClass('bdpp-post-masonry-wrap') ) {
			var masonry = true;
		} else {
			var masonry = false;
		}

		jQuery(this).on("click", function() {

			if( jQuery(this).hasClass('bdpp-load-more-disabled') ) {
				return false;
			}

			current_obj.addClass('bdpp-load-more-disabled');
			current_obj.find('.bdpp-load-more-icon').hide();
			current_obj.find('.bdpp-loader').css('display', 'inline-block');

			paged = paged ? ( parseInt(paged) + 1) : 2;
			var data = {
							action		: 'bdp_load_more_posts',
							shrt_param	: shrt_param,
							paged		: paged,
							count		: current_obj.attr('data-count')
						};
			jQuery.post(Bdpp.ajax_url, data, function(result) {

				if( result.status == 1 && result.data != '' ) {

					if( masonry ) {
						
						var obj_id		= cls_ele.attr('id');
						var msnry_id	= jQuery('#'+obj_id+' .bdpp-post-masonry-inr-wrap');
						var $content	= jQuery( result.data );
						
						$content.hide();

						msnry_id.append($content).imagesLoaded(function() {
							$content.show();
							msnry_id.append( $content ).masonry( 'appended', $content );
						});

					} else {
						cnt_ele.append( result.data );
					}

					current_obj.attr( 'data-count', result.count );
					if( result.last_page == 1 ) {
						current_obj.closest('.bdpp-paging').hide();
					}

				} else if(result.data == '') {

					current_obj.closest('.bdpp-paging').hide();
					var msg_info = '<div class="bdpp-info">'+Bdpp.no_post_found_msg+'</div>';

					cnt_ele.after( msg_info );
					setTimeout(function() {
						jQuery(".bdpp-info").fadeOut("normal", function() {
							jQuery(this).remove();
						});
					}, 3000 );
				}

				current_obj.find('.bdpp-load-more-icon').show();
				current_obj.find('.bdpp-loader').hide();
				current_obj.removeClass('bdpp-load-more-disabled');
			});
		});
		current_obj.addClass('bdpp-pagi-initialized');
	});
}