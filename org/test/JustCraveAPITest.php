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
		$cache_update = $this->updateCache();

		if (empty($cache_update)) {
			// No restaurants found for the given postcode

			echo "Error: JustCraveAPITest couldn't get cache update on line " . __LINE__;
			var_dump($cache_update);
			return;
		}

		$restaurant_ids = $this->flattenCacheResults($cache_update);

		$this->getSearchResults($restaurant_ids);
	}

	private function updateCache() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';

		$cache_update_result = $this->CacheUtility->updateCachedRestaurants($postcode);

		return $cache_update_result;
	}

	private function getSearchResults($restaurant_ids) {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';
		$search_text = isset($_REQUEST['query']) ? $_REQUEST['query'] : 'cola';

		$postcode = $this->Database->escapeString($postcode);
		$search_text = $this->Database->escapeString($search_text);

		// Generate the set of restaurants to search in
		$restaurant_in_string = implode(',', $restaurant_ids);

		if (empty($restaurant_ids)) {
			// If we don't have any restaurants, then exit this function

			echo "Error: JustCraveAPITest couldn't get restaurant_ids on line " . __LINE__;
			var_dump($restaurant_ids);
			return;
		}

		$sample_search_query = "CALL `justcrave`.`search_food_items`('$search_text', '$restaurant_in_string')";

		$sample_search_result = $this->Database->query($sample_search_query);

		// Add the path to the logo to each result
		foreach ($sample_search_result as &$result) {
			$result['itemLogo'] = 'images/logo/' . $result['restaurantId'] . '.gif';
		}

		// Dump the search results as raw jason
		echo json_encode($sample_search_result, JSON_PRETTY_PRINT);
	}

	private function flattenCacheResults($cache_results) {
		// This function turns a cache result into a flat array of restaurant id's

		$result_array = array();

		foreach ($cache_results as $result) {
			foreach ($result as $restaurant_id) {
				$result_array[$restaurant_id] = true;
			}
		}

		// Return the set of restaurant id's
		return array_keys($result_array);
	}
}

$JustCraveApiTest = new JustCraveApiTest();
?>

