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
 *
 * @var string $TIMEZONE A valid Timezone string.
 */
define( 'TIMEZONE', 'Default/Timezone' );

/**
 * Set Database Host.
 *
 * @var string $HOST A valid Database Host.
 */
define( 'HOST', 'localhost' );

/**
 * Set Database Name.
 *
 * @var string $DB_NAME A valid Database Name.
 */
define( 'DB_NAME', 'database_name' );

/**
 * Set Database Username.
 *
 * @var string $DB_USERUUID An existing Database Username.
 */
define( 'DB_USERUUID', 'database_user' );

/**
 * Set Database Password.
 *
 * @var string $DB_PASSWORD A valid Database User's password.
 */
define( 'DB_PASSWORD', 'database_password' );

/**
 * Set Database Charset.
 *
 * @var string $DB_CHARSET A valid Database Charset.
 */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Set Database Port.
 *
 * @var string $DB_PORT A valid Database Port.
 */
define( 'DB_PORT', '3306' );

/**
 * Core Paths.
 * Only modify if you know what you are doing.
 */
try {

	if ( ! isset( $_SERVER ) || ! isset( $_SERVER['DOCUMENT_ROOT'] ) ) {

		throw new Exception( '$_SERVER SuperGlobal or DOCUMENT_ROOT is not set.' );

	} else {

		/**
		 * Set absolute path to Classes folder.
		 *
		 * @var string $CLASS_PATH A valid absolute path to the /classes folder.
		 */
		define( 'CLASS_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/classes' );
		define( 'TEMPLATE_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/var/templates' );
		define( 'EXTENSION_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/var/extensions' );
		define( 'UPLOAD_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/var/uploads' );
		define( 'ADMIN_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/admin' );
		define( 'PUBLIC_PATH', htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/public' );
		define( 'SITE_ROOT', htmlspecialchars( stripslashes( dirname( __FILE__ ) ) ) );

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
require_once __DIR__ . '/classes/class-x-setup.php';
$x = new X_Setup();
$x->run();
$x->setup_timezone();
