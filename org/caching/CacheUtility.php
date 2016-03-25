<?php namespace org\caching;
/*
 * @classname CacheUtility
 * @author apclapp
 * @description This class will update the database cache of restaurants and items
 *				for a supplied postcode
 */

use org\commons\configuration\DatabaseConnectionMySQLConfig;
use org\commons\connection\DatabaseConnectionMySQL;
use org\justeat\JustEatUtility;

class CacheUtility {

	private $Database;
	private $DBConfig;
	private $JustEatUtility;

	public function __construct() {
		set_time_limit(1000);
		$this->DBConfig = new DatabaseConnectionMySQLConfig();
		$this->Database = new DatabaseConnectionMySQL($this->DBConfig);
		$this->JustEatUtility = new JustEatUtility();
	}

	public function updateCachedRestaurants($postcode) {
		// Get the restaurant set
		$restaurant_set = $this->JustEatUtility->getRestaurantsForPostcode($postcode);

		if (empty($restaurant_set)) {
			// No restaurant set returned
			echo "Error: CacheUtility couldn't get restaurants on line " . __LINE__;
			return;
		}

		$restaurant_ids = array_column($restaurant_set, 'id');
		$restaurant_names = array_column($restaurant_set, 'name');
		$restaurant_logos = array_column($restaurant_set, 'logo');

		$restaurant_pairs = array_combine($restaurant_ids, $restaurant_names);
		$restaurant_logo_pairs = array_combine($restaurant_ids, $restaurant_logos);

		// Get the sets of 'out of date', 'in date', 'not in cache' restaurants
		$restaurant_updates = $this->getRestaurantUpdates($restaurant_ids);

		// Exit the function if there are no restaurants found
		if (empty($restaurant_updates)) {
			echo "Error: CacheUtility couldn't get restaurant updates on line " . __LINE__;
			return;
		}

		// Remove the out of date restaurants, categories, and items
		$this->removeRestaurants($restaurant_updates['out_of_date']);

		// Loop through each restaurant, adding them, their categories and items
		foreach ($restaurant_updates['add_to_cache'] as $restaurant_id) {

			// Add the restaurant info to the cache
			$this->addRestaurantToCache($restaurant_id, $restaurant_pairs[$restaurant_id]);

			// Save the restaurant's logo
			if (!empty($restaurant_logo_pairs[$restaurant_id])) {
				$base_logo_path = dirname(__FILE__) . '/../../images/logo';
				$restaurant_logo_url = $restaurant_logo_pairs[$restaurant_id];
				$local_logo_path = "$base_logo_path/$restaurant_id.gif";
				file_put_contents($local_logo_path, file_get_contents($restaurant_logo_url));
			}

			// Get the categories for this restaurant as sets of 'menu_id' => array(<category_ids>)
			$restaurant_menus = $this->JustEatUtility->getCategoriesForRestaurant($restaurant_id);

			// Loop through all the menus a restaurant has
			foreach ($restaurant_menus as $restaurant_menu => $restaurant_categories) {

				// Loop through all the categories in this menu
				foreach ($restaurant_categories as $restaurant_category) {

					// Add the category info to the cache
					$this->addCategoryToCache($restaurant_id, $restaurant_menu,
						$restaurant_category['id'], $restaurant_category['name']);

					// Get the items for each category, and add these to the cache too.
					$category_items = $this->JustEatUtility->getItemsForCategory($restaurant_menu, $restaurant_category['id']);

					// Loop through all the items in this category
					foreach ($category_items as $item) {

						// Add the item info to the cache
						$this->addItemToCache($restaurant_id, $restaurant_category['id'], $restaurant_menu,
							$item['id'], $item['name'], $item['synonym'], $item['description'], $item['price']);
					}
				}
			}
		}

		// Return the set of changes applied (attempted)
		return $restaurant_updates;
	}

