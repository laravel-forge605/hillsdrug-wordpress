var timer,
	bdpp_shrt_notice_timeout;
var timeOut_Val			= 300;
var timeOut				= timeOut_Val; /* delay after last change to execute filter */
var tmpl_id 			= jQuery('.bdpp-customizer').attr('data-template');
var preview_shortcode	= jQuery('.bdpp-customizer').attr('data-shortcode');
var bdpp_shrt_nonce		= jQuery('.bdpp-customizer').attr('data-nonce');

var checked_show_dep	= [];
var dep_wrap 			= '.bdpp-shrt-fields-panel';
var dependency 			= jQuery(dep_wrap +' .bdpp-cust-dependency').attr('data-dependency');
	dependency 			= dependency ? JSON.parse( dependency ) : false;
var bdpp_shrt_ajax_fields = jQuery(dep_wrap +' .bdpp-cust-dependency').attr('data-ajax-fields');
	bdpp_shrt_ajax_fields = bdpp_shrt_ajax_fields ? JSON.parse( bdpp_shrt_ajax_fields ) : false;

( function($) {

	'use strict';

	$(document).on('click', '.bdpp-shrt-dwp', function() {
		$('body').toggleClass('bdpp-shrt-full-preview');
		$(this).toggleClass( 'bdpp-shrt-dwp-active' );
	});

	/* Customizer Accordian */
	$( "#bdpp-shrt-accordion" ).accordion({
		collapsible	: true,
		active		: false,
		heightStyle	: "content",
		icons		: {
						header		: "dashicons dashicons-arrow-down-alt2",
						activeHeader: "dashicons dashicons-arrow-up-alt2"
					}
	});

	/* Color Picker */
	if( $('.bdpp-cust-color-box').length > 0 ) {
		$('.bdpp-cust-color-box').wpColorPicker({
			change: function(event, ui) {
				bdpp_generate_shortcode_preview( true );
			},
			clear: function() {
				bdpp_generate_shortcode_preview( true );
			}
		});
	}

	/* Generate Shortcode */
	$(document).on('change', '.bdpp-shrt-fields-panel select, .bdpp-shrt-fields-panel input[type="number"]', function() {

		var curr_ele				= $(this);
		var check_invalid_param		= true;
		var cls_wrap				= curr_ele.closest('.bdpp-shrt-accordion');
		var field_timeout 			= curr_ele.attr('data-timeout');
			timeOut 				= (typeof(field_timeout) !== 'undefined') ? field_timeout : timeOut_Val;

		/* Post Type Selection */
		if( curr_ele.hasClass( 'bdpp-post-type-sel' ) ) {
			check_invalid_param = 'force';
		}

		bdpp_generate_shortcode_preview( check_invalid_param );
	});

	$(document).on('keyup', '.bdpp-shrt-fields-panel input[type="text"], .bdpp-shrt-fields-panel input[type="number"]', function() {
		var field_timeout 	= $(this).attr('data-timeout');
			timeOut 		= (typeof(field_timeout) !== 'undefined') ? field_timeout : timeOut_Val;

		bdpp_generate_shortcode_preview( true );
	});

	/* On Change of Customizer Shortcode */
	$(document).on('change', '.bdpp-shrt-switcher', function() {

		if( $('.bdpp-layout-wrap').length > 0 ) {

			var layout_title	= $('.bdpp-layout-wrap .bdpp-layout-title').val();
				layout_title	= ( layout_title ) ? layout_title : '';
			var layout_desc		= $('.bdpp-layout-wrap .bdpp-layout-desc').val();
				layout_desc		= ( layout_desc ) ? layout_desc : '';
			var layout_enable	= $('.bdpp-layout-enable').is(":checked") ? 1 : 0;
			var layout_temp_data = { title: layout_title, description: layout_desc, enable: layout_enable }
				layout_temp_data = JSON.stringify( layout_temp_data );

			bdpp_create_cookie( 'bdpp_layout_temp_data', layout_temp_data, false, false, 'Strict' );
		}

		var redirect = $(this).find(":selected").attr('data-url');

		if( typeof(redirect) !== 'undefined' && redirect != '' ) {
			window.location = redirect;
		}
	});

	/* Tweak - An extra care that form should not be refresh */
	jQuery('#bdpp-customizer-shrt-form').on("submit", function( event ) {
		var form_target = $(this).attr('target');

		if( typeof(form_target) == 'undefined' || form_target == '' ) {
			return false;
		}
	});

	/* On Click of Generate Button */
	$(document).on('click', '.bdpp-cust-shrt-generate', function(e, click_type) {

		var refreshed		= false;
		var ajax_request	= true; /* We want Ajax each time when we click generate button */
		var main_ele		= '.bdpp-shrt-fields-panel';
		var data			= bdpp_get_shortcode_params( '.bdpp-shrt-box' );
		var old_data		= bdpp_get_shortcode_params( '.bdpp-customizer-old-shrt' );
		var click_type		= ( typeof(click_type) != 'undefined' && click_type != '' ) ? click_type : 'click';

		/* If wrong shortcode then simply return */
		if( data && data.numeric[0] && data.numeric[0] !== preview_shortcode ) {
			alert( Bdpp_Shrt_Generator.shortcode_err );
			return false;
		}

		$('.bdpp-shrt-invalid-param-notice').hide();
		$('.bdpp-shrt-acc-header-warn-icon').hide();
		$('.bdpp-customizer-row').removeClass('bdpp-customizer-row-error');

		if( data.named ) {

			/* Ajax Parameters */
			var predefined_params = {};
			
			if( bdpp_shrt_ajax_fields ) {
				$.each( bdpp_shrt_ajax_fields, function( ajax_param_key, ajax_param ) {
					if( ajax_param in data.named ) {
						
						ajax_request = true;

						/* Taking Predefined data */
						var param_predefined_data = $('.bdpp-'+ajax_param).attr('data-predefined');
						if( param_predefined_data ) {
							predefined_params[ajax_param] = JSON.parse( param_predefined_data );
						}

						return;
					}
				});
			}

			if( ajax_request ) {

				var cls_wrap = $('.bdpp-shrt-accordion');
				$('.bdpp-shrt-fields-panel .bdpp-shrt-loader').fadeIn();

				var ajax_args = {
							action				: 'bdpp_get_shrt_params_data',
							shortcode			: preview_shortcode,
							params				: data.named,
							predefined_params	: predefined_params,
							nonce				: bdpp_shrt_nonce
						};

				$.post( ajaxurl, ajax_args, function(result) {

					/* Loop of invalid parameters */
					if( ! $.isEmptyObject(result.invalid_params) ) {
						var invalid_params_str = '';
						$.each( result.invalid_params, function( invalid_param_key, invalid_param_val ) {
							
							$('.bdpp-shrt-invalid-param-notice').show();

							var acc_header		= cls_wrap.find('.bdpp-'+invalid_param_key).closest('.bdpp-shrt-acc-cnt').attr('aria-labelledby');
							var shrt_field_row	= cls_wrap.find('.bdpp-'+invalid_param_key).closest('.bdpp-customizer-row');
							var shrt_field_lbl	= shrt_field_row.find('.bdpp-shrt-lbl').text();

							$('#'+acc_header+' .bdpp-shrt-acc-header-warn-icon').show();
							shrt_field_row.addClass('bdpp-customizer-row-error');

							invalid_params_str += shrt_field_lbl + ' ('+invalid_param_key+'="'+invalid_param_val+'"), ';
						});

						invalid_params_str = invalid_params_str.replace(/,\s*$/, "");
						$('.bdpp-shrt-invalid-param-notice .bdpp-shrt-invalid-params').text( invalid_params_str );
					}

					/* Loop of data */
					$.each( result.data, function( data_key, data_val ) {

						var field_type = cls_wrap.find('.bdpp-'+data_key).closest('.bdpp-customizer-row').data('type');

						if( 'multi-dropdown' == field_type || 'dropdown' == field_type ) {
							cls_wrap.find('.bdpp-'+data_key).html( result.data[data_key] );
						} else {
							cls_wrap.find('.bdpp-'+data_key).val( result.data[data_key] );
						}
					});

					bdpp_set_shortcode_params( data, old_data, click_type );

					$('.bdpp-shrt-fields-panel .bdpp-shrt-loader').fadeOut();

					bdpp_generate_shortcode_preview();
				});

			} else {

				refreshed = bdpp_set_shortcode_params( data, old_data, click_type );
				bdpp_generate_shortcode_preview();
			}
		}

		/* If no parameter is set and no ajax paramter is set then */
		if( refreshed != true && ajax_request != true ) {
			bdpp_generate_shortcode_preview();
		}
	});

	/* Template id is set then run it's shortcode */
	if( tmpl_id != '' ) {
		setTimeout(function() {
			$('.bdpp-cust-shrt-generate').trigger('click', ['trigger']);
		}, 10);
	} else {
		bdpp_generate_shortcode_preview();
	}

	/* Shortcode Customizer Dependency */
	if( dependency ) {
		$.each( dependency, function( key, dependency_val ) {

			if( key ) {

				/* Dependency on page load */
				setTimeout(function() {
					if( $.inArray( key, checked_show_dep ) == -1 ) {
						$(dep_wrap+' .bdpp-'+key+'').trigger('bdpp-cust-dependency-change');
					}
				}, 10);

				$(document).on('change keyup bdpp-cust-dependency-change', dep_wrap+' .bdpp-'+key+'', function() {
					
					var input_val = $(this).val();

					/* Show Dependency */
					if( dependency_val.show ) {
						$.each( dependency_val.show, function( sub_key, sub_dep_val ) {
							$(dep_wrap+' .bdpp-'+sub_key+'').closest('.bdpp-customizer-row').hide();
							$(dep_wrap+' .bdpp-'+sub_key+'').addClass('bdpp-cust-hidden-field');

							/* If value is present then show */
							if( ( $.inArray( input_val, sub_dep_val ) !== -1 ) ) {
								$(dep_wrap+' .bdpp-'+sub_key+'').closest('.bdpp-customizer-row').show();
								$(dep_wrap+' .bdpp-'+sub_key+'').removeClass('bdpp-cust-hidden-field');
							}

							/* Check if reference dependency is there then hide it's element also */
							bdpp_check_ref_dependency( sub_key );
						});
					}

					/* Hide Dependency */
					if( dependency_val.hide ) {
						$.each( dependency_val.hide, function( sub_key, sub_dep_val ) {

							$(dep_wrap+' .bdpp-'+sub_key+'').closest('.bdpp-customizer-row').show();
							$(dep_wrap+' .bdpp-'+sub_key+'').removeClass('bdpp-cust-hidden-field');

							if( ( $.inArray( input_val, sub_dep_val ) !== -1 ) ) {
								$(dep_wrap+' .bdpp-'+sub_key+'').closest('.bdpp-customizer-row').hide();
								$(dep_wrap+' .bdpp-'+sub_key+'').addClass('bdpp-cust-hidden-field');
							}

							/* Check if reference dependency is there then hide it's element also */
							bdpp_check_hide_ref_dependency( sub_key );
						});
					}
				});
			}
		});
	}
	/* Shortcode Customizer Dependency */

	/* Save shortcode template */
	$(document).on( 'submit', '.bdpp-layout-submit-form', function() {
		
		var data = bdpp_get_shortcode_params( '.bdpp-shrt-box' );

		/* If wrong shortcode then simply return */
		if( data && data.numeric[0] && data.numeric[0] !== preview_shortcode ) {
			alert( Bdpp_Shrt_Generator.shortcode_err );
			return false;
		}

		var shrt_val = $('.bdpp-shrt-box').val();
		$('.bdpp-layout-wrap .bdpp-layout-shrt-val').val( shrt_val );
	});

	$(document).on( 'click', '.bdpp-layout-debug-js', function() {
		$('.bdpp-layout-wrap .bdpp-shrt-box-wrp').toggleClass('bdpp-hide');
		return false;
	});

	/* Shortcode Layout Screen */
	if( $('.bdpp-layout-wrap').length > 0 ) {

		/* Delete Layout Temp Data */
		bdpp_delete_cookie( 'bdpp_layout_temp_data', 'Strict' );
	}

	/* Initialize Select2 */
	$( '.bdpp-select2' ).each(function( index ) {
		var curr_ele = $(this);
		curr_ele.select2({
						theme: "default bdpp-select2-inp",
						width: "100%",
					});
	});

	/* Initialize Select2 Ajax */
	$( '.bdpp-ajax-select2' ).each(function( index ) {
		var curr_ele 	= $(this);
		var predefined	= $(this).attr('data-predefined');
		var ajax_action	= $(this).data('ajax-action');
		var search_msg	= $(this).data('search-msg');
			search_msg	= search_msg ? search_msg : Bdpp_Shrt_Generator.select2_input_too_short;
		var placeholder = $(this).data('placeholder');
			placeholder	= placeholder ? placeholder : Bdpp_Shrt_Generator.select2_placeholder;

		if( typeof(ajax_action) == 'undefined' || ajax_action == '' ) {
			return;
		}

		curr_ele.select2({
			ajax: {
				url				: ajaxurl,
				dataType		: 'json',
				delay			: 500,
				data			: function ( params ) {
									var search_term = params.term.trim();

									delay: 0;

									return {
										action			: ajax_action,
										search			: search_term,
										nonce			: bdpp_shrt_nonce,
										post_type		: $('.bdpp-post-type-sel').val(),
										taxonomy		: $('.bdpp-taxonomy-sel').val(),
										all_parents_opt : ( curr_ele.hasClass('bdpp-filter_cat_parent') ) ? 1 : 0,
									};
								},
				processResults	: function( data ) {
									var options = [];

									if( predefined ) {
										options = JSON.parse( predefined );
										options = $.makeArray(options);
									}

									if ( data ) {
										$.each( data, function( index, text ) {
											options.push( { id: text[0], text: text[1] } );
										});
									}
									return {
										results: options
									};
								},
				cache			: true
			},
			width				: '100%',
			theme				: "default bdpp-select2-inp",
			minimumInputLength	: 1,
			allowClear			: true,
			closeOnSelect		: true,
			placeholder			: placeholder,
			language			: {
									inputTooShort : function() {
										return search_msg;
									},
									removeAllItems : function() {
										return Bdpp_Shrt_Generator.select2_remove_all_items;
									},
									removeItem : function() {
										return Bdpp_Shrt_Generator.select2_remove_item;
									},
									searching : function() {
										return Bdpp_Shrt_Generator.select2_searching;
									}
								}

		}).on("select2:unselect", function (e) {
			
			setTimeout(function() {
				curr_ele.select2('close');
			}, 10);
		});
	});

	/* Shortcode preview window loader */
	jQuery('.bdpp-customizer .bdpp-shrt-preview-frame').on("load", function() {
		jQuery('.bdpp-shrt-preview-window .bdpp-shrt-loader').fadeOut();
	});

})( jQuery );

