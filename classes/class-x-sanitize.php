<?php
/**
 * Instantiates Validation Class.
 *
 * @since 1.0.0
 * @package uhleloX\classes\security
 */

/**
 * Loads functions to validate data.
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
