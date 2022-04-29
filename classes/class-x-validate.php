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
class X_Validate {

	public static function check( $arr, $on = false ) {
		if ( $on === false ) {
			$on = $_REQUEST;
		}
		foreach ( $arr as $value ) {
			if ( empty( $on[ $value ] ) ) {
				self::throw_error( 'Data is missing', 900 );
			}
		}
	}

	public static function int( $val ) {
		$val = filter_var( $val, FILTER_VALIDATE_INT );
		if ( false === $val ) {
			self::throw_error( 'Invalid Integer', 901 );
		}
		return $val;
	}

	public static function str( $val ) {
		if ( ! is_string( $val ) ) {
			self::throw_error( 'Invalid String', 902 );
		}
		$val = trim( htmlspecialchars( $val ) );
		return $val;
	}

	public static function bool( $val ) {
		$val = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
		return $val;
	}

	public static function email( $val ) {
		$val = filter_var( $val, FILTER_VALIDATE_EMAIL );
		if ( false === $val ) {
			self::throw_error( 'Invalid Email', 903 );
		}
		return $val;
	}

	public static function url( $val ) {
		$val = filter_var( $val, FILTER_VALIDATE_URL );
		if ( false === $val ) {
			self::throw_error( 'Invalid URL', 904 );
		}
		return $val;
	}

	public static function throw_error( string $error = 'Error In Processing', int $error_code = 0 ) {
		if ( true === self::$errors ) {
			throw new Exception( $error, $error_code );
		}
	}
}