/* Check Valid Shortcode */
function bdpp_get_shortcode_params( ele ) {
	var shrt_val 	= jQuery( ele ).val();
		shrt_val 	= shrt_val.trim();
	var first_char 	= shrt_val.substr(0, 1);
	var last_char 	= shrt_val.substr(-1);

	/* Simply return if blank value */
	if( shrt_val == '' ) {
		return false;
	}

	if( first_char == '[' && last_char == ']' ) {
		shrt_val = shrt_val.slice(1, -1);
		shrt_val = shrt_val.trim();

		first_char 	= shrt_val.substr(0, 1);
		last_char 	= shrt_val.substr(-1);
	}

	if( first_char != '[' ) {
		shrt_val = '[' + shrt_val;
	}
	if( last_char != ']' ) {
		shrt_val = shrt_val + ']';
	}

	jQuery( ele ).val( shrt_val );

	temp_shrt_val = shrt_val.slice(1, -1);
	temp_shrt_val = temp_shrt_val.trim();
	var data = wp.shortcode.attrs( temp_shrt_val );

	return data;
}

/* Function to set shortcode parameters on panel */
function bdpp_set_shortcode_params( data, old_data, click_type ) {

	var refreshed	= false;
	var main_ele	= '.bdpp-shrt-fields-panel';

	/* Loop of old shortcode prameters and reset it */
	if( click_type == 'click' && typeof( old_data ) != 'undefined' && old_data != '' ) {
		jQuery.each( old_data.named, function( old_shrt_param, old_shrt_param_val ) {

			if( typeof( old_shrt_param ) == 'undefined' || old_shrt_param == '' ) {
				return;
			}

			/* If old param is present in the shortcode then skip it */
			if( old_shrt_param in data.named && data.named[old_shrt_param] != '' ) {
				return;
			}

			var field_type	= jQuery(main_ele+' .bdpp-'+old_shrt_param).closest('.bdpp-customizer-row').attr('data-type');
			var default_val	= jQuery(main_ele+' .bdpp-'+old_shrt_param).attr('data-default');
				default_val	= default_val ? default_val : '';

			if( field_type == 'dropdown' && ( jQuery(main_ele+' .bdpp-'+old_shrt_param+" option[value='" + default_val + "']").length > 0 ) ) {

				jQuery(main_ele+' .bdpp-'+old_shrt_param).val( default_val ).trigger('bdpp-cust-dependency-change');

			} else if( field_type == 'multi-dropdown' ) {

				if( jQuery(main_ele+' .bdpp-'+old_shrt_param).hasClass('bdpp-ajax-select2') ) {
					jQuery(main_ele+' .bdpp-'+old_shrt_param).val('');
				} else {
					jQuery.each(default_val.split(","), function(i, opt_val) {
						jQuery(main_ele+' .bdpp-'+old_shrt_param+" option[value='" + opt_val + "']").prop("selected", true);
					});
				}
				jQuery(main_ele+' .bdpp-'+old_shrt_param).trigger('bdpp-cust-dependency-change');

			} else if( field_type == 'colorpicker' ) {
				jQuery(main_ele+' .bdpp-'+old_shrt_param).val( default_val ).trigger('change');
			} else if( field_type == 'text' || field_type == 'number' ) {
				jQuery(main_ele+' .bdpp-'+old_shrt_param).val( default_val ).trigger('bdpp-cust-dependency-change');
			}
		});
	}

	/* Loop of shortcode parameters and set it */
	jQuery.each( data.named, function( shrt_param, shrt_param_val ) {
		if( shrt_param ) {

			var field_type = jQuery(main_ele+' .bdpp-'+shrt_param).closest('.bdpp-customizer-row').attr('data-type');

			if( field_type == 'dropdown' && ( jQuery(main_ele+' .bdpp-'+shrt_param+" option[value='" + shrt_param_val + "']").length > 0 ) ) {

				jQuery(main_ele+' .bdpp-'+shrt_param).val( shrt_param_val ).trigger('bdpp-cust-dependency-change');

			} else if( field_type == 'multi-dropdown' ) {
				
				jQuery.each(shrt_param_val.split(","), function(i, opt_val) {
					jQuery(main_ele+' .bdpp-'+shrt_param+" option[value='" + opt_val + "']").prop("selected", true);
				});
				jQuery(main_ele+' .bdpp-'+shrt_param).trigger('bdpp-cust-dependency-change');

			} else if( field_type == 'colorpicker' ) {
				jQuery(main_ele+' .bdpp-'+shrt_param).val( shrt_param_val ).trigger('change');
			} else if( field_type == 'text' || field_type == 'number' ) {
				jQuery(main_ele+' .bdpp-'+shrt_param).val( shrt_param_val ).trigger('bdpp-cust-dependency-change');
			}

			refreshed = true;
		}
	});

	return refreshed;
}

