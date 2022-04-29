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
	 * The Publication Date of the item.
	 *
	 * @var int $publicationdate When the article was published.
	 */
	public $publicationdate = null;

	/**
	 * The Title of the item.
	 *
	 * @var string $title Full title of the article.
	 */
	public $title = null;

	/**
	 * The Summary of the item.
	 *
	 * @var string $summary A short summary of the article.
	 */
	public $summary = null;

	/**
	 * The Content of the item.
	 *
	 * @var $content string The HTML content of the article.
	 */
	public $content = null;


	/**
	 * Sets the object's properties using the values in the supplied array
	 */
	public function __construct($columns=array()) {

		$this->db = new X_Db( HOST, DB_USERNAME, DB_PASSWORD, DB_NAME );

		foreach ( $columns as $column ) {
			$this->{$column->Field} = null;
		}
		$this->results = array();

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

		$method = true === $single ? 'fetch' : 'fetchAll';
		$this->results = $this->db->$method( $sql, $params );

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

		$method = true === $update ? 'update' : 'insert';
		$this->results = $this->db->$method( $sql, $params );

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

		$this->results = $this->db->tableExists( $sql );

		return $this->results;

	}

}
