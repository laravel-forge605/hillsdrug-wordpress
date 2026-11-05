/**
 * Keeps HFE header/footer atomic CSS after the Elementor editor's injected base styles.
 *
 * Inside the editor preview iframe, Elementor renders atomic base styles (e.g.
 * `.elementor .e-flexbox-base{flex-direction:row;padding:10px}`) into <head> after the
 * page loads. They share specificity with the templates' local styles, so source order
 * lets them override the header/footer Flexbox settings. Elementor re-appends nested
 * documents' `elementor-post-{id}-css` links for this reason, but not their atomic
 * `local-{id}-*-css` links, so this script does that for HFE templates.
 *
 * @since 2.9.6
 */
( function () {
	var config = window.hfeEditorPreviewStyles;

	if ( ! config || ! config.templateIds || ! config.templateIds.length || ! window.MutationObserver ) {
		return;
	}

	var head = document.head;
	var prefixes = config.templateIds.map( function ( id ) {
		return 'local-' + id + '-';
	} );

	function isTemplateLink( node ) {
		if ( ! node || 'LINK' !== node.nodeName || 'stylesheet' !== node.rel || ! node.id ) {
			return false;
		}

		if ( '-css' !== node.id.slice( -4 ) ) {
			return false;
		}

		return prefixes.some( function ( prefix ) {
			return 0 === node.id.indexOf( prefix );
		} );
	}

	function moveLinksToEnd() {
		var links = Array.prototype.filter.call( head.querySelectorAll( 'link[rel="stylesheet"]' ), isTemplateLink );

		if ( ! links.length ) {
			return;
		}

		// Already the trailing elements of <head>; nothing to do. Also stops the observer from looping on our own appends.
		var tail = head.lastElementChild;
		var trailing = 0;

		while ( tail && isTemplateLink( tail ) ) {
			trailing++;
			tail = tail.previousElementSibling;
		}

		if ( trailing === links.length ) {
			return;
		}

		links.forEach( function ( link ) {
			head.appendChild( link );
		} );
	}

	new MutationObserver( moveLinksToEnd ).observe( head, { childList: true } );

	moveLinksToEnd();
}() );
