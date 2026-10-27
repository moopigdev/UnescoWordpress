/**
 * Intro page map slider (template-parts/intro/map.php).
 * Number buttons / ‹ › arrows switch the slide text under the map (no popup — D21). Without JS slide 1 is shown.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-sh-map]' );
	if ( ! root ) {
		return;
	}

	var pins = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-map-pin]' ) );
	var slides = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-map-slide]' ) );
	var count = slides.length;
	var current = 0;

	function show( index ) {
		if ( ! count ) {
			return;
		}
		current = ( index + count ) % count;
		slides.forEach( function ( el, i ) {
			el.hidden = i !== current;
		} );
		pins.forEach( function ( el, i ) {
			var on = i === current;
			el.classList.toggle( 'is-active', on );
			el.setAttribute( 'aria-current', on ? 'true' : 'false' );
		} );
	}

	pins.forEach( function ( pin, i ) {
		pin.addEventListener( 'click', function () {
			show( i );
		} );
	} );

	var prev = root.querySelector( '[data-sh-map-prev]' );
	var next = root.querySelector( '[data-sh-map-next]' );
	if ( prev ) {
		prev.addEventListener( 'click', function () {
			show( current - 1 );
		} );
	}
	if ( next ) {
		next.addEventListener( 'click', function () {
			show( current + 1 );
		} );
	}
}() );