	private function getRestaurantUpdates(array $restaurant_ids) {
		// This function is passed an array of restaurant ID's
		// It will return an array indicating restaurants ID's that need to be updated,
		// And new restaurants that need to be added

		$return = array(
			'out_of_date' => array(), // These will be purged
			'up_to_date' => array(), // These will be purged
			'not_in_cache' => array(), // These will be purged
			'add_to_cache' => array(), // These will be added (this includes outdated ID's)
		);

		// Create an array to hold the missing restaurant_ids
		$missingRestaurants = array_flip($restaurant_ids); // We cut out existing restaurants from here

		// First, build the query to get back restaurants
		$id_string_set = '(' . implode(', ', $restaurant_ids) . ')';

		$restaurants_found_query = "SELECT
		    restaurantId,
		    DATEDIFF(NOW(), restaurants.lastUpdated) AS daysSinceUpdate
		FROM
		    `restaurants`
		WHERE
		    restaurantId IN $id_string_set;";

		// Perform the query
		$restaurants_found = $this->Database->query($restaurants_found_query);

		if ($restaurants_found === FALSE) {
			// Exit the function of the query is failing
			return FALSE;
		}

		foreach ($restaurants_found as $restaurant) {

			// Remove this restaurant from the list of restaurants that haven't been found
			unset($missingRestaurants[$restaurant['restaurantId']]);

			if ($restaurant['daysSinceUpdate'] > 30) {
				// If the last update to this restaurant is more than 30 days ago, flag it.
				$return['out_of_date'][] = $restaurant['restaurantId'];
			} else {
				// If the last update to this restaurant is less than 30 days ago, keep it.
				$return['up_to_date'][] = $restaurant['restaurantId'];
			}
		}

		// Flip the missing restaurants array back
		$missingRestaurants = array_flip($missingRestaurants);

		$return['not_in_cache'] = $missingRestaurants;

		// Create the set of all the restaurants that need to be created
		$return['add_to_cache'] = array_merge($return['not_in_cache'], $return['out_of_date']);

		return $return;
	}

	private function removeRestaurants($restaurant_ids) {
		// This function will remove all data associated with certain restaurants from the database.
		// It is useful to do this as the first half of an update for a new restaurant

		if (empty($restaurant_ids)) {
			// If we received an empty list of restaurants, exit this function
			return;
		}

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

	private function addRestaurantToCache($restaurant_id, $restaurant_name) {

		$add_restaurant_query =
		"INSERT INTO `justcrave`.`restaurants`
				(`restaurantId`,
				`restaurantName`)
			VALUES
				($restaurant_id,
				'" . $this->Database->escapeString($restaurant_name) . "');";

		// Execute the query to add the restaurant
		$add_restaurant_result = $this->Database->query($add_restaurant_query);

		// Return query success
		return $add_restaurant_result !== FALSE;
	}

	private function addCategoryToCache($restaurant_id, $menu_id, $category_id, $category_name) {

		$add_category_query =
		"INSERT INTO `justcrave`.`categories`
				(`categoryId`,
				`categoryName`,
				`restaurantId`,
				`menuId`)
			VALUES
				($category_id,
				'" . $this->Database->escapeString($category_name) . "',
				$restaurant_id,
				$menu_id);";

		// Execute the query to add the category
		$add_category_result = $this->Database->query($add_category_query);

		// Return query success
		return $add_category_result !== FALSE;
	}

	private function addItemToCache($restaurant_id, $category_id, $menu_id,
		$item_id, $item_name, $item_synonym, $item_description, $item_price) {

		$add_item_query =
		"INSERT INTO `justcrave`.`items`
				(`itemId`,
				`menuId`,
				`categoryId`,
				`restaurantId`,
				`itemName`,
				`itemSynonym`,
				`itemDescription`,
				`itemPrice`)
			VALUES
				($item_id,
				$menu_id,
				$category_id,
				$restaurant_id,
				'" . $this->Database->escapeString($item_name) . "',
				'" . $this->Database->escapeString($item_synonym) . "',
				'" . $this->Database->escapeString($item_description) . "',
				$item_price);";

		// Execute the query to add the item
		$add_item_result = $this->Database->query($add_item_query);

		// Return query success
		return $add_item_result !== FALSE;
	}
}

?>