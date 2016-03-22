<?php namespace org\test;
/*
 * @classname APIConnectionTest
 * @author apclapp
 * @description This class creates a connection to the Just-Eat API and dumps the response.
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\commons\connection\WebConnectionLocal;

class APIConnectionTest {

	private $WebConnectionLocal;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->WebConnectionLocal = new WebConnectionLocal();
	}

	protected function run() {
		$this->doAPICall();
	}

	private function doAPICall() {
		$this->setAPIHeaders();

		$postcode = "BA23QB";
		$item_result = $this->getItemsForPostCode($postcode);

		var_dump($item_result);
	}

	private function setAPIHeaders() {
		$this->WebConnectionLocal->setHeaderProperty('Accept-Tenant', 'uk');
		$this->WebConnectionLocal->setHeaderProperty('Accept-Language', 'en-GB');
		$this->WebConnectionLocal->setHeaderProperty('Authorization', 'Basic VGVjaFRlc3RBUEk6dXNlcjI=');
		$this->WebConnectionLocal->setHeaderProperty('Host', 'public.je-apis.com');
	}

	private function getItemsForPostCode($postcode) {
		$restaurant_set = $this->getRestaurantsForPostcode($postcode);

		foreach ($restaurant_set as &$restaurant) {
			$restaurant['items'] = array();
			$menu_set = $this->getMenusForRestaurant($restaurant['id']);

			foreach ($menu_set as $menu) {
				$category_set = $this->getCategoriesForRestaurant($menu);

				foreach ($category_set as $category) {
					$category_items = $this->getItemsForCategory($menu, $category);
					$restaurant['items'] = array_merge($restaurant['items'], $category_items);
				}
			}
		}

		return $restaurant_set;
	}

	private function getRestaurantsForPostcode($postcode) {
		$response = $this->WebConnectionLocal->getURL("https://public.je-apis.com/restaurants?q=$postcode");
		$decoded_response = json_decode($response);

		$result_restaurants = array();

		foreach ($decoded_response->Restaurants as $restaurant) {
			$result_restaurants[] = array(
				'name' => $restaurant->Name,
				'id' => $restaurant->Id,
			);
		}

		return $result_restaurants;
	}

	private function getMenusForRestaurant($restaurantId) {
		$response = $this->WebConnectionLocal->getURL('https://public.je-apis.com/restaurants/' . $restaurantId . '/menus');
		$decoded_response = json_decode($response);

		$result_menus = array();

		foreach ($decoded_response->Menus as $menu) {
			$result_menus[] = $menu->Id;
		}

		return $result_menus;
	}

	private function getCategoriesForRestaurant($menuId) {
		$response = $this->WebConnectionLocal->getURL('https://public.je-apis.com/menus/' . $menuId . '/productcategories');
		$decoded_response = json_decode($response);

		$result_categories = array();

		foreach ($decoded_response->Categories as $category) {
			$result_categories[] = $category->Id;
		}

		return $result_categories;
	}

	private function getItemsForCategory($menuId, $categoryId) {
		$response = $this->WebConnectionLocal->getURL("https://public.je-apis.com/menus/$menuId/productcategories/$categoryId/products");
		$decoded_response = json_decode($response);

		$result_items = array();

		foreach ($decoded_response->Products as $product) {
			$result_items[] = array(
				'name' => $product->Name,
				'synonym' => $product->Synonym,
				'price' => $product->Price,
			);
		}

		return $result_items;
	}
}

$APIConnectionTest = new APIConnectionTest();
?>