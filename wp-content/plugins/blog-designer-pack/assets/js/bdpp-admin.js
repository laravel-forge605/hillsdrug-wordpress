var bdpp_timer = null;

( function($) {

	'use strict';

	/* Media Uploader */
	$( document ).on( 'click', '.bdpp-img-upload', function() {

		var imgfield, showfield, file_frame;
		var ele_obj		= jQuery(this);
		imgfield		= ele_obj.parent().find('.bdpp-img-upload-input');
		showfield		= ele_obj.parent().find('.bdpp-img-preview');

		/* new media uploader */
		var button = jQuery(this);
	
		/* If the media frame already exists, reopen it. */
		if ( file_frame ) {
			file_frame.open();
		  return;
		}

		/* Create the media frame. */
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			title: button.data( 'uploader-title' ),
			button: {
				text: button.data( 'uploader-button-text' ),
			},
			multiple: false  /* Set to true to allow multiple files to be selected */
		});

		file_frame.on( 'menu:render:default', function(view) {
			/* Store our views in an object. */
			var views = {};

			/* Unset default menu items */
			view.unset('library-separator');
			view.unset('gallery');
			view.unset('featured-image');
			view.unset('embed');
			view.unset('playlist');
			view.unset('video-playlist');

			/* Initialize the views in our view object. */
			view.set(views);
		});

		/* When an image is selected, run a callback. */
		file_frame.on( 'insert', function() {

			/* Get selected size from media uploader */
			var selected_size = $('.attachment-display-settings .size').val();
			
			var selection = file_frame.state().get('selection');
			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();

				if( attachment.type == 'image' ) {

					/* Selected attachment url from media uploader */
					var attachment_url = attachment.sizes[selected_size].url;

					imgfield.val(attachment_url);
					ele_obj.parent().find('.bdpp-thumb-id').val( attachment.id );
					showfield.html('<img src="'+attachment_url+'" alt="" />');
				}
			});
		});

		/* Finally, open the modal */
		file_frame.open();
	});

	/* Clear Media */
	$( document ).on( 'click', '.bdpp-image-clear', function() {
		$(this).parent().find('.bdpp-img-upload-input').val('');
		$(this).parent().find('.bdpp-thumb-id').val('');
		$(this).parent().find('.bdpp-img-preview').html('');
	});

	/* WP Code Editor */
	if( BdppAdmin.syntax_highlighting == 1 && typeof(wp.codeEditor) !== 'undefined' && $('.bdpp-code-editor').length > 0 ) {
		jQuery('.bdpp-code-editor').each( function() {

			var cur_ele		= jQuery(this);
			var data_mode	= cur_ele.attr('data-mode');
			data_mode		= data_mode ? data_mode : 'css';

			if( cur_ele.hasClass('bdpp-code-editor-initialized') ) {
				return;
			}

			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
					indentUnit	: 4,
					tabSize		: 2,
					mode		: data_mode,
				}
			);
			var editor = wp.codeEditor.initialize( cur_ele, editorSettings );
			cur_ele.addClass('bdpp-code-editor-initialized');

			editor.codemirror.on( 'change', function( codemirror ) {
				cur_ele.val( codemirror.getValue() ).trigger( 'change' );
			});

			/* When post metabox is toggle */
			$(document).on('postbox-toggled', function( event, ele ) {
				if( $(ele).hasClass('closed') ) {
					return;
				}

				if( $(ele).find('.bdpp-code-editor').length > 0 ) {
					editor.codemirror.refresh();
				}
			});
		});
	}

	/* Shortcode Layout Screen */
	if( $('.bdpp-copy').length > 0 ) {

		var bdpp_copy = new ClipboardJS( '.bdpp-copy' );

		/**
		 * Handles media list copy media URL button.
		 */
		bdpp_copy.on( 'success', function( event ) {
			var trigger_element = $( event.trigger ),
				success_element = trigger_element.find( '.bdpp-copy-success' ),
				bdpp_copy_success_timeout;

			/* Clear the selection and move focus back to the trigger. */
			event.clearSelection();

			// Show success visual feedback.
			clearTimeout( bdpp_copy_success_timeout );
			success_element.removeClass( 'bdpp-hide' );

			// Hide success visual feedback after 3 seconds since last success and unfocus the trigger.
			bdpp_copy_success_timeout = setTimeout( function() {
				success_element.addClass( 'bdpp-hide' );
			}, 3000 );
		});
	}

	/* Alert Confirmation */
	$('.bdpp-confirm').on('click', function() {
		if( confirm( BdppAdmin.confirm_msg ) ) {
			return true;
		}
		return false;
	});

	/* Reset confirmation */
	jQuery( document ).on( "click", ".bdpp-reset-button", function() {
		var ans;
		ans = confirm( BdppAdmin.reset_msg );
		if(ans) {
			return true;
		} else {
			return false;
		}
	});

	/* Widget Accordian */
	$(document).on('click', '.bdpp-widget-acc', function() {
		var target		= $(this).attr('data-target');
		var cls_ele		= $(this).closest('form');
		var target_open	= cls_ele.find('.bdpp-widget-'+target).is(":visible");

		cls_ele.find('.bdpp-widget-acc-cnt-wrap').slideUp();
		cls_ele.find('.bdpp-widget-sel-tab').val('');

		if( ! target_open ) {
			cls_ele.find('.bdpp-widget-'+target).slideDown();
			cls_ele.find('.bdpp-widget-sel-tab').val( target );
		}
	});
})( jQuery );

/* Function to Create Cookie */
function bdpp_create_cookie(name, value, time_val, type, samesite) {

	var date, expires, expire_time, samesite;

	time_val	= time_val	? time_val	: false;
	type		= type		? type		: 'day';
	samesite	= samesite	? ";SameSite="+samesite : '';

	if( type == 'hour' ) {
		expire_time = (time_val * 60 * 60 * 1000);

	} else if( type == 'minutes' ) {
		expire_time = (time_val * 60 * 1000);

	} else {
		expire_time = (time_val * 24 * 60 * 60 * 1000);
	}

	if ( time_val ) {
		date = new Date();
		date.setTime( date.getTime() + expire_time );
		expires = "; expires="+date.toGMTString();
	} else {
		expires = "";
	}
	document.cookie = encodeURIComponent(name) + "=" + value + expires + "; path=/"+samesite;
}

/* Function to Delete Cookie */
function bdpp_delete_cookie( name, samesite ) {
	var expires		= "; expires=Thu, 01-Jan-1970 00:00:01 GMT";
	var samesite	= samesite	? ";SameSite="+samesite : '';

	document.cookie = encodeURIComponent(name) + "=" + expires + "; path=/"+samesite;
}