<?php
/**
 * X_Validate class
 *
 * @since 1.0.0
 * @package uhleloX\classes\security
 */

/**
 * Class to validate all inputs values
 *
 * @since 1.0.0
 * @todo there are some actual sanitization processes in here. Move them to X_Sanitize.
 */
class X_Validate {

	/**
	 * Check if any value in array is empty in input array
	 *
	 * Example:
	 * $_POST is array of ['one'=>'val', 'two'=>'']
	 * We want to validate both:
	 * check( array('one','two'), $_POST );
	 *
	 * @param array $validate The array of input array keys that we want to check for.
	 * @param array $input The input array we want to validate. Defaults to $_REQUEST.
	 */
	public static function check( array $validate = array(), array $input = array() ) {

		if ( empty( $input ) ) {
			$input = $_REQUEST;
		}

		foreach ( $validate as $value ) {

			if ( empty( $input[ $value ] ) ) {

				self::throw_error( 'Data is missing', 900 );

			}
		}

	}

	/**
	 * Check if any value is (int)
	 *
	 * @param mixed $val The value to check.
	 */
	public static function int( $val ) {

		$val = filter_var( $val, FILTER_VALIDATE_INT );

		if ( false === $val ) {

			self::throw_error( 'Invalid Integer', 901 );

		}

		return $val;

	}

	/**
	 * Check if any value is string
	 *
	 * @param mixed $val The value to check.
	 */
	public static function str( $val ) {

		if ( ! is_string( $val ) ) {

			self::throw_error( 'Invalid String', 902 );

		}

		$val = trim( htmlspecialchars( $val ) );

		return $val;

	}

	/**
	 * Check if any value is valid key
	 *
	 * @param mixed $val The value to check.
	 */
	public static function key( $val ) {

		if ( ! is_scalar( $val ) ) {

			self::throw_error( 'Invalid Key', 902 );

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
		// return apply_filters( 'sanitize_key', $sanitized_key, $key );
		return $val;

	}

	/**
	 * Check if any value is boolean
	 *
	 * @param mixed $val The value to check.
	 */
	public static function bool( $val ) {

		$val = filter_var( $val, FILTER_VALIDATE_BOOLEAN );

		if ( false === $val ) {

			self::throw_error( 'Not a boolean', 903 );

		}

		return $val;

	}

	/**
	 * Check if any value is a valid email.
	 *
	 * @param mixed $val The value to check.
	 */
	public static function email( $val ) {

		$val = filter_var( $val, FILTER_VALIDATE_EMAIL );

		if ( false === $val ) {

			self::throw_error( 'Invalid Email', 903 );

		}

		return $val;

	}

	/**
	 * Check if any value is a valid URL.
	 *
	 * @param mixed $val The value to check.
	 */
	public static function url( $val ) {

		$val = filter_var( $val, FILTER_VALIDATE_URL );

		if ( false === $val ) {

			self::throw_error( 'Invalid URL', 904 );

		}

		return $val;

	}

	/**
	 * Throw error on validation failure.
	 *
	 * @param string $error The error to throw.
	 * @param int    $error_code The error code.
	 * @throws InvalidArgumentException The validation error.
	 */
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
