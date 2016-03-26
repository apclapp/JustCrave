<?php namespace org\api;
/*
 * @classname ItemSuggestionService
 * @author apclapp
 * @description This class will respond to web requests, and serve search item suggestions
 */

use org\caching\CacheUtility;
use org\commons\configuration\DatabaseConnectionMySQLConfig;
use org\commons\connection\DatabaseConnectionMySQL;
use org\justeat\JustEatUtility;

class ItemSuggestionService {

	private $Database;
	private $DBConfig;
	private $CacheUtility;
	private $JustEatUtility;

	public function __construct() {
		$this->DBConfig = new DatabaseConnectionMySQLConfig();
		$this->Database = new DatabaseConnectionMySQL($this->DBConfig);
		$this->CacheUtility = new CacheUtility();
		$this->JustEatUtility = new JustEatUtility();

		// Set the header to JSON for API responses
		header('Content-Type: application/json');
	}

	public function getSuggestions() {
		// Get the search query from the request
		$search_query = isset($_REQUEST['query']) ? $_REQUEST['query'] : 'coca';

		// Escape the search query for SQL
		$search_query = $this->Database->escapeString($search_query);

		$suggestion_query = "CALL `justcrave`.`search_suggestions`('$search_query')";

		$suggestion_result = $this->Database->query($suggestion_query);

		// Check if the result is valid before this line!
		$suggestion_list = array_column($suggestion_result, 'commonName');

		echo json_encode($suggestion_list, JSON_PRETTY_PRINT);
	}
}
?>