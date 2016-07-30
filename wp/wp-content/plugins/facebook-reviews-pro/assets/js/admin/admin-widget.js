/**
 * Facebook Reviews Pro Backend JavaScripts
 */
jQuery.noConflict();
(function ( $ ) {
	'use strict';
	/*
	 * Initialize the API Request Method widget radio input toggles
	 */
	$( document ).ready( function () {

		facebookWidgetToggles();
		facebookWidgetTooltips();

	} );

	$( document ).on( 'click', '.fwp-clear-cache', function ( e ) {
		e.preventDefault();
		var $this = $( this );
		$this.next( '.cache-clearing-loading' ).fadeIn( 'fast' );
		var data = {
			action      : 'clear_widget_cache',
			transient_id: $( this ).data( 'transient-id' )
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post( ajaxurl, data, function ( response ) {
			$( '.cache-clearing-loading' ).fadeOut( 'fast' );
			$this.prev( '.cache-message' ).text( response ).fadeIn( 'fast' ).delay( 2000 ).fadeOut();

		} );

	} );

}( jQuery ));

function facebookWidgetToggles() {

	//API Method Toggle
	jQuery( '.widget-api-option .facebook-method-span:not("clickable")' ).each( function () {

		jQuery( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
			jQuery( this ).parent().parent().find( '.toggled' ).slideUp().removeClass( 'toggled' );
			jQuery( this ).find( 'input' ).attr( 'checked', 'checked' );
			if ( jQuery( this ).hasClass( 'search-api-option-wrap' ) ) {
				jQuery( this ).parent().next( '.toggle-api-option-1' ).slideToggle().toggleClass( 'toggled' );
			} else {
				jQuery( this ).parent().next().next( '.toggle-api-option-2' ).slideToggle().toggleClass( 'toggled' );
			}
		} );
	} );

	//Advanced Options Toggle (Bottom-gray panels)
	jQuery( '.facebook-toggler:not("clickable")' ).each( function () {

		jQuery( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
			jQuery( this ).toggleClass( 'toggled' );
			jQuery( this ).next().slideToggle();
		} )

	} );

	//Reviews Options Container Toggle
	jQuery( '.reviews-toggle:not("clickable")' ).each( function () {

		jQuery( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
			jQuery( this ).parent().next( '.reviews-toggle-container' ).slideToggle();
		} );
	} );


}


/*
 * Function to Refresh jQuery toggles for Facebook Reviews Pro upon saving specific widget
 */
jQuery( document ).ajaxSuccess( function ( e, xhr, settings ) {
	facebookWidgetToggles();
	facebookWidgetTooltips();
} );


function facebookWidgetTooltips() {
	//Tooltips for admins
	jQuery( '.tooltip-info' ).tipsy( {
		fade    : true,
		html    : true,
		gravity : 's',
		delayOut: 1000,
		delayIn : 500
	} );
}