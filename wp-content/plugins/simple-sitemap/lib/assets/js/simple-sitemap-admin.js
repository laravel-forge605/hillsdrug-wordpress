// eslint-disable-next-line no-undef
jQuery(document).ready(function ($) {
	// Reset plugin settings link
	$('#simple-sitemap-reset > a').on('click', function () {
		const res = window.confirm(
			'Are you sure? All plugin options will be reset to their default settings!'
		);
		if (res === true) {
			$('#simple-sitemap-reset-form').submit();
		}
	});

	// setup event listeners for expandable sections
	['blocks', 'attributes'].forEach(function (section) {
		const btn = $('#' + section + '-btn');
		const wrap = $('#' + section + '-wrap');

		btn.on('click', function () {
			const willExpand = btn.attr('aria-expanded') !== 'true';
			btn.attr('aria-expanded', willExpand ? 'true' : 'false');
			btn.html(
				willExpand
					? 'Collapse <span class="dashicons dashicons-arrow-up-alt2"></span>'
					: 'Expand <span class="dashicons dashicons-arrow-down-alt2"></span>'
			);
			wrap.stop(true, true).slideToggle(300);
		});
	});

	$('[data-simple-sitemap-copy]').on('click', function () {
		const button = $(this);
		const target = document.getElementById(
			button.data('simple-sitemap-copy')
		);
		const status = button.siblings('.simple-sitemap-copy-status');
		if (!target) {
			return;
		}

		const copied = () => status.text(` ${button.data('copied-label')}`);
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(target.value).then(copied, () => {
				target.select();
				document.execCommand('copy');
				copied();
			});
			return;
		}

		target.select();
		document.execCommand('copy');
		copied();
	});
});
