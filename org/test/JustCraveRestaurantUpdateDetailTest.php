<?php namespace org\test;
/*
 * @classname JustCraveApiSuggestionTest
 * @author apclapp
 * @description This class allows for website search requests to be responded to
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\commons\configuration\DatabaseConnectionMySQLConfig;
use org\commons\connection\DatabaseConnectionMySQL;
use org\justeat\JustEatUtility;

class JustCraveRestaurantDetailTest {

	private $Database;
	private $DBConfig;
	private $JustEatUtility;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->DBConfig = new DatabaseConnectionMySQLConfig();
		$this->Database = new DatabaseConnectionMySQL($this->DBConfig);
		$this->JustEatUtility = new JustEatUtility();
	}

	protected function run() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';
		$this->updateRestaurantDetails($postcode);
	}

	private function updateRestaurantDetails($postcode) {
		$restaurants = $this->JustEatUtility->getRestaurantsForPostcode($postcode);

		$update_restaurant_query_start = "INSERT into `restaurants` (restaurantId,address,postcode,city,url,is_halal,rating_stars) ";

		$update_value_items = array();

		foreach ($restaurants as $restaurant) {

			$update_value_items[] = "("
			. $restaurant['id'] . ",'" . $this->Database->escapeString($restaurant['address']) . "',"
			. "'" . $this->Database->escapeString($restaurant['postcode']) . "',"
			. "'" . $this->Database->escapeString($restaurant['city']) . "',"
			. "'" . $this->Database->escapeString($restaurant['url']) . "',"
			. "'" . ($restaurant['is_halal'] ? '1' : '0') . "',"
			. "'" . $this->Database->escapeString($restaurant['rating_stars']) . "'"
				. ")";
		}

		$update_restaurant_query_values = " VALUES " . implode(', ', $update_value_items);

		$update_restaurant_query_end = " ON DUPLICATE KEY UPDATE address = VALUES(address), postcode = VALUES(postcode), city = VALUES(city), url = VALUES(url), is_halal = VALUES(is_halal), rating_stars = VALUES(rating_stars);";

		$update_restaurant_query = $update_restaurant_query_start
			. $update_restaurant_query_values
			. $update_restaurant_query_end;

		echo ($update_restaurant_query);

		// $update_restaurant_result = $this->Database->query($update_restaurant_query);

		var_dump($update_restaurant_result);
	}
}

$JustCraveRestaurantDetailTest = new JustCraveRestaurantDetailTest();
?>

