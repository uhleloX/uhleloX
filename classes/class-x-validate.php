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

	public static function key( $val ) {

	    if ( ! is_scalar( $val ) ) {
	    	self::throw_error( 'Invalid Scalar', 902 );
	    }
	    $val = strtolower( $val );
	    $val = preg_replace( '/[^a-z0-9_\-]/', '', $val );
	 
	    /**
	     * This is an example of how WP allows to Filter a sanitized key string.
	     *
	     * @since 3.0.0
	     *
	     * @param string $sanitized_key Sanitized key.
	     * @param string $key           The key prior to sanitization.
	     */
	    //return apply_filters( 'sanitize_key', $sanitized_key, $key );
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
		try {
			if ( true === self::$errors ) {
				throw new InvalidArgumentException( $error, $error_code );
			}
		} catch ( InvalidArgumentException $e ) {
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			echo $e->getMessage();
			exit();
		}
	}
}
