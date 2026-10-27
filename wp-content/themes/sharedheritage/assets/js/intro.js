/**
 * Intro page map slider (template-parts/intro/map.php).
 * Number buttons / arrows switch the slide text under the map; a number button also opens that slide's popup
 * (Figma: pin ON_CLICK → overlay). Close: × button · Escape · click outside the popup. Without JS slide 1 is shown.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-sh-map]' );
	if ( ! root ) {
		return;
	}

	var pins = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-map-pin]' ) );
	var slides = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-map-slide]' ) );
	var popups = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-map-popup]' ) );
	var count = slides.length;
	var current = 0;
	var openPopup = null;
	var opener = null;

	function closePopup( restoreFocus ) {
		if ( ! openPopup ) {
			return;
		}
		openPopup.hidden = true;
		openPopup = null;
		if ( restoreFocus && opener ) {
			opener.focus();
		}
		opener = null;
	}

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
		if ( openPopup && Number( openPopup.getAttribute( 'data-sh-map-popup' ) ) !== current ) {
			closePopup( false );
		}
	}

	pins.forEach( function ( pin, i ) {
		pin.addEventListener( 'click', function () {
			var same = openPopup && Number( openPopup.getAttribute( 'data-sh-map-popup' ) ) === i;
			show( i );
			closePopup( false );
			if ( same ) {
				return;
			}
			popups.forEach( function ( p ) {
				if ( Number( p.getAttribute( 'data-sh-map-popup' ) ) === i ) {
					p.hidden = false;
					openPopup = p;
					opener = pin;
					var close = p.querySelector( '[data-sh-map-close]' );
					if ( close ) {
						close.focus();
					}
				}
			} );
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

	popups.forEach( function ( p ) {
		var close = p.querySelector( '[data-sh-map-close]' );
		if ( close ) {
			close.addEventListener( 'click', function () {
				closePopup( true );
			} );
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			closePopup( true );
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( openPopup && ! openPopup.contains( e.target ) && ! ( e.target.closest && e.target.closest( '[data-sh-map-pin]' ) ) ) {
			closePopup( false );
		}
	} );
}() );
