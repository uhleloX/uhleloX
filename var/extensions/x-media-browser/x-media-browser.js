/**
 * Implements CKFinder Customization
 *
 * @see https://ckeditor.com/docs/ckeditor5/latest/api/adapter-ckfinder.html
 * @since 1.0.0
 * @package uhleloX\var\extensions\x-media-browser
 */

(function( $ ) {
	'use strict';
	/**
	 * Initialise the ckEditor
	 *
	 * @since 1.0.0
	 */
	$( window ).on(
		'load',
		function() {

			/**
			 * Register progress bars for media load.
			 */
			$( document ).ajaxStart(
				function() {
					$( '#x_loading' ).show();
				}
			);
			$( document ).ajaxStop(
				function() {
					$( '#x_loading' ).hide();
				}
			);

			/**
			 * Declare URL to edit images.
			 */
			function x_edit_url_f( id ) {
				// ignore the WPCS telling you nonsense here.
				return `${window.location.origin}/admin.php?x_action=edit&id=${id}&x_type=media`;
			}
			/**
			 * Declare URL to get images
			 */
			 // ignore the WPCS telling you nonsense here.
			 var x_get_url = `${window.location.origin}/ajax.php`;

			/**
			 * HTML element to append Media Thumbs.
			 */
			const x_media_container = '#x_media_browser';

			/**
			 * Function to get all Media entries from the database.
			 * We use this produce a modal with all media files already uploaded to the server.
			 * We fetch all the files from /var/uploads/ by the slug received as response.
			 * Then the user can insert those media assets in the editor by clicking on them.
			 *
			 * @since 1.0.0
			 * @param object $editor The CK Editor Instance.
			 */
			function x_get_files( editor ) {
				$.ajax(
					{
						url: x_get_url,
						data: { type: "media" },
						cache: false,
						headers: {
							'X-CSRF-TOKEN': 'x_add',
							'X-CSRF-SEED': $( 'input[name=x_token]' ).val(),
							'X-REQUEST-SOURCE': 'x-browser',
						}
					}
				).done(
					function( data ) {

						if ( typeof $.parseJSON( data ).failed !== "undefined" ) {
							  $( x_media_container ).append( $.parseJSON( data ).failed.error.message );
						} else {
							$( x_media_container ).empty();
							$.each(
								$.parseJSON( data ),
								function(i, v){
									$( x_media_container ).append(
										'<div class="col">' +
										'<div class="card h-100">' +
										'<img src="' + v.url + '" class="card-img-top p-1 x_pointer_handle" alt="' + v.name + '" style="width: 100%;height: 123px;object-fit: cover;">' +
										'<div class="card-body p-0">' +
										'<span class="d-flex justify-content-center align-items-center">' +
										'<a href="' + x_edit_url_f( v.id ) + '" target="_blank" class="p-2"><span class="p-1 bg-dark text-white bi bi-pencil-square"></span></a>' +
										'<span class="p-2 x_view_media" style="cursor:pointer;"><span class="p-1 bg-dark text-white bi bi-eye"></span></span>' +
										'</span>' +
										'</div>' +
										'<div class="card-footer border-0 py-0 px-1">' +
      									'<small class="text-muted">' + v.name + '</small>' +
    									'</div>' +
										'</div>' +
										'</div>'
									);
									$( '#x_media_browser span.x_view_media' ).each(
										function(i){
											$( this ).on(
												'click',
												function(e) {
													$( '#x_media_view_modal' ).modal( 'show' );
													$( "#x_media_view" ).empty();
													$( "#x_media_view" ).append(
														'<img src="' + v.url + '" class="w-100">'
													);
												}
											);
										}
									);

								}
							);
						}

						$( '#x_media_browser img' ).each(
							function(i, v){
								$( this ).on(
									'click',
									function(e) {
										editor.execute( 'insertImage', { source: $( this ).attr( 'src' ) } );
									}
								);
							}
						);
					}
				).fail(
					function() {
						alert( 'Something went wrong' );
					}
				);
			}

			// Pass to window/filter in CKEditor SetCreator.
			window.x_ck_editor_setCreator = function(editor){
				const ckf = editor.commands.get( 'ckfinder' );

				if (ckf) {
					// Take over ckfinder execute().
					ckf.execute = () => {
						$( '#x_media_browser_modal' ).modal( 'show' );
						x_get_files( editor );
					};
				}
			}
			// Pass to window to add CKFinder icon.
			window.plugins_add_ck = 'CKFinder';
		}
	);
})( jQuery );
