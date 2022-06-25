<?php
/**
 * Provides an entry port for AJAX Requests.
 *
 * @since 1.0.0
 * @package uhleloX
 */

require_once __DIR__ . '/config.php';

$response = array();
$errors = array();
$to_json = array();
$functions = new X_Functions();
$validate = new X_Validate();
session_start();

/**
 * Check on referer (HTTP_REFERER)
 * Check on CSRF
 * Check on HTTP_COOKIE?
 * Check on HTTP_ORIGIN?
 * Check on HTTPS?
 * Check on REQUEST_TIME?
 */
error_log( print_r( $_SERVER, true ) );
if ( ! isset( $_SERVER )
	|| ! isset( $_SERVER['HTTP_X_CSRF_TOKEN'] )
	|| ! isset( $_SERVER['HTTP_X_CSRF_SEED'] )
	|| true !== $functions->verify_token( $_SERVER['HTTP_X_CSRF_TOKEN'], $_SERVER['HTTP_X_CSRF_SEED'], 'add' )
) {
	$errors['auth_error'] = array(
		'error' => array(
			'message' => 'Unauthorised Access',
		),
	);
	http_response_code( 401 );
}
try {
	if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

			$handler = new X_Post();

			if ( isset( $_FILES )
				&& ! empty( $_FILES )
			) {
				/**
				 * A file is being uploaded.
				 */
				foreach ( $_FILES as $inputname => $file_array ) {

					$file = $handler->upload( $validate->str( $inputname ) );

					if ( is_array( $file )
						&& array_key_exists( 'error', $file )
					) {
						$errors[ $inputname ] = array(
							'error' => array(
								'message' => $validate->str( $file['error'] ),
							),
						);
					} else {
						$functions = new X_Functions();
						$response[ $inputname ] = array(
							'url' => $functions->get_site_url() . '/var/uploads/' . $validate->str( $file_array['name'] ),
						);
					}
				}
			} else {

				/**
				 * This might be a base64 encoded image from the filerobot.
				 * We do not allow it from anywhere else.
				 * So check on the actor first.
				 */
				if ( 'filerobot-img-upl-editor' !== $_SERVER['HTTP_X_REQUEST_SOURCE'] ) {
					$errors['base64'] = array(
						'error' => array(
							'message' => 'Agent not allowed.',
						),
					);
				} else {

					$file = $handler->upload( $_POST['imgURL'], true );
					$response = $file;
				}
			}
		} elseif ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			$handler = new X_Get();
			$results = $handler->get_items( $validate->str( $_GET['type'] ) );
			if ( is_array( $results ) && ! empty( $results ) ) {
				$i = 0;
				foreach ( $results as $media_object ) {
					$response[] = array(
						'url' => $functions->get_site_url() . '/var/uploads/' . $media_object->slug,
						'name' => $media_object->title,
						'id' => $media_object->id,
					);
				}
			} else {
				$errors['failed'] = array(
					'error' => array(
						'message' => 'Nothing found.',
					),
				);
			}
		} else {

			throw new UnexpectedValueException( 'Only POST and GET accepted.' );

		}
	} else {

		throw new UnexpectedValueException( 'File accessed in an unexpected way.' );

	}
} catch ( UnexpectedValueException $e ) {

	error_log( $e->getMessage() . print_r( $e, true ), 0 );
	echo $e->getMessage();
	exit();

}

/**
 * Response
 *
 * You can pass the "requester" in HTTP_X_REQUEST_SOURCE
 * uhleloX uses that for 'ck-img-upl-editor' since the CK editor img uploader xpects a special response.
 */
if ( empty( $errors ) ) {

	if ( isset( $_SERVER['HTTP_X_REQUEST_SOURCE'] )
		&& 'ck-img-upl-editor' === $_SERVER['HTTP_X_REQUEST_SOURCE']
	) {
		$to_json = array_shift( $response );
	} else {
		$to_json = $response;
	}
} else {

	if ( isset( $_SERVER['HTTP_X_REQUEST_SOURCE'] )
		&& 'ck-img-upl-editor' === $_SERVER['HTTP_X_REQUEST_SOURCE']
	) {
		$to_json = array_shift( $errors );
	} else {
		$to_json = $errors;
	}
}

$json = json_encode( $to_json );
echo $json;
