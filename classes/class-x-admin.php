<?php
/**
 * Registers the Admin Class.
 *
 * @since 1.0.0
 * @package uhleloX\classes\admin
 */

/**
 * Template loading in the backend,
 * Loads Admin scripts and styles,
 * Handles Login and logout attempts,
 * Content management
 *
 * @since 1.0.0
 */
class X_Admin {

	/**
	 * Action being performed
	 *
	 * @since 1.0.0
	 * @var string $action The Action GET param.
	 */
	private $action = '';

	/**
	 * Type of content.
	 *
	 * @since 1.0.0
	 * @var string $type Type of content being edited or added.
	 */
	private $type = '';

	/**
	 * User trying to login.
	 *
	 * @since 1.0.0
	 * @var string $user The username tryng to login.
	 */
	private $username = '';

	/**
	 * Results of action.
	 *
	 * @since 1.0.0
	 * @var array $results The results of the action being performed.
	 */
	private $results = '';

	/**
	 * Columns to display.
	 *
	 * @since 1.0.0
	 * @var array $columns Columns from database rows to display.
	 */
	private $columns;

	/**
	 * Upload errors.
	 *
	 * @since 1.0.0
	 * @var array $upload_errors Upload errors when adding Files.
	 */
	private $upload_errors;

	/**
	 * Location Redirect Admin.
	 *
	 * @since 1.0.0
	 * @var object $admin_url The Admin PHP Header Location redirect value.
	 */
	private $admin_loc;

	private $item;
	private $items;

	/**
	 * Construct object.
	 *
	 * @since 1.0.0
	 * @param string $action The Action GET param.
	 * @param string $type The Type GET param.
	 * @param string $username The user trying to login.
	 */
	public function __construct( string $action = '', string $type = '', string $username = '' ) {

		$this->action = $action;
		$this->type = $type;
		$this->username = $username;
		$this->results = array(
			'title' => '',
			'error_message' => '',
		);
		$this->columns = array();
		$this->upload_errors = array(
			0 => 'There is no error, the file uploaded with success',
			1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
			2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
			3 => 'The uploaded file was only partially uploaded',
			4 => 'No file was uploaded',
			6 => 'Missing a temporary folder',
			7 => 'Failed to write file to disk.',
			8 => 'A PHP extension stopped the file upload.',
		);
		$this->admin_loc = 'Location: admin.php';

	}

	/**
	 * Load all Admin scripts
	 *
	 * @since 1.0.0
	 * @todo load all these scripts from local assets.
	 */
	public function load_scripts() {

		$functions = new X_Functions();
		$current_screen = new X_Current_View();

		$functions->add_script( 'jquery', 'scripts/jquery.min.js', array(), '', 'footer' );
		$functions->add_script( 'jquery-ui', 'scripts/jquery-ui.min.js', array(), '', 'footer' );

		if ( $current_screen->is_request( 'setup' ) ) {
			$functions->add_script( 'select2', 'scripts/select2.min.js', array(), '', 'footer' );
			$functions->add_script( 'bootstrap-select-2', 'admin/js/bootstrap-select-2.js', array(), '', 'footer' );
		}

		$functions->add_script( 'bootstrap', 'scripts/bootstrap.min.js', array(), '', 'footer' );

		if ( $current_screen->is_request( 'list' ) ) {
			$functions->add_script( 'datatables', 'scripts/simple-datatables.min.js', array(), '', 'footer' );
			$functions->add_script( 'datatables-simple', 'admin/js/datatables-simple.js', array(), '', 'footer' );
		}

		if ( $current_screen->is_request( 'edit' ) || $current_screen->is_request( 'add' ) ) {
			if ( ! $current_screen->is_request( 'media' ) ) {
				$functions->add_script( 'select2', 'scripts/select2.min.js', array(), '', 'footer' );
				$functions->add_script( 'bootstrap-select-2', 'admin/js/bootstrap-select-2.js', array(), '', 'footer' );
				$functions->add_script( 'image-upload', 'admin/js/image-upload.js', array(), '', 'footer' );
			}
			$functions->add_script( 'add-edit-layout', 'admin/js/add-edit-layout.js', array(), '', 'footer' );
		}

		$functions->add_script( 'sidebar-toggle', 'admin/js/sidebar-toggle.js', array(), '', 'footer' );
	}

