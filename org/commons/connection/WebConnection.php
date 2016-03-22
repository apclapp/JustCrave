<?php namespace org\commons\connection;
/*
 * @classname WebConnection
 * @author apclapp
 * @description This class defines how webpage-fetching classes should be implemented
 */

interface WebConnection {

	// Open the connection
	public function open();

	// Close the connection
	public function close();

	// [BOOLEAN] Whether the open or not.
	public function isOpen();

	// [STRING | FALSE] Get the raw response from a web page using a GET request, or FALSE on failure
	public function getURL($url);

	// [STRING | FALSE] Get the raw response from a web page using a POST request, or FALSE on failure
	public function postURL($url, $postData);

	// [VOID] Set credentials for basic authentication requests
	public function setCredentials($username, $password);

	// [VOID] Clear credentials for basic authentication requests
	public function clearCredentials();

	// [STRING] Get the last status message, or FALSE if there is none.
	public function getLastStatus();
}
?>