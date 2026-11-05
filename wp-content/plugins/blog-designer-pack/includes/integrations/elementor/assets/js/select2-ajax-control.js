jQuery(window).on("elementor:init", (function() {
	"use strict";

	var BdppElementorSelect2Ajax = elementor.modules.controls.BaseData.extend({
		onReady: function() {
			var $this = this,
				$select = $this.ui.select;

				var cls_ele		= $this.$el.closest('.elementor-control');
				var nonce		= $select.attr('data-nonce');

			$select.select2({
				ajax: {
					url			: BdppES2AC.ajax_url,
					dataType	: "json",
					delay		: 500,
					data		: function(params) {
									
									return {
										action		: 'bdpp_post_sugg',
										post_type	: BdppES2AC.post_type,
										search		: params.term,
										nonce		: nonce,
									}
								},
					processResults	: function( data ) {
									var options = [];

									if ( data ) {
										jQuery.each( data, function( index, text ) {
											options.push( { id: text[0], text: text[1] } );
										});
									}
									return {
										results: options
									};
								},
				},
				cache: !0
			});

			var controlValue = void 0 !== $this.getControlValue() ? $this.getControlValue() : "";

			controlValue.isArray && (controlValue = $this.getControlValue().join()), jQuery.ajax({
				url			: BdppES2AC.ajax_url,
				dataType	: "json",
				data		: {
								action		: 'bdpp_post_sugg',
								post_type	: BdppES2AC.post_type,
								post_status	: BdppES2AC.post_status,
								search		: String(controlValue),
								nonce		: nonce,
							},
				beforeSend	: function ( jqXHR, settings) {
								cls_ele.append('<div class="bdpp-loader-wrap bdpp-elementor-loader-wrap" style="position: absolute; background-color: rgba(255,255,255, 0.5); height: 100%; width: 100%; z-index: 9999; top: 0; left: 0; text-align: center; padding: 10px;"><div class="bdpp-loader" style="margin: auto; top: 40%; position: relative; background-color: #fff; padding: 5px 20px; display: inline-block; color: #333;">'+BdppES2AC.loading_text+'</div></div>');
							}
			}).then((function(response) {
				
				jQuery('.bdpp-elementor-loader-wrap').remove();

				if ( '' !== controlValue ) {
					null !== response && response.length > 0 && (jQuery.each(response, (function(index, element) {
						
						var option = new Option(element[1], element[0], !0, !0);

						$select.append(option);
						$select.append(option).trigger('change.select2');
						$select.val(controlValue).trigger('change.select2');
						
					})), $select.trigger({
						type: "select2:select",
						params: {
							data: response
						}
					}))
				}
			}))
		},

		onBeforeDestroy: function() {
			this.ui.select.data("select2") && this.ui.select.select2("destroy"), this.el.remove()
		}
	});

	elementor.addControlView("bdpp-select2-ajax-control", BdppElementorSelect2Ajax);
}));