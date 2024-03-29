<?php
/**
 * X_Functions class
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
 * Class to provide several methods used overall the project.
 *
 * @since 1.0.0
 */
class X_Functions {

	/**
	 * Toke Key name
	 *
	 * @var const KEY The name of the toke key  to listen to.
	 */
	const KEY = '_x_token';

	/**
	 * Search tag
	 *
	 * @var string $src The Search tag of link/script passed.
	 */
	private $src;

	/**
	 * Script version
	 *
	 * @var string $version The Version of script passed.
	 */
	private $version;

	/**
	 * Rel tag
	 *
	 * @var string $rel The Rel tag of link passed.
	 */
	private $rel;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->src     = '';
		$this->version = '';
		$this->rel     = '';

	}

	/**
	 * Set a token
	 *
	 * @since 1.0.0
	 * @param string $key The $_SESSION Key in which the token is saved. Is used to hash_hmac the token.
	 * @param string $action Optional Action to pass. Will be used to has_hmac the token.
	 * @param string $referer Optional referer to pass. Will be used to has_hmac the token.
	 * @return string $seed The seed generated for this token.
	 */
	public static function set_token( string $key = KEY, string $action = '', string $referer = '' ) {

		$seed             = bin2hex( random_bytes( 35 ) );
		$data             = $key . $action . $referer;
		$token            = hash_hmac( 'sha256', $data, $seed );
		$_SESSION[ $key ] = $token;

		return $seed;

	}

	/**
	 * Get a token
	 *
	 * @since 1.0.0
	 * @param string $key The $_SESSION Key in which the token is saved.
	 * @return bool|string The token from $_SESSION or false if not set.
	 */
	public static function get_token( string $key = KEY ) {

		if ( isset( $_SESSION[ $key ] ) ) {
			return $_SESSION[ $key ];
		}

		return false;

	}

	/**
	 * Verify a token
	 *
	 * @since 1.0.0
	 * @param string $key The $_SESSION Key in which the token is saved.
	 * @param string $seed The seed generated by set_token(), usually passed in a hidden Form input to $_POST.
	 * @param string $action Optional Action to pass. Will be used to has_hmac the token.
	 * @param string $referer Optional referer to pass. Will be used to has_hmac the token.
	 * @return bool true if the token is valid and set, false if not.
	 */
	public static function verify_token( string $key = KEY, string $seed = '', string $action = '', string $referer = '' ) {

		if ( isset( $_SESSION[ $key ] ) ) {

			$data  = $key . $action . $referer;
			$token = hash_hmac( 'sha256', $data, $seed );

			if ( $token === $_SESSION[ $key ] ) {
				return true;
			}

			return false;
		}

		return false;

	}

	public static function verify_key_phrase( string $key_phrase, string $hash ) {

		$submitted_hash = hash( 'sha256', $key_phrase );

		if ( $submitted_hash === $hash ) {
			return true;
		}

		return false;

	}

	/**
	 * Helper to enforce HTTPS everywhere.
	 *
	 * The uhleloX CMS does NOT permit install without HTTPS.
	 */
	public static function enforce_https() {

		$is_https = false;

		if ( isset( $_SERVER['HTTPS'] ) ) {
			$is_https = $_SERVER['HTTPS'];
		}

		if ( 'on' !== $is_https ) {
			header( 'Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			exit( 1 );
		}

	}

	/**
	 * Set CSP
	 *
	 * The uhleloX CMS enforces strong CSP natively.
	 */
	public static function set_security_headers() {

		// We would prefer only self, but CK Editor has an issue with this.
		header( "Content-Security-Policy: default-src 'none'; connect-src 'self'; script-src 'self'; img-src * data:; style-src 'self' 'unsafe-inline'; frame-src *; font-src 'self'" );
		header( 'X-Frame-Options: DENY' );
		header( 'Strict-Transport-Security: max-age=63072000; includeSubDomains; preload' );
		header( 'X-XSS-Protection: 1; mode=block' );

	}

	/**
	 * Provide a simple array with Timezones
	 *
	 * @since 1.0.0
	 * @return array $timezones An array with name and timezone.
	 */
	public static function timezones_select() {

		$regions = array(
			'Africa'     => DateTimeZone::AFRICA,
			'America'    => DateTimeZone::AMERICA,
			'Antarctica' => DateTimeZone::ANTARCTICA,
			'Asia'       => DateTimeZone::ASIA,
			'Atlantic'   => DateTimeZone::ATLANTIC,
			'Europe'     => DateTimeZone::EUROPE,
			'Indian'     => DateTimeZone::INDIAN,
			'Pacific'    => DateTimeZone::PACIFIC,
		);

		$timezones = array();

		foreach ( $regions as $name => $mask ) {

			$zones = DateTimeZone::listIdentifiers( $mask );

			foreach ( $zones as $timezone ) {

				// Lets sample the time there right now.
				$time = new DateTime( null, new DateTimeZone( $timezone ) );
				// Dumb down military time.
				$ampm = $time->format( 'H' ) > 12 ? ' (' . $time->format( 'g:i a' ) . ')' : '';
				// Remove region name and add a sample time.
				$timezones[ $name ][ $timezone ] = substr( $timezone, strlen( $name ) + 1 ) . ' - ' . $time->format( 'H:i' ) . $ampm;

			}
		}

		return $timezones;

	}

	/**
	 * Echo the script tag
	 */
	public function render_script() {
		echo '<script src="' . $this->src . $this->version . '"></script>';
	}

	/**
	 * Echo the link tag
	 */
	public function render_link() {
		echo '<link href="' . $this->src . $this->version . '" rel="' . $this->rel . '"></script>';
	}

	/**
	 * Add Scripts.
	 *
	 * @param string $handle The Name of the script.
	 * @param string $src The Location of the script.
	 * @param array  $dep The dependencies (array of handles).
	 * @param string $version A PHP valid version string.
	 * @param string $loc uhleloX location of script (head, footer).
	 * @param int    $priority Priority of the hook (-PHP_INT_MAX to +PHP_INT_MAX).
	 * @todo check to include most of these options https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script
	 */
	public function add_script( string $handle = '', string $src = '', array $dep = array(), string $version = '', string $loc = '', int $priority = 10 ) {

		$GLOBALS['x_scripts'] = array();
		$scriptss             = new self();

		if ( ! array_key_exists( $handle, $GLOBALS['x_scripts'] ) ) {
			$GLOBALS['x_scripts'] = array( $handle => $src );
		} else {
			return;
		}

		if ( version_compare( $version, '0.0.1', '>=' ) !== false ) {
			$scriptss->version = '?v=' . $version;
		} else {
			$scriptss->version = '';
		}

		if ( ! empty( $dep ) ) {
			foreach ( $dep as $handle ) {
				$this->add_script( $handle, $GLOBALS['x_scripts'][ $handle ] );
			}
		}
		$scriptss->src = $src;
		$plugin        = new X_Hooks();

		$plugin->add_action( 'x_' . $loc, array( $scriptss, 'render_script' ), $priority );

	}

	/**
	 * Add Styles.
	 *
	 * @param string $handle The Name of the script.
	 * @param string $src The Location of the script.
	 * @param array  $dep The dependencies (array of handles).
	 * @param string $version A PHP valid version string.
	 * @param string $rel Rel attribute of link tag.
	 * @param string $loc uhleloX location of script (head, footer).
	 * @param int    $priority Priority of the hook (-PHP_INT_MAX to +PHP_INT_MAX).
	 * @todo check to include most of these options https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script
	 */
	public function add_link( string $handle = '', string $src = '', array $dep = array(), string $version = '', string $rel = 'stylesheet', string $loc = '', int $priority = 10 ) {

		$GLOBALS['x_links'] = array();
		$scriptss           = new self();

		if ( ! array_key_exists( $handle, $GLOBALS['x_links'] ) ) {
			$GLOBALS['x_links'] = array( $handle => $src );
		} else {
			return;
		}

		if ( version_compare( $version, '0.0.1', '>=' ) !== false ) {
			$scriptss->version = '?v=' . $version;
		} else {
			$scriptss->version = '';
		}

		if ( ! empty( $dep ) ) {
			foreach ( $dep as $handle ) {
				$this->add_link( $handle, $GLOBALS['x_links'][ $handle ] );
			}
		}
		$scriptss->src = $src;
		$scriptss->rel = $rel;

		$plugin = new X_Hooks();
		$plugin->add_action( 'x_' . $loc, array( $scriptss, 'render_link' ), $priority );

	}

	/**
	 * Get absolute URL of any item.
	 *
	 * @param string $type The Database table (item type).
	 * @param mixed  $item The item to get URL of (int or string).
	 * @return string $url The Absolute URL to the item, without trailing slash.
	 */
	public function get_url( string $type = null, $item = null ) {

		$get      = new X_Get();
		$site_url = $this->get_site_url();
		$fragment = '';

		if ( is_null( $type ) || 'pages' === $type ) {

			$fragment = '/';

		} else {
			$fragment = $type . '/';
		}

		if ( is_numeric( $item ) ) {

			$item = $get->get_item_by_id( $type, $item );

			if ( is_object( $item )
				&& property_exists( $item, 'uuid' ) ) {
				$item = $item->uuid;
			} else {
				$item = '';
			}
		}

		return rtrim( $site_url, '/\\' ) . '/' . $fragment . $item;

	}

	/**
	 * Get Domain of install
	 *
	 * @param string $subdomain The Subdomain to pass (usually www).
	 * @return string $domain The Domain trailing slash.
	 */
	public function get_domain( string $subdomain = '' ) {

		$get       = new X_Get();
		$site_url  = $get->get_item_by( 'settings', 'uuid', 'x_site_url' );
		$subdomain = empty( $subdomain ) ? '' : $subdomain . '.';

		if ( false === $site_url ) {
			$site_url = $_SERVER['SERVER_NAME'];
		} else {
			$site_url = str_replace( array( 'https://', 'www.' ), '', $site_url->value );
		}

		return rtrim( $subdomain . $site_url, '\\/' );

	}

	/**
	 * The Install URL (settings)
	 *
	 * @todo check if HTTPS_HOST is safe to use here.
	 * @return string $url the Install URL from settings.
	 */
	public function get_site_url() {

		$get      = new X_Get();
		$site_url = $get->get_item_by( 'settings', 'uuid', 'x_site_url' );

		if ( ! $site_url ) {
			$site_url = 'https://' . $_SERVER['HTTP_HOST'];
		} else {
			$site_url = $site_url->value;
		}

		return rtrim( $site_url, '\\/' );

	}

	/**
	 * Check if is admin area
	 *
	 * This truly checks if we are in the admin.php area.
	 * While this is not intended for secuerity, it DOES provide a safe way to check where
	 * the user is located at this moment, no matter what.
	 *
	 * @return bool false|true True if server script name contains admin.php, default false.
	 */
	public function is_admin() {

		if ( isset( $_SERVER )
			&& isset( $_SERVER['SCRIPT_NAME'] )
			&& '/admin.php' === $_SERVER['SCRIPT_NAME']
		) {
			return true;
		}

		return false;

	}

	/**
	 * Get currrent user role
	 *
	 * Check if the current user has a certain role assigned
	 *
	 * @param  int    $user_id The user ID to check by.
	 * @param  string $role The role to check for.
	 * @return bool   true|false True if the user has that role. Default: false.
	 */
	public function current_user_has_role( $user_id, $role ) {

		$get   = new X_Get();
		$roles = $get->get_related_items(
			'user_role',
			array(
				'return'   => '*',
				'query_by' => 'id',
				'query_in' => 'l',
				's'        => $user_id,
				'select'   => 'r',
			)
		);

		if ( in_array( $role, array_column( $roles, 'uuid' ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Helper to setup constants.
	 *
	 * @param array $constants An associative array of constants 'CONSTANT' => 'value' to define.
	 */
	public static function set_constants( array $constants = array() ) {

		foreach ( $constants as $key => $value ) {

			if ( ! defined( $key ) ) {
				define( $key, $value );
			}
		}

	}

	/**
	 * Helper to maybe update password.
	 *
	 * @param  string $value  The password to hash.
	 * @return string| false $return The hash or bool true if passed to POST, false if not hashed.
	 */
	public function maybe_update_pwd( $value = null ) {

		if ( null === $value ) {
			if ( isset( $_POST['passwordhash'] )
				&& ! empty( $_POST['passwordhash'] )
			) {
				$_POST['passwordhash'] = password_hash( $_POST['passwordhash'], PASSWORD_DEFAULT );
				return true;
			} elseif ( ! isset( $_POST['passwordhash'] )
				|| empty( $_POST['passwordhash'] )
			) {
				unset( $_POST['passwordhash'] );
				return false;
			}
		} else {
			$hash = password_hash( $value, PASSWORD_DEFAULT );
			return $hash;
		}

	}

	/**
	 * Move files and folders recursively from source to target.
	 *
	 * @param string $source_path The path of source folder with files.
	 * @param string $target_path The path to target folder.
	 * @param bool   $overwrite   If to overwrite or not existing files.
	 */
	public function move_recursive( $source_path, $target_path, $overwrite ) {

		clearstatcache(); // Clear cache (fileoperations are affected).
		$dir = opendir( $source_path );

		while ( false !== ( $file = readdir( $dir ) ) ) {

			if ( '.' !== $file
				&& '..' !== $file
			) {
				if ( true === is_dir( $source_path . '/' . $file ) ) {
					if ( false === is_dir( $target_path . '/' . $file ) ) {

						/**
						 * Rename does the same as copy + unlink.
						 */
						rename( $source_path . '/' . $file, $target_path . '/' . $file );

					} else {

						/**
						 * Regressive
						 */
						$this->move_recursive( $source_path . '/' . $file, $target_path . '/' . $file, $overwrite );
						if ( $files = glob( $source_path . '/*' ) ) {
							// remove the empty directory.
							@rmdir( $source_path . '/' . $file );
						}
					}
				} else {
					if ( file_exists( $target_path . '/' . $file ) ) {

						if ( true === $overwrite ) {
							// overwrite the file.
							rename( $source_path . '/' . $file, $target_path . '/' . $file );
						}
					} else {
						// if the target file does not exist, simply move the file.
						rename( $source_path . '/' . $file, $target_path . '/' . $file );
					}
				}
			}
		}

		closedir( $dir );

	}

}
