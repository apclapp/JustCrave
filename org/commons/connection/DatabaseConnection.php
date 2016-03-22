<?php namespace org\commons\connection;
/*
 * @classname DatabaseConnection
 * @author apclapp
 * @description This class handles connections to this program's database
 */

use org\commons\configuration\DatabaseConnectionConfig as Config;
use \PDO as PDO;

class DatabaseConnection {
	private $CFG;
	private $DB;

	public function __construct(Config $Config) {

		// Load configuration settings for this class
		$this->CFG = $Config;

		// Create or connect to the SQLite database
		$this->connect();
	}

	private function connect() {

		// Sanitize the path to the database file
		$db_path = rtrim($this->CFG->getSetting('CFG_DB_PATH'), "\\/");
		$db_path .= '/';

		if ($this->CFG->getSetting('CFG_USE_HOSTNAME')) {
			$this->DB = new PDO('sqlite:' . $db_path . gethostname() . '.sqlite');
		} else {
			$this->DB = new PDO('sqlite:' . $db_path . $this->CFG->getSetting('CFG_CUSTOM_FILENAME') . '.sqlite');
		}

	}

	public function query($query_string) {
		try {
			// Send the query to the database
			$result = $this->DB->query($query_string);
		} catch (PDOException $e) {
			echo $e->getMessage();
			return FALSE;
		}

		if ($this->CFG->getSetting('CFG_DEBUG_QUERY')) {
			var_dump(array($query_string, $result !== false));
		}

		// Return the result
		return $result;
	}

	public function execute($query_string) {
		try {
			// Send the query to the database
			$result = $this->DB->exec($query_string);
		} catch (PDOException $e) {
			echo $e->getMessage();
			return FALSE;
		}

		if ($this->CFG->getSetting('CFG_DEBUG_QUERY')) {
			var_dump(array($query_string, $result !== false));
		}

		// Return the result
		return $result;
	}

	public function getLastInsertId() {
		return $this->DB->lastInsertId();
	}

	public function escapeString($string) {
		return $this->DB->escapeString($string);
	}
}

?>