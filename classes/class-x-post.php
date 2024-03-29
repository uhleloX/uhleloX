<?php
/**
 * X_Post class
 *
 * @since 1.0.0
 * @package uhleloX\classes\models
 */

/**
 * Security: Do not access directly.
 */
if ( count( get_included_files() ) === 1 ) {
	echo 'Direct access not allowed';
	exit();
}

/**
 * Class to Post data to the Database.
 *
 * Implements all methods to send content.
 * Acts as an orchestrator between X_Db and presenters to POST data.
 *
 * @since 1.0.0
 */
class X_Post extends X_Model {

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

		$this->data = $data;
		unset( $this->data['id'], $this->data['save'], $this->data['setup'], $this->data['x_token'], $this->data['password'], $this->data['key_phrase'] );

	}

	/**
	 * This and the parent function hve to be reviewed.
	 * Something fishy here
	 *
	 * @param array $columns The columns to setup.
	 * @return obj Object with properties matching the columns.
	 */
	public function set_columns( array $columns = array() ) {

		$item = new stdClass();
		foreach ( $columns as $column ) {

			/**
			 * Ignore invalid SnakeCase.
			 */
			$item->{$column->Field} = null;

		}

		return $item;
	}

	/**
	 * Function to upload Files.
	 * Needs to be reviewed for sanity and validation
	 *
	 * @param string $input_name the $_FILES['inputname'].
	 * @param bool   $base_64 If the uploaded item is a base64 encoded media.
	 */
	public function upload( string $input_name = '', bool $base_64 = false ) {

		$result = array();
		$upload_ok = false;
		$target_dir = SITE_ROOT . '/var/uploads/';
		$get = new X_Get();
		$upload_size = $get->get_item_by( 'settings', 'uuid', 'x_upload_max_size' );
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
			$result = false;
			return $result;
		}

		if ( false === $base_64
			&& isset( $_FILES[ $input_name ]['error'] )
			&& 0 !== $_FILES[ $input_name ]['error'] ) {

			$result['error'] = $_FILES[ $input_name ]['error'];
			return $result;

		}

		// Check if image file is a actual image or fake image.
		if ( false === $base_64 ) {
			$image_size = filesize( $_FILES[ $input_name ]['tmp_name'] );
		} else {
			$data = $_POST['imgURL'];
			list( $type, $data ) = explode( ';', $data );
			list( $enc, $data)      = explode( ',', $data );
			$image_size = (int) ( strlen( rtrim( $data, '=' ) ) * 0.75 ); // in byte.
			$data = base64_decode( $data );
		}
		if ( false !== $image_size ) {
			$upload_ok = true;
		} else {
			$result['error'] = 'Could not analyse the item. Probably no image?';
			return $result;
		}

		// Check file size.
		if ( $image_size > intval( $upload_size->value ) ) {
			$result['error'] = 'File size is too large.';
			return $result;
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

			$result['error'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
			return $result;

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

			$result['error'] = 'Sorry, your file was not uploaded.';
			return $result;

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
					'uuid' => pathinfo( $target_file, PATHINFO_FILENAME ) . '.' . strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) ),
					'title' => pathinfo( $target_file, PATHINFO_FILENAME ) . '.' . strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) ),
					'type' => $image_file_type,
					'publicationdate' => '2022-01-01',
					'editdate' => '2022-01-01',
				);

				$media->setup_data( $media_array );
				$media_id = $media->insert( 'media' );
				if ( false !== $media_id ) {
					$result['success'] = $media_array;
				} else {
					$result['success'] = 'The File was uploaded, but not saved to the database.';
				}

				return $result;

			} elseif ( file_put_contents( $target_file, $data ) ) {
					$result = true;
					return $result;

			} else {

				$result['error'] = 'Sorry, there was an error uploading your file.';

				return $result;

			}
		}
	}

	/**
	 * Inserts the current object into the database, and sets its ID property.
	 *
	 * @param string $type The Type of content to insert (must match the table name).
	 * @return mixed ID of inserted item or false.
	 */
	public function insert( string $type = '' ) {

		if ( ! is_null( $this->id ) ) {
			trigger_error( 'Attempt to insert an object that already has its ID property set:' . $this->id, E_USER_ERROR );
		}

		$what = implode( ', ', array_keys( $this->data ) );
		$values = implode( ', ', array_fill( 0, count( $this->data ), '?' ) );
		$sql = 'INSERT INTO ' . $this->whitelist_tables( $type ) . ' ( ' . $what . ' ) VALUES ( ' . $values . ' )';
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
	 * @return mixed ID of connected item or false.
	 */
	public function connect( string $type = '', int $left = null, int $right = null ) {

		if ( is_null( $left ) || is_null( $right ) ) {
			trigger_error( 'Attempt to connect one or more items with ID NULL. You must pass a relationship uuid, a "left" and "right" item to connect.', E_USER_ERROR );
		}

		$entities = explode( '_', $this->whitelist_tables( $type ) );

		$sql = 'INSERT INTO ' . $this->whitelist_tables( $type ) . ' ( ' . $entities[0] . ', ' . $entities[1] . ' ) VALUES ( ?, ? )';
		$params = array( $left, $right );
		$this->post( $sql, $params, false );

		return $this->results;

	}

	/**
	 * Inserts an arbitrary table.
	 *
	 * @param string $name The new database table name insert.
	 * @param string $config The new table configuration.
	 */
	public function add_table( string $name = '', string $config = '' ) {

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $name . '( ' . $config . ' ) ENGINE=InnoDB ROW_FORMAT=COMPRESSED;';
		$this->post( $sql, array(), false );

	}

	/**
	 * Inserts a relationship table.
	 *
	 * @param string $name The new database table name insert.
	 * @param string $entity_a The "left" part of the relationship.
	 * @param string $entity_b The "left" part of the relationship.
	 */
	public function add_rel_table( string $name = '', string $entity_a = '', string $entity_b = '' ) {

		$config = 'id BIGINT UNSIGNED AUTO_INCREMENT, ' . $entity_a . ' BIGINT UNSIGNED, ' . $entity_b . ' BIGINT UNSIGNED, PRIMARY KEY (`id`, `' . $entity_a . '`, `' . $entity_b . '`)';

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $name . '( ' . $config . ' ) ENGINE=InnoDB ROW_FORMAT=COMPRESSED;';
		$this->post( $sql, array(), false );

	}


	/**
	 * Updates the current Article object in the database.
	 *
	 * @param string $type The Type of content to insert (must match the table name).
	 * @return mixed ID of updated item or false.
	 */
	public function update( string $type = '' ) {

		if ( is_null( $this->id ) ) {
			trigger_error( 'Attempt to update an Article object that does not have its ID property set.', E_USER_ERROR );
		}

		/**
		 * Note, `id = LAST_INSERT_ID(id)` is there to pass back the LastInsertId to the app.
		 * PDO by default (well, SQL in general) does not pass back the UPDATED item, only the last(first)
		 * INSERTED ID by default.
		 * This is excused by saying "you might update several items at once". Fact thou is, you can also insert
		 * several items at once, and yet still it will return at least one ID when inserting, so this is
		 * just garbage from developers trying to explain their omittances.
		 * Found the solution here https://stackoverflow.com/questions/26498960/lastinsertid-for-update-in-prepared-statement#answer-26499145
		 */
		$what = implode( ' = ?, ', array_keys( $this->data ) );
		$sql = 'UPDATE ' . $this->whitelist_tables( $type ) . ' SET ' . $what . ' = ?, id = LAST_INSERT_ID(id) WHERE id = ?';
		$params = array_values( $this->data );
		$params[] = $this->id;
		$this->change( $sql, $params, true );
		$this->id = $this->results;

		return $this->results;

	}

	/**
	 * Insert Column.
	 *
	 * @todo review @params
	 * @see X_Post::post()
	 */
	public function alter( string $type = '', string $name = '', string $definition = '', string $position = '' ) {

		$after = empty( $position ) ? '' : 'AFTER ';
		$sql = 'ALTER TABLE ' . $this->whitelist_tables( $type ) . ' ADD ' . $name . ' ' . $definition . ' ' . $after . $position;

		$this->post( $sql, array(), false );

	}


}
