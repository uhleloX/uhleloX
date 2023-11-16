<?php
/**
 * The main Index file.
 *
 * Bootstrap uhleloX on the front end
 * Route to related classes and files depending on location
 *
 * @since 1.0.0
 * @package uhleloX
 */

/**
 * If no config.php file exists, this is a new install.
 * Load X_Setup and Setup Forms.
 *
 * @since 1.0.0
 * @throws object Exception Logs the error message.
 */
try {

	if ( ! file_exists( 'config.php' ) ) {

		/**
		 * Only redirect to setup if no URL parameteres are added.
		 * Otherwise follow URL parameters.
		 */
		if ( ! empty( $_SERVER )
			&& isset( $_SERVER['REQUEST_URI'] )
			&& '/' === $_SERVER['REQUEST_URI']
		) {
			header( 'Location: /?x_action=setup' );
		}

		/**
		 * Require class-x-setup.php because autoloader not available yet.
		 * Instantiate X_Setup (version, db_version and name).
		 * Run uhleloX Setup (autoloader, enforce https)
		 */
		require_once __DIR__ . '/classes/class-x-setup.php';
		$x_setup = new X_Setup( 'uhleloX', true );
		$x_setup->run();

		/**
		 * Get and check $_GET param.
		 * We expect `setup` to be set in `x_action`
		 * No other params allowed
		 */
		$x_action = isset( $_GET ) && isset( $_GET['x_action'] ) ? X_Validate::str( stripslashes( $_GET['x_action'] ) ) : '';

		if ( 'setup' === $x_action ) {

			/**
			 * Everything ready to setup uhleloX
			 * Open session. This sesssion is destroyed if:
			 * X_Install() did succesfully create config.php
			 */
			session_start(
				array(
					'cookie_lifetime' => 0,
				)
			);

			/**
			 * Attempt to install uhleloX
			 *
			 * If succesfull, will redirect to create account form
			 * On failure, throws error
			 */
			$x_install = new X_Install( 'setup' );
			$x_install->load_template();

		} else {

			/**
			 * Config File not found, but URL param bogus or not set.
			 * Let the user know what the issue is.
			 */
			require_once 'classes/exceptions/class-x-file-not-found.php';
			throw new X_File_Not_Found( 'The configuration file was not found. This install is incomplete.' );

		}
	}
} catch ( X_File_Not_Found $e ) {

	error_log( $e->getMessage() . print_r( $e, true ), 0 );
	echo $e->getMessage();
	exit();

}
/**
 * If a config.file exists, either we are in the setup process,
 * or uhleloX is running fine.
 *
 * @since 1.0.0
 * @throws object Exception Logs the error message.
 */
try {

	if ( file_exists( 'config.php' ) ) {

		/**
		 * Require configurations
		 */
		require_once 'config.php';

		/**
		 * Get and check $_GET param.
		 * We expect either `create_account` to be set in `x_action` or empty GET
		 * No other params allowed
		 *
		 * @todo allow other params in $_GET (for queries)
		 */
		$x_action = isset( $_GET ) && isset( $_GET['x_action'] ) ? htmlspecialchars( stripslashes( $_GET['x_action'] ) ) : '';

		if ( 'create_account' === $x_action ) {

			/**
			 * Everything ready to create an account for uhleloX
			 * Open session. This sesssion is destroyed if:
			 * X_Install() did succesfully create new user.
			 */
			session_start(
				array(
					'cookie_lifetime' => 0,
				)
			);

			/**
			 * Attempt to create new user
			 *
			 * If succesfull, will redirect to admin.php login form
			 * On failure, throws error
			 */
			$x_install = new X_Install( 'create_account' );
			$x_install->load_template();

		} elseif ( empty( $x_action ) && empty( $_GET ) ) {
			/**
			 * THere is a weird thing happening here.
			 * If a file does not exist, no matter wher it is caled from (admin.php or else)
			 * things somehow redirect here.
			 * And then logically errors get logged as database tables wont exist for the requested content.
			 * Execution is not stipped.
			 *
			 * This might be due to htaccess.
			 */

			/**
			 * No Actions set and no GET parameter set
			 *
			 * A Front end content is requested
			 * Route to the appropriate template
			 */
			$x_router   = new X_Router();
			$x_request  = $x_router->route();
			$x_template = new X_Template_Loader( $x_request );
			$x_template->load_template();

		} else {

			/**
			 * There are some weird _GET queries going on.
			 * Back out immediately, but don't tell.
			 *
			 * @todo we want to allow GET queries.
			 */
			$x_template = new X_Template_Loader( array( 'bogus' => 'queries' ) );
			$x_template->load_template();

		}
	}
} catch ( Exception $e ) {

	error_log( $e->getMessage() . print_r( $e, true ), 0 );
	exit();

}
