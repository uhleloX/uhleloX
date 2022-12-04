<?php
/**
 * X_Sanitize class
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
 * Class to sanitise output in all kind of ways and formats.
 *
 * @since 1.0.0
 */
class X_Sanitize {

	/**
	 * Sanitize general output (mostly text/string in HTML).
	 *
	 * @since 1.0.0
	 * @param string $string The String to sanitize for output.
	 * @return string $string The Safe string to echo.
	 * @todo apply filters to change these options.
	 */
	public static function out_html( $string = '' ) {

		if ( ! is_string( $string ) ) {
			return $string;
		}

		$flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5;
		$encoding = 'UTF-8';
		$double_encode = true;

		$string = htmlspecialchars( (string) $string, $flags, $encoding, $double_encode );

		return $string;

	}



}
