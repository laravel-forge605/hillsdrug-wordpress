( function ( root, factory ) {
	'use strict';

	var api = factory( root );

	if ( 'object' === typeof module && module.exports ) {
		module.exports = api;
	} else {
		root.wpgoIntroductoryPricing = api;
		api.boot();
	}
}( 'undefined' !== typeof window ? window : this, function ( root ) {
	'use strict';

	function normaliseTitle( value ) {
		return String( value || '' ).toLowerCase().replace( /\s+/g, ' ' ).trim();
	}

	function getLicenceQuantity( title ) {
		var normalised = normaliseTitle( title );
		var match;

		if ( /\bsingle site\b/.test( normalised ) ) {
			return '1';
		}

		match = normalised.match( /\b(\d+)\s+sites?\b/ );

		return match ? match[ 1 ] : '';
	}

	function addCouponToUrl( currentUrl, coupon ) {
		var url = new URL( currentUrl );

		url.searchParams.set( 'billing_cycle', 'annual' );
		url.searchParams.set( 'coupon', coupon );

		return url.toString();
	}

	function replacePlaceholder( template, price ) {
		return String( template || '' ).replace( '%s', price );
	}

	function getTierForCard( card, tiers ) {
		var heading = card.querySelector( '.fs-plan-title' );
		var quantity = heading ? getLicenceQuantity( heading.textContent ) : '';

		return quantity && tiers[ quantity ] ? tiers[ quantity ] : null;
	}

	function isAnnualCard( card ) {
		var cycle = card.querySelector( '.fs-selected-pricing-cycle' );

		return ! cycle || /annual/i.test( cycle.textContent );
	}

	function decorateCard( card, tier, labels ) {
		var amount = card.querySelector( '.fs-selected-pricing-amount' );
		var cycle = card.querySelector( '.fs-selected-pricing-cycle' );
		var terms;
		var regularPrice;
		var firstYearPrice;
		var firstYearLabel;
		var renewalLabel;

		if ( ! amount || ! isAnnualCard( card ) ) {
			return false;
		}

		if ( ! amount.querySelector( '.wpgo-introductory-pricing__price' ) ) {
			amount.textContent = '';
			amount.setAttribute(
				'aria-label',
				tier.firstYear + ' ' + labels.firstYear.toLowerCase() + '. ' +
				replacePlaceholder( labels.renews, tier.renewal )
			);

			terms = root.document.createElement( 'span' );
			terms.className = 'wpgo-introductory-pricing__price';
			terms.setAttribute( 'aria-hidden', 'true' );
			regularPrice = root.document.createElement( 'del' );
			regularPrice.textContent = tier.renewal;
			firstYearPrice = root.document.createElement( 'strong' );
			firstYearPrice.textContent = tier.firstYear;
			terms.appendChild( regularPrice );
			terms.appendChild( firstYearPrice );
			amount.appendChild( terms );
		}

		terms = card.querySelector( '.wpgo-introductory-pricing__terms' );
		if ( ! terms ) {
			terms = root.document.createElement( 'p' );
			terms.className = 'wpgo-introductory-pricing__terms';
			firstYearLabel = root.document.createElement( 'strong' );
			firstYearLabel.textContent = labels.firstYear;
			renewalLabel = root.document.createElement( 'span' );
			renewalLabel.textContent = replacePlaceholder( labels.renews, tier.renewal );
			terms.appendChild( firstYearLabel );
			terms.appendChild( renewalLabel );

			if ( cycle ) {
				cycle.insertAdjacentElement( 'afterend', terms );
			} else {
				amount.insertAdjacentElement( 'afterend', terms );
			}
		}

		card.setAttribute( 'data-wpgo-introductory-pricing', 'true' );

		return true;
	}

	function boot() {
		var config = root.wpgoIntroductoryPricingConfig;
		var scheduled = false;
		var observer;

		if ( ! config || ! config.tiers || ! root.document ) {
			return;
		}

		function decorate() {
			var cards = root.document.querySelectorAll( '#fs_pricing_wrapper li.fs-package' );

			scheduled = false;
			cards.forEach( function ( card ) {
				var tier = getTierForCard( card, config.tiers );

				if ( tier ) {
					decorateCard( card, tier, config.labels );
				}
			} );
		}

		function scheduleDecoration() {
			if ( scheduled ) {
				return;
			}

			scheduled = true;
			( root.requestAnimationFrame || root.setTimeout )( decorate );
		}

		root.document.addEventListener( 'click', function ( event ) {
			var button = event.target.closest ? event.target.closest( '.fs-upgrade-button' ) : null;
			var card;
			var tier;
			var destination;

			if ( ! button ) {
				return;
			}

			card = button.closest( 'li.fs-package' );
			tier = card ? getTierForCard( card, config.tiers ) : null;

			if ( ! tier || ! tier.coupon ) {
				return;
			}

			destination = addCouponToUrl( root.location.href, tier.coupon );
			root.history.replaceState( root.history.state, '', destination );
		}, true );

		observer = new MutationObserver( scheduleDecoration );
		observer.observe( root.document.documentElement, { childList: true, subtree: true } );
		scheduleDecoration();
	}

	return {
		addCouponToUrl: addCouponToUrl,
		boot: boot,
		decorateCard: decorateCard,
		getLicenceQuantity: getLicenceQuantity,
		normaliseTitle: normaliseTitle
	};
} ) );
