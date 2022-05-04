<?php
/**
 * Simple PHP PDO Class
 * @author Miks Zvirbulis (twitter.com/MiksZvirbulis)
 * @version 1.1
 * 1.0 - First version launched. Allows access to one database and a few regular functions have been created.
 * 1.1 - Added a constructor which allows multiple databases to be called on different variables.
 */
class X_Db {

	// Connection variable. DO NOT CHANGE!
	protected $connection;

	// @bool default for this is to be left to FALSE, please. This determines the connection state.
	public $connected = false;

	// @bool this controls if the errors are displayed. By default, this is set to true.
	private $errors = true;

	public function __construct(){
		try{
			$this->connected = true;

			$this->connection = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		}
		catch(PDOException $e){
			$this->connected = false;
			if($this->errors === true){
				$this->error($e->getMessage());
			}else{
				return false;
			}
		}
	}

	public function __destruct(){
		$this->connected = false;
		$this->connection = null;
	}

	public function error($error){
		error_log( print_r( $error, true ), 0 );
	}

	public function fetch($query, $parameters = array()){
		if($this->connected === true){
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				return $query->fetch();
			}
			catch(PDOException $e){
				if($this->errors === true){
					$this->error($e->getMessage());
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	public function fetch_all($query, $parameters = array()){
		if($this->connected === true){
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				return $query->fetchAll();
			}
			catch(PDOException $e){
				if($this->errors === true){
					$this->error($e->getMessage());
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	public function count($query, $parameters = array()){
		if($this->connected === true){
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				return $query->rowCount();
			}
			catch(PDOException $e){
				if($this->errors === true){
					$this->error($e->getMessage());
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	public function insert($query, $parameters = array()){
		if($this->connected === true){
			try{
				$query = $this->connection->prepare($query);
				$query->execute($parameters);
				return $this->connection->lastInsertId();
			}
			catch(PDOException $e){
				if($this->errors === true){
					$this->error($e->getMessage());
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	public function update($query, $parameters = array()){
		if($this->connected === true){
			return $this->insert($query, $parameters);
		}else{
			return false;
		}
	}

	public function delete($query, $parameters = array()){
		if($this->connected === true){
			return $this->insert($query, $parameters);
		}else{
			return false;
		}
	}

	public function table_exists($table){
		if($this->connected === true){
			try{
				$query = $this->count("SHOW TABLES LIKE '$table'");
				return ($query > 0) ? true : false;
			}
			catch(PDOException $e){
				if($this->errors === true){
					$this->error($e->getMessage());
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
}
