<?php
/**
 * Post data to the database.
 *
 * @since 1.0.0
 * @package uhleloX\classes\models
 */

/**
 * The Class to Post data to the Database.
 *
 * Implements all methods to post content.
 *
 * @since 1.0.0
 */
class X_Post extends X_Model {

	/**
	 * Sets the object's properties using the values in the supplied array
	 */
	public function __construct($columns=array()) {

		parent::__construct($columns);

	}

	/**
	 * Sets the object's properties using the edit form post values in the supplied array
	 *
	 * @param array $data The form post values.
	 * @todo $this->single values are useless. Sanitize them and put to $this->data array..
	 */
	public function setup_data( $data ) {

		if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
			$this->id = (int) $data['id'];
		}

		if ( isset( $data['title'] ) ) {
			$this->title = preg_replace( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", '', $data['title'] );
		}

		if ( isset( $data['slug'] ) ) {
			$this->slug = strtolower( trim( preg_replace('/[^A-Za-z0-9-]+/', '-', $data['slug'] ) ) );
		}

		if ( isset( $data['summary'] ) ) {
			$this->summary = preg_replace( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", '', $data['summary'] );
		}
		if ( isset( $data['content'] ) ) {
			$this->content = $data['content'];
		}

		$this->data = $data;
		unset( $this->data['id'], $this->data['save'], $this->data['setup'], $this->data['token'] );

	}

	public function upload( string $input_name = '', bool $base_64 = false ) {

		$error = array();
		$upload_ok = false;
		$target_dir = SITE_ROOT . '/var/uploads/';
		$get = new X_Get();
		$upload_size = $get->get_item_by( 'settings', 'slug', 'x_upload_max_size' );
		$suffix = 1;
		if ( false === $base_64 ) {
			$name = $_FILES[ $input_name ]['name'];
			$tmp_name = $_FILES[ $input_name ]['tmp_name'];
		} else {
			$name = $_POST['imgFullName'];
			$tmp_name = '';
		}

		if ( false === $base_64
			&& ( ! isset( $_FILES[ $input_name ] )
				|| empty( $_FILES[ $input_name ] )
			)
		) {
			return;
		}

		if ( false === $base_64 
			&& isset( $_FILES[ $input_name ]['error'] )
			&& 0 !== $_FILES[ $input_name ]['error'] ) {

			$error[ 'error' ] = $_FILES[ $input_name ]['error'];
			return $error;

		}

		// Check if image file is a actual image or fake image.
		if ( false === $base_64 ) {
			$image_size = filesize( $_FILES[ $input_name ]['tmp_name'] );
		} else {
			$data = $_POST['imgURL'];
			list( $type, $data ) = explode( ';', $data );
			list( $enc, $data)      = explode( ',', $data );
			$image_size = (int)(strlen(rtrim($data, '=')) * 0.75); // in byte
			$data = base64_decode($data);
		}
		if ( false !== $image_size ) {
			$upload_ok = true;
		} else {
			$error['error'] = 'Could not analyse the item. Probably no image?';
			return $error;
		}

		// Check file size.
		error_log( print_r( $image_size, true ) );
		error_log( print_r( $upload_size->value, true ) );
		if ( $image_size > intval( $upload_size->value ) ) {
			$error[ 'error' ] = 'File size is too large.';
		  	return $error;
		}

		/**
		 * Should be an image, not too big, no errors.
		 *
		 * Proceed checking for type and exis
		 */
		$target_file = $target_dir . basename( $name );
		$actual_name = pathinfo( $target_file, PATHINFO_FILENAME );
		$original_name = $actual_name;
		$image_file_type = strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) );

