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

	private function getRestaurantUpdates(array $restaurant_ids) {
		// This function is passed an array of restaurant ID's
		// It will return an array indicating restaurants ID's that need to be updated,
		// And new restaurants that need to be added

		$return = array(
			'outdated' => array(), // These will be purged
			'to_add' => array(), // These will be added (this includes outdated ID's)
		);

		// Create an array to hold the missing restaurant_ids
		$missingRestaurants = array_flip($restaurant_ids); // We cut out existing restaurants from here

		// First, build the query to get back restaurants
		$id_string_set = '(' . implode(', ', $restaurant_ids) . ')';

		$restaurants_found_query = "SELECT
		    restaurantId,
		    DATEDIFF(NOW (), restaurants.lastUpdated) AS daysSinceUpdate
		FROM
		    `restaurants`
		WHERE
		    restaurantId IN $id_string_set;";

		// Perform the query
		$restaurants_found = $this->Database->query($restaurants_found_query);

		if (!$restaurants_found) {
			// Exit the function of the query is failing
			return FALSE;
		}

		foreach ($restaurants_found as $restaurant) {

			// Remove this restaurant from the list of restaurants that haven't been found
			unset($missingRestaurants[$restaurant['restaurantId']]);

			if ($restaurant['daysSinceUpdate'] > 30) {
				// If the last update to this restaurant is more than 30 days ago, flag it.
				$return['outdated'][] = $restaurant['restaurantId'];
			}
		}

		// Flip the missing restaurants array back
		$missingRestaurants = array_flip($missingRestaurants);

		// Create the set of all the restaurants that need to be created
		$return['to_add'] = array_merge($missingRestaurants, $return['outdated']);

		return $return;
	}

	private function removeRestaurants(array $restaurant_ids) {
		// This function will remove all data associated with certain restaurants from the database.
		// It is useful to do this as the first half of an update for a new restaurant

		// First, build the list of restaurants
		$id_string_set = '(' . implode(', ', $restaurant_ids) . ')';

		// Prepare a query to remove data from the restaurants table
		$restaurants_delete_query = "DELETE
		FROM
		    `restaurants`
		WHERE
		    restaurantId IN $id_string_set;";

		// Prepare a query to remove data from the categories table
		$categories_delete_query = "DELETE
		FROM
		    `categories`
		WHERE
		    restaurantId IN $id_string_set;";

		// Prepare a query to remove data from the items table
		$items_delete_query = "DELETE
		FROM
		    `items`
		WHERE
		    restaurantId IN $id_string_set;";

		// Execute all the delete queries
		$restaurants_delete_result = $this->Database->query($restaurants_delete_query);
		$categories_delete_result = $this->Database->query($categories_delete_query);
		$items_delete_result = $this->Database->query($items_delete_query);

		// Return with the combined success of all three deletion queries
		return ($restaurants_delete_result && $categories_delete_result && $items_delete_result);
	}

	private function addRestaurants(array $restaurant_ids) {
		// This function is passed an array of restaurant ids
		// It will add the restaurant, it's categories, and items to the database

		foreach ($restaurant_ids as $restaurant_id) {
			// Get the restaurant name

			// Get the restaurant categories

			// Get the category items
		}
	}
}

$MySQLQueryTest = new MySQLQueryTest();
?>

