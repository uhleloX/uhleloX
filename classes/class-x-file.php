<?php
/**
 * X_File trait
 *
 * @package uhleloX\classes\presenters
 * @since 1.0.0
 */

/**
 * Trait to provide methods to initiate $_FILE uploads and related error handling.
 *
 * @since 1.0.0
 */
trait X_File {

	/**
	 * Helper function to upload files.
	 */
	private function upload_files() {

		if ( empty( $_FILES ) ) {
			/**
			 * No files uploaded, back out.
			 */
			return;
		}

		foreach ( $_FILES as $inputname => $file_array ) {

			/**
			 * There was no file uploded.
			 *
			 * @todo this could be nice to be a setting or filter.
			 */
			if ( 4 === $file_array['error'] ) {
				continue;
			}

			/**
			 * Try to upload the files to the server.
			 *
			 * At this point we are sure a file was uploded.
			 * The handle_media_error() will abort unless error 0 ('file uploaded without error'.)
			 */
			$this->media[ $inputname ] = $this->post->upload( $inputname );
			$this->handle_media_error( $this->media[ $inputname ] );

			/**
			 * Save the uploaded file name to the current saved item column 'mugshot'
			 * or to the custom meta table for this item type.
			 */
			if ( 'mugshot' === $inputname ) {

				/**
				 * Pass the name of the file to the POSTed data column 'mugshot'
				 * This then references the uploaded mugshot for this user.
				 */
				$_POST['mugshot'] = X_Validate::str( $this->media[ $inputname ]['success']['uuid'] );

			} elseif ( 'media_asset' === $inputname ) {

				/**
				 * Pass the name of the file to the POSTed data column 'uuid'
				 * This then references the uploaded media for this media item.
				 */
				$_POST['uuid'] = X_Validate::str( $this->media[ $inputname ]['success']['uuid'] );

			} else {
				/**
				 * The file shuldn't be added to the POSTed content type,
				 * rather to a specified type connected to the POSTed type 'meta_{content-type}'
				 *
				 * We expect that meta table to exist already at this point.
				 */

			}
		}

	}

	/**
	 * Hnadle media error
	 *
	 * @param array $media_array The $_FILES[inpuname] array.
	 */
	private function handle_media_error( array $media_array = array() ) {

		if ( array_key_exists( 'error', $media_array )
			&& 0 !== $media_array['error']
		) {
			$this->results['error_message'] = is_int( $media_array['error'] ) ? $this->upload_errors[ $media_array['error'] ] : $media_array['error'];

			$this->hooks->add_action( 'x_edit_screen_errors', array( $this, 'display_errors' ) );
			require_once ADMIN_PATH . '/partials/edit.php';
			exit();

		}

	}
}
