/**
 * Implements filerobot edit interface.
 *
 * @see https://github.com/scaleflex/filerobot-image-editor
 * @since 1.0.0
 * @package uhleloX\var\extensions\x-file-robot
 */

(function( $ ) {
	'use strict';

	$( window ).on(
		'load',
		function() {

			/**
			 * Upload the Image to server/Save existing image.
			 */
			function download_update( action ) {

				$.ajax(
					{
						method: 'POST',
						url: window.location.origin + '/ajax.php',
						data: { 'action' : action },
						headers: { 
							'X-CSRF-TOKEN': 'x_add', 
							'X-CSRF-SEED': $( '#x_download_update' ).data('token'), 
							'X-REQUEST-SOURCE': 'uhlelox-core', 
						},
					}
				).done(
					function( msg ) {

						if ( 'downloaded' === $.parseJSON( msg ).response ) {
							$( '#x_download_update > .spinner-border').addClass('d-none');
							$( '#x_download_update' ).prop('disabled', true);
							$( '#x_install_update').prop('disabled', false);
						} else if ( 'installed' === $.parseJSON( msg ).response ) {
							$( '#x_install_update > .spinner-border').addClass('d-none');
							$( '#x_install_update' ).prop('disabled', true);
							$( '#x_install_update').prop('disabled', true);
						} else {
							alert( $.parseJSON( msg ).response );
						}
					}
				);

			}

			$( '#x_download_update' ).on( 'click', function(){
				$( '#x_download_update > .spinner-border').removeClass('d-none');
				download_update( 'download_update' );
			});

			$( '#x_install_update' ).on( 'click', function(){
				$( '#x_install_update > .spinner-border').removeClass('d-none');
				download_update( 'install_update' );
			});

		}
	);
})( jQuery );
