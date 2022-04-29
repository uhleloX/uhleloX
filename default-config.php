<?php
/**
 * The main configurations file of uhleloX.
 *
 * Define default Timezone, Host,
 * Database name, Database User name, Password.
 * Define Class Path, Templates Path, Extensions Path,
 * Admin Path and Public Path.
 *
 * @since 1.0.0
 * @package uhleloX
 * @todo check if somehow we can encrypt or else hide these sensitive login details.
 */

/**
 * Set default timezone.
 */
define( 'TIMEZONE', 'Default/Timezone' );

/**
 * Host, Database Name, User, Password.
 */
define( 'HOST', 'localhost' );
define( 'DB_NAME', 'database_name' );
define( 'DB_USERNAME', 'database_user' );
define( 'DB_PASSWORD', 'database_password' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_PORT', '3306' );

/**
 * Core Paths.
 * Only modify if you know what you are doing.
 */
try {

	if ( ! isset( $_SERVER ) || ! isset( $_SERVER['DOCUMENT_ROOT'] ) ) {

		throw new Exception( '$_SERVER SuperGlobal or DOCUMENT_ROOT is not set.' );

	} else {

		define( 'CLASS_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/classes' );
		define( 'TEMPLATE_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/var/templates' );
		define( 'EXTENSION_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/var/extensions' );
        define( 'UPLOAD_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/var/uploads' );
		define( 'ADMIN_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/admin' );
		define( 'PUBLIC_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/public' );

	}
} catch ( Exception $e ) {

	error_log( $e->getMessage() . print_r( $e, true ), 0 );
	echo $e->getMessage();
	exit();

}

/**
 * Setup uhleloX.
 * You may pass a custom name for the CMS to X_Setup().
 *
 * @see X_Setup::$name
 */
require_once( __DIR__ . '/classes/class-x-setup.php' );
$x = new X_Setup();
$x->run();
$x->setup_timezone();
