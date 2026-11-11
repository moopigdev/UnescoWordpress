/**
 * Zone 5 page — mobile ZONE bar (template-parts/zone4/zone-bar.php): when the bar image is wider than its box
 * (≤767px, see taxonomy-zone-zone-5.css) start scrolled so the current zone tab (data-sh-zone, 5 equal segments) is
 * centred, and make the scrollable box keyboard-reachable. Card/History hover is CSS only.
 */
( function () {
	'use strict';

	var view = document.querySelector( '[data-sh-zbar]' );
	if ( ! view ) {
		return;
	}

	function place() {
		var number = parseInt( view.getAttribute( 'data-sh-zone' ), 10 );
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
