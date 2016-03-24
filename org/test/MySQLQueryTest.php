<?php namespace org\test;
/*
 * @classname MySQLQueryTest
 * @author apclapp
 * @description This class creates a connection to the Just-Eat API and dumps the response.
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\commons\configuration\DatabaseConnectionMySQLConfig;
use org\commons\connection\DatabaseConnectionMySQL;

class MySQLQueryTest {

	private $Database;
	private $Config;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->Config = new DatabaseConnectionMySQLConfig();
		$this->Database = new DatabaseConnectionMySQL($this->Config);
	}

	protected function run() {
		$this->doQueryTest();
	}

	private function doQueryTest() {
		$sample = $this->Database->query('SELECT * FROM `restaurants`');
		var_dump($this->Database->getLastRowCount(), $sample);
	}
}

$MySQLQueryTest = new MySQLQueryTest();
?>

