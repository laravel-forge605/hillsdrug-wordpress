(function () {
	'use strict';

	function initializeTabset(container) {
		if (container.dataset.simpleSitemapTabsReady === 'true') {
			return;
		}

		const controls = Array.from(
			container.querySelectorAll(':scope > .simple-sitemap-tab-control')
		);
		const content = container.querySelector(
			':scope > .simple-sitemap-content'
		);
		if (!controls.length || !content) {
			return;
		}

		const labels = controls
			.map((control) =>
				container.querySelector(`label[for="${control.id}"]`)
			)
			.filter(Boolean);
		const panels = Array.from(
			content.querySelectorAll(':scope > [role="tabpanel"]')
		);
		if (
			labels.length !== controls.length ||
			panels.length !== controls.length
		) {
			return;
		}
		const tabs = labels.map((label, index) => {
			const button = document.createElement('button');
			button.id = `${label.id}-button`;
			button.className = 'simple-sitemap-tab-button';
			button.type = 'button';
			while (label.firstChild) {
				button.appendChild(label.firstChild);
			}
			button
				.querySelectorAll('h1, h2, h3, h4, h5, h6')
				.forEach((heading) => {
					heading.setAttribute('role', 'none');
				});
			label.appendChild(button);
			panels[index].setAttribute('aria-labelledby', button.id);

			return button;
		});

		const tablist = document.createElement('div');
		tablist.className = 'screen-reader-text simple-sitemap-tablist';
		tablist.setAttribute('role', 'tablist');
		tablist.setAttribute(
			'aria-label',
			window.wp?.i18n?.__('Sitemap sections', 'simple-sitemap') ||
				'Sitemap sections'
		);
		tablist.setAttribute('aria-owns', tabs.map((tab) => tab.id).join(' '));
		container.insertBefore(tablist, controls[0]);

		function selectTab(index, moveFocus) {
			controls.forEach((control, itemIndex) => {
				const selected = itemIndex === index;
				control.checked = selected;
				control.tabIndex = -1;
				control.setAttribute('aria-hidden', 'true');
				tabs[itemIndex].setAttribute('role', 'tab');
				tabs[itemIndex].setAttribute(
					'aria-controls',
					panels[itemIndex].id
				);
				tabs[itemIndex].setAttribute('aria-selected', String(selected));
				tabs[itemIndex].tabIndex = selected ? 0 : -1;
				panels[itemIndex].hidden = !selected;
			});

			if (moveFocus) {
				tabs[index].focus();
			}
		}

		controls.forEach((control, index) => {
			control.addEventListener('change', () => selectTab(index, false));
			tabs[index].addEventListener('click', () =>
				selectTab(index, false)
			);
			tabs[index].addEventListener('keydown', (event) => {
				let nextIndex = index;
				if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
					nextIndex = (index + 1) % tabs.length;
				} else if (
					event.key === 'ArrowLeft' ||
					event.key === 'ArrowUp'
				) {
					nextIndex = (index - 1 + tabs.length) % tabs.length;
				} else if (event.key === 'Home') {
					nextIndex = 0;
				} else if (event.key === 'End') {
					nextIndex = tabs.length - 1;
				} else {
					return;
				}

				event.preventDefault();
				selectTab(nextIndex, true);
			});
		});

		const selectedIndex = Math.max(
			0,
			controls.findIndex((control) => control.checked)
		);
		selectTab(selectedIndex, false);
		container.dataset.simpleSitemapTabsReady = 'true';
	}

	function initialize() {
		document
			.querySelectorAll('.simple-sitemap-container.tab-enabled')
			.forEach(initializeTabset);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initialize);
	} else {
		initialize();
	}
})();
