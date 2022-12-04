<?php
/**
 * X_Delete class
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
 * Class to delete items from the Database.
 *
 * @since 1.0.0
 */
class X_Delete extends X_Model {

	/**
	 * Deletes an item by ID from the database.
	 *
	 * @param string $type The Database Table.
	 * @param int    $id   The row ID to delete.
	 */
	public function delete_by_id( string $type = '', int $id = 0 ) {

		$sql = 'DELETE FROM ' . $this->whitelist_tables( $type ) . ' WHERE id = ?';
		$this->delete( $sql, array( $id ) );

		return $this->results;

	}

}
