<?php namespace org\test;
/*
 * @classname APIConnectionTest
 * @author apclapp
 * @description This class creates a connection to the Just-Eat API and dumps the response.
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\commons\connection\WebConnectionLocal;
use org\justeat\JustEatUtility;

class APIConnectionTest {

	private $WebConnectionLocal;
	private $JustEatUtility;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->WebConnectionLocal = new WebConnectionLocal();
		$this->JustEatUtility = new JustEatUtility();

		header('Content-Type: application/json');
	}

	protected function run() {
		$this->doAPICall();
	}

	private function doAPICall() {
		$postcode = isset($_REQUEST['postcode']) ? $_REQUEST['postcode'] : 'BA23QB';
		$item_result = $this->JustEatUtility->getAllItemsForPostCode($postcode);

		echo json_encode($item_result, JSON_PRETTY_PRINT);
	}
}

$APIConnectionTest = new APIConnectionTest();
?>