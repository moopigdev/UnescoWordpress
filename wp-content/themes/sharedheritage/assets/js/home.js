/**
 * Home behaviour (front-page.php) — DESIGN-SPEC §3.5 · §6.1 · §6.2.
 *  1. Navigator dots: scroll to the section in 0.3s (anchor href = no-JS fallback).
 *  2. Trails "Read more >>": show / hide the two long paragraphs.
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

	dots.forEach( function ( dot ) {
		dot.addEventListener( 'click', function ( e ) {
			var id = ( dot.getAttribute( 'href' ) || '' ).replace( /^#/, '' );
			var section = id ? document.getElementById( id ) : null;
			if ( ! section || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey ) {
				return;
			}
			e.preventDefault();
			scrollToY( section.getBoundingClientRect().top + window.pageYOffset );
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', '#' + id );
			}
		} );
	} );

	/* current section → aria-current on its dot (no visual change: Figma has one dot style only) */
	if ( dots.length && 'IntersectionObserver' in window ) {
		var seen = {};
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( en ) {
				seen[ en.target.id ] = en.isIntersecting ? en.boundingClientRect.top : null;
			} );
			var current = null;
			dots.forEach( function ( dot ) {
				var id = dot.getAttribute( 'href' ).replace( /^#/, '' );
				if ( seen[ id ] !== null && seen[ id ] !== undefined ) {
					current = id;
				}
				dot.removeAttribute( 'aria-current' );
			} );
			dots.forEach( function ( dot ) {
				if ( current && dot.getAttribute( 'href' ) === '#' + current ) {
					dot.setAttribute( 'aria-current', 'true' );
				}
			} );
		}, { rootMargin: '-40% 0px -40% 0px' } );
		dots.forEach( function ( dot ) {
			var target = document.getElementById( dot.getAttribute( 'href' ).replace( /^#/, '' ) );
			if ( target ) {
				io.observe( target );
			}
		} );
	}

	/* 2 · trails read more */
	var toggle = document.querySelector( '.sh-home-trails__toggle' );
	var more = document.getElementById( 'sh-home-trails-more' );
	if ( toggle && more ) {
		toggle.addEventListener( 'click', function () {
			var open = toggle.getAttribute( 'aria-expanded' ) !== 'true';
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			more.classList.toggle( 'is-open', open );
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
		// `loop` is set (so the poster/first frame never sticks); the first pass is the animation → reveal at its end.
		video.addEventListener( 'timeupdate', function () {
			if ( video.duration && video.currentTime >= video.duration - 0.25 ) {
				reveal();
			}
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