		/**
		 * Check file type allowed.
		 *
		 * @todo Provide a setting for this.
		 */
		if ( 'jpg' !== $image_file_type
			&& 'png' !== $image_file_type
			&& 'jpeg' !== $image_file_type
			&& 'gif' !== $image_file_type ) {

			$error['error'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
			return $error;

		}

		// If file exists, suffix -n.
		while ( file_exists( $target_file )
			&& false === $base_64
		) {

			$actual_name = (string) $original_name . '-' . $suffix;
			$target_file = $target_dir . $actual_name . '.' . $image_file_type;
			$suffix++;

		}

		// Check if $upload_ok is set to 0 by an error.
		if ( true !== $upload_ok ) {

			$error['error'] = 'Sorry, your file was not uploaded.';
			return $error;

		} else {

			/**
			 * Move the temp file to the target location.
			 * On failure, error.
			 * On success, insert a database row for media, and return true.
			 *
			 * @todo maybe retunr file data on success.
			 * @todo set correct owner, date.
			 */
			if ( move_uploaded_file( $tmp_name, $target_file ) ) {

				$media = new self();
				$media_array = array(
					'owner' => 1,
					'slug' => pathinfo( $target_file, PATHINFO_FILENAME ) . '.' . strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) ),
					'title' => pathinfo( $target_file, PATHINFO_FILENAME ) . '.' . strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) ),
					'type' => $image_file_type,
					'publicationdate' => '2022-01-01',
					'editdate' => '2022-01-01',
				);

				$media->setup_data( $media_array );
				$media->insert( 'media' );

				return true;

			} elseif ( file_put_contents( $target_file, $data ) ) {

					return true;

				} else {

				$error['error'] = 'Sorry, there was an error uploading your file.';

				return $error;

			}
		}
	}

	/**
	 * Inserts the current object into the database, and sets its ID property.
	 *
	 * @param string $type The Type of content to insert (must match the table name).
	 */
	public function insert( string $type = '' ) {

		if ( ! is_null( $this->id ) ) {
			trigger_error( 'Attempt to insert an object that already has its ID property set:' . $this->id, E_USER_ERROR );
		}

		$what = implode( ', ', array_keys( $this->data ) );
		$values = implode( ', ', array_fill( 0, count( $this->data ), '?' ) );
		$sql = 'INSERT INTO ' . $type . ' ( ' . $what . ' ) VALUES ( ' . $values . ' )';
		$params = array_values( $this->data );
		$this->post( $sql, $params, false );
		$this->id = $this->results;

		return $this->results;

	}

	/**
	 * Connects 2 arbitrary items in their relationship table (new entry)
	 *
	 * @param string $type The Relationship.
	 * @param int    $left The left entity.
	 * @param int    $right The right entity.
	 */
	public function connect( string $type = '', int $left = null, int $right = null ) {

		if ( is_null( $left ) || is_null( $right ) ) {
			trigger_error( 'Attempt to connect one or more items with ID NULL. You must pass a relationship slug, a "left" and "right" item to connect.', E_USER_ERROR );
		}

		$entities = explode( '_', $type );

		$sql = 'INSERT INTO ' . $type . ' ( ' . $entities[0] . ', ' . $entities[1] . ' ) VALUES ( ?, ? )';
		$params = array( $left, $right );
		$this->post( $sql, $params, false );

		return $this->results;

	}

	/**
	 * Inserts the current object into the database, and sets its ID property.
	 *
	 * @param string $type The Type of content to insert (must match the table name).
	 */
	public function add_table( string $name = '', string $config = '' ) {

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $name . '( ' . $config . ' ) ENGINE=InnoDB ROW_FORMAT=COMPRESSED;';
		$this->post( $sql, array(), false );

	}


	/**
	 * Updates the current Article object in the database.
	 *
	 * @param string $type The Type of content to insert (must match the table name).
	 */
	public function update( string $type = '' ) {

		if ( is_null( $this->id ) ) {
			trigger_error( 'Attempt to update an Article object that does not have its ID property set.', E_USER_ERROR );
		}

		$what = implode( ' = ?, ', array_keys( $this->data ) );
		$sql = 'UPDATE ' . $type . ' SET ' . $what . ' = ? WHERE id = ?';
		$params = array_values( $this->data );
		$params[] = $this->id;

		$this->change( $sql, $params, true );
		$this->id = $this->results;

	}

	/**
	 * Insert Column.
	 *
	 * @param string $sql The SQL Query.
	 * @param array  $params The values to query by.
	 * @param bool   $update Whether to update or insert an item. Default True.
	 * @see $this->post()
	 */
	public function alter( string $type = '', string $name = '', string $definition = '', string $position = '' ) {

		$after = empty( $position ) ? '' : 'AFTER ';
		$sql = 'ALTER TABLE ' . $type . ' ADD ' . $name . ' ' . $definition . ' ' . $after . $position;
		//$sql = 'ALTER TABLE ' . $type . ' ADD ' . $name . ' ? ' . $after . '?';

		$this->post( $sql, array(), false );

	}


}
