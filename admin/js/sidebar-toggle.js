/**
 * Implements Sidebar Toogle Logic
 *
 * @since 1.0.0
 * @package uhleloX\admin\js
 */

(function( $ ) {
	'use strict';
		$( window ).on(
			'DOMContentLoaded',
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

			}
		);
})( jQuery );