	/**
	 * Load all Admin styles
	 *
	 * @since 1.0.0
	 * @todo load all these scripts from local assets.
	 */
	public function load_styles() {

		$functions = new X_Functions();
		$current_screen = new X_Current_View();

		if ( $current_screen->is_request( 'edit' ) || $current_screen->is_request( 'add' ) ) {
			$functions->add_link( 'jquery', 'styles/jquery-ui.min.css', array(), '', 'stylesheet', 'head' );
		}
		$functions->add_link( 'bootstrap', 'styles/bootstrap.min.css', array(), '', 'stylesheet', 'head' );
		$functions->add_link( 'bootstrap-icons', 'styles/bootstrap-icons.min.css', array(), '', 'stylesheet', 'head' );
		//if ( $current_screen->is_request( 'setup' ) ) {
			$functions->add_link( 'select2', 'styles/select2.min.css', array(), '', 'stylesheet', 'head' );
			$functions->add_link( 'select2-bootstrap', 'styles/select2-bootstrap.min.css', array(), '', 'stylesheet', 'head' );
		//}
		$functions->add_link( 'style', 'admin/css/styles.css', array(), '', 'stylesheet', 'head' );

	}

	/**
	 * Load all Admin Templates
	 *
	 * @since 1.0.0
	 */
	public function load_template() {

		$get = new X_Get();
		$user = $get->get_item_by( 'users', 'username', $this->username );

		if ( empty( $this->username ) || false === $user ) {

			$this->login();
			exit;

		} elseif ( ! empty( $this->username ) && false !== $user ) {

			switch ( $this->action ) {

				case 'login':
					$this->login();
					break;
				case 'logout':
					$this->logout();
					break;
				case 'add':
					$this->insert();
					break;
				case 'edit':
					$this->edit();
					break;
				case 'delete':
					$this->delete();
					break;
				case 'list':
					$this->list();
					break;
				default:
					$this->dashboard();

			}
		}

	}

	/**
	 * Handle a login attempt
	 *
	 * @since 1.0.0
	 */
	private function login() {

		$this->results['page_title'] = 'Admin Login | ' . X_NAME;

		if ( isset( $_REQUEST['token'] )
			&& X_Functions::verify_token( '_x_login', stripslashes( $_REQUEST['token'] ), 'login' )
			&& isset( $_POST['login'] )
		) {

			$posted_username = X_Validate::str( $_POST['username'] );
			$get = new X_Get();
			$user = $get->get_item_by( 'users', 'username', $posted_username );

			if ( false !== $user
				&& isset( $user->passwordhash )
				&& true === password_verify( X_Validate::str( $_POST['password'] ), $user->passwordhash )
			) {

				$_SESSION['username'] = $user->username;
				header( $this->admin_loc );

			} else {

				$this->results['error_message'] = 'Login Failed. Please try again.';
				require( PUBLIC_PATH . '/partials/login-form.php' );

			}
		} else {

			  require( PUBLIC_PATH . '/partials/login-form.php' );

		}

	}

	/**
	 * Handle a logout
	 *
	 * @since 1.0.0
	 */
	private function logout() {

		session_unset();
		session_destroy();
		header( $this->admin_loc );

	}

