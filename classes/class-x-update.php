<?php
/**
 * X_Update class
 *
 * @package uhleloX\classes\models
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
 * Class to handle updates
 *
 * Defines Update paths and variables,
 * Tests connection to remote,
 * Checks versions available,
 * Downloads and validates update,
 * Installs update.
 *
 * @since 1.0.0
 */
class X_Update {

	/**
	 * Trigger after update files.
	 *
	 * @since 1.0.0
	 * @var string $triggers_file The file triggered after an update.
	 */
	private $triggers_file = 'triggers.php';

	/**
	 * Temporary filename.
	 *
	 * @since 1.0.0
	 * @var string $update_file Filename of the temporary update zip file.
	 */
	private $update_file = 'update.zip';

	/**
	 * Signature filename.
	 *
	 * @since 1.0.0
	 * @var string $update_signature_file Filename of the signature file.
	 */
	private $update_signature_file = 'signature';

	/**
	 * Update URL.
	 *
	 * @since 1.0.0
	 * @var string $update_url The remote update URL.
	 */
	private $update_url = 'https://api.uhlelox.com/updates/index.php';

	/**
	 * Update URL.
	 *
	 * @since 1.0.0
	 * @var string $update_url The remote update URL.
	 */
	private $version_file = '';

	/**
	 * Path to public key.
	 *
	 * @since 1.0.0
	 * @var string $update_file Path to the public key file.
	 */
	private $pubkey_path = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->version_file = htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/version';
		$this->pubkey_path = htmlspecialchars( stripslashes( $_SERVER['DOCUMENT_ROOT'] ) ) . '/public_key.pem';

	}

	/**
	 * Get current version
	 *
	 * @return array | string $return The current version data array or string error.
	 */
	public function get_current_version() {

		$return = json_decode( file_get_contents( $this->version_file ), true );

		if ( null === $return ) {

			$return = 'Failed loading version.';

		}

		return $return;

	}

	/**
	 * Get new version
	 *
	 * @return array | bool The new version data array or bool false.
	 */
	private function get_new_version() {

		$current_version = $this->get_current_version();
		$new_ver_url     = $this->update_url . '?operation=update&buildid=' . $current_version['buildid'];
		$test            = $this->test_connection( $new_ver_url );
		if ( 200 === $test[0] ) {
			return json_decode( $test[1], true );
		}
		return false;

	}

	/**
	 * Get roadmap
	 *
	 * @return array | bool The new version data array or bool false.
	 */
	public function get_roadmap() {

		$current_version = $this->get_current_version();
		$new_ver_url     = $this->update_url . '?operation=update&buildid=' . ( $current_version['buildid'] - 1 );
		$test            = $this->test_connection( $new_ver_url );
		if ( 200 === $test[0] ) {
			return json_decode( $test[1], true );
		}
		return false;

	}

	/**
	 * Get the new version update data
	 *
	 * @since 1.0.0
	 * @return array The new version data array.
	 */
	public function get_version_update() {

		$test = $this->test_connection( $this->update_url );

		if ( 202 !== $test[0] ) {

			return 'Could not connect';

		} else {

			return $this->get_new_version();

		}

	}

	/**
	 * Test connection to remote
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The Remote URL.
	 * @return array Array of status code and curl error content.
	 */
	private function test_connection( $url ) {

		$ch = curl_init( $url . '?operation=test' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 2 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$content  = @curl_exec( $ch );

		if( curl_errno( $ch ) ) {
			$content = curl_error( $ch );
		}

		$code = curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		curl_close( $ch );

		return array( $code, $content );

	}

	/**
	 * Download the update
	 *
	 * @return string | bool true if Download successful, error string if not.
	 */
	public function download_update() {

		$new_version = $this->get_new_version();
		$download_ok = $this->check_download( $this->update_file, $this->update_signature_file, $new_version['buildid'], $this->pubkey_path );

		return $download_ok;
	}

	/**
	 * Check download
	 *
	 * @param string $update_file The name of the update file.
	 * @param string $update_signature_file THe name of the update signature file.
	 * @param int    $current_build_id The ID of the build.
	 * @param string $pubkey_path The path to the public keyfile.
	 * @return bool | string true or false if code 200, else errors string.
	 */
	private function check_download( $update_file, $update_signature_file, $current_build_id, $pubkey_path ) {

		$download_url = $this->update_url . '?operation=download&buildid=' . $current_build_id;
		$res_code     = $this->download_file( $update_file, $download_url );

		if ( 200 === $res_code ) {

			$this->download_file( $update_signature_file, $download_url . '&signature' );

			$key       = openssl_pkey_get_public( file_get_contents( $pubkey_path ) );
			$hash      = ( hash_file( 'sha512', $update_file ) );
			$signature = base64_decode( file_get_contents( $update_signature_file ) );

			openssl_public_decrypt( $signature, $decrypted, $key );

			$decrypted = trim( $decrypted );

			if ( $decrypted === $hash ) {
				return 'downloaded';
			} else {
				return 'Zip corrupt or key invalid.';
			}

		} else {
			return 'Received status code' . $res_code . 'from update server. Expected 200. Aborting';
		}
	}

	/**
	 * Download the update file.
	 *
	 * @param string $name Name of the file downloaded.
	 * @param string $url The remote URL.
	 * @throws Exception File open error.
	 * @return string | int The cURL error or status code.
	 */
	private function download_file( $name, $url ) {

		$fp = fopen( $name, 'w+' );

		if ( false === $fp ) {
			throw new Exception( 'Could not open: ' . $name );
		}

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 2 );
		@curl_exec( $ch );
		if( curl_errno( $ch ) ){
		  return curl_error( $ch );
		}
		$status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		return $status_code;

	}

	/**
	 * Install the udpate
	 */
	public function install_update() {

		if ( ! file_exists( $this->update_file )
			|| ! file_exists( $this->update_signature_file ) ) {

			return 'Missing update file.';

		} else {

			$zip = new ZipArchive();
			$res = $zip->open( $this->update_file );
			if ( true === $res ) {

				$path = pathinfo( realpath( $this->update_file ), PATHINFO_DIRNAME );
				$zip->extractTo( $path );
				$zip->close();

				/**
				 * Move all files from downloaded, unzipped folder to the install folder.
				 *Overwrites existing, leaves others alone.
				 *
				 * @see X_Functions->move_recursive();
				 */
				$functions = new X_Functions();
				$functions->move_recursive( $path . '/uhleloX-' . $this->get_version_update()['version'], dirname( __DIR__, 2 ) . '/' . basename( dirname( __DIR__, 1 ) ), true );

				/**
				 * Delete the folder unzipped, after we moved all files.
				 */
				rmdir( $path . '/uhleloX-' . $this->get_version_update()['version'] );

				/**
				 * Run triggers, if any
				 */
				if ( file_exists( $this->triggers_file ) ) {

					include_once $this->triggers_file;

				}

				/**
				 * Delete the original update ZIP, signature, trigger file.
				 */
				$cu1 = unlink( $this->update_file );
				$cu2 = unlink( $this->update_signature_file );
				$cu3 = true;
				if ( file_exists( $this->triggers_file ) ) {
					$cu3 = unlink( $this->triggers_file );
				}

				if ( ! $cu1
					|| ! $cu2
					|| ! $cu3
				) {
					return 'Could not delete operation files.';
				}

				$updated_v_file = $this->update_version_file();

				if ( ! $updated_v_file ) {
					return 'Could not update version file';
				}

				return 'installed';

			} else {
				return 'Couold not open update file';
			}
		}
	}

	/**
	 * Update the version file
	 *
	 * @return bool Boolean true if version file succeess, false if not.
	 */
	private function update_version_file() {

		$version = $this->get_current_version();
		$new_url = $this->update_url . '?operation=update&buildid=' . $version['buildid'];
		$ret     = $this->test_connection( $new_url );

		if ( ! $ret ) {
			return false;
		} else {
			return file_put_contents( 'version', $ret[1] );
		}

	}

}
