<?php namespace org\test;
/*
 * @classname JustCraveApiSuggestionTest
 * @author apclapp
 * @description This class allows for website search requests to be responded to
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\justeat\JustEatUtility;

class JustCraveRestaurantDetailTest {

	private $JustEatUtility;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->JustEatUtility = new JustEatUtility();
	}

	protected function run() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';
		$this->updateRestaurantDetails($postcode);
	}

	private function updateRestaurantDetails($postcode) {
		$restaurants = $this->JustEatUtility->getRestaurantsForPostcode($postcode);
		var_dump($restaurants);

	}
}

$JustCraveRestaurantDetailTest = new JustCraveRestaurantDetailTest();
?>

