<?php namespace org\commons\configuration;
/*
 * @classname WebConnectionLocalConfig
 * @author apclapp
 * @description This class defines configuration settings for the WebConnectionLocal class.
 */

class WebConnectionLocalConfig extends GenericConfiguration {
	protected $SETTINGS = array(
		"CFG_USER_AGENT" => array(
			"The user agent to pose as for cURL requests",
			"JustCrave (Development 0.1) No harm intended, heavy caching (apclapp@gmail.com, saborrie@gmail.com)",
			// "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.235",
		),
		"CFG_FOLLOW_REDIRECT" => array(
			"Whether cURL requests should follow redirect requests",
			TRUE,
		),
		"CFG_GET_HEADER" => array(
			"Whether cURL requests should get the page header",
			TRUE,
		),
		"CFG_TIMEOUT" => array(
			"Connection timeout duration",
			250,
		),
		"CFG_PASSTHROUGH_SSL" => array(
			"Whether cURL requests should allow unverified SSL requests",
			TRUE,
		),
		"CFG_USE_COOKIES" => array(
			"Whether cURL requests should utilize cookies",
			FALSE,
		),
		"CFG_BYPASS_VM" => array(
			"Whether cURL requests should mask their referrer to bypass Virgin Media blocking",
			FALSE,
		),
	);
}