/**
 * Implements ckEditor 5
 *
 * @see https://ckeditor.com/docs/ckeditor5/latest/installation/index.html
 * @since 1.0.0
 * @package uhleloX\var\extensions\x-ck-editor
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
             * If no adequate TextEditor Exists (currently #content single instance)
             * backout immediately, don't waste resources.
             */
            if ( ! document.querySelector( '#content' ) ) {
                return;
            }

            /**
             * Apply filters for plugins in toolbar.
             *
             * @todo this needs to be less hardcoded.
             */
            if ( typeof window.plugins_add_ck === "undefined" || window.plugins_add_ck !== "CKFinder" ) {
                window.plugins_ck = 'CKFinder';
            }

            /**
             * Declare Upload URL (AJAX)
             */
            var x_upload_url = window.location.origin + '/ajax.php';

            const watchdog = new CKSource.EditorWatchdog();

            /**
             * Create a Custom CK Upload Adapter
             *
             * @see https://ckeditor.com/docs/ckeditor5/latest/framework/guides/deep-dive/upload-adapter.html
             * @since 1.0.0
             */
            class x_upload_adapter {

                constructor( loader, url ) {

                    // The file loader instance to use during the upload.
                    this.loader = loader;
                    this.url = url;

                }

                // Starts the upload process.
                upload() {
                    return this.loader.file
                        .then( file => new Promise( ( resolve, reject ) => {
                            this._initRequest();
                            this._initListeners( resolve, reject, file );
                            this._sendRequest( file );
                        } ) );
                }

                // Aborts the upload process.
                abort() {
                    if ( this.xhr ) {
                        this.xhr.abort();
                    }
                }

                // Initializes the XMLHttpRequest object using the URL passed to the constructor.
                _initRequest() {
                    const xhr = this.xhr = new XMLHttpRequest();

                    // Note that your request may look different. It is up to you and your editor
                    // integration to choose the right communication channel. This example uses
                    // a POST request with JSON as a data structure but your configuration
                    // could be different.
                    xhr.open( 'POST', this.url, true );
                    xhr.responseType = 'json';
                }

                // Initializes XMLHttpRequest listeners.
                _initListeners( resolve, reject, file ) {
                    const xhr = this.xhr;
                    const loader = this.loader;
                    const genericErrorText = `Couldn't upload file: ${ file.name }.`;

                    xhr.addEventListener( 'error', () => reject( genericErrorText ) );
                    xhr.addEventListener( 'abort', () => reject() );
                    xhr.addEventListener( 'load', () => {
                        const response = xhr.response;

                        // This example assumes the XHR server's "response" object will come with
                        // an "error" which has its own "message" that can be passed to reject()
                        // in the upload promise.
                        //
                        // Your integration may handle upload errors in a different way so make sure
                        // it is done properly. The reject() function must be called when the upload fails.
                        if ( !response || response.error ) {
                            return reject( response && response.error ? response.error.message : genericErrorText );
                        }

                        // If the upload is successful, resolve the upload promise with an object containing
                        // at least the "default" URL, pointing to the image on the server.
                        // This URL will be used to display the image in the content. Learn more in the
                        // UploadAdapter#upload documentation.
                        resolve( {
                            default: response.url
                        } );
                    } );

                    // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
                    // properties which are used e.g. to display the upload progress bar in the editor
                    // user interface.
                    if ( xhr.upload ) {
                        xhr.upload.addEventListener( 'progress', evt => {
                            if ( evt.lengthComputable ) {
                                loader.uploadTotal = evt.total;
                                loader.uploaded = evt.loaded;
                            }
                        } );
                    }
                }

                // Prepares the data and sends the request.
                _sendRequest( file ) {
                    // Prepare the form data.
                    const data = new FormData();

                    data.append( 'upload', file );

                    // Important note: This is the right place to implement security mechanisms
                    // like authentication and CSRF protection. For instance, you can use
                    // XMLHttpRequest.setRequestHeader() to set the request headers containing
                    // the CSRF token generated earlier by your application.
                    this.xhr.setRequestHeader('X-CSRF-TOKEN', 'x_add')
                    this.xhr.setRequestHeader('X-CSRF-SEED', $( 'input[name=x_token]' ).val())
                    this.xhr.setRequestHeader('X-REQUEST-SOURCE', 'ck-img-upl-editor')

                    // Send the request.
                    this.xhr.send( data );
                }
            }

            // Instantiate the CKEDitor Plugin for custom uploader.
            function x_upload_adapter_plugin( editor ) {
                editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
                    // Configure the URL to the upload script in your back-end here!
                    return new x_upload_adapter( loader, x_upload_url );
                };
            }

            /**
             * Error handler for failure of CKEditor.
             *
             * @since 1.0.0
             */
            function handleError( error ) {
                console.error( 'Oops, something went wrong!' );
                console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                console.warn( 'Build id: 1qzcnkqn6t3b-zb9fodomu0rf' );
                console.error( error );
            }

            // To create CKEditor 5 classic editor with watchdog.
            watchdog.setCreator( ( element, config ) => {
                return CKSource.Editor.create( element, config ).then(editor => {

                    /**
                     * Apply Filters for Creator.
                     *
                     * @todo this needs ot be less hardcoded.
                     */
                    if (typeof window.x_ck_editor_setCreator !== "undefined") { 
                        window.x_ck_editor_setCreator( editor );
                    }
                    
                    return editor;
                })
            });
        
            watchdog.setDestructor( editor => {
                return editor.destroy();
            });
        
            watchdog.on( 'error', handleError );

            watchdog.create( document.querySelector( '#content' ), {
                
                extraPlugins: [ x_upload_adapter_plugin ],
                removePlugins: ["MediaEmbedToolbar"],// follow https://github.com/ckeditor/ckeditor5/issues/9824 and https://github.com/ckeditor/ckeditor5-react/issues/267
                toolbar: {
                    removeItems: [window.plugins_ck],// see window.plugins_add_ck.
                },                

            }).catch( handleError );

        }
	);

})( jQuery );
