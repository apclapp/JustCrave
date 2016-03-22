<?php namespace org\commons\connection;
/*
 * @classname WebConnectionLocal
 * @author apclapp
 * @description This class uses cURL to fetch pages directly.
 */

use org\commons\configuration\WebConnectionLocalConfig as Config;
use org\commons\connection\WebConnection as WebConnection;

class WebConnectionLocal implements WebConnection {

	// Set HTTP Status code constants
	const HTTP_SUCCESS_CODE = 200;

	// Create a curl handle
	private $curlHandle;
	private $curlHeader;
	private $lastHeader;
	private $lastBody;
	private $curlInfo;
	private $CFG;

	public function __construct() {
		// Load configuration settings for this class
		$this->CFG = new Config();

		// Initialize the curl handle when this class is loaded
		$this->open();

		// Set configuration settings for the cURL requests
		$this->setCurlDefaults();

		$this->curlHeader = array();
		$this->lastHeader = FALSE;
		$this->lastBody = FALSE;
	}

	public function __destruct() {
		// Destroy the curl handle when this class is unloaded
		$this->close();
	}

	public function open() {
		// Close the curl handle if it's already open
		$this->close();

		// Initialize the curl handle
		$this->curlHandle = curl_init();
	}

	public function close() {
		// If a curl handle is currently open, close it.
		if ($this->curlHandle) {
			curl_close($this->curlHandle);
		}
	}

	// [BOOLEAN] Whether the open or not.
	public function isOpen() {
		return is_resource($this->curlHandle);
	}

	public function getURL($url) {

		// Specify the URL to make the request to
		curl_setopt($this->curlHandle, CURLOPT_URL, $url);

		// Specify this is a GET request
		curl_setopt($this->curlHandle, CURLOPT_POST, FALSE);

		// Set the request header to anything the use has specified
		curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $this->getSendHeader());

		// Execute the curl request and record info returned
		$curl_response = curl_exec($this->curlHandle);
		$this->curlInfo = curl_getinfo($this->curlHandle);

		// If the curl request was successful...
		if ($curl_response) {
			// Get the response as two parts; a header and body
			$responseParts = explode("\r\n\r\n", $curl_response, 2);

			if (sizeof($responseParts) == 0) {
				// Either the header or body was not present, but something came back. We return the whole response.
				return $curl_response;
			}

			// If there is a first chunk in the response, record it as the header, otherwise, the header is false.
			$this->lastHeader = $responseParts[0];

			// If there is a second chunk in the response, record it as the body, otherwise, the body is false.
			$this->lastBody = $responseParts[1];
		} else {
			$this->lastHeader = FALSE;
			$this->lastBody = FALSE;
		}

