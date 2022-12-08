<?php
/**
 * X_Admin class
 *
 * @package uhleloX\classes\presenters
 * @since 1.0.0
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

/**
 * Class to handle admin area
 *
 * Loads adequate backend templates,
 * Loads Admin scripts and styles,
 * Handles Login and logout attempts,
 * Passes Database operations from partials to models and returns results to partials.
 *
 * @since 1.0.0
 */
class X_Admin {

	/**
	 * Load X_File trait and X_Relationship trait.
	 * Merely done to re-use the File upload and erro handling logic out of the Admin class,
	 * and reuse it elsewhere.
	 */
	use X_File, X_Relationship;

	/**
	 * Action being performed
	 *
	 * @since 1.0.0
	 * @var string $action The Action GET param.
	 */
	private $action;

	/**
	 * Type of content.
	 *
	 * @since 1.0.0
	 * @var string $type Type of content being edited or added.
	 */
	private $type;

	/**
	 * User trying to login.
	 *
	 * @since 1.0.0
	 * @var string $user The useruuid tryng to login.
	 */
	private $useruuid;

	/**
	 * Results of action.
	 *
	 * @since 1.0.0
	 * @var array $results The results of the action being performed.
	 */
	private $results;

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
	 * @var object $admin_loc The Admin PHP Header Location redirect value.
	 */
	private $admin_loc;

	/**
	 * Location Redirect for Add Items.
	 *
	 * @since 1.0.0
	 * @var object $add_loc The Admin Add Items PHP Header Location redirect value.
	 */
	private $add_loc;

	/**
	 * Location Redirect for Edit Items.
	 *
	 * @since 1.0.0
	 * @var object $edit_loc The Admin Edit Items PHP Header Location redirect value.
	 */
	private $edit_loc;

	/**
	 * Location paths to partials.
	 *
	 * @since 1.0.0
	 * @var object $paths The paths to single partials.
	 */
	private $paths;

	/**
	 * Single Item Object.
	 *
	 * @since 1.0.0
	 * @var object $item The Single Item Object retrieved from the database (a row).
	 */
	private $item;

	/**
	 * All Items Objects.
	 *
	 * @since 1.0.0
	 * @var array $items Array of item objects retrieved (rows).
	 */
	private $items;

	/**
	 * Media Objects.
	 *
	 * @since 1.0.0
	 * @var array $media Array of media items.
	 */
	private $media;

	/**
	 * All X_Functions.
	 *
	 * @since 1.0.0
	 * @var X_Functions $functions X_Functions Class Object to make all functions accessible.
	 */
	private $functions;

	/**
	 * The Current View.
	 *
	 * @since 1.0.0
	 * @var X_Current_View $current_screen X_Current_View Class Object to access current screen data.
	 */
	private $current_screen;

	/**
	 * GET Handler.
	 *
	 * @since 1.0.0
	 * @var X_Get $get X_Get Class Object to handle all GET requests.
	 */
	private $get;

	/**
	 * POST Handler.
	 *
	 * @since 1.0.0
	 * @var X_Post $post X_Post Class Object to handle all POST requests.
	 */
	private $post;

	/**
	 * DELETE Handler.
	 *
	 * @since 1.0.0
	 * @var X_Delete $delete X_Delete Class Object to handle all Delete requests.
	 */
	private $delete;

