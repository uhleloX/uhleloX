/**
 * Implements Bootstrap 5 Select2 4.1
 *
 * @see https://apalfrey.github.io/select2-bootstrap-5-theme/#4-1
 * @since 1.0.0
 * @package uhleloX\admin\js
 */

(function( $ ) {
	'use strict';
		$( window ).on(
			'load',
			function() {

				/**
				 * Instantiate Select2 Bootstrap on:
				 * - setup screen (timezone)
				 * - wherever the User Role selector is used
				 */
				if ( document.querySelector( '#page_user' ) ) {
					$( '#page_user' ).select2( {
						theme: "bootstrap-5",
						width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
						placeholder: $( this ).data( 'placeholder' ),
					});
				}
				if ( document.querySelector( '#timezone' ) ) {
					$( '#timezone' ).select2( {
						theme: "bootstrap-5",
						width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
						placeholder: $( this ).data( 'placeholder' ),
					});
				}
			}
		);
})( jQuery );
