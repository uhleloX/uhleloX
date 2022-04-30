<?php
/**
 * Setup uhleloX CMS.
 *
 * @since 1.0.0
 * @package uhleloX
 */

/**
 * Autoloads uhleloX Classes.
 * Enfroces globally HTTPS.
 *
 * @since 1.0.0
 * @package uhleloX
 */
class X_Setup {

	/**
	 * WhiteLabel Name
	 *
	 * @since 1.0.0
	 * @var string $name The uhleloX Whitelable Name.
	 */
	public $name;

	/**
	 * The uhleloX Version number
	 *
	 * @since 1.0.0
	 * @var string $version The Version Number. Follows Semver.
	 */
	public $version;

	/**
	 * The uhleloX Databse Version number
	 *
	 * @since 1.0.0
	 * @var string $db_version The Databse Version number. Follows Semver.
	 */
	public $db_version;

	/**
	 * The CKEditor version
	 *
	 * @see https://ckeditor.com/ckeditor-5/
	 *
	 * @since 1.0.0
	 * @var string $ck_version The CKEditor Version number used by uhleloX
	 */
	public $ck_version;

	/**
	 * The CKEditor build number
	 *
	 * @see https://ckeditor.com/ckeditor-5/
	 *
	 * @since 1.0.0
	 * @var string $ck_version The CKEditor Build number used by uhleloX
	 */
	public $ck_build;

	/**
	 * The uhleloX FileRobot Version number
	 *
	 * @see https://github.com/scaleflex/filerobot-image-editor
	 *
	 * @since 1.0.0
	 * @var string $fr_version The FileRobot Version number used by uhleloX
	 */
	public $fr_version;

	/**
	 * The uhleloX Boostrap Version number
	 *
	 * @see https://getbootstrap.com/docs/5.0/getting-started/introduction/
	 *
	 * @since 1.0.0
	 * @var string $bs_version The Bootstrap Version number used by uhlelX.
	 */
	public $bs_version;

	/**
	 * The uhleloX Select2 Version number
	 *
	 * @see https://select2.org/
	 *
	 * @since 1.0.0
	 * @var string $s2_version The Select2 Version number used by uhleloX.
	 */
	public $s2_version;

	/**
	 * The uhleloX jQuery Version number
	 *
	 * @see https://jquery.com/
	 *
	 * @since 1.0.0
	 * @var string $jq_version The jQuery Version number used by uhleloX.
	 */
	public $jq_version;

	/**
	 * The uhleloX jQuery UI Version number
	 *
	 * @see https://jqueryui.com/
	 *
	 * @since 1.0.0
	 * @var string $jq_version The jQueryUI Version number used by uhleloX.
	 */
	public $jqui_version;

	/**
	 * The uhleloX Simple DataTables Version number
	 *
	 * @see https://github.com/fiduswriter/Simple-DataTables
	 *
	 * @since 1.0.0
	 * @var string $jq_version The jSimple DataTables Version number used by uhleloX.
	 */
	public $dt_version;

	/**
	 * Kick off the base object.
	 *
	 * @since 1.0.0
	 * @param string $name The Human Name of the uhleloX CMS.
	 */
	public function __construct( string $name = 'uhleloX' ) {

		$this->name = $name;
		$this->version = '1.0.0'; // Core version.
		$this->db_version = '1.0.0';// Database Version.
		$this->ck_version = '5.0.0'; // CKEditor Version.
		$this->ck_build = '1qzcnkqn6t3b-zb9fodomu0rf'; // CKEditor Build.
		$this->fr_version = '4.1.1'; // FileRobot Version.
		$this->bs_version = '5.0.2'; // Bootstrap Version.
		$this->s2_version = '4.1.0-rc.0'; // Select2 Version.
		$this->jq_version = '3.6.0'; // jQuery Version.
		$this->jqui_version = '1.13.1'; // jQuery UI Version.
		$this->dt_version = '3.2.0'; // Datatables Version.

	}

	/**
	 * Runs uhleloX initial setup.
	 *
	 * Autoloads uhleloX Classes
	 * Enforce HTTPS globally
	 *
	 * @since 1.0.0
	 */
	public function run() {

		/**
		 * Register Autoloader
		 *
		 * @since 1.0.0
		 */
		spl_autoload_register( array( $this, 'load_dependencies' ) );

		/**
		 * Setup a few constants.
		 */
		X_Functions::set_constants( array( 'X_NAME' => $this->name ) );

		/**
		 * Enforce HTTPS everywhere.
		 *
		 * @since 1.0.0
		 */
		X_Functions::enforce_https();

		/**
		 * Enforce maximal security headers
		 * Note: due to CKEditor, not 100% strictness is possible.
		 */
		X_Functions::set_security_headers();

		/**
		 * Load any extension.
		 * This does not fire the code in the extensions,
		 * however it adds them to the extensions database table
		 * and readies them for usage.
		 */
		$this->load_extensions();

	}

	/**
	 * Setup the CMS Timezone.
	 *
	 * @since 1.0.0
	 * @throws Exception If the Timezone Constant is invalid.
	 */
	public function setup_timezone() {

		$timezone = date_default_timezone_set( TIMEZONE );

		if ( false === $timezone ) {
			throw new Exception( 'Timezone set in TIMEZONE Constant is invalid. Please check your config.php file.' );
		}

	}

	/**
	 * Provide a list of registered Tables
	 *
	 * @since 1.0.0
	 * @param string $visibility Return 'public', 'private' or 'all' tables.
	 */
	public static function tables( string $visibility = 'all' ) {

		$x_hook = new X_Hooks();
		$public_tables = $x_hook->apply_filters( 'x_public_tables', array( 'media', 'pages', 'users' ) );
		$private_tables = $x_hook->apply_filters( 'x_private_tables', array( 'settings', 'roles', 'user_page', 'user_role', 'languages', 'language_translation', 'relationships', 'extensions', 'page_user' ) );

		if ( 'public' === $visibility ) {
			return $public_tables;
		} elseif ( 'private' === $visibility ) {
			return $private_tables;
		} elseif ( 'all' === $visibility ) {
			return array_merge( $public_tables, $private_tables );
		} else {
			return array();
		}

	}

	/**
	 * Load all dependencies.
	 *
	 * @since 1.0.0
	 * @param string $class The Classname to autoload.
	 */
	private function load_dependencies( $class = null ) {

		if ( ! is_null( $class ) ) {

			$class = strtolower( str_replace( '_', '-', $class ) );
			include 'class-' . $class . '.php';

		}

	}

	/**
	 * Load Active Extensions.
	 *
	 * Extensions need be added in the database.
	 * Slug of folder AND main file or (if single file) Slug of file must match database entry.
	 *
	 * @since 1.0.0
	 */
	private function load_extensions() {

		$get = new X_Get();
		$active_extensions = $get->get_items_in( 'extensions', array( 'status' ), 'active' );

		foreach ( $active_extensions as $active_extension ) {

			if ( file_exists( EXTENSION_PATH . '/' . $active_extension->slug ) ) {
				/**
				 * This is a directory, because plugin slugs do not have a .php extension.
				 */
				require_once( EXTENSION_PATH . '/' . $active_extension->slug . '/' . $active_extension->slug . '.php' );
			} elseif ( file_exists( EXTENSION_PATH . '/' . $active_extension->slug . '.php' ) ) {
				/**
				 * This is a single plugin file, since it has an extension
				 */
				require_once( EXTENSION_PATH . '/' . $active_extension->slug . '.php' );
			}
		}

	}

}
