<?php namespace org\test;
/*
 * @classname JustCraveApiTest
 * @author apclapp
 * @description This class allows for website search requests to be responded to
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\caching\CacheUtility;
use org\commons\configuration\DatabaseConnectionMySQLConfig;
use org\commons\connection\DatabaseConnectionMySQL;
use org\justeat\JustEatUtility;

class JustCraveApiTest {

	private $Database;
	private $DBConfig;
	private $CacheUtility;
	private $JustEatUtility;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->Config = new DatabaseConnectionMySQLConfig();
		$this->Database = new DatabaseConnectionMySQL($this->Config);
		$this->CacheUtility = new CacheUtility();
		$this->JustEatUtility = new JustEatUtility();

		header('Content-Type: application/json');
	}

	protected function run() {
		$this->updateCache();
		$this->getSearchResults();
	}

	private function updateCache() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';

		$this->CacheUtility->updateCachedRestaurants($postcode);
	}

	private function getSearchResults() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';
		$search_text = isset($_REQUEST['query']) ? $_REQUEST['query'] : 'cola';

		$postcode = $this->Database->escapeString($postcode);
		$search_text = $this->Database->escapeString($search_text);

		//$sample_search = $this->Database->query("SELECT * FROM `items` WHERE itemName LIKE '%$search_text%'");
		$sample_search_query =
			"SELECT
			    r.restaurantName, c.categoryName, i . *
			FROM
			    `justcrave`.`items` i
			        INNER JOIN
			    `justcrave`.`categories` c ON i.categoryId = c.categoryId
			        INNER JOIN
			    `justcrave`.`restaurants` r ON i.restaurantId = r.restaurantId
			WHERE
				MATCH(i.itemName) AGAINST('$search_text' IN NATURAL LANGUAGE MODE);";
		// CONCAT_WS(' ', c.categoryName, i.itemName, i.itemSynonym)  LIKE '%$search_text%';";

		$sample_search_result = $this->Database->query($sample_search_query);

		// Add the path to the logo to each result
		foreach ($sample_search_result as &$result) {
			$result['itemLogo'] = 'images/logo/' . $result['restaurantId'] . '.gif';
		}

		// Dump the search results as raw jason
		echo json_encode($sample_search_result, JSON_PRETTY_PRINT);
	}

}

$JustCraveApiTest = new JustCraveApiTest();
?>

