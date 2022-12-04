<?php
/**
 * X_Current_View class
 *
 * @since 1.0.0
 * @package uhleloX\classes\presenters
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

/**
 * Class to return current View data.
 *
 * @since 1.0.0
 */
class X_Current_View {

	public function get_request() {

		if ( ! empty( $_SERVER )
			&& isset( $_SERVER['REQUEST_URI'] )
		) {

			return $_SERVER['REQUEST_URI'];

		} else {

			return false;

		}

	}

	public function is_request( $val ) {

		$request = $this->get_request();

		return str_contains( $request, $val );

	}

}
