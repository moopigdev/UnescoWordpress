/**
 * Zone page.
 *  1. Text block (§4.3b · BACKLOG #8): "Read more >>" swaps the short intro for the full text and the button reads
 *     "Hide >>" (data-sh-label-hide); "Hide >>" folds it back. The height animates over 0.3s (Figma smart-animate).
 *  2. Mobile banner strip: when the ZONE 1–5 image is wider than its box (≤767px, see zone.css),
 * start scrolled so the active zone's tab (data-zone on .sh-zone, 5 equal segments) is centred, and make
 * the scrollable box keyboard-reachable.
 */
( function () {
	'use strict';

	var intro = document.querySelector( '.sh-zone__intro.has-more' );
	var toggle = intro ? intro.querySelector( '.sh-zone__intro-toggle' ) : null;
	var body = intro ? intro.querySelector( '.sh-zone__intro-body' ) : null;
	if ( toggle && body ) {
		var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var labelMore = toggle.textContent;
		var labelHide = intro.getAttribute( 'data-sh-label-hide' ) || labelMore;
		var timer = null;
		toggle.addEventListener( 'click', function () {
			var open = toggle.getAttribute( 'aria-expanded' ) !== 'true';
			var from = body.offsetHeight;
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.textContent = open ? labelHide : labelMore;
			intro.classList.toggle( 'is-open', open );
			window.clearTimeout( timer );
			body.classList.remove( 'is-animating' );
			body.style.height = '';
			if ( reduceMotion ) {
				return;
			}
			var to = body.offsetHeight;
			body.style.height = from + 'px';
			void body.offsetHeight; // commit the start height before transitioning
			body.classList.add( 'is-animating' );
			body.style.height = to + 'px';
			timer = window.setTimeout( function () {
				body.classList.remove( 'is-animating' );
				body.style.height = '';
			}, 300 );
		} );
	}

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
