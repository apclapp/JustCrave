<?php namespace org\commons\configuration;
/*
 * @classname GenericConfiguration
 * @author apclapp
 * @description This interface defines how configuration classes should be designed.
 */

abstract class GenericConfiguration {
	protected $SETTINGS;

	public function __construct() {
		$this->load();
	}

	// List avaliable configuration settings and their descriptions
	public function listSettings() {
		return $SETTINGS;
	}

	// Load saved configuration settings
	public function load() {

	}

	// Save configuration settings
	public function save() {

	}

	// Get a setting by name
	public function getSetting($settingName) {
		if (isset($this->SETTINGS[$settingName])) {
			return $this->SETTINGS[$settingName][1];
		} else {
			var_dump($this->SETTINGS);
			trigger_error("Setting '$settingName' does not exist in configuration class " . __CLASS__, E_USER_ERROR);
		}
	}

	// Set a setting by name
	public function setSetting($settingName, $settingValue) {
		if (isset($this->SETTINGS[$settingName])) {
			$this->SETTINGS[$settingName][1] = $settingValue;
		} else {
			var_dump($this->SETTINGS);
			trigger_error("Setting '$settingName' does not exist in configuration class " . __CLASS__, E_USER_ERROR);
		}
	}
}
