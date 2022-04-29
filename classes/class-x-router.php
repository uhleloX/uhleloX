<?php
/**
 * In this file the Router Class is defined.
 *
 * @since 1.0.0
 * @package uhleloX\classes\views
 */

/**
 * This Class allows "pretty" URLs such as /type/item instead of ?type=my-type&item=8
 *
 * @since 1.0.0
 */
class X_Router {

	public function __construct() {

		$this->request = $_SERVER['REQUEST_URI'];
		$this->segments = $this->get_segments();
		$this->main_index = $this->is_main_index();
		$this->main_item = $this->is_main_item();
		$this->is_archive = $this->is_archive();

	}

	private function get_segments() {

		return explode( '/', ltrim( trim( trim( $this->request ), '/' ), '/' ) );

	}

	private function is_main_index() {

		if ( is_array( $this->segments )
			&& isset( $this->segments[0] )
			&& empty( $this->segments[0] )
		) {
			return true;
		}

		return false;

	}

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
	 */
	private function is_archive() {

		if ( true === $this->main_item ) {

			if ( ! in_array( $this->segments[0], X_Setup::tables() ) ) {
				return false;
			}

			$get = new X_Get();
			$table = array_search( $this->segments[0], X_Setup::tables() );
			$is_type = $get->check_if_exists( X_Setup::tables()[ $table ] );

			return $is_type;

		}

	}

	/**
	 * This should probably be renamed to $request when returned
	 *
	 * @todo above.
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