	/**
	 * Hooks Handler.
	 *
	 * @since 1.0.0
	 * @var X_Hooks $hooks X_Hooks Class Object to handle all hooks.
	 */
	private $hooks;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @param string $action The Action to be performed (Usually from $_GET param x_action).
	 * @param string $type The Type (db table) requested (usually from $_GET param x_type).
	 * @param string $useruuid The Current User ( usually from $_SESSION x_useru_uid).
	 */
	public function __construct( string $action = '', string $type = '', string $useruuid = '' ) {

		/**
		 * Class Constructor arguments
		 */
		$this->action   = $action;
		$this->type     = $type;
		$this->useruuid = $useruuid;

		/**
		 * Result/data of current operation
		 */
		$this->results = array(
			'title'              => '',
			'action'             => '',
			'error_message'      => '',
			'not_found'          => 'The Item was not found',
			'invalid_submission' => 'Invalid Form submission. Try refreshing the page.',
		);

		/**
		 * Available row columns (used as "fields" to output/populate)
		 */
		$this->columns = array();

		/**
		 * Possible upload errors
		 */
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

		/**
		 * Location redirects.
		 */
		$this->admin_loc = 'Location: admin.php';
		$this->add_loc   = '?x_action=add&x_type=';
		$this->edit_loc  = '?x_action=edit&id=';

		/**
		 * Partials Paths
		 */
		$this->paths = array(
			'login_path'  => '/partials/login-form.php',
			'edit_path'   => '/partials/edit.php',
			'list_path'   => '/partials/list.php',
			'dash_path'   => '/partials/dashboard.php',
			'update_path' => '/partials/update.php',
			'error_path'  => '/partials/error.php',
		);

		/**
		 * Results from queries
		 */
		$this->item  = null;
		$this->items = array();
		$this->media = array();

		/**
		 * Dependencies
		 */
		$this->functions      = new X_Functions();
		$this->current_screen = new X_Current_View();
		$this->get            = new X_Get();
		$this->post           = new X_Post();
		$this->delete         = new X_Delete();
		$this->hooks          = new X_Hooks();

	}

	/**
	 * Load all Admin scripts
	 *
	 * @since 1.0.0
	 * @todo load all these scripts from local assets.
	 */
	public function load_scripts() {

		$this->functions->add_script( 'jquery', $this->functions->get_site_url() . '/scripts/jquery.min.js', array(), '', 'footer' );
		$this->functions->add_script( 'jquery-ui', $this->functions->get_site_url() . '/scripts/jquery-ui.min.js', array(), '', 'footer' );
		$this->functions->add_script( 'bootstrap', $this->functions->get_site_url() . '/scripts/bootstrap.min.js', array(), '', 'footer' );
		$this->functions->add_script( 'datatables', $this->functions->get_site_url() . '/scripts/simple-datatables.min.js', array(), '', 'footer' );
		$this->functions->add_script( 'select2', $this->functions->get_site_url() . '/scripts/select2.min.js', array(), '', 'footer' );
		$this->functions->add_script( 'bootstrap-select-2', $this->functions->get_site_url() . '/admin/js/bootstrap-select-2.js', array(), '', 'footer' );
		$this->functions->add_script( 'datatables-simple', $this->functions->get_site_url() . '/admin/js/datatables-simple.js', array(), '', 'footer' );
		$this->functions->add_script( 'image-upload', $this->functions->get_site_url() . '/admin/js/image-upload.js', array(), '', 'footer' );
		$this->functions->add_script( 'add-edit-layout', $this->functions->get_site_url() . '/admin/js/add-edit-layout.js', array(), '', 'footer' );
		$this->functions->add_script( 'admin-js', $this->functions->get_site_url() . '/admin/js/admin-js.js', array(), '', 'footer' );
		$this->functions->add_script( 'update-js', $this->functions->get_site_url() . '/admin/js/update.js', array(), '', 'footer' );

	}

	/**
	 * Load all Admin styles
	 *
	 * @since 1.0.0
	 * @todo load all these scripts from local assets.
	 */
	public function load_styles() {

		$this->functions->add_link( 'jquery', $this->functions->get_site_url() . '/styles/jquery-ui.min.css', array(), '', 'stylesheet', 'head' );
		$this->functions->add_link( 'bootstrap', $this->functions->get_site_url() . '/styles/bootstrap.min.css', array(), '', 'stylesheet', 'head' );
		$this->functions->add_link( 'bootstrap-icons', $this->functions->get_site_url() . '/styles/bootstrap-icons.min.css', array(), '', 'stylesheet', 'head' );
		$this->functions->add_link( 'select2', $this->functions->get_site_url() . '/styles/select2.min.css', array(), '', 'stylesheet', 'head' );
		$this->functions->add_link( 'select2-bootstrap', $this->functions->get_site_url() . '/styles/select2-bootstrap.min.css', array(), '', 'stylesheet', 'head' );
		$this->functions->add_link( 'style', $this->functions->get_site_url() . '/admin/css/styles.css', array(), '', 'stylesheet', 'head' );

	}