/* Function to generate shortcode preview */
function bdpp_generate_shortcode_preview( check_invalid_param ) {

	/* Taking some variables */
	var shortcode_val   	= '';
	var main_ele			= jQuery('.bdpp-customizer');
	var cls_ele         	= jQuery('.bdpp-shrt-fields-panel');
	var check_invalid_param	= check_invalid_param ? check_invalid_param : false;

	clearTimeout(timer); /* if we pressed the key, it will clear the previous timer and wait again */
	timer = setTimeout(function() {
		console.log('preview initialized');
		jQuery('.bdpp-shrt-preview-window .bdpp-shrt-loader').fadeIn();

		shortcode_val += '['+preview_shortcode;

		/* Loop of each shortcode parameters */
		cls_ele.find('input[type="text"], input[type="checkbox"]:checked, input[type="radio"], input[type="number"], textarea, select').each(function(i, field){

			if( jQuery(this).hasClass('bdpp-cust-hidden-field') ) {
				return;
			}

			var field_val	= jQuery(this).val();
				field_val	= field_val ? field_val : '';
			var field_name  = jQuery(this).attr('name');
			var default_val	= jQuery(this).attr('data-default');
			var allow_empty	= jQuery(this).attr('data-empty');

			if( typeof(field_val) != 'undefined' && ( field_val != '' || allow_empty ) && field_val != default_val ) {
				shortcode_val += ' '+field_name+'='+'"'+field_val+'"';
			}
		});

		shortcode_val += ']';

		/* Append shortcode */
		main_ele.find('.bdpp-shrt-box').val(shortcode_val);
		main_ele.find('.bdpp-customizer-old-shrt').val(shortcode_val);

		/* Check Invalid Parameters */
		var ajax_request = false;

		if( check_invalid_param ) {

			jQuery('.bdpp-shrt-invalid-param-notice').hide();
			jQuery('.bdpp-shrt-acc-header-warn-icon').hide();
			jQuery('.bdpp-customizer-row').removeClass('bdpp-customizer-row-error');

			var predefined_params	= {};
			var shrt_atts			= bdpp_get_shortcode_params('.bdpp-shrt-box');

			if( check_invalid_param == 'force' ) {
				ajax_request = true;
			}

			if( bdpp_shrt_ajax_fields ) {
				jQuery.each( bdpp_shrt_ajax_fields, function( ajax_param_key, ajax_param ) {
					if( ajax_param in shrt_atts.named ) {

						ajax_request = true;

						/* Taking Predefined data */
						var param_predefined_data = jQuery('.bdpp-'+ajax_param).attr('data-predefined');
						if( param_predefined_data ) {
							predefined_params[ajax_param] = JSON.parse( param_predefined_data );
						}
					}
				});
			}
		}

		if( ajax_request ) {

			var cls_wrap = jQuery('.bdpp-shrt-accordion');
			jQuery('.bdpp-shrt-fields-panel .bdpp-shrt-loader').fadeIn();

			var ajax_args = {
						action				: 'bdpp_get_shrt_params_data',
						shortcode			: preview_shortcode,
						params				: shrt_atts.named,
						predefined_params	: predefined_params,
						nonce				: bdpp_shrt_nonce
					};

			jQuery.post( ajaxurl, ajax_args, function(result) {

				/* Loop of invalid parameters */
				if( ! jQuery.isEmptyObject(result.invalid_params) ) {
					var invalid_params_str = '';
					jQuery.each( result.invalid_params, function( invalid_param_key, invalid_param_val ) {
						
						jQuery('.bdpp-shrt-invalid-param-notice').show();

						var acc_header		= cls_wrap.find('.bdpp-'+invalid_param_key).closest('.bdpp-shrt-acc-cnt').attr('aria-labelledby');
						var shrt_field_row	= cls_wrap.find('.bdpp-'+invalid_param_key).closest('.bdpp-customizer-row');
						var shrt_field_lbl	= shrt_field_row.find('.bdpp-shrt-lbl').text();

						jQuery('#'+acc_header+' .bdpp-shrt-acc-header-warn-icon').show();
						shrt_field_row.addClass('bdpp-customizer-row-error');

						invalid_params_str += shrt_field_lbl + ' ('+invalid_param_key+'="'+invalid_param_val+'"), ';
					});

					invalid_params_str = invalid_params_str.replace(/,\s*$/, "");
					jQuery('.bdpp-shrt-invalid-param-notice .bdpp-shrt-invalid-params').text( invalid_params_str );
				}

				/* Loop of data */
				jQuery.each( result.data, function( data_key, data_val ) {

					var field_type = cls_wrap.find('.bdpp-'+data_key).closest('.bdpp-customizer-row').data('type');

					if( 'multi-dropdown' == field_type || 'dropdown' == field_type ) {
						cls_wrap.find('.bdpp-'+data_key).html( result.data[data_key] );
					} else {
						cls_wrap.find('.bdpp-'+data_key).val( result.data[data_key] );
					}
				});

				jQuery('.bdpp-shrt-fields-panel .bdpp-shrt-loader').fadeOut();

				jQuery('#bdpp-customizer-shrt-form').trigger("submit");
			});

		} else {
			jQuery('#bdpp-customizer-shrt-form').trigger("submit");
		}

	}, timeOut);
}

