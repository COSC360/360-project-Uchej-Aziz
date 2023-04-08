<?php 
require_once 'Environment.class.php';
	
// Load Environment variables
/**
 * ConnectDB
 */
class ConnectDB {
	private $DATABASE_HOST= "";
	private $DATABASE_DB = "";
	private $DATABASE_USER = "";
	private $DATABASE_PASSWORD = "";

    /**
     * __constructor
     *
     */

	 function __construct() {
		(new Environment($_SERVER["DOCUMENT_ROOT"].'/.env'))->load();
		$this->setDatabaseHost(getenv('DATABASE_HOST'));
		$this->setDatabaseDb(getenv('DATABASE_DB'));
		$this->setDatabaseUser(getenv('DATABASE_USER'));
		$this->SetDatabasePassword(getenv('DATABASE_PASSWORD'));
	}
	
	/**
	 * setDabaseHost
	 *
	 * @param  string $dbhost
	 * @return void
	 */
	private function setDatabaseHost(string $dbhost) : void {
		$this->DATABASE_HOST = $dbhost;
	}
	
	/**
	 * setDatabaseDb
	 *
	 * @param  string $db
	 * @return void
	 */
	private function setDatabaseDb(string $db) : void {
		$this->DATABASE_DB = $db;
	}
	
	/**
	 * setDatabaseUser
	 *
	 * @param  string $dbuser
	 * @return void
	 */
	private function setDatabaseUser(string $dbuser) : void {
		$this->DATABASE_USER = $dbuser;
	}
	
	/**
	 * setDatabasePassword
	 *
	 * @param  string $dbpassword
	 * @return void
	 */
	private function setDatabasePassword(string $dbpassword) : void {
		$this->DATABASE_PASSWORD = $dbpassword;
	}
	
	/**

	 */
	 function testConnection() {
		$dbCon = @mysqli_connect($this->DATABASE_HOST, $this->DATABASE_USER, $this->DATABASE_PASSWORD, $this->DATABASE_DB);
		
		$error = mysqli_connect_error();

		if ($error != null) {
			return false;
		}
		mysqli_close($dbCon);
		return $dbCon;
	}

    /**
     * connect
     */
	 function connect() {
		return mysqli_connect($this->DATABASE_HOST, $this->DATABASE_USER, $this->DATABASE_PASSWORD, $this->DATABASE_DB);
	}
}
?>