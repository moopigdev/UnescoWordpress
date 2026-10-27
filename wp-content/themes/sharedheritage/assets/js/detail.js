/**
 * Detail page — hero video plays ONCE, from first entering the screen (D22 · DESIGN-SPEC §6.3 · DOD B4).
 * The <video> is muted/playsinline (no loop) with preload="metadata" + poster, so nothing big loads at
 * first paint; play() starts the download when the hero is in view. If it leaves the screen before the end
 * it pauses and resumes on return; once `ended` it holds the last frame and is never played again
 * (play() on an ended video would restart it, so ended videos are unobserved). Reduced motion: not played,
 * the poster stays.
 */
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
