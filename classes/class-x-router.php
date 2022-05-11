<?php
/**
 * X_Router class
 *
 * @since 1.0.0
 * @package uhleloX\classes\presenter
 */

/**
 * Class to create routes based on request
 * and build base for "pretty" URLs such as /type/item instead of ?type=my-type&item=8
 *
 * @since 1.0.0
 */
class X_Router {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->request = $_SERVER['REQUEST_URI'];
		$this->segments = $this->get_segments();
		$this->main_index = $this->is_main_index();
		$this->main_item = $this->is_main_item();
		$this->is_archive = $this->is_archive();

	}

	/**
	 * Get the segments of the request.
	 *
	 * @return array $request An array with request segments.
	 */
	private function get_segments() {

		return explode( '/', ltrim( trim( trim( $this->request ), '/' ), '/' ) );

	}

	/**
	 * Check if it is a main index request.
	 *
	 * @return bool True if it is a main index request. Default false.
	 */
	private function is_main_index() {

		if ( is_array( $this->segments )
			&& isset( $this->segments[0] )
			&& empty( $this->segments[0] )
		) {
			return true;
		}

		return false;

	}

	/**
	 * Check if it is a main item
	 *
	 * @return bool True if is a main item. Default false.
	 */
	private function is_main_item() {

		if ( false === $this->main_index
			 && 1 === count( $this->segments )
		) {

			return true;

		}

		return false;

	}

	/**
	 * Checks if the main item requested is a valid "archive"
	 *
	 * @since 1.0.0
	 * @return bool True if is archive, false if not.
	 */
	private function is_archive() {

		if ( true === $this->main_item ) {

			if ( ! in_array( $this->segments[0], X_Setup::tables( 'public' ) ) ) {
				return false;
			}

			$get = new X_Get();
			$table = array_search( $this->segments[0], X_Setup::tables( 'public' ) );
			$is_type = $get->check_if_exists( X_Setup::tables( 'public' )[ $table ] );

			return $is_type;

		}

	}

	/**
	 * Create route and pass segments
	 *
	 * @return array $item Associative array with route and segment.
	 * @todo This should probably be renamed to $request when returned.
	 */
	public function route() {

		$item = array();

		if ( false === $this->main_index
			&& true === $this->main_item
		) {
			if ( true === $this->is_archive ) {

				$item['archive'] = $this->segments[0];

			} else {
				$item['item'] = $this->segments[0];

			}
		} elseif ( false === $this->main_index
				   && false === $this->main_item
		) {

			$item['type'] = $this->segments[0];
			$item['item'] = $this->segments[1];

		} elseif ( true === $this->main_index ) {
			$item['is_index'] = true;
		}
		return $item;

	}
}
