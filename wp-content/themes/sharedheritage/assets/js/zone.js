/**
 * Zone page — mobile banner strip: when the ZONE 1–5 image is wider than its box (≤767px, see zone.css),
 * start scrolled so the active zone's tab (data-zone on .sh-zone, 5 equal segments) is centred, and make
 * the scrollable box keyboard-reachable.
 */
( function () {
	'use strict';

	var zone = document.querySelector( '.sh-zone[data-zone]' );
	var view = document.querySelector( '[data-sh-zone-banner]' );
	if ( ! zone || ! view ) {
		return;
	}

	function place() {
		var number = parseInt( zone.getAttribute( 'data-zone' ), 10 );
		var scrolls = view.scrollWidth > view.clientWidth + 1;
		if ( scrolls ) {
			view.setAttribute( 'tabindex', '0' );
			view.setAttribute( 'role', 'group' );
			view.setAttribute( 'aria-label', view.getAttribute( 'data-sh-label' ) || '' );
		} else {
			view.removeAttribute( 'tabindex' );
			view.removeAttribute( 'role' );
			view.removeAttribute( 'aria-label' );
		}
		if ( scrolls && number >= 1 && number <= 5 ) {
			view.scrollLeft = ( ( number - 0.5 ) / 5 ) * view.scrollWidth - view.clientWidth / 2;
		}
	}

	var lastWidth = 0;
	function onResize() {
		if ( window.innerWidth !== lastWidth ) {
			lastWidth = window.innerWidth;
			place();
		}
	}

	onResize();
	window.addEventListener( 'load', place );
	window.addEventListener( 'resize', onResize );
} )();
