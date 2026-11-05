(function ($) {
	"use strict";

	var BdppElementorInit = function () {

		/* Slider */
		bdpp_init_post_slider();

		/* Carousel Slider */
		bdpp_init_post_carousel();

		/* GridBox Slider */
		bdpp_init_post_gridbox_slider();

		/* Ticker */
		bdpp_init_post_ticker();

		/* Masonry */
		bdpp_init_post_masonry();
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdpp_layout_elementor_widget.default', BdppElementorInit);
		elementorFrontend.hooks.addAction('frontend/element_ready/shortcode.default', BdppElementorInit);
		elementorFrontend.hooks.addAction('frontend/element_ready/text-editor.default', BdppElementorInit);

		/* Tabs Element */
		elementorFrontend.hooks.addAction( 'frontend/element_ready/tabs.default', function($scope) {
			
			$scope.find('.bdpp-post-slider-wrap').each(function( index ) {
				var slider_id = $(this).attr('id');
				$('#'+slider_id).css({'visibility': 'hidden', 'opacity': 0});

				bdpp_init_post_slider();

				setTimeout(function() {
					if( typeof(slider_id) !== 'undefined' && slider_id != '' ) {
						$('#'+slider_id).trigger('refresh.owl.carousel');
						$('#'+slider_id).css({'visibility': 'visible', 'opacity': 1});
					}
				}, 300);
			});	
		});
	});
}(jQuery));