/**
 * Home behaviour (front-page.php) — DESIGN-SPEC §3.5 · §6.1 · §6.2.
 *  1. Navigator dots: scroll in 0.3s to the top / ZONE bar / Trails / Zone 4–5 (anchor href = no-JS fallback).
 *  2. Trails "Read more >>" ↔ "Hide >>": open / fold the two long paragraphs (0.3s).
 *  3. Trails stage: the footprint video (walking.webm) plays once the section is on screen, then the 17 trail
 *     icons fade in (D12). No video support / reduced motion / error → icons straight away.
 */
( function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* 1 · navigator */
	var dots = Array.prototype.slice.call( document.querySelectorAll( '.sh-home-nav__dot[data-sh-scroll]' ) );

	function scrollToY( target ) {
		var start = window.pageYOffset;
		var max = document.documentElement.scrollHeight - window.innerHeight;
		var to = Math.max( 0, Math.min( target, max ) );
		if ( reduceMotion || Math.abs( to - start ) < 2 ) {
			window.scrollTo( 0, to );
			return;
		}
		var duration = 300; // Figma scroll-to 0.3s
		var t0 = null;
		function step( now ) {
			if ( t0 === null ) {
				t0 = now;
			}
			var p = Math.min( 1, ( now - t0 ) / duration );
			var e = p < 0.5 ? 2 * p * p : 1 - Math.pow( -2 * p + 2, 2 ) / 2; // ease-in-out
			window.scrollTo( 0, start + ( to - start ) * e );
			if ( p < 1 ) {
				window.requestAnimationFrame( step );
			}
		}
		window.requestAnimationFrame( step );
	}

	// Where a dot scrolls to: 'top' = 0 · otherwise the element's top minus its CSS scroll-margin-top (the zone bar keeps
	// some room above it so the whole ZONE 1–5 strip, animals included, is on screen — BACKLOG #20).
	function targetY( id ) {
		if ( id === 'top' ) {
			return 0;
		}
		var el = document.getElementById( id );
		if ( ! el ) {
			return null;
		}
		var margin = parseFloat( window.getComputedStyle( el ).scrollMarginTop ) || 0;
		return el.getBoundingClientRect().top + window.pageYOffset - margin;
	}

	function dotId( dot ) {
		return ( dot.getAttribute( 'href' ) || '' ).replace( /^#/, '' );
	}

	dots.forEach( function ( dot ) {
		dot.addEventListener( 'click', function ( e ) {
			var id = dotId( dot );
			var y = id ? targetY( id ) : null;
			if ( y === null || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
				return;
			}
			e.preventDefault();
			scrollToY( y );
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', '#' + id );
			}
		} );
	} );

	/* current section → aria-current on its dot (no visual change: Figma has one dot style only — §3.5) */
	if ( dots.length ) {
		var ticking = false;
		var markCurrent = function () {
			ticking = false;
			var line = window.innerHeight * 0.4;
			var current = null;
			dots.forEach( function ( dot ) {
				var y = targetY( dotId( dot ) );
				if ( y !== null && y - window.pageYOffset <= line ) {
					current = dot;
				}
			} );
			// Page end reached (the last target cannot scroll up to the line) → last dot.
			if ( window.pageYOffset >= document.documentElement.scrollHeight - window.innerHeight - 2 ) {
				current = dots[ dots.length - 1 ];
			}
			dots.forEach( function ( dot ) {
				if ( dot === current ) {
					dot.setAttribute( 'aria-current', 'true' );
				} else {
					dot.removeAttribute( 'aria-current' );
				}
			} );
		};
		var onScroll = function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( markCurrent );
			}
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll );
		markCurrent();
	}

	/* 2 · trails "Read more >>" ↔ "Hide >>" (§3.4 · BACKLOG #4): height animates over 0.3s (smart-animate) */
	var toggle = document.querySelector( '.sh-home-trails__toggle' );
	var more = document.getElementById( 'sh-home-trails-more' );
	if ( toggle && more ) {
		var holder = toggle.closest( '[data-sh-label-hide]' );
		var labelMore = toggle.textContent;
		var labelHide = ( holder && holder.getAttribute( 'data-sh-label-hide' ) ) || labelMore;
		var timer = null;
		toggle.addEventListener( 'click', function () {
			var open = toggle.getAttribute( 'aria-expanded' ) !== 'true';
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.textContent = open ? labelHide : labelMore;
			window.clearTimeout( timer );
			if ( reduceMotion ) {
				more.style.height = '';
				more.classList.toggle( 'is-open', open );
				return;
			}
			var from = open ? 0 : more.offsetHeight;
			more.classList.add( 'is-open' );
			var to = open ? more.scrollHeight : 0;
			more.style.height = from + 'px';
			void more.offsetHeight; // commit the start height before transitioning
			more.classList.add( 'is-animating' );
			more.style.height = to + 'px';
			timer = window.setTimeout( function () {
				more.classList.remove( 'is-animating' );
				more.style.height = '';
				more.classList.toggle( 'is-open', open );
			}, 300 );
		} );
	}

	/* 3 · trails stage: footprint video → icons */
	var stage = document.querySelector( '.sh-home-trails__stage[data-footprints]' );
	var video = stage ? stage.querySelector( '.sh-home-trails__video' ) : null;
	if ( stage && video ) {
		var revealed = false;
		var reveal = function () {
			if ( revealed ) {
				return;
			}
			revealed = true;
			stage.classList.add( 'is-revealed' );
			video.pause();
		};

		if ( reduceMotion || ! video.canPlayType || ! video.canPlayType( 'video/webm' ) || ! ( 'IntersectionObserver' in window ) ) {
			// Nothing to play (or animation unwanted): icons stay visible, video hidden.
			stage.classList.add( 'is-revealed' );
			return;
		}

		stage.classList.add( 'is-armed' );

		// The poster file is a mid-animation frame; Figma state 1 (frame 1470) is the first frame. Once the first frame
		// is decodable show it instead (poster stays as the fallback when the video cannot be decoded).
		var firstFrame = function () {
			// A seek makes the browser decode + paint the first frame (preload="metadata" alone leaves it blank).
			if ( video.readyState >= 1 && video.currentTime === 0 && ! video.dataset.shFirst ) {
				video.dataset.shFirst = '1';
				video.currentTime = 0.001;
			}
		};
		video.addEventListener( 'seeked', function () {
			if ( video.currentTime < 1 && video.readyState >= 2 ) {
				video.removeAttribute( 'poster' );
			}
		} );
		video.addEventListener( 'loadedmetadata', firstFrame );
		firstFrame();
		video.addEventListener( 'error', reveal );
		// `loop` is set (so the poster/first frame never sticks); the first pass is the animation → reveal at its end,
		// read from the file itself (video.duration — walking.webm 2026-11 = 3.85s). timeupdate fires only every
		// ~250ms, so a jump back to the start (the loop wrapped before the last-quarter-second check ran) also reveals.
		var lastTime = 0;
		video.addEventListener( 'timeupdate', function () {
			var t = video.currentTime;
			if ( ( video.duration && t >= video.duration - 0.25 ) || t + 0.5 < lastTime ) {
				reveal();
			}
			lastTime = t;
		} );
		video.addEventListener( 'ended', reveal );

		var watcher = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( en ) {
				if ( ! en.isIntersecting || revealed ) {
					return;
				}
				watcher.disconnect();
				var played = video.play();
				if ( played && typeof played.catch === 'function' ) {
					played.catch( reveal ); // autoplay blocked → show the icons
				}
			} );
		}, { threshold: 0.4 } );
		watcher.observe( stage );
	}
}() );
