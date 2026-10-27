/**
 * Timeline v3 (D26 · D27) — DESIGN-SPEC §6.4c. One number drives the page: c = continuous index of the object at
 * the centre (1…N, load 2.5 = Slides 31). Objects/● move by --c (CSS); everything else follows p_i:
 *   p_i = clamp(1 − |i − c|·376.6 / 188, 0, 1)      (at most one object has p > 0)
 *   object opacity 0.6 + 0.4p · scale 1 + (s_i − 1)p (CSS) · title α 0.62p² · photo α 0.40p · metadata α 0.47p
 *   settle: no input for 150 ms and |d| < 8 px → title/photo/metadata α → 1 in 400 ms ease-out.
 * Input: ‹ › 600 ms · ● / mini-map / object click: min(900, 400 + 80·|Δ|) ms · cubic-bezier(.4,0,.2,1) ·
 * drag (1 pitch = 376.6·k px) → snap 400 ms ease-out · horizontal wheel → idle 150 ms → snap 300 ms ·
 * ← → Home End. Reduced motion: no tween/settle (jumps, centre object full at once). Stage fit k: §4.5c.5.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-sh-tl]' );
	var stage = root && root.querySelector( '[data-sh-tl-stage]' );
	var track = root && root.querySelector( '[data-sh-tl-track]' );
	if ( ! root || ! stage || ! track ) {
		return;
	}
	var all = function ( sel ) {
		return Array.prototype.slice.call( root.querySelectorAll( sel ) );
	};
	var items = all( '[data-sh-tl-item]' );
	var infos = all( '[data-sh-tl-info]' );
	var dots = all( '[data-sh-tl-dot]' );
	var prevBtn = root.querySelector( '[data-sh-tl-prev]' );
	var nextBtn = root.querySelector( '[data-sh-tl-next]' );
	var live = root.querySelector( '[data-sh-tl-live]' );
	var N = items.length;
	if ( ! N ) {
		return;
	}

	var PITCH = 376.6;           // §4.5c.2 (design px)
	var HALF = 188;              // §6.4c p window
	var SETTLE_PX = 8;
	var IDLE_MS = 150;
	var SETTLE_MS = 400;
	var MQ_TABLET = window.matchMedia( '(max-width: 1279.98px)' );
	var MQ_MOBILE = window.matchMedia( '(max-width: 767.98px)' );
	var MQ_REDUCE = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function bezier( x1, y1, x2, y2 ) {
		function at( a, b, t ) {
			return ( ( ( 1 - 3 * b + 3 * a ) * t + ( 3 * b - 6 * a ) ) * t + 3 * a ) * t;
		}
		return function ( x ) {
			if ( x <= 0 || x >= 1 ) {
				return x <= 0 ? 0 : 1;
			}
			var t = x;
			for ( var n = 0; n < 8; n++ ) {
				var dx = at( x1, x2, t ) - x;
				var d = ( 3 * ( 1 - 3 * x2 + 3 * x1 ) * t + 2 * ( 3 * x2 - 6 * x1 ) ) * t + 3 * x1;
				if ( Math.abs( dx ) < 1e-6 || ! d ) {
					break;
				}
				t = Math.min( 1, Math.max( 0, t - dx / d ) );
			}
			return at( y1, y2, t );
		};
	}
	var EASE = bezier( 0.4, 0, 0.2, 1 );
	var EASE_OUT = bezier( 0, 0, 0.58, 1 );

	function clamp( v, lo, hi ) {
		return Math.min( hi, Math.max( lo, v ) );
	}

	var c = clamp( 2.5, 1, N );
	var pitchPx = PITCH;         // one object step on screen (px)
	var tween = null;
	var drag = null;
	var wheelAt = 0;
	var wheelPending = false;
	var lastInput = -Infinity;
	var settleAt = null;
	var settleIdx = 0;
	var settleS = 0;
	var announced = 0;
	var suppressUntil = 0;
	var raf = 0;
	var last = [];

	function px( v ) {
		return v.toFixed( 3 ) + 'px';
	}

	/* §4.5c.5 (desktop, k) · §4.5c.6 (tablet / mobile pitch and mini-map). */
	function layout() {
		var W = root.clientWidth;
		var s = root.style;
		if ( ! MQ_TABLET.matches ) {
			var header = document.querySelector( '.sh-header' );
			var headH = header ? header.getBoundingClientRect().height : 150;
			var k = clamp( Math.min( W / 1440, ( window.innerHeight - headH - 24 ) / 723 ), 0.62, 1 );
			var wide = W > 1440 * k + 0.5;
			var ext = wide ? ( W / k - 1440 ) / 2 : 0;
			pitchPx = PITCH * k;
			s.setProperty( '--k', k.toFixed( 5 ) );
			s.setProperty( '--pitch', PITCH + 'px' );
			s.setProperty( '--line-l', wide ? px( -ext ) : '31px' );
			s.setProperty( '--line-r', wide ? px( -ext ) : '28px' );
			s.setProperty( '--tr', Math.max( 1, 14 / ( 18 * k ) ).toFixed( 4 ) );
			s.setProperty( '--mr', Math.max( 1, 12 / ( 14 * k ) ).toFixed( 4 ) );
			s.setProperty( '--m0', '19px' );
			s.setProperty( '--mp', '51.6px' );
			s.setProperty( '--mfw', '195px' );
		} else {
			var mobile = MQ_MOBILE.matches;
			var pad = mobile ? 20 : 24;
			var mp = ( W - 2 * pad ) / N;
			pitchPx = ( mobile ? 0.62 : 0.2615 ) * W;
			s.setProperty( '--k', '1' );
			s.setProperty( '--pitch', px( pitchPx ) );
			s.setProperty( '--line-l', '0px' );
			s.setProperty( '--line-r', '0px' );
			s.setProperty( '--tr', '1' );
			s.setProperty( '--mr', '1' );
			s.setProperty( '--m0', px( mp / 2 ) );
			s.setProperty( '--mp', px( mp ) );
			s.setProperty( '--mfw', px( W * mp / pitchPx ) );
		}
		fitTitles();
	}

	/*
	 * Desktop: a title that needs more than 2 lines at 340 (Pod Duang: 3 lines would reach the mini-map) is widened
	 * to fit 2 lines [P · DEFERRED]. Tablet / mobile: the line sits under the tallest "title + 19 + active object".
	 */
	function fitTitles() {
		var s = root.style;
		var titles = items.map( function ( el ) {
			return el.querySelector( '.sh-tl__title' );
		} );
		if ( ! MQ_TABLET.matches ) {
			s.removeProperty( '--line-y' );
			titles.forEach( function ( t ) {
				if ( ! t ) {
					return;
				}
				t.style.removeProperty( '--tw' );
				var lh = parseFloat( window.getComputedStyle( t ).lineHeight ) || 22;
				for ( var w = 340, n = 0; t.offsetHeight > 2 * lh + 1 && n < 12; n++ ) {
					w += 20;
					t.style.setProperty( '--tw', w + 'px' );
				}
			} );
			// Metadata longer than the 7 lines the 723 stage was drawn for: the stage grows downwards (page scrolls).
			var metaH = 0;
			infos.forEach( function ( info ) {
				var m = info.querySelector( '.sh-tl__meta' );
				metaH = Math.max( metaH, m ? m.offsetHeight : 0 );
			} );
			s.setProperty( '--stage-h', Math.max( 723, Math.ceil( 496 + metaH + 24 ) ) + 'px' );
			return;
		}
		var mini = root.querySelector( '[data-sh-tl-mini]' );
		var top = mini ? mini.offsetTop + mini.offsetHeight + 12 : 140;
		var need = 0;
		items.forEach( function ( el, i ) {
			var obj = el.querySelector( '.sh-tl__obj' );
			var grow = parseFloat( el.style.getPropertyValue( '--s' ) ) || 1;
			var h = ( titles[ i ] ? titles[ i ].offsetHeight + 19 : 0 ) + ( obj ? obj.offsetHeight * grow : 0 );
			need = Math.max( need, h );
		} );
		s.setProperty( '--line-y', px( Math.ceil( top + need + 2 ) ) );
	}

	function set( el, key, name, value ) {
		if ( last[ key ] !== value ) {
			last[ key ] = value;
			el.style.setProperty( name, value );
		}
	}

	function toggle( el, key, cls, on ) {
		if ( last[ key ] !== on ) {
			last[ key ] = on;
			el.classList.toggle( cls, on );
		}
	}

	// α while moving → 1 as the settle progresses (s 0 → 1).
	function mix( a, s ) {
		return ( a + ( 1 - a ) * s ).toFixed( 4 );
	}

	function render() {
		root.style.setProperty( '--c', c.toFixed( 5 ) );
		for ( var i = 0; i < N; i++ ) {
			var idx = i + 1;
			var p = clamp( 1 - Math.abs( idx - c ) * PITCH / HALF, 0, 1 );
			var s = idx === settleIdx ? settleS : 0;
			var center = p >= 0.5;
			set( items[ i ], 'p' + i, '--p', p.toFixed( 4 ) );
			set( items[ i ], 't' + i, '--ta', mix( 0.62 * p * p, s ) );
			toggle( items[ i ], 'v' + i, 'is-vis', p > 0 );
			toggle( items[ i ], 'c' + i, 'is-center', center );
			if ( infos[ i ] ) {
				set( infos[ i ], 'pa' + i, '--pa', mix( 0.4 * p, s ) );
				set( infos[ i ], 'ma' + i, '--ma', mix( 0.47 * p, s ) );
				toggle( infos[ i ], 'iv' + i, 'is-vis', p > 0 );
				toggle( infos[ i ], 'ic' + i, 'is-center', center );
			}
			if ( last[ 'tab' + i ] !== center ) {
				last[ 'tab' + i ] = center;
				var link = items[ i ].querySelector( '.sh-tl__title a' );
				if ( link ) {
					link.tabIndex = center ? 0 : -1;
				}
			}
			if ( dots[ i ] ) {
				// Roving tabindex: only the ● nearest the centre is a Tab stop (the others may be off screen); ← → move.
				var stop = idx === clamp( Math.round( c ), 1, N );
				if ( last[ 'r' + i ] !== stop ) {
					last[ 'r' + i ] = stop;
					dots[ i ].tabIndex = stop ? 0 : -1;
					if ( stop && document.activeElement && document.activeElement.hasAttribute( 'data-sh-tl-dot' ) && root.contains( document.activeElement ) ) {
						dots[ i ].focus( { preventScroll: true } );
					}
				}
				var cur = Math.abs( c - idx ) < 0.5;
				if ( last[ 'd' + i ] !== cur ) {
					last[ 'd' + i ] = cur;
					if ( cur ) {
						dots[ i ].setAttribute( 'aria-current', 'true' );
					} else {
						dots[ i ].removeAttribute( 'aria-current' );
					}
				}
			}
		}
		if ( prevBtn ) {
			prevBtn.disabled = c <= 1 + 1e-6;
		}
		if ( nextBtn ) {
			nextBtn.disabled = c >= N - 1e-6;
		}
		root.classList.toggle( 'is-moving', !! ( tween || ( drag && drag.moved ) || wheelPending ) );
	}

	function announce( idx ) {
		if ( ! live || announced === idx || ! infos[ idx - 1 ] ) {
			return;
		}
		announced = idx;
		var info = infos[ idx - 1 ];
		var period = info.getAttribute( 'data-period' );
		live.textContent = info.getAttribute( 'data-title' ) + ( period ? ', ' + period : '' );
	}

	// Any user movement: α goes back to the position functions immediately.
	function touched() {
		lastInput = performance.now();
		settleAt = null;
		settleS = 0;
		settleIdx = 0;
		announced = 0;
	}

	function kick() {
		if ( ! raf ) {
			raf = window.requestAnimationFrame( frame );
		}
	}

	function frame( now ) {
		raf = 0;
		var more = false;
		if ( tween ) {
			var t = Math.min( 1, ( now - tween.start ) / tween.dur );
			c = tween.from + ( tween.to - tween.from ) * tween.ease( t );
			lastInput = now;
			if ( t >= 1 ) {
				c = tween.to;
				tween = null;
			}
			more = true;
		}
		if ( wheelPending ) {
			more = true;
			if ( now - wheelAt >= IDLE_MS ) {
				wheelPending = false;
				go( Math.round( c ), 300, EASE_OUT );
			}
		}
		if ( ! tween && ! wheelPending && ! ( drag && drag.moved ) ) {
			var near = Math.round( c );
			if ( Math.abs( near - c ) * PITCH < SETTLE_PX ) {
				if ( MQ_REDUCE.matches ) {
					settleIdx = near;
					settleS = 1;
				} else if ( now - lastInput >= IDLE_MS ) {
					if ( null === settleAt ) {
						settleAt = now;
					}
					settleIdx = near;
					var u = Math.min( 1, ( now - settleAt ) / SETTLE_MS );
					settleS = EASE_OUT( u );
					more = more || u < 1;
				} else {
					more = true;
				}
				if ( 1 === settleS ) {
					announce( near );
				}
			}
		}
		render();
		if ( more ) {
			kick();
		}
	}

	function target() {
		return tween ? tween.to : c;
	}

	function go( to, dur, ease ) {
		to = clamp( Math.round( to ), 1, N );
		if ( ! tween && Math.abs( to - c ) < 1e-6 ) {
			return;
		}
		touched();
		if ( MQ_REDUCE.matches ) {
			tween = null;
			c = to;
		} else {
			tween = { from: c, to: to, start: performance.now(), dur: dur, ease: ease || EASE };
		}
		kick();
	}

	function jump( to ) {
		go( to, Math.min( 900, 400 + 80 * Math.abs( to - c ) ) );
	}

	function step( dir ) {
		var base = target();
		go( dir > 0 ? Math.floor( base + 1e-6 ) + 1 : Math.ceil( base - 1e-6 ) - 1, 600 );
	}

	/* ---- clicks: ● · mini-map · object on the line (not the centre one) · ‹ › ---- */
	root.addEventListener( 'click', function ( e ) {
		if ( performance.now() < suppressUntil ) {
			e.preventDefault();
			e.stopPropagation();
			return;
		}
		var el = e.target.closest ? e.target.closest( '[data-sh-tl-go],[data-sh-tl-prev],[data-sh-tl-next]' ) : null;
		if ( ! el || ! root.contains( el ) ) {
			return;
		}
		if ( el.hasAttribute( 'data-sh-tl-prev' ) || el.hasAttribute( 'data-sh-tl-next' ) ) {
			step( el.hasAttribute( 'data-sh-tl-next' ) ? 1 : -1 );
			return;
		}
		var idx = parseInt( el.getAttribute( 'data-sh-tl-go' ), 10 );
		if ( idx >= 1 && idx <= N ) {
			jump( idx );
		}
	}, true );

	/* ---- keys (focus inside the stage) ---- */
	stage.addEventListener( 'keydown', function ( e ) {
		var map = { ArrowLeft: -1, ArrowRight: 1, Home: 'first', End: 'last' };
		if ( ! ( e.key in map ) || e.altKey || e.ctrlKey || e.metaKey ) {
			return;
		}
		e.preventDefault();
		if ( 'first' === map[ e.key ] || 'last' === map[ e.key ] ) {
			jump( 'first' === map[ e.key ] ? 1 : N );
		} else {
			step( map[ e.key ] );
		}
	} );

	/* ---- horizontal wheel / trackpad on the stage (vertical wheel stays page scroll) ---- */
	stage.addEventListener( 'wheel', function ( e ) {
		var dx = e.deltaX;
		if ( e.shiftKey && ! dx ) {
			dx = e.deltaY;
		} else if ( Math.abs( dx ) <= Math.abs( e.deltaY ) ) {
			return;
		}
		if ( ! dx ) {
			return;
		}
		e.preventDefault();
		dx *= 1 === e.deltaMode ? 16 : ( 2 === e.deltaMode ? root.clientWidth : 1 );
		tween = null;
		touched();
		c = clamp( c + dx / pitchPx, 1, N );
		wheelAt = performance.now();
		wheelPending = true;
		kick();
	}, { passive: false } );

	/* ---- drag on the line row (mouse / touch / pen) ---- */
	track.addEventListener( 'pointerdown', function ( e ) {
		if ( 'mouse' === e.pointerType && 0 !== e.button ) {
			return;
		}
		drag = { id: e.pointerId, x0: e.clientX, c0: c, moved: false, pts: [ [ performance.now(), e.clientX ] ] };
	} );

	track.addEventListener( 'pointermove', function ( e ) {
		if ( ! drag || e.pointerId !== drag.id ) {
			return;
		}
		var dx = e.clientX - drag.x0;
		if ( ! drag.moved ) {
			if ( Math.abs( dx ) < 6 ) {
				return;
			}
			drag.moved = true;
			drag.x0 = e.clientX;
			drag.c0 = c;
			dx = 0;
			tween = null;
			wheelPending = false;
			try {
				track.setPointerCapture( e.pointerId );
			} catch ( err ) {
				// Pointer already released.
			}
		}
		touched();
		c = clamp( drag.c0 - dx / pitchPx, 1, N );
		drag.pts.push( [ performance.now(), e.clientX ] );
		if ( drag.pts.length > 6 ) {
			drag.pts.shift();
		}
		kick();
	} );

	function endDrag( e ) {
		if ( ! drag || e.pointerId !== drag.id ) {
			return;
		}
		var d = drag;
		drag = null;
		if ( ! d.moved ) {
			return;
		}
		suppressUntil = performance.now() + 400;
		var a = d.pts[ 0 ];
		var b = d.pts[ d.pts.length - 1 ];
		var v = b[ 0 ] - a[ 0 ] > 0 && performance.now() - b[ 0 ] < 100 ? ( b[ 1 ] - a[ 1 ] ) / ( b[ 0 ] - a[ 0 ] ) : 0;
		var to = clamp( Math.round( c - ( v * 200 ) / pitchPx ), Math.round( c ) - 2, Math.round( c ) + 2 );
		if ( MQ_REDUCE.matches ) {
			tween = null;
			c = clamp( to, 1, N );
			kick();
		} else {
			go( to, 400, EASE_OUT );
			if ( ! tween ) {
				kick();
			}
		}
	}
	track.addEventListener( 'pointerup', endDrag );
	track.addEventListener( 'pointercancel', endDrag );

	/* ---- layout ---- */
	var resizeRaf = 0;
	window.addEventListener( 'resize', function () {
		if ( ! resizeRaf ) {
			resizeRaf = window.requestAnimationFrame( function () {
				resizeRaf = 0;
				layout();
				render();
			} );
		}
	} );

	layout();
	render();
	root.classList.add( 'is-ready' );
	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( fitTitles );
	}
}() );