/* Function to check reference dependency */
function bdpp_check_ref_dependency( sub_key ) {

	var ref_dep = sub_key in dependency;

	if( ref_dep ) {

		var ref_input_val = jQuery(dep_wrap+' .bdpp-'+sub_key+'').val();

		jQuery.each( dependency[sub_key]['show'], function( ref_key, ref_dep_val ) {

			jQuery(dep_wrap+' .bdpp-'+ref_key+'').closest('.bdpp-customizer-row').hide();
			jQuery(dep_wrap+' .bdpp-'+ref_key+'').addClass('bdpp-cust-hidden-field');

			if( jQuery.inArray( ref_input_val, ref_dep_val ) !== -1 && (!jQuery(dep_wrap+' .bdpp-'+sub_key+'').hasClass('bdpp-cust-hidden-field')) ) {
				jQuery(dep_wrap+' .bdpp-'+ref_key+'').closest('.bdpp-customizer-row').show();
				jQuery(dep_wrap+' .bdpp-'+ref_key+'').removeClass('bdpp-cust-hidden-field');
			}

			/* Check if reference dependency is there then hide it's element also */
			bdpp_check_ref_dependency( ref_key );
		});

		checked_show_dep.push( sub_key ); /* Log checked show dependency */
	}
}

/* Function to check hide reference dependency */
function bdpp_check_hide_ref_dependency( sub_key ) {

	var ref_dep = sub_key in dependency;

	if( ref_dep ) {

		var ref_input_val = jQuery(dep_wrap+' .bdpp-'+sub_key+'').val();

		jQuery.each( dependency[sub_key]['hide'], function( ref_key, ref_dep_val ) {

			jQuery(dep_wrap+' .bdpp-'+ref_key+'').closest('.bdpp-customizer-row').hide();
			jQuery(dep_wrap+' .bdpp-'+ref_key+'').addClass('bdpp-cust-hidden-field');

			if( jQuery.inArray( ref_input_val, ref_dep_val ) == -1 && (!jQuery(dep_wrap+' .bdpp-'+sub_key+'').hasClass('bdpp-cust-hidden-field')) ) {
				jQuery(dep_wrap+' .bdpp-'+ref_key+'').closest('.bdpp-customizer-row').show();
				jQuery(dep_wrap+' .bdpp-'+ref_key+'').removeClass('bdpp-cust-hidden-field');
			}

			/* Check if reference dependency is there then hide it's element also */
			bdpp_check_hide_ref_dependency( ref_key );
		});
	}
}

/* Function to display notice */
function bdpp_shrt_display_notice( msg, type = 'success' ) {

	if( ! msg ) {
		return;
	}

	jQuery('.bdpp-shrt-notice').remove();
	jQuery('body').append('<div class="bdpp-shrt-notice bdpp-shrt-notice-'+type+'">'+msg+'</div>');
	jQuery('.bdpp-shrt-notice').fadeIn();

	clearTimeout( bdpp_shrt_notice_timeout );

	bdpp_shrt_notice_timeout = setTimeout(function() {
		jQuery('.bdpp-shrt-notice').fadeOut();
	}, 5000);
}