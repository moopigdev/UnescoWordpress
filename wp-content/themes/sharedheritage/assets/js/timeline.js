/**
 * Timeline (Zone 4) horizontal scrolling — page-timeline.php · DESIGN-SPEC §6.4.
 *  - native: trackpad / shift+wheel / touch swipe / keyboard arrows on the focused scroller
 *  - mouse wheel: once the page is scrolled to its bottom (or has no vertical scroll), vertical wheel
 *    moves the timeline sideways; at either end the page scrolls normally again
 *  - mouse drag on the illustration / images (text stays selectable)
 *  - ‹ › buttons step one card (the card lands where the first card starts), hidden at the ends
 * Without JS the scroller still scrolls natively and the buttons stay hidden.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-sh-timeline]' );
	var vp = root ? root.querySelector( '[data-sh-timeline-viewport]' ) : null;
	if ( ! vp ) {
		return;
	}
	var prev = root.querySelector( '[data-sh-timeline-prev]' );
	var next = root.querySelector( '[data-sh-timeline-next]' );
	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function maxScroll() {
		return Math.max( 0, vp.scrollWidth - vp.clientWidth );
	}

	function update() {
		var x = vp.scrollLeft;
		var m = maxScroll();
		if ( prev ) {
			prev.hidden = x <= 1;
		}
		if ( next ) {
			next.hidden = x >= m - 1;
		}
	}

	// Scroll positions that put each card where the first card starts.
	function stops() {
		var items = root.querySelectorAll( '[data-sh-timeline-stop]' );
		if ( ! items.length ) {
			return [];
		}
		var base = items[ 0 ].offsetLeft;
		var m = maxScroll();
		return Array.prototype.map.call( items, function ( el ) {
			return Math.min( m, Math.max( 0, el.offsetLeft - base ) );
		} );
	}

	function go( dir ) {
		var x = vp.scrollLeft;
		var list = stops();
		var target = dir > 0 ? maxScroll() : 0;
		var i;
		if ( dir > 0 ) {
			for ( i = 0; i < list.length; i++ ) {
				if ( list[ i ] > x + 2 ) {
					target = list[ i ];
					break;
				}
			}
		} else {
			for ( i = list.length - 1; i >= 0; i-- ) {
				if ( list[ i ] < x - 2 ) {
					target = list[ i ];
					break;
				}
			}
		}
		vp.scrollTo( { left: target, behavior: reduceMotion ? 'auto' : 'smooth' } );
	}

	if ( prev ) {
		prev.addEventListener( 'click', function () {
			go( -1 );
		} );
	}
	if ( next ) {
		next.addEventListener( 'click', function () {
			go( 1 );
		} );
	}

	var ticking = false;
	function onScroll() {
		if ( ! ticking ) {
			ticking = true;
			window.requestAnimationFrame( function () {
				ticking = false;
				update();
			} );
		}
	}
	vp.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', onScroll );
	window.addEventListener( 'load', update );

	// Vertical wheel → horizontal, only when the page itself cannot go further that way.
	vp.addEventListener(
		'wheel',
		function ( e ) {
			if ( e.ctrlKey || e.shiftKey || Math.abs( e.deltaX ) >= Math.abs( e.deltaY ) ) {
				return;
			}
			var unit = 1 === e.deltaMode ? 16 : ( 2 === e.deltaMode ? vp.clientWidth : 1 );
			var dy = e.deltaY * unit;
			var doc = document.documentElement;
			var atBottom = window.innerHeight + window.pageYOffset >= doc.scrollHeight - 2;
			var x = vp.scrollLeft;
			if ( atBottom && ( ( dy > 0 && x < maxScroll() - 1 ) || ( dy < 0 && x > 1 ) ) ) {
				e.preventDefault();
				vp.scrollLeft = x + dy;
			}
		},
		{ passive: false }
	);

	// Mouse drag (not on text, so card text can still be selected).
	var drag = null;
	var dragged = false;
	vp.addEventListener( 'pointerdown', function ( e ) {
		dragged = false;
		if ( 'mouse' !== e.pointerType || 0 !== e.button || ( e.target.closest && e.target.closest( '.sh-tl-card__text' ) ) ) {
			return;
		}
		drag = { x: e.clientX, left: vp.scrollLeft, id: e.pointerId };
	} );
	vp.addEventListener( 'pointermove', function ( e ) {
		if ( ! drag ) {
			return;
		}
		var dx = e.clientX - drag.x;
		if ( ! dragged && Math.abs( dx ) > 5 ) {
			dragged = true;
			root.classList.add( 'is-dragging' );
			try {
				vp.setPointerCapture( drag.id );
			} catch ( err ) {
				// Pointer already released.
			}
		}
		if ( dragged ) {
			vp.scrollLeft = drag.left - dx;
		}
	} );
	function endDrag() {
		drag = null;
		root.classList.remove( 'is-dragging' );
	}
	vp.addEventListener( 'pointerup', endDrag );
	vp.addEventListener( 'pointercancel', endDrag );
	// A drag that ends on a link must not open it.
	vp.addEventListener(
		'click',
		function ( e ) {
			if ( dragged ) {
				e.preventDefault();
				e.stopPropagation();
				dragged = false;
			}
		},
		true
	);

	update();
}() );
