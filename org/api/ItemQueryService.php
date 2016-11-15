<?php namespace org\api;
/*
 * @classname ItemQueryService
 * @author apclapp
 * @description This class will respond to web requests, and serve search item queries
 */

use org\caching\CacheUtility;
use org\commons\configuration\DatabaseConnectionMySQLConfig;
use org\commons\connection\DatabaseConnectionMySQL;
use org\justeat\JustEatUtility;

class ItemQueryService {

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

	public function searchItems() {

		// Get the postcode and search query from the request
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';
		$query = isset($_REQUEST['query']) ? $_REQUEST['query'] : 'cola';

		// Get the search results
		$search_results = $this->getSearchResults($postcode, $query);

		// JSON encode and write out the search results
		echo json_encode($search_results, JSON_PRETTY_PRINT);
	}

	private function getSearchResults($postcode, $search_query) {

		// Update the restaurant cache
		$cache_update = $this->updateCache($postcode);

		if (empty($cache_update)) {
			// If we don't have any restaurants, then exit this function
			echo "Error: JustCraveAPITest couldn't get cache results on line " . __LINE__;
			return;
		}

		// Get the restaurants found from the cache update
		$restaurant_ids = $this->flattenCacheResults($cache_update);

		if (empty($restaurant_ids)) {
			// If we don't have any restaurants, then exit this function
			echo "Error: JustCraveAPITest couldn't find any restaurants on line " . __LINE__;
			return;
		}

		// Generate the set of restaurants to search in
		$restaurant_in_string = implode(',', $restaurant_ids);

		// Escape the search query
		$search_query = $this->Database->escapeString($search_query);
		$sample_search_query = "CALL `justcrave`.`search_food_items_filters`('$search_query', '$restaurant_in_string')";

		// Execute the query
		$sample_search_result = $this->Database->query($sample_search_query);

		// Get the common synonyms and categories
		$common_syn_cat = $this->generateCommonSynAndCat($sample_search_result);

		// Organize the result structure
		$result = array(
			'common_synonyms' => $common_syn_cat['synonyms'],
			'common_categories' => $common_syn_cat['categories'],
			'search_results' => $sample_search_result,
		);

		// Return array of search results
		// return $sample_search_result;
		return $result;
	}

	private function updateCache($postcode) {
		$cache_update_result = $this->CacheUtility->updateCachedRestaurants($postcode);

		return $cache_update_result;
	}

	private function generateCommonSynAndCat($search_result) {
		// This function generates a list of filters from the common synonyms in the search results

		$result = array('synonyms' => array(), 'categories' => array());

		$common_synonyms = array();
		$common_categories = array();

		if (empty($search_result)) {
			// If the search result it empty, exit this function.
			return $result;
		}

		foreach ($search_result as $item) {

			// If this item has a common synonym
			if ($item['isCommonSyn'] == 1) {
				// Add the synonym id to the set of seen synonyms
				if (!isset($common_synonyms[$item['friendlySynonymId']])) {
					$common_synonyms[$item['friendlySynonymId']]['id'] = $item['friendlySynonymId'];
					$common_synonyms[$item['friendlySynonymId']]['name'] = $item['friendlySynonym'];
					$common_synonyms[$item['friendlySynonymId']]['count'] = 0;
				}

				// Add to the seen count for this synonym
				$common_synonyms[$item['friendlySynonymId']]['count']++;
			}

			// If this item has a common category
			if ($item['isCommonCat'] == 1) {
				// Add the synonym id to the set of seen synonyms
				if (!isset($common_categories[$item['friendlyCategoryId']])) {
					$common_categories[$item['friendlyCategoryId']]['id'] = $item['friendlyCategoryId'];
					$common_categories[$item['friendlyCategoryId']]['name'] = $item['categoryName'];
					$common_categories[$item['friendlyCategoryId']]['count'] = 0;
				}

				// Add to the seen count for this synonym
				$common_categories[$item['friendlyCategoryId']]['count']++;
			}
		}

		// Sort the synonym array by most common synonym
		usort($common_synonyms, array('org\api\ItemQueryService', 'sortByOccurrenceCountDesc'));

		// Sort the category array by most common category
		usort($common_categories, array('org\api\ItemQueryService', 'sortByOccurrenceCountDesc'));

		// Re-index the synonym array from zero
		$common_synonyms = array_values($common_synonyms);

		// Re-index the category array from zero
		$common_categories = array_values($common_categories);

		$result['synonyms'] = $common_synonyms;
		$result['categories'] = $common_categories;

		// Return the set of common synonyms
		return $result;
	}

	private static function sortByOccurrenceCountDesc($a, $b) {
		return $b['count'] - $a['count'];
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

?>