	/**
	 * Inserts data to the datbase (of all kind).
	 *
	 * @todo do not hardcode the image $_POST[key].
	 * @todo file uploads are repeated in the edit() function. Unify.
	 */
	private function insert() {

		$this->results['title'] = 'New ' . rtrim( ucfirst( $this->type ), 's' );
		$this->results['action'] = 'add';

		$get = new X_Get();
		$this->columns = $get->show_columns( $this->type, true );
		$this->item = new X_Post( $this->columns );;
		if ( isset( $_REQUEST['token'] )
			&& X_Functions::verify_token( '_x_add', X_Validate::str( stripslashes( $_REQUEST['token'] ) ), 'add' )
			&& ! empty( $_POST )
		) {

			if ( isset( $_POST['save'] ) ) {
				if ( isset( $_FILES['mugshot'] ) && isset( $_FILES['mugshot']['name'] ) ) {
					$_POST['mugshot'] = X_Validate::str( $_FILES['mugshot']['name'] );
					$media = $this->item->upload( 'mugshot' );
				}

				// User has posted the item edit form: save the new item.
				$this->item->setup_data( $_POST );

				// AFTER setting up post data, we handle media validation.
				if ( is_array( $media ) && array_key_exists( 'error', $media ) ) {

					if ( is_int( $media['error'] ) ) {

						$_SESSION['error_message'] = $this->upload_errors[ $media['error'] ];

					} else {

						$_SESSION['error_message'] = $media['error'];

					}

					header( $this->admin_loc . '?action=add&type=' . $this->type );

				} else {

					$this->item->insert( $this->type );

					// This is a new relationship, create the table.
					if ( 'relationships' === $this->type ) {

						$relationships_db_table_config = 'id BIGINT UNSIGNED AUTO_INCREMENT, ' . X_Validate::str( stripslashes( $_POST['entity_a'] ) ) . ' BIGINT UNSIGNED, ' . X_Validate::str( stripslashes( $_POST['entity_b'] ) ) . ' BIGINT UNSIGNED, PRIMARY KEY (`id`, `' . X_Validate::str( stripslashes( $_POST['entity_a'] ) ) . '`, `' . X_Validate::str( stripslashes( $_POST['entity_b'] ) ) . '`)';
						$this->item->add_table( X_Validate::str( stripslashes( $_POST['slug'] ) ), $relationships_db_table_config  );
						// Relationships cannot be edited after creating them.
						header( $this->admin_loc . '?action=list&type=' . $this->type );
						return;

					}

					// Load the edit screen after succesful insert.
					header( $this->admin_loc . '?action=edit&id=' . intval( $this->item->id ) . 'status=saved&type=' . $this->type );

				}

			} elseif ( isset( $_POST['cancel'] ) ) {

				// User has cancelled their edits: return to the list.
				header( $this->admin_loc . '?action=list&type=' . $this->type );

			}
		} elseif ( ! empty( $_POST ) ) {

			$_SESSION['error_message'] = 'Invalid Form Submission';
			// User has submitted the form but there is a Token mismatch. We do not disclose this error.
			header( $this->admin_loc . '?action=add&type=' . $this->type );
		} else {

			// User has not posted the article edit form yet: display the form.
			require( ADMIN_PATH . '/partials/edit.php' );

		}

	}

