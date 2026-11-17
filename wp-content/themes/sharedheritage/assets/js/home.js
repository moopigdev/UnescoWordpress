/**
 * Home behaviour (front-page.php) — DESIGN-SPEC §3.5 · §6.1 · §6.2.
 *  1. Navigator dots: scroll in 0.3s to the top / ZONE bar / Trails / Zone 4–5 (anchor href = no-JS fallback).
 *  2. Trails "Read more >>" ↔ "Hide >>": open / fold the two long paragraphs (0.3s).
 *  2b. "Your animal guide is..." (D48 · §9.3): random card · Read more / Hide.
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

	/* 2b · "Your animal guide is..." (D48 · §9.3): random card · re-pick never repeats the one on show ·
	   Read more >> / Hide >> · every page load starts at "?" (nothing stored) */
	( function () {
		var root = document.querySelector( '[data-sh-guide]' );
		if ( ! root ) {
			return;
		}
		var templates = Array.prototype.slice.call( root.querySelectorAll( 'template[data-sh-guide-card]' ) );
		var picks = Array.prototype.slice.call( root.querySelectorAll( '[data-sh-guide-pick]' ) );
		var card = root.querySelector( 'button.sh-guide__card' );
		var result = root.querySelector( '[data-sh-guide-result]' );
		var slot = root.querySelector( '[data-sh-guide-slot]' );
		var live = root.querySelector( '[data-sh-guide-live]' );
		if ( ! templates.length || ! card || ! result || ! slot || ! window.HTMLTemplateElement ) {
			return;
		}
		var labelMore = root.getAttribute( 'data-label-more' ) || '';
		var labelHide = root.getAttribute( 'data-label-hide' ) || labelMore;
		var labelAgain = root.getAttribute( 'data-label-again' ) || '';
		var current = null;
		var busy = false;
		var backImg = card.querySelector( 'img' );
		var back = backImg ? { src: backImg.getAttribute( 'src' ), srcset: backImg.getAttribute( 'srcset' ), sizes: backImg.getAttribute( 'sizes' ) } : null;

		// uniform integer 0..m-1 from crypto (rejection sampling · no modulo bias); Math.random only if crypto is missing
		function rand( m ) {
			var c = window.crypto || window.msCrypto;
			if ( ! c || ! c.getRandomValues ) {
				return Math.floor( Math.random() * m );
			}
			var buf = new Uint32Array( 1 );
			var limit = Math.floor( 4294967296 / m ) * m;
			do {
				c.getRandomValues( buf );
			} while ( buf[ 0 ] >= limit );
			return buf[ 0 ] % m;
		}

		function nextIndex() {
			var n = templates.length;
			if ( current === null ) {
				return rand( n );
			}
			if ( n < 2 ) {
				return current;
			}
			var r = rand( n - 1 ); // the 16 others
			return r >= current ? r + 1 : r;
		}

		// the 17 big pictures are fetched when the visitor shows interest (hover / focus / touch) — not at first paint
		var warmed = false;
		function warm() {
			if ( warmed ) {
				return;
			}
			warmed = true;
			templates.forEach( function ( tpl ) {
				var src = tpl.content.querySelector( 'img' );
				if ( src ) {
					var im = new Image();
					im.sizes = '230px';
					if ( src.getAttribute( 'srcset' ) ) {
						im.srcset = src.getAttribute( 'srcset' );
					}
					im.src = src.getAttribute( 'src' );
				}
			} );
		}

		function cardImage() {
			var img = card.querySelector( 'img' );
			if ( ! img ) {
				img = document.createElement( 'img' );
				img.className = 'sh-guide__card-img';
				img.alt = '';
				var fb = card.querySelector( '.sh-guide__card-fallback' );
				if ( fb ) {
					fb.remove();
				}
				card.appendChild( img );
			}
			return img;
		}

		function setCardImage( img, big ) {
			img.removeAttribute( 'srcset' );
			if ( big.getAttribute( 'srcset' ) ) {
				img.setAttribute( 'srcset', big.getAttribute( 'srcset' ) );
			}
			img.setAttribute( 'sizes', '230px' ); // same candidate as the big card → one download
			img.setAttribute( 'loading', 'eager' );
			img.setAttribute( 'src', big.getAttribute( 'src' ) );
		}

		function whenLoaded( img, done ) {
			if ( img.complete ) {
				done();
				return;
			}
			var fired = false;
			var fin = function () {
				if ( ! fired ) {
					fired = true;
					done();
				}
			};
			img.addEventListener( 'load', fin, { once: true } );
			img.addEventListener( 'error', fin, { once: true } );
			window.setTimeout( fin, 500 );
		}

		function wireToggle() {
			var btn = slot.querySelector( '.sh-guide__toggle-btn' );
			if ( ! btn ) {
				return;
			}
			btn.addEventListener( 'click', function () {
				var open = ! result.classList.contains( 'is-expanded' );
				result.classList.toggle( 'is-expanded', open );
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
				btn.textContent = open ? labelHide : labelMore;
			} );
		}

		// swap in the content of card i (collapsed state)
		function render( i ) {
			var tpl = templates[ i ];
			var frag = tpl.content.cloneNode( true );
			var big = frag.querySelector( '.sh-guide__big-img' );
			if ( big ) {
				big.setAttribute( 'loading', 'eager' );
			}
			slot.textContent = '';
			slot.appendChild( frag );
			wireToggle();
			result.classList.remove( 'is-expanded' );
			result.classList.add( 'is-open' );
			current = i;
			card.setAttribute( 'aria-label', labelAgain );
			var name = slot.querySelector( '.sh-guide__name' );
			var words = slot.querySelector( '.sh-guide__words' );
			if ( live ) {
				live.textContent = ( name ? name.textContent : '' ) + ( words ? '. ' + words.textContent : '' );
			}
		}

		function pick() {
			if ( busy ) {
				return;
			}
			warm();
			var i = nextIndex();
			var big = templates[ i ].content.querySelector( '.sh-guide__big-img' );
			var img = cardImage();
			if ( ! big ) {
				// a card without a picture must never leave another animal's picture on the small card → back to "?"
				if ( back ) {
					img.setAttribute( 'sizes', back.sizes || '120px' );
					if ( back.srcset ) {
						img.setAttribute( 'srcset', back.srcset );
					} else {
						img.removeAttribute( 'srcset' );
					}
					img.setAttribute( 'src', back.src );
				}
				render( i );
				return;
			}
			if ( reduceMotion ) {
				setCardImage( img, big );
				img.alt = '';
				render( i );
				return;
			}
			busy = true;
			img.classList.add( 'is-flip-out' ); // rotateY 0 → 90° (0.2s ease-in)
			window.setTimeout( function () {
				setCardImage( img, big );
				whenLoaded( img, function () {
					render( i );
					img.classList.remove( 'is-flip-out' );
					img.classList.add( 'is-flip-in' ); // 90° → 0 (0.2s ease-out)
					window.setTimeout( function () {
						img.classList.remove( 'is-flip-in' );
						busy = false;
					}, 200 );
				} );
			}, 200 );
		}

		picks.forEach( function ( el ) {
			el.addEventListener( 'click', pick );
			el.addEventListener( 'pointerenter', warm, { once: true } );
			el.addEventListener( 'focus', warm, { once: true } );
			el.addEventListener( 'touchstart', warm, { once: true, passive: true } );
		} );
	}() );

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
