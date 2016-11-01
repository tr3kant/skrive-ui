/**
 * Skrive UI
 * http://webdevstudios.com
 *
 * Licensed under the GPLv2+ license.
 */

window.SkriveUI = window.SkriveUI || {};

( function( window, document, $, plugin ) {
	$( document ).ready( function() {
		var singlepost = $( 'body' ).hasClass( 'single-post' ),
			$paragraph = $( '.entry-content' ).find( 'p' ),
			$headline = $( '.entry-content' ).find( 'h1,h2,h3,h4,h5' ),
			$list = $( '.entry-content' ).find( 'ul,dl' );

		if ( singlepost ) {
			$paragraph.wrap( '<div class="article-block article-block--content"><div class="field--body block-text"></div></div>' );
			$headline.wrap( '<div class="article-block article-block--content"><div class="field--headline block-text"></div></div>' );
			$list.wrap( '<div class="article-block article-block--content"><div class="field--list block-text"></div></div>' );
		}
	} );
	$( plugin.init );
}( window, document, jQuery, window.SkriveUI ) );
