<?php
/**
 * The main Admin file.
 *
 * Bootstrap uhleloX on the backend end
 * Route to related classes and files depending on location
 *
 * @since 1.0.0
 * @package uhleloX
 */

/**
 * If no config.php file exists, either this is a new install or something is wrong.
 *
 * @since 1.0.0
 * @throws object Exception Throws a message to the user and logs the error.
 */
try {

	if ( ! file_exists( 'config.php' ) ) {
		require_once 'classes/exceptions/class-x-file-not-found.php';
		throw new X_File_Not_Found( 'The configuration file was not found. This install is incomplete.' );

	}
} catch ( X_File_Not_Found $e ) {

	echo $e->getMessage();
	error_log( $e->getMessage() . print_r( $e, true ), 0 );
	exit();

}

/**
 * Configuration exists, start session and route to login form or backend.
 *
 * @since 1.0.0
 */
require_once 'config.php';

/**
 * Everything ready to run uhleloX backend
 * Open session. This sesssion is destroyed if:
 * User Logs out
 */
session_start(
	array(
		'cookie_lifetime' => 0,
	)
);

/**
 * Get and check $_GET param.
 * We allow `x_action` and `x_type` params.
 * No other params allowed
 */
$x_action = isset( $_GET['x_action'] ) ? X_Validate::key( $_GET['x_action'] ) : '';
$x_type   = isset( $_GET['x_type'] ) ? X_Validate::key( $_GET['x_type'] ) : '';

/**
 * Get and check $_SESSION param.
 * We expect `x_user_uuid` param.
 * No other params allowed.
 */
$x_user_uuid = isset( $_SESSION['x_user_uuid'] ) ? X_Validate::key( $_SESSION['x_user_uuid'] ) : '';

/**
 * Load uhleloX Admin
 *
 * Add Scripts, Styles and load respective template.
 * Security (if user logged in) handled in X_Admin
 *
 * @see X_Admin::load_template()
 */
$x_admin = new X_Admin( $x_action, $x_type, $x_user_uuid );
$x_admin->load_styles();
$x_admin->load_scripts();
$x_admin->load_template();
