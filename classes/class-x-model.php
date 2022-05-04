<?php
/**
 * Implements the structures necessary to post, delete and get.
 *
 * @since 1.0.0
 * @package uhleloX\classes\models
 */

/**
 * The Class to implement a model for database post, delete and get.
 *
 * @since 1.0.0
 */
class X_Model {

	/**
	 * The ID of the item.
	 *
	 * @since 1.0.0
	 * @var int $id The article ID from the database.
	 */
	public $id = null;


	/**
	 * Sets the object's properties using the values in the supplied array
	 */
	public function __construct() {

		$this->db = new X_Db();

		$this->allowed_db_tables = X_Setup::tables( 'all' );
		$this->results = array();

	}

	/**
	 * This and the child function hve to be reviewed.
	 * Something fishy here
	 *
	 * @param array $columns The columsn to setup.
	 */
	protected function set_columns( array $columns = array() ) {

		return $columns;

	}

	/**
	 * Get Data.
	 *
	 * @param string $sql The SQL Query.
	 * @param array  $params The values to query by.
	 * @param bool   $single Whether to return single object or array of obecjts. Default true.
	 * @return mixed Object or array of objects, false on failure or no results.
	 */
	protected function get( string $sql = '', array $params = array(), bool $single = true ) {

		if ( true === $single ) {
			$this->results = $this->db->fetch( $sql, $params );
		} else {
			$this->results = $this->db->fetch_all( $sql, $params );
		}

		return $this->results;

	}


	/**
	 * Insert Data.
	 *
	 * @param string $sql The SQL Query.
	 * @param array  $params The values to query by.
	 * @param bool   $update Whether to update or insert an item. Default True.
	 * @return mixed ID of inserted item or false.
	 */
	protected function post( string $sql = '', array $params = array(), $update = false ) {

		if ( true === $update ) {
			$this->results = $this->db->update( $sql, $params );
		} else {
			$this->results = $this->db->insert( $sql, $params );
		}

		return $this->results;

	}


	/**
	 * Update data.
	 *
	 * @param string $sql The SQL Query.
	 * @param array  $params The values to query by.
	 * @param bool   $update Whether to update or insert an item. Default True.
	 * @see $this->post()
	 */
	protected function change( string $sql = '', array $params = array(), $update = true ) {

		$this->post( $sql, $params, $update );

	}


	/**
	 * Delete data.
	 *
	 * @param string $sql The SQL Query.
	 * @param array  $params The values to query by.
	 * @return bool True or False.
	 */
	protected function delete( string $sql = '', array $params = array() ) {

		$this->results = $this->db->delete( $sql, $params );

		return $this->results;

	}

	/**
	 * Check if exists.
	 *
	 * @param string $sql The SQL Query.
	 * @return bool|error Boolean true if exists, false if not, error if connection failure.
	 */
	protected function check( string $sql = '' ) {

		$this->results = $this->db->table_exists( $sql );

		return $this->results;

	}

	/**
	 * Validate passed table to allowed tables
	 *
	 * @param string $table The table name to check.
	 * @throws DomainException $e The exception.
	 */
	protected function whitelist_tables( string $table = '' ) {

		try {

			if ( in_array( $table, $this->allowed_db_tables ) ) {
				return $table;
			} else {
				throw new DomainException( 'Trying to access invalid Database Table.', 1 );
			}
		} catch ( DomainException $e ) {

			error_log( $e->getMessage() . print_r( $e, true ), 0 );
			error_log( 'Table attempted to access: ' . $table );
			echo $e->getMessage();
			exit();

		}
	}

}
