<?php
/**
 * Implements File Not Found extension of Exception
 *
 * @since 1.0.0
 * @package uhleloX\classes\exceptions
 */

/**
 * Custom Exception Class for File Not Found case.
 *
 * @since 1.0.0
 */
class X_File_Not_Found extends Exception {

	/**
	 * Redefine the exception so message isn't optional.
	 *
	 * @param string    $message The Error message.
	 * @param int       $code Error Severity.
	 * @param Throwable $previous The Previous exception thrown.
	 */
	public function __construct( string $message, int $code = 0, Throwable $previous = null ) {
		// some code.

		// make sure everything is assigned properly.
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Custom string representation of object.
	 */
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

	/**
	 * Custom Error handling.
	 */
	public function file_not_found() {
		echo "A custom function for this type of exception\n";
	}

}
