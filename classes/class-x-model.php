<?php
/**
 * Holds a Class to orchstrate Database Operations between models (post, delete, get)
 * and the PDO database object.
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
 * The Class to implement a model for database post, delete and get.
 *
 * @since 1.0.0
 */
class X_Model {

	/**
	 * The DB PDO Object
	 *
	 * @since 1.0.0
	 * @var obj $db The new PDO Object.
	 */
	private $db = null;

	/**
	 * The Allowed DB tables.
	 *
	 * @since 1.0.0
	 * @var array $allowed_db_tables Allowed DB tables of uhleloX.
	 */
	private $allowed_db_tables = array();

	/**
	 * The Results of any query operation.
	 *
	 * @since 1.0.0
	 * @var array|bool $results array of objects or false as result of any query.
	 */
	public $results = false;

	/**
	 * The ID of the item.
	 *
	 * @since 1.0.0
	 * @var int $id The object ID from the database.
	 */
	public $id = null;


	/**
	 * Constructor
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
	 * @see X_Model::post()
	 * @return void
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
