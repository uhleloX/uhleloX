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
             * register progress bars for media load
             */
            $( document ).ajaxStart(function() {
                $('#x_loading').show();
            });
            $( document ).ajaxStop(function() {
                $('#x_loading').hide();
            });

            /**
             * Declare URL to edit images.
             */
            function x_edit_url_f( id ) {
                return `${window.location.origin}/admin.php?action=edit&id=${id}&type=media`;
            }
            /**
             * Declare URL to get images
             */
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
                $.ajax({
                    url: x_get_url,
                    data: { type: "media" },
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': 'x_add',
                        Authorization: $( 'input[name=x_token]' ).val(),
                        'X-REQUEST-SOURCE': 'x-browser',
                    }
                }).done( function( data ) {

                    if( typeof $.parseJSON(data).failed !== "undefined" ) {
                        $( x_media_container ).append( $.parseJSON(data).failed.error.message );
                    } else {
                        $( x_media_container ).empty();
                        $.each( $.parseJSON(data), function(i, v){
                            $( x_media_container ).append(
                                '<figure class="figure position-relative m-0 p-1" style="cursor:pointer;">' +
                                    '<span class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center">' +
                                    '<a href="' + x_edit_url_f( v.id ) + '" target="_blank" class="p-2"><span class="p-1 bg-dark text-white bi bi-pencil-square"></span></a>' +
                                    '<span class="p-2 x_view_media" style="cursor:pointer;"><span class="p-1 bg-dark text-white bi bi-eye"></span></span>' +
                                    '</span>' +
                                    '<img src="' + v.url + '" class="rounded img-thumbnail" width="123px" height="123px" style="width: 123px; height:123px">' +
                                    '<figcaption class="figure-caption text-center">' + v.name +'</figcaption>' +
                                '</figure>'
                            );
                            $('#x_media_browser span.x_view_media').each(function(i){
                                $(this).on('click', function(e) {
                                    $('#x_media_view_modal').modal('show');
                                    $( "#x_media_view" ).empty();
                                    $( "#x_media_view" ).append(
                                        '<img src="' + v.url + '" class="w-100">'
                                    );
                                });
                            });

                        });
                    }
                    
                    $('#x_media_browser img').each(function(i, v){
                        $(this).on('click', function(e) {
                            editor.execute( 'insertImage', { source: $(this).attr('src') } );
                        });
                    });
                }).fail( function() {
                    alert( "Something went wrong" );
                });
            }

            // Pass to window/filter in CKEditor SetCreator
      		window.x_ck_editor_setCreator = function(editor){
            	const ckf = editor.commands.get('ckfinder');               

                if (ckf) {
                   	// Take over ckfinder execute()
                    ckf.execute = () => {
                       	$('#x_media_browser_modal').modal('show');
                        x_get_files(editor);
                    };
                }      
            }
            // Pass to window to add CKFinder icon.
            window.plugins_add_ck = 'CKFinder';
        }
	);
})( jQuery );
