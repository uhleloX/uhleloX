<?php
/**
 * X_Install class
 *
 * @package uhleloX\classes\presenters
 * @since 1.0.0
 */

/**
 * Class to handle install flow
 *
 * Create config.php file,
 * Setup account,
 * Setup Database,
 * Passes Database operations from partials to models and returns results to partials.
 *
 * @since 1.0.0
 */
class X_Install {

	/**
	 * Action being performed
	 *
	 * @since 1.0.0
	 * @var string $action The Action GET param.
	 */
	private $action = '';

	/**
	 * Results of action.
	 *
	 * @since 1.0.0
	 * @var array $results The results of the action being performed.
	 */
	private $results;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param string $action The Action GET param.
	 */
	public function __construct( string $action = '' ) {

		$this->action = $action;
		$this->results = array(
			'title' => '',
			'error_message' => '',
		);

		$admin = new X_Admin();
		$admin->load_styles();
		$admin->load_scripts();

	}

	/**
	 * Template loader.
	 *
	 * @since 1.0.0
	 */
	public function load_template() {

		switch ( $this->action ) {
			case 'setup':
				$this->setup();
				break;
			case 'create_account':
				$this->create_account();
				break;
			default:
				return;
		}

	}

	/**
	 * Setup the Main Config File.
	 *
	 * At this point X_NAME constant exists.
	 *
	 * @since 1.0.0
	 * @todo we are not checking if database connection is succesful a this point
	 * Instead, we load setuo clas in config.php which then cheks for connections, but that is not only too late, it also loads each time the config os loaded :(
	 */
	private function setup() {

		$this->results['title'] = 'Database Connection | ' . X_NAME;

		$hash = file_get_contents( 'hash.txt' );

		if ( isset( $_REQUEST['x_token'] )
			&& ! empty( $_REQUEST['x_token'] )
			&& X_Functions::verify_token( '_x_setup', htmlspecialchars( stripslashes( $_REQUEST['x_token'] ) ), 'setup' )
			&& isset( $_POST['timezone'] )
			&& isset( $_POST['host'] )
			&& isset( $_POST['db'] )
			&& isset( $_POST['db_usr'] )
			&& isset( $_POST['db_pwd'] )
			&& isset( $_POST['db_charset'] )
			&& isset( $_POST['db_port'] )
			&& isset( $_POST['key_phrase'] )
			&& X_Functions::verify_key_phrase( htmlspecialchars( stripslashes( $_POST['key_phrase'] ) ), $hash )
		) {

			$default_config = 'default-config.php';
			$contents = file_get_contents( $default_config );
			$contents = str_replace( 'Default/Timezone', htmlspecialchars( $_POST['timezone'] ), $contents );
			$contents = str_replace( 'localhost', htmlspecialchars( $_POST['host'] ), $contents );
			$contents = str_replace( 'database_name', htmlspecialchars( $_POST['db'] ), $contents );
			$contents = str_replace( 'database_user', htmlspecialchars( $_POST['db_usr'] ), $contents );
			$contents = str_replace( 'database_password', htmlspecialchars( $_POST['db_pwd'] ), $contents );
			$contents = str_replace( 'utf8mb4', htmlspecialchars( $_POST['db_charset'] ), $contents );
			$contents = str_replace( '3306', htmlspecialchars( $_POST['db_port'] ), $contents );

			$config = file_put_contents( 'config.php', $contents );

			if ( false !== $config ) {

				session_unset();
				session_destroy();

				require_once( 'config.php' );

				$default_tables = $this->default_tables();
				$this->setup_database( $default_tables );

				header( 'Location: /?x_action=create_account' );

			} else {

				$this->results['error_message'] = 'Could not create Configuration settings. Make sure your server root is writeable.';

			}
		} else {

			require_once( dirname( __DIR__, 1 ) . '/public/partials/setup-form.php' );

		}

	}

	/**
	 * Create an account.
	 *
	 * At this point X_NAME constant exists.
	 *
	 * @since 1.0.0
	 * @todo handle these fields that are default set empty (mugshot/description). They throw error "default value not set" when inserting new user because the form has no such fields
	 */
	private function create_account() {

		$this->results['title'] = 'Create Admin User | ' . X_NAME;

		$hash = file_get_contents( 'hash.txt' );

		if ( isset( $_REQUEST['x_token'] )
			&& ! empty( $_REQUEST['x_token'] )
			&& X_Functions::verify_token( '_x_newuser', htmlspecialchars( stripslashes( $_REQUEST['x_token'] ) ), 'newuser' )
			&& isset( $_POST['username'] )
			&& isset( $_POST['password'] )
			&& isset( $_POST['firstname'] )
			&& isset( $_POST['lastname'] )
			&& isset( $_POST['email'] )
			&& isset( $_POST['key_phrase'] )
			&& X_Functions::verify_key_phrase( htmlspecialchars( stripslashes( $_POST['key_phrase'] ) ), $hash )
		) {

			$db = new X_Post();
			$data = $_POST;
			$data['joindate'] = date( 'Y/m/d' );
			$data['passwordhash'] = password_hash( $_POST['password'], PASSWORD_DEFAULT );
			$data['description'] = '';
			$data['mugshot'] = '';
			$db->setup_data( $data );
			$new_user = $db->insert( 'users' );

			if ( false !== $new_user ) {

				session_unset();
				session_destroy();
				header( 'Location: /admin.php?x_action=login' );

			} else {
				$this->results['error_message'] = 'Could not create new user.';
				require( dirname( __DIR__, 1 ) . '/public/partials/create-user-form.php' );
			}
		} else {

			require( dirname( __DIR__, 1 ) . '/public/partials/create-user-form.php' );

		}
	}

