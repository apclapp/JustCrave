<?php namespace org\test;
/*
 * @classname JustCraveApiSuggestionTest
 * @author apclapp
 * @description This class allows for website search requests to be responded to
 */

// An autoloader needs to be required
require_once '../core/ClassLoader.php';

use org\api\ItemSuggestionService;

class JustCraveApiSuggestionTest {

	private $ItemSuggestionService;

	public function __construct() {
		$this->init();
		$this->run();
	}

	protected function init() {
		$this->ItemSuggestionService = new ItemSuggestionService();
	}

	protected function run() {
		$this->ItemSuggestionService->getSuggestions();
	}
}

$JustCraveApiSuggestionTest = new JustCraveApiSuggestionTest();
?>

