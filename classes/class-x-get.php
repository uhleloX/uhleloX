<?php
/**
 * Get data from the database.
 *
 * @since 1.0.0
 * @package uhleloX\classes\models
 */

/**
 * The Class to Get data from the Database.
 *
 * Implements all methods to retrieve content.
 *
 * @since 1.0.0
 */
class X_Get extends X_Model {

	/**
	 * Returns an object of any Type of Content by ID
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param int    $id The Item ID to retrieve.
	 * @return object The article object, or false if the record was not found or there was a problem
	 */
	public function get_item_by_id( string $type = '', int $id = 0 ) {

		$sql = 'SELECT * FROM ' . $type . ' WHERE id = ?';
		$params = array( $id );

		$this->get( $sql, $params, true );

		return $this->results;

	}

	/**
	 * Returns an object of any Type of Content by any column name
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param string $where The Column to query.
	 * @param string $val The value to fetch by.
	 * @return object The article object, or false if the record was not found or there was a problem
	 */
	public function get_item_by( string $type = '', string $where = 'id', string $val = '' ) {

		$sql = 'SELECT * FROM ' . $type . ' WHERE ' . $where . ' = ?';
		$params = array( $val );

		$this->get( $sql, $params, true );
		return $this->results;

	}

	/**
	 * Returns objects of any Type of Content where value is in either columns.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param string $where The Column to query.
	 * @param string $val The value to fetch by.
	 * @return object The article object, or false if the record was not found or there was a problem
	 */
	public function get_items_in( string $type = '', array $where = array('id'), string $val = '' ) {

		$where = implode(', ', $where);
		$sql = 'SELECT * FROM ' . $type . ' WHERE ? IN (' . $where . ')';
		$params = array( $val );

		$this->get( $sql, $params, false );
		return $this->results;

	}

	/**
	 * Returns all (or a range of) Objects of any Type of Content.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param array  $args Associative array of arguments to pass to the query.
	 * @return Array|false Array of Objects or false if none.
	 */
	public function get_items( string $type = '', array $args = array() ) {

		$default_args = array(
			'orderby' => 'id',
			'order' => 'ASC',
			'limit' => 100,
		);

		$args = array_merge( $default_args, $args );
		$limit = '-1' === $args['limit'] ? '' : ' limit ?';

		$sql = 'SELECT * FROM ' . $type . ' ORDER BY ? ' . $args['order'] . $limit;
		$params = empty( $limit ) ? array( $args['orderby'] ) : array( $args['orderby'], $args['limit'] );

		$this->get( $sql, $params, false );

		return $this->results;

	}

	/**
	 * Returns an object of any Type of Content by any column name
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param string $where The Column to query.
	 * @param string $val The value to fetch by.
	 * @return object The article object, or false if the record was not found or there was a problem
	 */
	public function get_related_items( string $from = '', string $relation = '', string $on = '', string $from_on = '', string $what = '', string $result = '' ) {
	//public function get_related_items( $from, $relation, $on, $from_on, $what, $result, $return_a, $return_b, $where_side, $where_col, $value ) {
		// SELECT a.*, b.* FROM users a INNER JOIN user_role ab ON ab.userid = a.id INNER JOIN roles b ON b.id = ab.role
		// 'users', 'user_role', 'userid', 'id', 'roles', 'id', '*','*'
		$sql = 'SELECT a.*, b.* FROM ' . $from . ' a INNER JOIN ' . $relation . ' ab ON ab.' . $on . ' = a.' . $from_on . ' INNER JOIN ' . $what . ' b ON b.' . $result . ' = ab.' . $result;
		// $sql = 'SELECT a.' . $return_a . ', b.' . $return_b . ' FROM ' . $from . ' a INNER JOIN ' . $relation . ' ab ON ab.' . $on . '= a.' . $from_on . ' INNER JOIN ' . $what . ' b ON b.' . $result . '= ab.' . $result . ' WHERE ' . $where_side . '.' . $where_col . ' = ' . $value;
		//$sql = 'SELECT * FROM ' . $relation . ' WHERE ' . $by . ' = ' . $what;
		$params = array();

		$this->get( $sql, $params, false );

		return $this->results;

	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @return bool|error Boolean true if exists, false if not, error if connection failure.
	 */
	public function check_if_exists( string $type = '' ) {

		$this->check( $type );

		return $this->results;

	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @return bool|error Boolean true if exists, false if not, error if connection failure.
	 */
	public function show_columns( string $type = '', bool $full = false ) {

		$full = false === $full ? '' : 'FULL';
		$sql = 'SHOW ' . $full . ' COLUMNS FROM ' . $type;
		$this->get( $sql, array(), false );

		return $this->results;

	}

}