	/**
	 * Default Database Tables of uhleloX.
	 *
	 * When adding Tables, also update X_TABLES
	 *
	 * @see X_Setup::tables()
	 *
	 * @since 1.0.0
	 * @return array $tables The default Database Tables.
	 * @todo use _ underscores for column names, it allwos oto later spearte the words beter
	 * @todo use UUID insetad of slugs
	 * @todo probably prefix all database tables.
	 * @see https://treewebsolutions.com/articles/multilanguage-database-design-in-mysql-6 for translations
	 */
	private function default_tables() {

		return array(
			'settings' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'slug' => 'VARCHAR(255) NOT NULL',
				'title' => 'TEXT NOT NULL',
				'value' => 'VARCHAR(255) NOT NULL',
				'description' => 'LONGTEXT NOT NULL',
				'publicationdate' => 'DATETIME NOT NULL',
				'editdate' => 'DATETIME NOT NULL',
			),
			'extensions' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'slug' => 'VARCHAR(255) NOT NULL',
				'title' => 'TEXT NOT NULL',
				'description' => 'LONGTEXT NOT NULL',
				'status' => 'TINYTEXT NOT NULL',
			),
			'templates' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'slug' => 'VARCHAR(255) NOT NULL',
				'title' => 'TEXT NOT NULL',
				'description' => 'LONGTEXT NOT NULL',
				'status' => 'TINYTEXT NOT NULL',
			),
			'media' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'owner' => 'BIGINT UNSIGNED NOT NULL',
				'slug' => 'VARCHAR(255) NOT NULL',
				'title' => 'TEXT NOT NULL',
				'type' => 'VARCHAR(60) NOT NULL',
				'publicationdate' => 'DATE NOT NULL',
				'editdate' => 'DATE NOT NULL',
			),
			'pages' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'owner' => 'BIGINT UNSIGNED NOT NULL',
				'slug' => 'VARCHAR(255) NOT NULL',
				'title' => 'TEXT NOT NULL',
				'summary' => 'MEDIUMTEXT',
				'content' => 'LONGTEXT',
				'publicationdate' => 'DATETIME NOT NULL',
				'editdate' => 'DATETIME NOT NULL',
			),
			'users' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'username' => 'VARCHAR(60) NOT NULL',
				'passwordhash' => 'VARCHAR(255) NOT NULL',
				'firstname' => 'VARCHAR(255)',
				'lastname' => 'VARCHAR(255)',
				'email' => 'VARCHAR(100) NOT NULL',
				'joindate' => 'DATE NOT NULL',
				'description' => 'LONGTEXT',
				'mugshot' => 'VARCHAR(255)',
			),
			'roles' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT',
				'role' => 'VARCHAR(20) NOT NULL',
				'title' => 'TEXT NOT NULL',
				'description' => 'TEXT NOT NULL',
				'PRIMARY KEY' => '(`id`, `role`)',
			),
			'user_page' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT',
				'user' => 'BIGINT UNSIGNED',
				'page' => 'BIGINT UNSIGNED',
				'PRIMARY KEY' => '(`id`, `user`, `page`)',
			),
			'user_role' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT',
				'user' => 'BIGINT UNSIGNED',
				'role' => 'BIGINT UNSIGNED',
				'PRIMARY KEY' => '(`id`, `user`, `role`)',
			),
			'languages' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'code' => 'VARCHAR(60) NOT NULL',
				'slug' => 'VARCHAR(255) NOT NULL',
				'name' => 'VARCHAR(255) NOT NULL',
				'flag' => 'VARCHAR(255) NOT NULL',
			),
			'language_translation' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'origin' => 'VARCHAR(60) NOT NULL',
				'target' => 'VARCHAR(60) NOT NULL',
				'translation' => 'VARCHAR(255) NOT NULL',
			),
			'relationships' => array(
				'id' => 'BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
				'slug' => 'VARCHAR(255) NOT NULL',
				'name' => 'TEXT NOT NULL',
				'type' => 'VARCHAR(60) NOT NULL',
				'entity_a' => 'VARCHAR(60) NOT NULL',
				'entity_b' => 'VARCHAR(60) NOT NULL',
			),
		);

	}

	/**
	 * Setup the Datbase Tables
	 *
	 * @todo get this out of there and put in install.
	 */
	private function setup_database( $default_tables ) {

		$db = new X_Post();
		foreach ( $default_tables as $table_name => $table_columns ) {

			$tables = '';

			foreach ( $table_columns as $key => $type ) {
				$tables .= $key . ' ' . $type . ', ';
			}
			$tables = rtrim( $tables, ', ' );

			$db->add_table( $table_name, $tables );

		}
		$db = null;

	}

}
