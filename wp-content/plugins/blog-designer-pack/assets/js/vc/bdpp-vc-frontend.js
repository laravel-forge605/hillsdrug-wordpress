(function ( $ ) {

	window.InlineShortcodeView_bdpp_tmpl = window.InlineShortcodeView.extend({
		render: function () {
			var model 		= this.model;
			var model_id	= this.model.get( 'id' );
			window.InlineShortcodeView_bdpp_tmpl.__super__.render.call( this );
			vc.frame_window.vc_iframe.addActivity( function() {
				bdpp_vc_init_shortcodes( model );
			});
			return this;
		}
	});

	/**
	 * WP Bakery Shortcode Methods
	 * Shortcodes Events are add, shortcodeView:updated and shortcodeView:ready
	 */
	window.vc.events.on( 'shortcodeView:ready', function ( model ) {
		bdpp_vc_init_shortcodes( model );
	});

	/* Initialize Plugin Shortcode */
	function bdpp_vc_init_shortcodes( model ) {

		var modelId, settings;
		modelId		= model.get( 'id' );
		settings	= vc.map[ model.get( 'shortcode' ) ] || false;

		if( settings.base == 'vc_raw_html'
			|| settings.base == 'vc_column_text'
			|| settings.base == 'vc_wp_text'
			|| settings.base == 'vc_message'
			|| settings.base == 'vc_toggle'
			|| settings.base == 'vc_cta'
			|| settings.base == 'bdpp_tmpl'
		) {

			window.vc.frame_window.bdpp_init_post_slider();
			window.vc.frame_window.bdpp_init_post_carousel();
			window.vc.frame_window.bdpp_init_post_gridbox_slider();
			window.vc.frame_window.bdpp_init_post_ticker();
			window.vc.frame_window.bdpp_init_post_masonry();
		}
	}

})( window.jQuery );