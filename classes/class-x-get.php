<?php
/**
 * X_Get class
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
 * Class to Get data from the Database.
 *
 * Implements all methods to retrieve content.
 * Acts as an orchestrator between X_Db and presenters to GET data.
 *
 * @since 1.0.0
 */
class X_Get extends X_Model {

	/**
	 * Returns an object of any Type of Content by ID
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param int    $id The Item ID to retrieve.
	 * @return object|false The item object, or false if the record was not found or there was a problem
	 */
	public function get_item_by_id( string $type = '', int $id = 0 ) {

		$sql = 'SELECT * FROM ' . $this->whitelist_tables( $type ) . ' WHERE id = ?';
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
	 * @return object|false The item object, or false if the record was not found or there was a problem
	 */
	public function get_item_by( string $type = '', string $where = 'id', string $val = '' ) {

		$sql = 'SELECT * FROM ' . $this->whitelist_tables( $type ) . ' WHERE ' . $where . ' = ?';
		$params = array( $val );

		$this->get( $sql, $params, true );
		return $this->results;

	}

	/**
	 * Returns objects of any Type of Content where value is in either columns.
	 * 
	 * this is the longer things
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param array  $where The Column to query.
	 * @param string $val The value to fetch by.
	 * @return array|bool An array of item objects, or false if the record was not found or there was a problem
	 */
	public function get_items_in( string $type = '', array $where = array( 'id' ), string $val = '' ) {

		$where = implode( ', ', $where );
		$sql = 'SELECT * FROM ' . $this->whitelist_tables( $type ) . ' WHERE ? IN (' . $where . ')';
		$params = array( $val );

		$this->get( $sql, $params, false );
		return $this->results;

	}

	/**
	 * Returns all (or a range of) Objects of any Type of Content.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param array  $args Associative array of arguments to pass to the query.
	 * @return array|false An Array of Objects or false if none.
	 */
	public function get_items( string $type = '', array $args = array() ) {

		$default_args = array(
			'orderby' => 'id',
			'order' => 'ASC',
			'limit' => 100,
		);

		$args = array_merge( $default_args, $args );
		$limit = '-1' === $args['limit'] ? '' : ' limit ?';

		$sql = 'SELECT * FROM ' . $this->whitelist_tables( $type ) . ' ORDER BY ? ' . $args['order'] . $limit;
		$params = empty( $limit ) ? array( $args['orderby'] ) : array( $args['orderby'], $args['limit'] );

		$this->get( $sql, $params, false );

		return $this->results;

	}

	/**
	 * Returns an array either merged relationship objects,
	 * or an array of objects that are in a relationship representing one partner in the relationship.
	 *
	 * @param string $relationship The Relationship (as stored in the database table "relationships").
	 * @param array  $args         An array of arguments to get related objects and return data.
	 * @return array $results      An array of objects representing the results.
	 */
	public function get_related_items( string $relationship = '', array $args = array() ) {

		/**
		 * Get the relationship's object.
		 */
		$relationship = $this->get_item_by( 'relationships', 'uuid', $relationship );

		/**
		 * Validate the relationship table and partner tables to query.
		 */
		$relationship_table = $this->whitelist_tables( $relationship->uuid );
		$left = $this->whitelist_tables( $relationship->entity_a );
		$right = $this->whitelist_tables( $relationship->entity_b );

		/**
		 * Allowed arguemnts for the $args parameter.
		 */
		$args_internal = array(
			'return' => 'uuid', // What to return of the found objects. '*' for all columns, default 'id'.
			'select' => 'both', // 'both', 'l', 'r'. Both returns both found objects in a relationship merged, l returns the left partner's object, r the right partner's object.
			'query_by' => '', // By what column name to query by.
			'query_in' => '', // In what partner to query by the column name value. Possible values 'l', 'r'.
			's' => '', // The search term to query by in the specific partner's column.
		);
		$allowed_args = array( 'return', 'select', 'query_by', 'query_in', 's' );
		$filtered_args = array_filter(
			$args,
			function ( $key ) use ( $allowed_args ) {
				return in_array( $key, $allowed_args );
			},
			ARRAY_FILTER_USE_KEY
		);

		/**
		 * Merge user args into the default args.
		 */
		$args = array_merge( $args_internal, $filtered_args );

		/**
		 * Build placeholders for SQL Query.
		 */
		$return = 'id' === $args['return'] ? 'id' : '*';
		$select = 'both' === $args['select'] ? 'SELECT l.' . $return . ', r.' . $return : 'SELECT ' . $args['select'] . '.' . $return;
		$where = ! empty( $args['query_by'] ) && ! empty( $args['query_in'] ) ? 'WHERE ' . $args['query_in'] . '.' . $args['query_by'] . ' = ?' : '';
		$l = rtrim( $left, 's' ); // column name in the relationship table for the entity_a.
		$r = rtrim( $right, 's' ); // column name in the relationship table for the entity_b.

		/**
		 * Build SQL
		 */
		$sql2 = $select . '
		    FROM ' . $left . ' l
		        INNER JOIN ' . $relationship_table . ' lr
		        ON lr.' . $l . ' = l.id
		        INNER JOIN ' . $right . ' r
		        ON r.id = lr.' . $r . ' ' .
			$where;

		/**
		 * Pass the search params to PDO
		 */
		$params = array( $args['s'] );

		/**
		 * Get the results and return them.
		 */
		$this->get( $sql2, $params, false );

		return $this->results;

	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @return bool|string True if exists, false if not, error string if connection failure.
	 */
	public function check_if_exists( string $type = '' ) {

		$this->check( $this->whitelist_tables( $type ) );

		return $this->results;

	}

	/**
	 * Show columns of table.
	 *
	 * @param string $type The Content Type (must match the DB Tablename).
	 * @param bool   $full Boolean tru for full information.
	 * @return array|bool An array of column names or false if none.
	 */
	public function show_columns( string $type = '', bool $full = false ) {

		$full = false === $full ? '' : 'FULL';
		$sql = 'SHOW ' . $full . ' COLUMNS FROM ' . $this->whitelist_tables( $type );
		$this->get( $sql, array(), false );

		return $this->results;

	}

}
