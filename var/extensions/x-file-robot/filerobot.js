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
			function x_send_img( imageData, imageDesignState ) {

				$.ajax(
					{
						method: 'POST',
						url: window.location.origin + '/ajax.php',
						data: { 'imgURL' : imageData.imageBase64, 'imgFullName' : imageData.fullName, 'mimeType' : imageData.mimeType },
						headers: { 
							'X-CSRF-TOKEN': 'x_add', 
							'X-CSRF-SEED': $( 'input[name=x_token]' ).val(), 
							'X-REQUEST-SOURCE': 'filerobot-img-upl-editor', 
						},
					}
				).done(
					function( msg ) {
						if ( 'true' === msg ) {
							var d = new Date();
							$( '#x_media_item_still' ).attr( 'src', window.location.origin + '/var/uploads/' + $( 'input[name=uuid]' ).val() + '?' + d )
							$( '#x_filerobot_loading' ).hide();
						}
					}
				);

			}

			/**
			 * Instantiate filerobot.
			 */
			const { TABS, TOOLS } = window.FilerobotImageEditor;
			const config = {
				source: window.location.origin + '/var/uploads/' + $( 'input[name=uuid]' ).val(),
				onBeforeSave: function(info){
					// Return false to interrupt default onSave operations.
					return false
				},
				onSave: function( imageData, imageDesignState ) {
					x_send_img( imageData, imageDesignState );
				},
				annotationsCommon: {
					fill: '#ff0000'
				},
				Text: { text: 'uhleloX is just cool...' },
				// Translations pull data from external source, abort.
				useBackendTranslations: false,
				translations: {
					profile: 'Profile',
					coverPhoto: 'Cover photo',
					facebook: 'Facebook',
					socialMedia: 'Social Media',
					fbProfileSize: '180x180px',
					fbCoverPhotoSize: '820x312px',
				},
				Crop: {
					presetsItems: [
						{
							titleKey: 'classicTv',
							descriptionKey: '4:3',
							ratio: 4 / 3,
					},
						{
							titleKey: 'cinemascope',
							descriptionKey: '21:9',
							ratio: 21 / 9,
					},
					],
					presetsFolders: [
						{
							titleKey: 'socialMedia', // will be translated into Social Media as backend contains this translation key.
							groups: [
								{
									titleKey: 'facebook',
									items: [
										{
											titleKey: 'profile',
											width: 180,
											height: 180,
											descriptionKey: 'fbProfileSize',
									},
										{
											titleKey: 'coverPhoto',
											width: 820,
											height: 312,
											descriptionKey: 'fbCoverPhotoSize',
									},
									],
							},
							],
					},
					],
				},
				tabsIds: [ TABS.ADJUST, TABS.FINETUNE, TABS.FILTERS, TABS.WATERMARK, TABS.ANNOTATE, TABS.RESIZE ],
				defaultTabId: TABS.ADJUST,
				defaultToolId: TOOLS.CROP,
			};

			// Assuming we have a div with id="media_item_container".
			const filerobotImageEditor = new FilerobotImageEditor(
				document.querySelector( '#media_item_containerEditor' ),
				config,
			);

			filerobotImageEditor.render(
				{
					// Options, if any.
				}
			);
		}
	);
})( jQuery );