	/**
	 * Route to respective Admin Templates
	 *
	 * @since 1.0.0
	 */
	public function load_template() {

		$user = $this->get->get_item_by( 'users', 'uuid', $this->useruuid );

		if ( empty( $this->useruuid )
			|| false === $user
		) {

			$this->login();
			exit;

		} elseif ( ! empty( $this->useruuid )
			&& false !== $user
		) {

			switch ( $this->action ) {

				case 'logout':
					$this->logout();
					break;
				case 'add':
					if ( true === $this->functions->current_user_has_role( $user->id, 'owner' ) ) {
						$this->insert();
					} else {
						$this->dashboard();
					}
					break;
				case 'edit':
					if ( true === $this->functions->current_user_has_role( $user->id, 'owner' ) ) {
						$this->edit();
					} else {
						$this->dashboard();
					}
					break;
				case 'delete':
					if ( true === $this->functions->current_user_has_role( $user->id, 'owner' ) ) {
						$this->delete();
					} else {
						$this->dashboard();
					}
					break;
				case 'change_status':
					if ( true === $this->functions->current_user_has_role( $user->id, 'owner' ) ) {
						$this->change_status();
					} else {
						$this->dashboard();
					}
					break;
				case 'list':
					if ( true === $this->functions->current_user_has_role( $user->id, 'owner' ) ) {
						$this->list();
					} else {
						$this->dashboard();
					}
					break;
				case 'update':
					if ( true === $this->functions->current_user_has_role( $user->id, 'owner' ) ) {
						$this->update();
					} else {
						$this->dashboard();
					}
					break;
				default:
					$this->dashboard();

			}
		}

	}

	/**
	 * Add a callback to x_dashboard_errors for displaying eventual errors.
	 *
	 * Error has to be passed in URL parameter (key) and will match $this->results[key]
	 */
	public function display_errors() {

		if ( isset( $this->results['error_message'] )
			&& ! empty( $this->results['error_message'] )
		) {
			include_once ADMIN_PATH . $this->paths['error_path'];
		}
	}

