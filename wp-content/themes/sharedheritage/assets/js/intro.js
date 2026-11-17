/**
 * Introduction page (DESIGN-SPEC §9.4 E · D50): one page, four chapters switched in place.
 * - Buttons 1–4 and ‹ › show one chapter (map + content) and hide the others; header / heading / buttons stay.
 * - URL hash #1–#4 = chapter (open with a hash · browser back/forward switch chapters) · no or bad hash = chapter 1.
 * - Animated images (data-sh-anim) load only once their chapter is shown and near the viewport; not at all with
 *   prefers-reduced-motion (the still stays).
 * - Without JS every chapter is visible one after another (the page is not broken).
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-sh-intro]' );
	if ( ! root ) {
		return;
	}

	var stage = root.querySelector( '[data-sh-stage]' );
	var chapters = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-chapter]' ) );
	var tabs = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-tab]' ) );
	var prev = root.querySelector( '[data-sh-prev]' );
	var next = root.querySelector( '[data-sh-next]' );
	var down = root.querySelector( '[data-sh-intro-down]' );
	var count = chapters.length;
	if ( ! stage || ! count ) {
		return;
	}

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var current = 0;
	var wanted = 0; // chapter being shown (current changes after the fade)
	var fadeTimer = 0;
	var observer = null;

	function fromHash() {
		var n = parseInt( window.location.hash.replace( '#', '' ), 10 );
		return n >= 1 && n <= count ? n : 1;
	}

	function loadAnimations( chapter ) {
		var imgs = chapter.querySelectorAll( 'img[data-sh-anim]' );
		if ( reduceMotion || ! imgs.length ) {
			return;
		}
		imgs.forEach( function ( img ) {
			if ( ! ( 'IntersectionObserver' in window ) ) {
				img.src = img.getAttribute( 'data-sh-anim' );
				img.removeAttribute( 'srcset' );
				img.removeAttribute( 'data-sh-anim' );
				return;
			}
			if ( ! observer ) {
				observer = new IntersectionObserver( function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( ! entry.isIntersecting ) {
							return;
						}
						var el = entry.target;
						observer.unobserve( el );
						el.removeAttribute( 'srcset' );
						el.removeAttribute( 'sizes' );
						el.src = el.getAttribute( 'data-sh-anim' );
						el.removeAttribute( 'data-sh-anim' );
					} );
				}, { rootMargin: '400px 0px' } );
			}
			observer.observe( img );
		} );
	}

	function setControls( n ) {
		tabs.forEach( function ( tab ) {
			var on = parseInt( tab.getAttribute( 'data-sh-tab' ), 10 ) === n;
			tab.classList.toggle( 'is-active', on );
			tab.setAttribute( 'aria-selected', on ? 'true' : 'false' );
		} );
		if ( prev ) {
			prev.hidden = false;
			prev.classList.toggle( 'is-off', n <= 1 );
			prev.tabIndex = n <= 1 ? -1 : 0;
		}
		if ( next ) {
			next.hidden = false;
			next.classList.toggle( 'is-off', n >= count );
			next.tabIndex = n >= count ? -1 : 0;
		}
	}

	function reveal( n, scroll ) {
		document.documentElement.className = document.documentElement.className.replace( /\bsh-intro-(js|c\d+)\b/g, '' ).trim();
		chapters.forEach( function ( el, i ) {
			el.hidden = i !== n - 1;
			el.classList.remove( 'is-fading' );
		} );
		current = n;
		setControls( n );
		loadAnimations( chapters[ n - 1 ] );
		if ( scroll && stage.getBoundingClientRect().top < 0 ) {
			stage.scrollIntoView( { block: 'start', behavior: 'auto' } );
		}
	}

	// Fade out (0.2s) → swap → fade in (0.2s): the 0.4s cross-fade of §9.4 E.3.
	function show( n, scroll ) {
		window.clearTimeout( fadeTimer );
		wanted = n;
		if ( ! current || reduceMotion || n === current ) {
			reveal( n, scroll );
			return;
		}
		chapters[ current - 1 ].classList.add( 'is-fading' );
		setControls( n );
		fadeTimer = window.setTimeout( function () {
			reveal( n, scroll );
			chapters[ n - 1 ].classList.add( 'is-fading' );
			window.requestAnimationFrame( function () {
				window.requestAnimationFrame( function () {
					chapters[ n - 1 ].classList.remove( 'is-fading' );
				} );
			} );
		}, 200 );
	}

	function go( n ) {
		if ( n < 1 || n > count || n === wanted ) {
			return;
		}
		if ( window.location.hash !== '#' + n && window.history && window.history.pushState ) {
			window.history.pushState( null, '', '#' + n ); // back / forward step through the chapters
		}
		show( n, true );
	}

	tabs.forEach( function ( tab ) {
		tab.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			go( parseInt( tab.getAttribute( 'data-sh-tab' ), 10 ) );
		} );
	} );
	if ( prev ) {
		prev.addEventListener( 'click', function () {
			go( wanted - 1 );
		} );
	}
	if ( next ) {
		next.addEventListener( 'click', function () {
			go( wanted + 1 );
		} );
	}
	window.addEventListener( 'popstate', function () {
		if ( fromHash() !== wanted ) {
			show( fromHash(), false );
		}
	} );
	window.addEventListener( 'hashchange', function () {
		if ( fromHash() !== wanted ) {
			show( fromHash(), false );
		}
	} );

	if ( down ) {
		down.addEventListener( 'click', function ( event ) {
			var target = document.getElementById( 'sh-intro-env' );
			if ( target ) {
				event.preventDefault();
				target.scrollIntoView( { block: 'start', behavior: reduceMotion ? 'auto' : 'smooth' } );
			}
		} );
	}

	reveal( fromHash(), false );
}() );
