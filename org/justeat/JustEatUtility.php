<?php namespace org\justeat;
/*
 * @classname JustEatUtility
 * @author apclapp
 * @description This class handles making api calls and getting responses from Just Eat
 */

use org\commons\connection\WebConnectionLocal;

class JustEatUtility {
	private $WebConnectionLocal;

	public function __construct() {
		// Initiate the web connection
		$this->WebConnectionLocal = new WebConnectionLocal();

		// Set the headers required for our API requests
		$this->setAPIHeaders();
	}

	private function setAPIHeaders() {
		$this->WebConnectionLocal->setHeaderProperty('Accept-Tenant', 'uk');
		$this->WebConnectionLocal->setHeaderProperty('Accept-Language', 'en-GB');
		$this->WebConnectionLocal->setHeaderProperty('Authorization', 'Basic VGVjaFRlc3RBUEk6dXNlcjI=');
		$this->WebConnectionLocal->setHeaderProperty('Host', 'public.je-apis.com');
		$this->WebConnectionLocal->setHeaderProperty('Content-Type', 'application/json');
	}

	public function getAllItemsForPostCode($postcode) {
		$restaurant_set = $this->getRestaurantsForPostcode($postcode);

		// trim the results to two items:
		// array_splice($restaurant_set, 1);

		foreach ($restaurant_set as &$restaurant) {
			$restaurant['items'] = array();
			$menu_set = $this->getMenusForRestaurant($restaurant['id']);

			foreach ($menu_set as $menu) {
				$category_set = $this->getCategoriesForMenu($menu);

				foreach ($category_set as $category) {
					$category_items = $this->getItemsForCategory($menu, $category['id']);
					$restaurant['items'][$category['name']] = $category_items;
				}
			}
		}

		return $restaurant_set;
	}

	public function getRestaurantsForPostcode($postcode) {
		$postcode = urlencode($postcode);

		$response = $this->WebConnectionLocal->getURL("https://public.je-apis.com/restaurants?q=$postcode");
		$decoded_response = json_decode($response);
		if (empty($decoded_response)) {
			// If we didn't get any results back, exit the function
			echo "Error: JustEatUtility couldn't decode response on line " . __LINE__;
			return;
		}

		$result_restaurants = array();

		foreach ($decoded_response->Restaurants as $restaurant) {

			// Get the logo url
			$restaurant_logo_url = "";
			if (isset($restaurant->Logo[0])) {
				$restaurant_logo_url = $restaurant->Logo[0]->StandardResolutionURL;
			}

			$result_restaurants[$restaurant->Id] = array(
				'name' => trim($restaurant->Name),
				'id' => $restaurant->Id,
				'logo' => $restaurant_logo_url,
				'address' => $restaurant->Address,
				'postcode' => $restaurant->Postcode,
				'city' => $restaurant->City,
				'url' => $restaurant->Url,
				'is_open_now' => $restaurant->IsOpenNow,
				'unique_name' => $restaurant->UniqueName,
				'is_close_by' => $restaurant->IsCloseBy,
				'is_halal' => $restaurant->IsHalal,
				'is_delivering' => $restaurant->IsOpenNowForDelivery,
				'is_collecting' => $restaurant->IsOpenNowForCollection,
				'rating_stars' => $restaurant->RatingStars,
			);
		}

		return $result_restaurants;
	}

	public function getMenusForRestaurant($restaurantId) {
		$response = $this->WebConnectionLocal->getURL('https://public.je-apis.com/restaurants/' . $restaurantId . '/menus');
		$decoded_response = json_decode($response);

		$result_menus = array();

		foreach ($decoded_response->Menus as $menu) {
			$result_menus[] = $menu->Id;
		}

		return $result_menus;
	}

	public function getCategoriesForMenu($menuId) {
		$response = $this->WebConnectionLocal->getURL('https://public.je-apis.com/menus/' . $menuId . '/productcategories');
		$decoded_response = json_decode($response);

		$result_categories = array();

		foreach ($decoded_response->Categories as $category) {
			$result_categories[] = array(
				'name' => trim($category->Name),
				'id' => $category->Id,
			);
		}

		return $result_categories;
	}

	public function getCategoriesForRestaurant($restaurantId) {

		$return_categories = array();

		$menu_set = $this->getMenusForRestaurant($restaurantId);

		foreach ($menu_set as $menu) {
			$return_categories[$menu] = $this->getCategoriesForMenu($menu);
		}

		return $return_categories;
	}

	public function getItemsForCategory($menuId, $categoryId) {
		$response = $this->WebConnectionLocal->getURL("https://public.je-apis.com/menus/$menuId/productcategories/$categoryId/products");
		$decoded_response = json_decode($response);

		$result_items = array();

		foreach ($decoded_response->Products as $product) {
			$result_items[] = array(
				'id' => trim($product->Id),
				'name' => trim($product->Name),
				'synonym' => trim($product->Synonym),
				'description' => trim($product->Description),
				'price' => $product->Price,
			);
		}

		return $result_items;
	}
}
?>