	/**
	 * Handle a login attempt
	 *
	 * @since 1.0.0
	 */
	private function login() {

		/**
		 * Set page Title.
		 */
		$this->results['title'] = 'Admin Login | ' . X_NAME;

		/**
		 * If POSTed, verify token
		 */
		if ( isset( $_POST['login'] )
			&& isset( $_REQUEST['x_token'] )
			&& X_Functions::verify_token( 'x_login', X_Validate::key( $_REQUEST['x_token'] ), 'login' )
		) {

			$user = $this->get->get_item_by( 'users', 'uuid', X_Validate::key( $_POST['uuid'] ) );

			if ( false !== $user
				&& isset( $user->passwordhash )
				&& true === password_verify( X_Validate::str( $_POST['password'] ), $user->passwordhash )
			) {

				$_SESSION['x_user_uuid'] = $user->uuid;
				header( $this->admin_loc );
				exit();

			} else {

				$this->results['error_message'] = 'Login Failed. Please try again.';
				$this->hooks->add_action( 'x_login_form_errors', array( $this, 'display_errors' ) );
				require PUBLIC_PATH . $this->paths['login_path'];

			}
		} elseif ( ! empty( $_POST ) ) {

			/**
			 * Form was submitted with invalid token.
			 */
			$this->results['error_message'] = 'Login Form Token Invalid.';
			$this->hooks->add_action( 'x_login_form_errors', array( $this, 'display_errors' ) );
			require PUBLIC_PATH . $this->paths['login_path'];

		} else {

			require PUBLIC_PATH . $this->paths['login_path'];

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
		exit();

	}

	/**
	 * Inserts data to the datbase (of all kind).
	 *
	 * @todo do not hardcode the image $_POST[key].
	 * @todo file uploads are repeated in the edit() function. Unify.
	 */
	private function insert() {

		$this->results['title']  = 'New ' . rtrim( ucfirst( $this->type ), 's' );
		$this->results['action'] = 'add';

		/**
		 * Create an empty object for the item since it does not exist yet.
		 *
		 * Since each edit or add process is based on the columns of a table,
		 * we use the available columns to generate the item's object.
		 */
		$this->columns = $this->get->show_columns( $this->type, true );
		$this->item    = $this->post->set_columns( $this->columns );

		/**
		 * We need to gather potential relations and their entities,
		 * even if in ADD process, we cannot connect items (due to ID not existing).
		 * We use that information to display a smart warning instead.
		 *
		 * @todo if we want to allow connecting items at the point of creating,
		 * we would need to store the $_POST[relationship] into a temporary variable,
		 * so we can use it _after_ the post is submitted, by which point naturally the $_POST[relationship]
		 * is already unset.
		 * This is possible, but it requires more work and shouln't be too big of an issue,
		 * strictly speaking, why would you want to connect something that does techincally not yet exist?
		 */
		$this->relationships = $this->get->get_items_in( 'relationships', array( 'entity_a', 'entity_b' ), $this->type );
		$this->gather_parter_candidates();

		/**
		 * If POSTed, verify Token.
		 */
		if ( ! empty( $_POST )
			&& isset( $_REQUEST['x_token'] )
			&& X_Functions::verify_token( 'x_add', X_Validate::key( $_REQUEST['x_token'] ), 'add' )
		) {

			/**
			 * Upload eventual files, abort on eventual errors.
			 *
			 * @see X_Admin::upload_files()
			 */
			$this->upload_files();

			/**
			 * If we reach this point, was no media error, setup and insert the POSTed data.
			 *
			 * @see X_Admin::handle_media_error()
			 */
			$this->functions->maybe_update_pwd();
			$this->post->setup_data( $_POST );
			$this->post->insert( $this->type );

			/**
			 * If this is a new Relationship, create a new table as well.
			 */
			$this->maybe_add_relationship_table();

			/**
			 * After succesful POST, reload the edit screen with POSTed data.
			 */
			header( $this->admin_loc . $this->edit_loc . (int) $this->post->id . '?status=saved&x_type=' . $this->type );
			exit();

		} elseif ( ! empty( $_POST ) ) {

			/**
			 * The user submitted this form with invalid Tokens.
			 */
			$this->results['error_message'] = 'Insert Form Token Invalid.';
			$this->hooks->add_action( 'x_edit_screen_errors', array( $this, 'display_errors' ) );
			require ADMIN_PATH . $this->paths['edit_path'];

		} else {

			/**
			 * Require the Edit Template.
			 */
			require ADMIN_PATH . $this->paths['edit_path'];

		}

	}

	/**
	 * Update data to the datbase (of all kind).
	 *
	 * @todo relationships not fully functional at this point.
	 * @todo do not hardcode the image $_POST[key].
	 * @todo file uploads are repeated in the edit() function. Unify.
	 * @todo the relationship stuff ... :mute:
	 */
	private function edit() {

		$this->results['title']  = 'Edit ' . rtrim( ucfirst( $this->type ), 's' );
		$this->results['action'] = 'edit';

		$this->columns       = $this->get->show_columns( $this->type, true );
		$this->relationships = $this->get->get_items_in( 'relationships', array( 'entity_a', 'entity_b' ), $this->type );
		$this->gather_parter_candidates();
		/**
		 * If POSTed, verify token.
		 */
		if ( ! empty( $_POST )
			&& isset( $_REQUEST['x_token'] )
			&& X_Functions::verify_token( 'x_add', X_Validate::str( stripslashes( $_REQUEST['x_token'] ) ), 'add' )
		) {

			/**
			 * Upload eventual files, abort on eventual errors.
			 *
			 * @see X_Admin::upload_files()
			 */
			$this->upload_files();

			/**
			 * If we reach this point, was no media error.
			 * Setup the POSTed data, connect related items, and post the item.
			 *
			 * @see X_Admin::handle_media_error()
			 */
			$this->functions->maybe_update_pwd();
			$this->post->setup_data( $_POST );
			$this->maybe_disconnect_partners();
			$this->maybe_connect_partners();
			$this->post->update( $this->type );

			/**
			 * After succesful POST, reload the edit screen with POSTed data.
			 */
			header( $this->admin_loc . $this->edit_loc . (int) $this->post->id . '&status=saved&x_type=' . $this->type );
			exit();

		} elseif ( ! empty( $_POST ) ) {

			/**
			 * The user submitted this form with invalid Tokens.
			 */
			$this->results['error_message'] = 'Edit Form Token Invalid.';
			$this->hooks->add_action( 'x_edit_screen_errors', array( $this, 'display_errors' ) );
			require_once ADMIN_PATH . $this->paths['edit_path'];

		} else {

			/**
			 * Perhaps accidentaly ID in GET is invalid or not set.
			 * Redirect to dashboard.
			 */
			if ( ! isset( $_GET['id'] )
				|| false === $this->get->get_item_by_id( $this->type, (int) $_GET['id'] )
			) {
				header( $this->admin_loc );
				exit();
			}

			$this->item = $this->get->get_item_by_id( $this->type, (int) $_GET['id'] );
			$this->link = $this->functions->get_url( $this->type, (int) $_GET['id'] );
			require_once ADMIN_PATH . $this->paths['edit_path'];

		}

	}

	/**
	 * Delete data fro,m the datbase (of all kind).
	 */
	private function delete() {

		if ( ! isset( $_GET['id'] )
			|| false === $this->get->get_item_by_id( $this->type, (int) $_GET['id'] )
		) {
			header( $this->admin_loc );
			exit();
		} else {

			$this->delete->delete_by_id( $this->type, (int) $_GET['id'] );
			header( $this->admin_loc . '?x_action=list&x_type=' . $this->type );
			exit();

		}
	}

	/**
	 * Used to change extension stauts
	 * But probably this could just be used to POST with GET variables?
	 */
	private function change_status() {

		/**
		 * Unlikely case where the ID is not set in the link
		 */
		if ( ! isset( $_GET['id'] )
			|| false === $this->get->get_item_by_id( $this->type, (int) $_GET['id'] )
		) {

			header( 'Location:' . X_Validate::url( $_SERVER['HTTP_REFERER'] ) );
			exit();

		} else {

			/**
			 * Unset data we cannot use to POST
			 */
			unset( $_GET['x_action'], $_GET['x_type'] );
			/**
			 * Setup POST data.
			 * Update item.
			 * Redirect to referer.
			 */
			$this->post->setup_data( $_GET );
			$this->post->update( $this->type );
			header( 'Location:' . X_Validate::url( $_SERVER['HTTP_REFERER'] ) );
			exit();

		}
	}

	/**
	 * List data from the database (of all kind).
	 */
	private function list() {

		/**
		 * Setup Page title
		 */
		$this->results['title'] = 'All ' . ucfirst( $this->type );

		/**
		 * Get all items and columns.
		 * (Note, by default gets only 100)
		 */
		$this->items   = $this->get->get_items( $this->type );
		$this->columns = $this->get->show_columns( $this->type );

		require_once ADMIN_PATH . $this->paths['list_path'];

	}

	/**
	 * The main Dashboard.
	 */
	private function dashboard() {

		/**
		 * Setup Page title
		 */
		$this->results['title'] = 'Dashboard';

		/**
		 * Add action to x_dashboard_errors
		 */
		$this->hooks->add_action( 'x_dashboard_errors', array( $this, 'display_errors' ) );

		require_once ADMIN_PATH . $this->paths['dash_path'];

	}

	/**
	 * The main Dashboard.
	 */
	private function update() {

		/**
		 * Setup Page title
		 */
		$this->results['title'] = 'Update uhleloX';

		$x_update = new X_Update();
		require_once ADMIN_PATH . $this->paths['update_path'];

	}

}
