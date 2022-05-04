/**
 * Implements Global Admin JS Scripts.
 *
 * @since 1.0.0
 * @package uhleloX\admin\js
 */

(function( $ ) {
	'use strict';
		$( window ).on(
			'load',
			function() {

				/**
				 * This simply allows us to:
				 * - toggle the sidebar
				 * - persist the choice in localstorage
				 *
				 * @todo probably better use getElementByID and not querySelector
				 * @todo the sub-folded menu items do not persist atm
				 */
				const x_sidebar_toggle = document.body.querySelector( '#x_sidebar_toggle' );
				const x_sidebar_toggled = 'x-admin-sidebar_toggled';
				const x_localstorage_sidebar_toggle = 'x|sidebar-toggle';

				if ( x_sidebar_toggle ) {

					if ( localStorage.getItem( x_localstorage_sidebar_toggle ) === 'true' ) {
						document.body.classList.toggle( x_sidebar_toggled );
					}
					x_sidebar_toggle.addEventListener(
						'click',
						e => {
							e.preventDefault();
							document.body.classList.toggle( x_sidebar_toggled );
							localStorage.setItem( x_localstorage_sidebar_toggle, document.body.classList.contains( x_sidebar_toggled ) );
						}
					);
				}

				/**
				 * Implement a simple warning bound to any click on item with class 'x_confirm'
				 *
				 * A Custom message can be passed in the custom data-x_confirm attribute.
				 */
				$( '.x_confirm' ).each( function() {
					$(this).on( 'click', function (e) {

						var message = 'Are you sure?';
						if ( $( this ).data( 'x_confirm' ) ) {
							  message = $( this ).data( 'x_confirm' );
						}

						return confirm( message );

					});
				});

				/**
				 * Automatically slugify Title to Slug
				 *
				 * This will also fill the field with Title (slugified) on first typing (when creating an item)
				 * Later the user is free to change the slug, but it will be keeping sluggiying.
				 */
				const input_target = 'input[name="slug"]'
				const input_source = 'input[name="title"]'
				if ( $( input_target ) && $( input_source ) ) {
					if ( '' === $( input_target ).val() ) {
						$( input_source ).on('input', function() {
							$( input_target ).val( slugify( $( input_source ).val() ) );
						});
					} else {
						$( input_target ).on('keyup', function() {
							$(this).val( slugify( $(this).val() ) );
						});
					}
				}
				function slugify( string ) {
				  	return string
				  	.toString()
				    .trim()
				    .toLowerCase()
				    .replace(/\s+/g, "-")
				    .replace(/[^\w\-]+/g, "")
				    .replace(/\-\-+/g, "-")
				    .replace(/^-+/, "")
				    .replace(/-+$/, "");
				}

			}
		);
})( jQuery );