	/**
	 * Update data to the datbase (of all kind).
	 *
	 * @todo relationships need to be implemented to be saved, as well their respective Select2.
	 * @todo these relationships are currently partially hardcoded, which needs to be resolved.
	 * @todo fazit, relationships not fully functional at this point.
	 * @todo do not hardcode the image $_POST[key].
	 * @todo file uploads are repeated in the edit() function. Unify.
	 * @todo the relationship stuff ... :mute:
	 */
	private function edit() {

		$this->results['title'] = 'Edit ' . rtrim( ucfirst( $this->type ), 's' );
		$this->results['action'] = 'edit';

		$get = new X_Get();
		$this->columns = $get->show_columns( $this->type, true );
		$post = new X_Post();

		$relationships = $get->get_items_in( 'relationships', array( 'entity_a', 'entity_b' ), $this->type );

		if ( ! empty( $relationships ) && isset( $relationships ) ) {
			foreach ( $relationships as $relationship ) {

				$related_entity = $relationship->entity_a === $this->type ? $relationship->entity_b : $relationship->entity_a;
				$entity_candidates[ $relationship->slug ] = $get->get_items( $related_entity );

				$current_thing = isset( $_GET['id'] ) ? $_GET['id'] : ( isset( $_POST['id'] ) ? $_POST['id'] : null );
				$related_things = array();
				if (!is_null($current_thing)){
					$related_things[ $relationship->slug ] = $get->get_items_in( $relationship->slug, array( rtrim( $this->type, 's' ) ), $current_thing );
				}

			}
		}

		if ( isset( $_REQUEST['token'] )
			&& X_Functions::verify_token( '_x_add', X_Validate::str( stripslashes( $_REQUEST['token'] ) ), 'add' )
			&& ! empty( $_POST )
		) {
			if ( isset( $_POST['save'] ) ) {

				// User has posted the item edit form: save the item changes.
				if ( false === $get->get_item_by_id( $this->type, intval( $_POST['id'] ) ) ) {

					header( $this->admin_loc . '?error_message=Item was not found' );
					return;

				}

				if ( isset( $_FILES['mugshot'] ) && isset( $_FILES['mugshot']['name'] ) ) {

					$_POST['mugshot'] = X_Validate::str( stripslashes( $_FILES['mugshot']['name'] ) );
					$media = $post->upload( 'mugshot' );

				}

				if ( isset ( $_POST['page_user'] ) ) {
					foreach ( $_POST['page_user'] as $partner ) {
						$connection = $post->connect( 'page_user', (int) $_POST['id'], (int) $partner );
					}
				}
				/**
				 * We do not need the relationship data anymore.
				 */
				unset($_POST['page_user']);

				$post->setup_data( $_POST );

				// AFTER setting up post data, we handle media validation.
				if ( isset( $media )
					&& is_array( $media )
					&& array_key_exists( 'error', $media )
				) {

					if ( is_int( $media['error'] ) ) {

						$_SESSION['error_message'] = $this->upload_errors[ $media['error'] ];

					} else {

						$_SESSION['error_message'] = $media['error'];

					}

					header( $this->admin_loc . '?action=edit&id=' . intval( $_POST['id'] ) . '&status=error&type=' . $this->type );

				} else {

					// Everything is valid at this point, we can update.
					$post->update( $this->type );

					header( $this->admin_loc . '?action=edit&id=' . intval( $_POST['id'] ) . '&status=saved&type='. $this->type );

				}
			} elseif ( isset( $_POST['cancel'] ) ) {

				// User has cancelled their edits: return to the article list.
				header( $this->admin_loc );

			}
		} elseif ( ! empty( $_POST ) ) {
			$_SESSION['error_message'] = 'Invalid Form Submission';
			// User has submitted the form but there is a Token mismatch. We do not disclose this error.
			header( $this->admin_loc . '?action=add&type=' . $this->type );

		} else {

			// User has not posted the item edit form yet: display the form.
			if ( ! isset( $_GET['id'] ) || empty( $_GET ) ) {

				$_SESSION['error_message'] = 'Invalid or Missing ID';
				header( $this->admin_loc );

			} elseif ( false === $get->get_item_by_id( $this->type, intval( $_GET['id'] ) ) ) {

				$_SESSION['error_message'] = 'Item was not found';
				header( $this->admin_loc );

			} else {

				$functions = new X_Functions();
				$this->item = $get->get_item_by_id( $this->type, intval( $_GET['id'] ) );
				$this->link = $functions->get_url( $this->type, intval( $_GET['id'] ) );
				require( ADMIN_PATH . '/partials/edit.php' );

			}
		}

	}

	/**
	 * Delete data fro,m the datbase (of all kind).
	 */
	private function delete() {

		$get = new X_Get();
		$delete = new X_Delete();

		if ( ! isset( $_GET['id'] ) || empty( $_GET ) ) {

			$_SESSION['error_message'] = 'Invalid or Missing ID';
			header( $this->admin_loc );

		} elseif ( false === $get->get_item_by_id( $this->type, intval( $_GET['id'] ) ) ) {

			$_SESSION['error_message'] = 'Item was not found';
			header( $this->admin_loc );

		} else {

			$delete->delete_by_id( $this->type, intval( $_GET['id'] ) );
			header( $this->admin_loc . '?action=list&status=deleted&type=' . $this->type );

		}
	}

	/**
	 * List data from the database (of all kind).
	 */
	private function list() {

		$this->results['title'] = 'All ' . ucfirst( $this->type );

		$get = new X_Get();
		$this->items = $get->get_items( $this->type );
		$this->columns = $get->show_columns( $this->type );

		require( ADMIN_PATH . '/partials/list.php' );

	}

	/**
	 * The main Dashboard.
	 */
	private function dashboard() {

		$this->results['title'] = 'Dashboard';
		$hooks = new X_Hooks();
		$setup = new X_Setup();

		require( ADMIN_PATH . '/partials/dashboard.php' );

	}

}
