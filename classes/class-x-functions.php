<?php
/**
 * Implements util functions class.
 *
 * @since 1.0.0
 * @package uhleloX\classes\functions
 */

/**
 * Implements several functions for global usage.
 *
 * @todo bring scripts and styles global and object out of this class into its own.
 */
class X_Functions {

	static $errors = true;

	const KEY = '_x_token';

	private $version;
	private $rel;

	public function __construct(){
		$this->src = '';
		$this->version = '';
		$this->rel = '';
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

		$seed = bin2hex( random_bytes( 35 ) );
		$data = $key . $action . $referer;
		$token = hash_hmac( 'sha256', $data, $seed );
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

			$data = $key . $action . $referer;
			$token = hash_hmac( 'sha256', $data, $seed );

			if ( $token === $_SESSION[ $key ] ) {
				return true;
			}

			return false;
		}

		return false;

	}

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

	public static function set_security_headers() {

		// We would prefer only self, but CK Editor has an issue with this.
		header( "Content-Security-Policy: default-src 'none'; connect-src 'self'; script-src 'self'; img-src * data:; style-src 'self' 'unsafe-inline'; frame-src *; font-src 'self'" );
		header( "X-Frame-Options: DENY" );
		header( "Strict-Transport-Security: max-age=63072000; includeSubDomains; preload" );
		header( "X-XSS-Protection: 1; mode=block" );

	}

	/**
	 * Provide a simple array with Timezones
	 *
	 * @since 1.0.0
	 * @return array $timezones An array with name and timezone.
	 */
	public static function timezones_select() {

		$regions = array(
			'Africa' => DateTimeZone::AFRICA,
			'America' => DateTimeZone::AMERICA,
			'Antarctica' => DateTimeZone::ANTARCTICA,
			'Asia' => DateTimeZone::ASIA,
			'Atlantic' => DateTimeZone::ATLANTIC,
			'Europe' => DateTimeZone::EUROPE,
			'Indian' => DateTimeZone::INDIAN,
			'Pacific' => DateTimeZone::PACIFIC,
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
	 * Add Scripts.
	 *
	 * @param string $handle The Name of the script
	 * @param string $src The Location of the script
	 * @param array $dep The dependencies (array of handles)
	 * @param string $version A PHP valid version string
	 * @todo check to include most of these options https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script
	 */
	public function add_script( string $handle = '', string $src = '', array $dep = array(), string $version = '', string $loc = '', int $priority = 10  ) {

		$GLOBALS['x_scripts'] = array();
		$scriptss = new self;

		if ( ! array_key_exists( $handle, $GLOBALS['x_scripts'] ) ) {
			$GLOBALS['x_scripts'] = array( $handle => $src );
		} else {
			return;
		}

		if( version_compare( $version, '0.0.1', '>=' ) !== false ) {
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
		$plugin = new X_Hooks();

		$plugin->add_action( 'x_' . $loc, array( $scriptss, 'render_script' ), $priority );

	}

	public function render_script() {
		echo '<script src="' . $this->src . $this->version . '"></script>';
	}

	public function render_link() {
		echo '<link href="' . $this->src . $this->version . '" rel="' . $this->rel . '"></script>';
	}

	/**
	 * Add Styles.
	 *
	 * @param string $handle The Name of the script
	 * @param string $src The Location of the script
	 * @param array $dep The dependencies (array of handles)
	 * @param string $version A PHP valid version string
	 * @todo check to include most of these options https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script
	 */
	public function add_link( string $handle = '', string $src = '', array $dep = array(), string $version = '', string $rel = 'stylesheet' , string $loc = '', int $priority = 10 ) {

		$GLOBALS['x_links'] = array();
		$scriptss = new self;

		if ( ! array_key_exists( $handle, $GLOBALS['x_links'] ) ) {
			$GLOBALS['x_links'] = array( $handle => $src );
		} else {
			return;
		}

		if( version_compare( $version, '0.0.1', '>=' ) !== false ) {
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

	public function get_url( string $type = null, $item = null  ) {

		$get = new X_Get();
		$site_url = $this->get_site_url();
		$fragment = '';

		if ( is_null( $type ) ) {

			$type = 'pages';
			$fragment = $type . '/';

		}

		if ( is_numeric( $item ) ) {

			$item = $get->get_item_by_id( $type, $item );

			if ( is_object( $item )
				&& property_exists( $item, 'slug' ) ) {
				$item = $item->slug;
			} else {
				$item = '';
			}

		}

		return rtrim( $site_url, '/\\' ) . '/' . $fragment . $item;

	}

	public function get_domain( string $subdomain = ''  ) {

		$get = new X_Get();
		$site_url = $get->get_item_by( 'settings', 'slug', 'x_site_url' );
		$subdomain = empty( $subdomain ) ? '' : $subdomain . '.';

		if ( false === $site_url ) {
			$site_url = $_SERVER['SERVER_NAME'];
		} else {
			$site_url = str_replace( array( 'https://', 'www.' ), '', $site_url->value);
		}
		
		return rtrim(  $subdomain . $site_url, '\\/' );

	}

	public function get_site_url() {

		$get = new X_Get();
		$site_url = $get->get_item_by( 'settings', 'slug', 'x_site_url' );

		if ( false === $site_url ) {
			$site_url = $_SERVER['SERVER_NAME'];
		} else {
			$site_url = $site_url->value;
		}
		
		return rtrim( $site_url, '\\/' );

	}

	public function is_admin( ) {
		
		if ( isset( $_SERVER )
			&& isset( $_SERVER['SCRIPT_NAME'] )
			&& '/admin.php' === $_SERVER['SCRIPT_NAME']
		) {
			return true;
		}

		return false;

	}

	/**
	 * Setup constants.
	 */
	public static function set_constants( array $constants = array() ) {

		foreach ( $constants as $key => $value ) {

			define( $key, $value );

		}

	}

}
