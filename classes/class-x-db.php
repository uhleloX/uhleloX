<?php
/**
 * X_Db class
 *
 * @package uhleloX\classes\models
 * @since 1.0.0
 */

/**
 * Class to create a PDO instance.
 *
 * @author Miks Zvirbulis (twitter.com/MiksZvirbulis)
 * @see https://github.com/MiksZvirbulis/PHP-PDO-Class
 *
 * @since 1.0.0
 * Amended for usage in uhleloX.
 */
class X_Db {

	/**
	 * PDO Connection
	 *
	 * @var obj $connection The PDO Connection.
	 */
	protected $connection;

	/**
	 * Connection state
	 *
	 * @var bool $connected Describes connection state. Default false.
	 */
	public $connected = false;

	/**
	 * Error display
	 *
	 * @var bool $errors Whether the errors should be displayed or not. Default true.
	 */
	private $errors = true;

	/**
	 * Constructor
	 */
	public function __construct() {

		try {

			$this->connected = true;

			$this->connection = new PDO( 'mysql:host=' . HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD );
			$this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->connection->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
			$this->connection->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );

		} catch ( PDOException $e ) {

			$this->connected = false;
			if ( true === $this->errors ) {

				$this->error( $e );

			} else {

				return false;

			}
		}
	}

	/**
	 * Destruct the connection once Object is shutdown.
	 */
	public function __destruct() {

		$this->connected = false;
		$this->connection = null;

	}

	/**
	 * Log errors
	 *
	 * @param mixed $error The PDO Exception message.
	 */
	public function error( $error ) {

		error_log( $error->getMessage() . print_r( $error, true ), 0 );

	}

	/**
	 * Fetch one item from the Database.
	 *
	 * @param string $query the SQL Query.
	 * @param array  $parameters Prepare query parameters.
	 */
	public function fetch( string $query = '', array $parameters = array() ) {

		if ( true === $this->connected ) {

			try {

				$query = $this->connection->prepare( $query );
				$query->execute( $parameters );

				return $query->fetch();

			} catch ( PDOException $e ) {

				if ( true === $this->errors ) {

					$this->error( $e );

				} else {

					return false;

				}
			}
		} else {

			return false;

		}
	}

	/**
	 * Fetch a number of items from the Database.
	 *
	 * @param string $query the SQL Query.
	 * @param array  $parameters Prepare query parameters.
	 */
	public function fetch_all( string $query = '', array $parameters = array() ) {

		if ( true === $this->connected ) {

			try {

				$query = $this->connection->prepare( $query );
				$query->execute( $parameters );

				return $query->fetchAll();

			} catch ( PDOException $e ) {

				if ( true === $this->errors ) {

					$this->error( $e );

				} else {

					return false;

				}
			}
		} else {

			return false;

		}
	}

	/**
	 * Count a number of items from the Database.
	 *
	 * @param string $query the SQL Query.
	 * @param array  $parameters Prepare query parameters.
	 */
	public function count( string $query = '', array $parameters = array() ) {

		if ( true === $this->connected ) {

			try {

				$query = $this->connection->prepare( $query );
				$query->execute( $parameters );

				return $query->rowCount();

			} catch ( PDOException $e ) {

				if ( true === $this->errors ) {

					$this->error( $e );

				} else {

					return false;

				}
			}
		} else {

			return false;

		}
	}

	/**
	 * Insert an item into the Database.
	 *
	 * @param string $query the SQL Query.
	 * @param array  $parameters Prepare query parameters.
	 */
	public function insert( string $query = '', array $parameters = array() ) {

		if ( true === $this->connected ) {

			try {

				$query = $this->connection->prepare( $query );
				$query->execute( $parameters );

				return $this->connection->lastInsertId();

			} catch ( PDOException $e ) {

				if ( true === $this->errors ) {

					$this->error( $e );

				} else {

					return false;

				}
			}
		} else {

			return false;

		}
	}

	/**
	 * Update and item in the Database.
	 *
	 * @param string $query the SQL Query.
	 * @param array  $parameters Prepare query parameters.
	 */
	public function update( string $query = '', array $parameters = array() ) {

		if ( true === $this->connected ) {

			return $this->insert( $query, $parameters );

		} else {

			return false;

		}
	}

	/**
	 * Delete and item from the Database.
	 *
	 * @param string $query the SQL Query.
	 * @param array  $parameters Prepare query parameters.
	 */
	public function delete( string $query = '', array $parameters = array() ) {

		if ( true === $this->connected ) {

			return $this->insert( $query, $parameters );

		} else {

			return false;

		}
	}

	/**
	 * Check if a table exists in the Database.
	 *
	 * @param string $table the table name to check for.
	 */
	public function table_exists( $table ) {

		if ( true === $this->connected ) {

			try {

				$query = $this->count( "SHOW TABLES LIKE '$table'" );
				return ( $query > 0 ) ? true : false;

			} catch ( PDOException $e ) {

				if ( true === $this->errors ) {

					$this->error( $e );

				} else {

					return false;

				}
			}
		} else {

			return false;

		}
	}
}
