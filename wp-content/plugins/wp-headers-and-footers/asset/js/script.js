(function ($) {
	'use strict';

	function refreshCodeEditors( $scope ) {
		( $scope || $( document ) ).find( '.CodeMirror' ).each( function () {
			if ( this.CodeMirror ) {
				this.CodeMirror.refresh();
			}
		} );
	}

	function initCodeEditors() {
		if ( typeof wp === 'undefined' || ! wp.codeEditor ) {
			return;
		}

		wp.codeEditor.initialize( $( '[id="wpheaderandfooter_basics[wp_header_textarea]"]' ) );
		wp.codeEditor.initialize( $( '[id="wpheaderandfooter_basics[wp_body_textarea]"]' ) );
		wp.codeEditor.initialize( $( '[id="wpheaderandfooter_basics[wp_footer_textarea]"]' ) );
		refreshCodeEditors( $( '#wpheaderandfooter_basics' ) );
	}

	function downloadLogFile(response) {
		if (globalThis.navigator.msSaveOrOpenBlob) {
			const blobObject = new Blob([response]);
			globalThis.navigator.msSaveBlob(blobObject, 'wp-headers-and-footers.txt');
			return;
		}

		$('<a />', {
			download: 'wp-headers-and-footers-log.txt',
			href: 'data:text/plain;charset=utf-8,' + encodeURIComponent(response),
		}).appendTo('body').on('click', function () {
			$(this).remove();
		})[0].click();
	}

	function onLogDownloadSuccess(response) {
		$('.log-file-spinner').hide();
		$('.log-file-text').show();
		downloadLogFile(response);
		setTimeout(function () {
			$('.log-file-text').fadeOut();
		}, 3000);
	}

	function autoDismissSettingsNotice() {
		var $notice = $('#setting-error-settings_updated');

		if (!$notice.length) {
			return;
		}

		setTimeout(function () {
			$notice.fadeOut(400, function () {
				$(this).remove();
			});
		}, 4000);
	}

	$(function () {
		autoDismissSettingsNotice();
		// Init after tab is_show is applied (inline script also runs on ready).
		setTimeout( initCodeEditors, 0 );
	});

	$(globalThis).on('load', function () {
		refreshCodeEditors( $( '#wpheaderandfooter_basics.is_show' ) );

		$('#wpheaderandfooter_diagnostic_log-header').on('click', function (event) {
			event.preventDefault();
			$('#wpheaderandfooter_diagnostic_log-tab').trigger('click');
		});

		$('.wpheaderandfooter-log-file').on('click', function (event) {
			event.preventDefault();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wpheadersandfooters_log_download',
					security: wpheadersandfooters_log.help_nonce,
				},
				beforeSend: function () {
					$('.log-file-spinner').show();
				},
				success: onLogDownloadSuccess,
			});
		});
	});
})(jQuery);