		//return the response
		return $this->lastBody;
	}

	public function postURL($url, $postData) {
		// Format the POST data as a web-safe string
		$postDataString = $this->postDataToString($postData);

		// Specify the URL to make the request to
		curl_setopt($this->curlHandle, CURLOPT_URL, $url);

		// Specify this is a POST request
		curl_setopt($this->curlHandle, CURLOPT_POST, TRUE);

		// Set the POST fields to the ones passed in the arguments
		curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $postDataString);

		// Set the request header to anything the use has specified
		curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $this->getSendHeader());

		// Execute the curl request and record info returned
		$curl_response = curl_exec($this->curlHandle);
		$this->curlInfo = curl_getinfo($this->curlHandle);

		// If the curl request was successful...
		if ($curl_response) {
			// Get the response as two parts; a header and body
			$responseParts = explode("\r\n\r\n", $curl_response, 2);

			if (sizeof($responseParts) == 0) {
				// Either the header or body was not present, but something came back. We return the whole response.
				return $curl_response;
			}

			// If there is a first chunk in the response, record it as the header, otherwise, the header is false.
			$this->lastHeader = $responseParts[0];

			// If there is a second chunk in the response, record it as the body, otherwise, the body is false.
			$this->lastBody = $responseParts[1];
		} else {
			$this->lastHeader = FALSE;
			$this->lastBody = FALSE;
		}

		//return the response
		return $this->lastBody;
	}

	private function setCurlDefaults() {

		// Accept a response
		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, TRUE);

		// Retrieve the header
		curl_setopt($this->curlHandle, CURLOPT_HEADER, $this->CFG->getSetting('CFG_GET_HEADER'));

		// Set a connection timeout
		curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, $this->CFG->getSetting('CFG_TIMEOUT'));

		// Set the request header
		curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array());

		// Allow gzip encoded responses
		curl_setopt($this->curlHandle, CURLOPT_ENCODING, 'gzip');

		// Follow redirects
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, $this->CFG->getSetting('CFG_FOLLOW_REDIRECT'));

		// A user-agent to provide.
		curl_setopt($this->curlHandle, CURLOPT_USERAGENT, $this->CFG->getSetting('CFG_USER_AGENT'));

		// Verify SSL peers. FALSE allows insecure SSL connections, TRUE fails unless php is configured check the certificate.
		curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, !$this->CFG->getSetting('CFG_PASSTHROUGH_SSL'));
		// Verify SSL hosts. FALSE allows insecure SSL connections, TRUE fails unless php is configured check the certificate.
		curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYHOST, !$this->CFG->getSetting('CFG_PASSTHROUGH_SSL'));

		// Whether to use cookies
		curl_setopt($this->curlHandle, CURLOPT_COOKIEFILE, $this->CFG->getSetting('CFG_USE_COOKIES') ? '.s_cookie' : FALSE);
		curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $this->CFG->getSetting('CFG_USE_COOKIES') ? '.s_cookie' : FALSE);

		// Follow redirects
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, $this->CFG->getSetting('CFG_FOLLOW_REDIRECT'));

		// Bypass virgin media ISP blocking
		curl_setopt($this->curlHandle, CURLOPT_REFERER, $this->CFG->getSetting('CFG_BYPASS_VM') ? 'assets.virginmedia.com' : 'localhost');
	}

	// [VOID] Set credentials for basic authentication requests
	public function setCredentials($username, $password) {
		// Enable HTTP authentication
		curl_setopt($this->curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

		// Set the credentials used for HTTP authentication
		curl_setopt($this->curlHandle, CURLOPT_USERPWD, ($username . ':' . $password));
	}

	// [VOID] Clear credentials for basic authentication requests
	public function clearCredentials() {
		// Reset the credentials used for HTTP authentication
		curl_setopt($this->curlHandle, CURLOPT_USERPWD, '');
	}

	// [STRING] Convert an array post variables to a single string
	private function postDataToString($postData) {
		$postVariables = '';

		// If an array of variables has been passed...
		if (is_array($postData)) {
			// Add each key/value pair to the post string
			foreach ($postData as $key => $value) {
				$postVariables .= $key . '=' . $value . '&';
			}
		} else {
			// The post data is an unnamed string, send that string alone.
			$postVariables = $postData;
		}

		return $postVariables;
	}

	// [VOID] Set or remove a header property manually.
	public function setHeaderProperty($key, $value, $removeKey = FALSE) {
		if ($removeKey) {
			// Check the key to be removed exists
			if (array_key_exists($key, $this->headerArray)) {
				// Remove it from the set of headers
				unset($this->headerArray[$key]);
			}
		} else {
			$this->curlHeader[$key] = $value;
		}
	}

	// [STRING] Return a request-ready string of the user-specified header properties
	public function getSendHeader() {
		$tmp = array();

		foreach ($this->curlHeader as $key => $value) {
			$tmp[] = $key . ': ' . $value;
		}

		return $tmp;
	}

	// [STRING] Get the last status message, or FALSE if there is none.
	public function getLastStatus() {
		if (isset($this->curlInfo)) {
			// Return the HTTP code generated by the previous request.
			return $this->curlInfo['http_code'];
		} else {
			return FALSE;
		}
	}

	// [STRING] Get a certain value from the last response header, or FALSE if there is none
	public function getLastHeaderProperty($headerProperty) {

		// If there was a header returned by the previous request...
		if (isset($this->lastHeader)) {
			// Break apart each line of the header
			$header_array = explode("\n", $this->lastHeader);

			foreach ($header_array as $header_row) {
				// Split each line of the header into a [key: value] pair
				$header_parts = explode(':', $header_row, 2);

				// If the header line is a valid [key: value] pair...
				if (sizeof($header_parts) > 1) {
					$header_key = $header_parts[0];
					$header_value = ltrim($header_parts[1]);

					if (strcmp($header_key, $headerProperty) === 0) {
						// If we found the requested key, return the value found.
						return $header_value;
					}
				} else {
					// This line of the header is not a [key: value] pair.
					continue;
				}
			}
		} else {
			// The requested header could not be found in the header given by the last request.
			return FALSE;
		}
	}
}

?>