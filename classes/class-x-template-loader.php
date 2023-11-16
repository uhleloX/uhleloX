<?php
/**
 * X_Template_Loader class
 *
 * @since 1.0.0
 * @package uhleloX\classes\presenters
 */

/**
 * Security: Do not access directly.
 *
 * @since 1.0.0
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

/**
 * Class to handle front end area
 *
 * Loads adequate front end templates,
 * Passes Database operations from partials to models and returns results to partials.
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
	private $request = array();

	/**
	 * The template.
	 *
	 * @since 1.0.0
	 * @see X_Router()
	 * @var string The installed and active template.
	 */
	private $template = null;

	/**
	 * The requested list of items.
	 *
	 * @since 1.0.0
	 * @see X_Router()
	 * @var array $x_list The requested list of items to display.
	 */
	private $x_list = array();

	/**
	 * The requested item object.
	 *
	 * @since 1.0.0
	 * @see X_Router()
	 * @var obj $x_item The requested object item.
	 */
	private $x_item = null;

	/**
	 * The default home page.
	 *
	 * @since 1.0.0
	 * @see X_Router()
	 * @var obj $x_home_uuid The page (uuid) set as homepage.
	 */
	private $x_home_id = null;

	/**
	 * The GET Class
	 *
	 * @since 1.0.0
	 * @see X_Get()
	 * @var obj $get The GET Class.
	 */
	private $get = null;

	/**
	 * The Functions Class
	 *
	 * @since 1.0.0
	 * @see X_Functions()
	 * @var obj $functions The Functions class.
	 */
	private $functions = null;

	/**
	 * Construct the request array.
	 *
	 * @since 1.0.0
	 * @param array $request the Requested Source ID and Type.
	 */
	public function __construct( array $request = array() ) {

		$this->request   = $request;
		$this->get       = new X_Get();
		$this->functions = new X_Functions();
		$this->template  = $this->get->get_item_by( 'settings', 'uuid', 'x_active_template' );

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

		$this->x_list = $this->get->get_items( $this->request['archive'] );

		require_once TEMPLATE_PATH . '/' . $this->template->value . '/list.php';

	}

	/**
	 * Load the Data and Template for single views.
	 *
	 * @since 1.0.0
	 * @throws Exception Throws exception if item does not exist.
	 */
	private function view_single() {

		try {

			if ( is_numeric( $this->request['item'] ) && array_key_exists( 'type', $this->request ) ) {

				$this->x_item = $this->get->get_item_by_id( $this->request['type'], $this->request['item'] );

			} elseif ( array_key_exists( 'type', $this->request ) ) {

				$this->x_item = $this->get->get_item_by( $this->request['type'], 'uuid', $this->request['item'] );

			} else {

				if ( is_numeric( $this->request['item'] ) ) {

					$this->x_item = $this->get->get_item_by_id( 'pages', $this->request['item'] );

				} else {

					$this->x_item = $this->get->get_item_by( 'pages', 'uuid', $this->request['item'] );

				}
			}

			if ( false === $this->x_item ) {

				/**
				 * Provide a proper 404 handling.
				 *
				 * @todo This needs to be refactored.
				 */
				throw new Exception( 'The Item does not exist', 1 );

			}
		} catch ( Exception $e ) {

			echo $e->getMessage();
			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			exit();

		}

		require_once TEMPLATE_PATH . '/' . $this->template->value . '/single.php';

	}

	/**
	 * Load main index
	 */
	private function index() {

		$this->x_home_id = $this->get->get_item_by( 'settings', 'uuid', 'x_home_page' )->value;
		$this->x_item = $this->get->get_item_by( 'pages', 'id', $this->x_home_id );
		require_once TEMPLATE_PATH . '/' . $this->template->value . '/index.php';

	}

}
