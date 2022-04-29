<?php
/**
 * Delete data from the database.
 *
 * @since 1.0.0
 * @package uhleloX\classes\models
 */

/**
 * The Class to Delete data from the Database.
 *
 * Implements all methods to delete content.
 *
 * @since 1.0.0
 */
class X_Delete extends X_Model {

	/**
	 * Deletes the current Article object from the database.
	 */
	public function delete_by_id( string $type = '', int $id = 0 ) {

		$sql = 'DELETE FROM ' . $type . ' WHERE id = ?';
		$this->delete( $sql, array( $id ) );

		return $this->results;

	}

}
