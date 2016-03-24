<?php namespace org\test;
/*
 * @classname UpdateCacheTest
 * @author apclapp
 * @description This class checks and updates the local cache of restaurant info
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\caching\CacheUtility;

class UpdateCacheTest {

	private $CacheUtility;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->CacheUtility = new CacheUtility();
	}

	protected function run() {
		$this->doUpdate();
	}

	private function doUpdate() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';

		$update_result = $this->CacheUtility->updateCachedRestaurants($postcode);

		var_dump($update_result);
	}

}

$UpdateCacheTest = new UpdateCacheTest();
?>