/**
 * Detail page — title width guard (D42) · hero video plays ONCE, from first entering the screen (D22 · DESIGN-SPEC §6.3 · DOD B4).
 * The <video> is muted/playsinline (no loop) with preload="metadata" + poster, so nothing big loads at
 * first paint; play() starts the download when the hero is in view. If it leaves the screen before the end
 * it pauses and resumes on return; once `ended` it holds the last frame and is never played again
 * (play() on an ended video would restart it, so ended videos are unobserved). Reduced motion: not played,
 * the poster stays.
 */
( function () {
	'use strict';

	/*
	 * D42 (P-185) guard: a title wider than its column (.sh-detail__title--wide) falls back to wrapping in
	 * the column (D28, --fit) when one of its lines would cross a panel image or the screen edge.
	 */
	var title = document.querySelector( '.sh-detail__title--wide' );
	if ( ! title ) {
		return;
	}
	var images = document.querySelectorAll( '.sh-detail__panel .sh-detail__image' );
	var queued = false;

	function hits( a, b ) {
		return a.right > b.left && a.left < b.right && a.bottom > b.top && a.top < b.bottom;
	}

	function check() {
		queued = false;
		title.classList.remove( 'sh-detail__title--fit' );
		var range = document.createRange();
		range.selectNodeContents( title );
		var lines = range.getClientRects();
		var edge  = document.documentElement.clientWidth;
		var bad   = false;
		for ( var i = 0; i < lines.length && ! bad; i++ ) {
			bad = lines[ i ].right > edge;
			for ( var j = 0; j < images.length && ! bad; j++ ) {
				bad = hits( lines[ i ], images[ j ].getBoundingClientRect() );
			}
		}
		if ( bad ) {
			title.classList.add( 'sh-detail__title--fit' );
		}
	}

	function queue() {
		if ( ! queued ) {
			queued = true;
			window.requestAnimationFrame( check );
		}
	}

	check();
	window.addEventListener( 'resize', queue );
	window.addEventListener( 'load', queue );
	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( queue );
	}
}() );

( function () {
	'use strict';

	var videos = document.querySelectorAll( 'video[data-sh-autoplay]' );
	if ( ! videos.length ) {
		return;
	}

	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	function play( video ) {
		if ( video.ended ) {
			return;
		}
		var p = video.play();
		if ( p && typeof p.catch === 'function' ) {
			p.catch( function () {} ); // autoplay refused (e.g. data saver): the poster stays.
		}
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		Array.prototype.forEach.call( videos, play );
		return;
	}

	var observer;

	observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.target.ended ) {
					observer.unobserve( entry.target );
				} else if ( entry.isIntersecting ) {
					play( entry.target );
				} else {
					entry.target.pause();
				}
			} );
		},
		{ threshold: 0.25 }
	);

	Array.prototype.forEach.call( videos, function ( video ) {
		video.addEventListener( 'ended', function () {
			observer.unobserve( video ); // hold the last frame; never replay on re-entry.
		} );
		observer.observe( video );
	} );
}() );
