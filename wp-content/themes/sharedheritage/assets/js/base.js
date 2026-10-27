/**
 * Base behaviour — every page (enqueued by inc/enqueue.php, deferred, in footer).
 *  - marks <html> with .sh-js so the mobile menu can collapse (without JS the menu stays open)
 *  - mobile menu toggle (Header #02)
 *  - Back button (template-parts/common/back-button.php): history back when the previous page is
 *    on this site, otherwise follow the link's href (DESIGN-SPEC §3.3 "click = BACK")
 */
( function () {
	'use strict';

	document.documentElement.classList.add( 'sh-js' );

	var toggle = document.querySelector( '.sh-nav-toggle' );
	var header = document.querySelector( '.sh-header' );
	if ( toggle && header ) {
		toggle.addEventListener( 'click', function () {
			var open = toggle.getAttribute( 'aria-expanded' ) !== 'true';
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			header.classList.toggle( 'sh-header--nav-open', open );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				header.classList.remove( 'sh-header--nav-open' );
				toggle.focus();
			}
		} );
	}

	function cameFromThisSite() {
		if ( ! document.referrer || window.history.length < 2 ) {
			return false;
		}
		try {
			return new URL( document.referrer ).origin === window.location.origin;
		} catch ( err ) {
			return false;
		}
	}

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest ? e.target.closest( '[data-sh-back]' ) : null;
		if ( ! link || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
			return;
		}
		if ( cameFromThisSite() ) {
			e.preventDefault();
			window.history.back();
		}
	} );
}() );
