<?php namespace org\test;
/*
 * @classname JustCraveApiTest
 * @author apclapp
 * @description This class allows for website search requests to be responded to
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\api\ItemQueryService;

class JustCraveApiTest {

	private $ItemQueryService;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->ItemQueryService = new ItemQueryService();
	}

	protected function run() {
		$this->ItemQueryService->searchItems();
	}
}

$JustCraveApiTest = new JustCraveApiTest();
?>

