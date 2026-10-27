/**
 * Detail page — hero video plays while it is on screen (DESIGN-SPEC §6.3 · CONVENTIONS "วิดีโอ" · DOD B4).
 * The <video> is muted/playsinline/loop with preload="metadata" + poster, so nothing big loads at first
 * paint; play() starts the download when the hero is in view, pause() when it leaves.
 */
( function () {
	'use strict';

	var videos = document.querySelectorAll( 'video[data-sh-autoplay]' );
	if ( ! videos.length ) {
		return;
	}

	function play( video ) {
		var p = video.play();
		if ( p && typeof p.catch === 'function' ) {
			p.catch( function () {} ); // autoplay refused (e.g. data saver): the poster stays.
		}
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		Array.prototype.forEach.call( videos, play );
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					play( entry.target );
				} else {
					entry.target.pause();
				}
			} );
		},
		{ threshold: 0.25 }
	);

	Array.prototype.forEach.call( videos, function ( video ) {
		observer.observe( video );
	} );
}() );
