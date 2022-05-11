/**
 * Implements Image Upload
 *
 * @since 1.0.0
 * @package uhleloX\admin\js
 */

(function( $ ) {
	'use strict';
	/**
	 * Initialise Image Uploader
	 *
	 * Will be used on any img src input with ID mugshot_group/mugshot_input
	 * Image max size is set in uhleloX - and HAS to be set, otherwise it is just 5b
	 *
	 * @since 1.0.0
	 * @todo this should likley listen to a class instead
	 * @todo currently uses HTML5 image placeholder. Self host something instead.
	 */
	$( document ).ready(
		function() {

			if ( $( '#mugshot_group' ).length > 0 ) {
				var img_id = '#mugshot_group';
				var img_inp = '#mugshot_input';
				var plchldr = 'https://via.placeholder.com/300.png?text=To+upload+mugshot,+click+here.';
			} else if ( $( '#media_asset_group' ).length > 0 ) {
				var img_id = '#media_asset_group';
				var img_inp = '#media_asset_input';
				var plchldr = 'https://via.placeholder.com/1200x675.png?text=To+upload+a+media+asset,+click+here.';
			}
			var current_src = $( img_id ).attr( 'src' );

			// If no image, add placeholder.
			if ( current_src === null || current_src === '' ) {
				$( img_id ).attr( 'src', plchldr );
			}

			// On click on existing image, trigger click on actual input.
			$( img_id ).on(
				'click',
				function() {
					$( img_inp ).trigger( 'click' );
				}
			);

			// Read URL.
			function read_url( input ) {
				if ( input.files && input.files[0] ) {
					var reader = new FileReader();
					reader.onload = function ( e ) {
						$( img_id ).attr( 'src', e.target.result );
					};
					reader.readAsDataURL( input.files[0] );
				}
			}

			/**
			 * Listen to any new images added
			 */
			$( img_inp ).change(
				function() {
					read_url( this );
				}
			);

		}
	);
})( jQuery );
