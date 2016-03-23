<?php namespace org\commons\connection;
/*
 * @classname DatabaseConnectionMySQL
 * @author apclapp
 * @description This class handles MySQL connections and queries
 */

use org\commons\configuration\DatabaseConnectionMySQLConfig as Config;
use \mysqli as MySQLi;

class DatabaseConnectionMySQL implements DatabaseConnection {

	private $DB;
	private $CFG;

	private $last_row_count;
	private $last_insert_id;

	public function __construct(Config $Config) {

		// Load configuration settings for this class
		$this->CFG = $Config;

		// Create or connect to the database
		$this->connect();
	}

	public function __destruct() {
		// Close the connection to the database
		$this->close();
	}

	public function connect() {
		$address = $this->CFG->getSetting('CFG_DB_ADDRESS');
		$port = $this->CFG->getSetting('CFG_DB_PORT');
		$schema = $this->CFG->getSetting('CFG_DB_SCHEMA');
		$username = $this->CFG->getSetting('CFG_DB_USERNAME');
		$password = $this->CFG->getSetting('CFG_DB_PASSWORD');

		$this->DB = new MySQLi($address, $username, $password, $schema, $port);
	}

	public function close() {
		// Close the connection to the database
		$this->DB->close();
	}

	public function query($query_string) {
		// Set 'last query' variables to false to prevent confusion
		$this->last_row_count = FALSE;
		$this->last_insert_id = FALSE;

		$result_obj = $this->DB->query($query_string);
		$return_rows = array();

		if (!$result_obj) {
			return FALSE; // Query failed
		}

		// Record the query row count
		$this->last_row_count = $result_obj->num_rows;

		while ($row = $result_obj->fetch_assoc()) {
			$return_rows[] = $row;
		}

		// Record the insert id
		$this->last_insert_id = $this->DB->insert_id;

		return $return_rows;
	}

	public function execute($query_string) {
		// Implementation dependent
	}

	public function getLastRowCount() {
		// Return the last insert id
		return $this->last_row_count;
	}

	public function getLastInsertId() {
		// Return the last insert id
		return $this->last_insert_id;
	}

	public function escapeString($unescaped_string) {
		return $this->DB->real_escape_string($unescaped_string);
	}
}

?>