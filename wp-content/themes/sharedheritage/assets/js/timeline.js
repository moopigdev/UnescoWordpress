/**
 * Timeline v3 (D26 · D27) — DESIGN-SPEC §6.4c. One number drives the page: c = continuous index of the object at
 * the centre (1…N, load 1 = first object selected, D30). Objects/● move by --c (CSS); everything else follows p_i:
 *   p_i = clamp(1 − |i − c|·376.6 / 188, 0, 1)      (at most one object has p > 0)
 *   object opacity 0.6 + 0.4p · scale 1 + p (#14: 0.30 → 0.60 × photo · CSS) · title α 0.62p² · photo α 0.40p · metadata α 0.47p
 *   D35/D36 photo with a "before" image (sh_line_art): phase before (coming in: before α 0.40p · colour 0) → settle
 *   cross-fade 400 ms cubic-bezier(0,0,.2,1) e: before 0.40(1 − e) · colour e → phase colour (moving again: colour
 *   0.40p, before 0 — leaving fades the colour photo) → back to phase before at p = 0 · reduced motion / load (D30) =
 *   colour at once.
 *   settle: no input for 150 ms and |d| < 8 px → title/photo/metadata α → 1 in 400 ms ease-out.
 * Input: ‹ › 600 ms · ● / mini-map / object click: min(900, 400 + 80·|Δ|) ms · cubic-bezier(.4,0,.2,1) ·
 * drag (1 pitch = 376.6·k px) → snap 400 ms ease-out · horizontal wheel → idle 150 ms → snap 300 ms ·
 * ← → Home End. Reduced motion: no tween/settle (jumps, centre object full at once). Stage fit k: §4.5c.5.
 * D34 (§4.5c.7): when the page has a Reflection text it is one more stop NS = N + 1 (● only, no image) — c ∈ [1, NS] ·
 *   text α = metadata formula 0.47p → settle 1 · the stage grows by (H_ref − stage)·p (desktop) / --refl-h·p (stacked).
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
	var lines = infos.map( function ( info ) {
		return info.querySelector( '.sh-tl__photo-line' );
	} );
	var dots = all( '[data-sh-tl-dot]' );
	var prevBtn = root.querySelector( '[data-sh-tl-prev]' );
	var nextBtn = root.querySelector( '[data-sh-tl-next]' );
	var live = root.querySelector( '[data-sh-tl-live]' );
	var refl = root.querySelector( '[data-sh-tl-refl]' );
	var N = items.length;
	if ( ! N ) {
		return;
	}
	var NS = N + ( refl ? 1 : 0 );   // stops: objects + Reflection (D34)

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
	var EASE_XF = bezier( 0, 0, 0.2, 1 );   // D35/D36 before → colour cross-fade (§6.4c)

	function clamp( v, lo, hi ) {
		return Math.min( hi, Math.max( lo, v ) );
	}

	var c = 1;                   // D30: open with object 1 selected (PHP already renders its state)
	var pitchPx = PITCH;         // one object step on screen (px)
	var tween = null;
	var drag = null;
	var wheelAt = 0;
	var wheelPending = false;
	var lastInput = -Infinity;
	var settleAt = null;
	var settleIdx = 1;           // D30: object 1 starts fully settled (α 1) — no load animation
	var settleS = 1;
	var settleU = 1;             // linear settle progress 0 → 1 (settleS = eased)
	var colorIdx = 1;            // object whose photo is in phase "colour" (D35/D36) — D30: object 1 at load
	var xfIdx = 0;               // object whose before → colour cross-fade belongs to the current settle
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
	 * Desktop: a title that needs more than 2 lines at 280 (Pod Duang: 3 lines would reach the mini-map) is widened
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
				for ( var w = 280, n = 0; t.offsetHeight > 2 * lh + 1 && n < 12; n++ ) {
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
			var stageH = Math.max( 723, Math.ceil( 496 + metaH + 24 ) );
			s.setProperty( '--stage-h', stageH + 'px' );
			// D34 2e: H_ref = max(723, title top 428.5 + title + 15 + body + 24) → extra height grown in by p (CSS).
			s.setProperty( '--stage-x', ( refl ? Math.max( 0, Math.ceil( 428.5 + refl.offsetHeight + 24 ) - stageH ) : 0 ) + 'px' );
			s.removeProperty( '--refl-h' );
			return;
		}
		s.removeProperty( '--stage-x' );
		if ( refl ) {
			s.setProperty( '--refl-h', refl.offsetHeight + 'px' );
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
		for ( var i = 0; i < NS; i++ ) {
			var idx = i + 1;
			var p = clamp( 1 - Math.abs( idx - c ) * PITCH / HALF, 0, 1 );
			var s = idx === settleIdx ? settleS : 0;
			var center = p >= 0.5;
			if ( i >= N ) {
				// D34 Reflection: one block (title + text) · α 0.47p → settle 1 · --rp grows the stage.
				set( root, 'rp', '--rp', p.toFixed( 4 ) );
				set( refl, 'ra', '--ra', mix( 0.47 * p, s ) );
				toggle( refl, 'rv', 'is-vis', p > 0 );
				toggle( refl, 'rc', 'is-center', center );
				dotState( i, idx );
				continue;
			}
			set( items[ i ], 'p' + i, '--p', p.toFixed( 4 ) );
			set( items[ i ], 't' + i, '--ta', mix( 0.62 * p * p, s ) );
			toggle( items[ i ], 'v' + i, 'is-vis', p > 0 );
			toggle( items[ i ], 'c' + i, 'is-center', center );
			if ( infos[ i ] ) {
				if ( lines[ i ] ) {
					var settling = idx === settleIdx && settleU > 0;
					if ( colorIdx !== idx && ( MQ_REDUCE.matches ? p > 0 : settling ) ) {
						colorIdx = idx;              // phase colour from the first frame of the cross-fade
						xfIdx = MQ_REDUCE.matches ? 0 : idx;
					}
					var pa;
					var la = 0;
					if ( xfIdx === idx && settling ) {
						var e = EASE_XF( settleU );  // before 0.40 → 0 · colour 0 → 1
						pa = e;
						la = 0.4 * ( 1 - e );
					} else if ( colorIdx === idx ) {
						pa = parseFloat( mix( 0.4 * p, s ) );
					} else {
						pa = 0;
						la = 0.4 * p;
					}
					if ( xfIdx === idx && ! settling ) {
						xfIdx = 0;                   // moved again: colour 0.40p at once, before gone
					}
					if ( colorIdx === idx && 0 === p ) {
						colorIdx = 0;                // out of the window: comes in as "before" next time
					}
					set( infos[ i ], 'pa' + i, '--pa', pa.toFixed( 4 ) );
					set( lines[ i ], 'la' + i, '--la', la.toFixed( 4 ) );
					toggle( lines[ i ], 'lo' + i, 'is-on', la > 0 );
				} else {
					set( infos[ i ], 'pa' + i, '--pa', mix( 0.4 * p, s ) );
				}
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
			dotState( i, idx );
		}
		arrows();
	}

	// ● of stop idx (object or Reflection): roving tabindex + aria-current.
	function dotState( i, idx ) {
		if ( dots[ i ] ) {
			// Roving tabindex: only the ● nearest the centre is a Tab stop (the others may be off screen); ← → move.
			var stop = idx === clamp( Math.round( c ), 1, NS );
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

	function arrows() {
		// D18: an arrow is hidden (visibility) at its end; keep keyboard focus on the arrow that is still available.
		if ( prevBtn ) {
			prevBtn.disabled = c <= 1 + 1e-6;
			if ( prevBtn.disabled && document.activeElement === prevBtn && nextBtn && ! nextBtn.disabled ) {
				nextBtn.focus( { preventScroll: true } );
			}
		}
		if ( nextBtn ) {
			nextBtn.disabled = c >= NS - 1e-6; // D34: › stays on object 15 when there is a Reflection stop
			if ( nextBtn.disabled && document.activeElement === nextBtn && prevBtn && ! prevBtn.disabled ) {
				prevBtn.focus( { preventScroll: true } );
			}
		}
		root.classList.toggle( 'is-moving', !! ( tween || ( drag && drag.moved ) || wheelPending ) );
	}

	function announce( idx ) {
		if ( live && announced !== idx && refl && idx === NS ) {
			announced = idx;
			live.textContent = refl.getAttribute( 'data-title' );
			return;
		}
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
		settleU = 0;
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
					settleU = 1;
				} else if ( now - lastInput >= IDLE_MS ) {
					if ( null === settleAt ) {
						settleAt = now;
					}
					settleIdx = near;
					var u = Math.min( 1, ( now - settleAt ) / SETTLE_MS );
					settleU = u;
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
		to = clamp( Math.round( to ), 1, NS );
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
		if ( idx >= 1 && idx <= NS ) {
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
			jump( 'first' === map[ e.key ] ? 1 : NS );
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
		c = clamp( c + dx / pitchPx, 1, NS );
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
		c = clamp( drag.c0 - dx / pitchPx, 1, NS );
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
			c = clamp( to, 1, NS );
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
