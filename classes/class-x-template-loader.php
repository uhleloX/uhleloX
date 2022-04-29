<?php
/**
 * Handles template loading.
 *
 * @since 1.0.0
 * @package uhleloX\classes\views
 */

/**
 * Class to load the correct front end Template.
 *
 * Implements single view, list view and index data and loads appropriate template.
 *
 * @since 1.0.0
 */
class X_Template_Loader {

	/**
	 * The requested resource.
	 *
	 * @since 1.0.0
	 * @see X_Router()
	 * @var array $request The requested resource ID and type.
	 */
	private $request = '';

	private $x_list = false;
	private $x_item = false;

	/**
	 * Construct the request array.
	 *
	 * @since 1.0.0
	 * @param array $request the Requested Source ID and Type.
	 */
	public function __construct( array $request = array() ) {

		$this->request = $request;

	}

	/**
	 * Load the specific template depending on the requested type and item.
	 *
	 * @since 1.0.0
	 * @todo Archive should be a type content instead.
	 */
	public function load_template() {

		foreach ( $this->request as $req_type => $value ) {

			switch ( $req_type ) {
				case 'archive':
					$this->view_list();
					break;
				case 'item':
				case 'type':
					$this->view_single();
					break;
				default:
					$this->index();
			}
		}

	}

	/**
	 * Load the Data and Template for list views.
	 *
	 * @since 1.0.0
	 * @todo Archive should be a type content instead.
	 */
	private function view_list() {

		$get = new X_Get();
		$functions = new X_Functions();
		$this->template = $get->get_item_by( 'settings', 'slug', 'x_active_template' );
		$this->x_list = $get->get_items( $this->request['archive'] );

		require_once TEMPLATE_PATH . '/' . $this->template->value . '/list.php';

	}

	/**
	 * Load the Data and Template for single views.
	 *
	 * @since 1.0.0
	 */
	private function view_single() {

		$get = new X_Get();
		$this->template = $get->get_item_by( 'settings', 'slug', 'x_active_template' );

		try {

			if ( is_numeric( $this->request['item'] ) && array_key_exists( 'type', $this->request ) ) {

				$this->x_item = $get->get_item_by_id( $this->request['type'], $this->request['item'] );

			} elseif ( array_key_exists( 'type', $this->request ) ) {

				$this->x_item = $get->get_item_by( $this->request['type'], 'slug', $this->request['item'] );

			} else {

				if ( is_numeric( $this->request['item'] ) ) {

					$this->x_item = $get->get_item_by_id( 'pages', $this->request['item'] );

				} else {

					$this->x_item = $get->get_item_by( 'pages', 'slug', $this->request['item'] );

				}
			}

			if ( false === $this->x_item ) {

				throw new Exception( 'The Item does not exist', 1 );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		require_once TEMPLATE_PATH . '/' . $this->template->value . '/single.php';

	}

	private function index() {

		$get = new X_Get();
		$this->template = $get->get_item_by( 'settings', 'slug', 'x_active_template' );
		require_once TEMPLATE_PATH . '/' . $this->template->value . '/index.php';

	}

}
