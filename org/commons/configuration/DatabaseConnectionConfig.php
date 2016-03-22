<?php namespace org\commons\configuration;
/*
 * @classname DatabaseConnectionConfig
 * @author apclapp
 * @description This class defines configuration settings for database creation.
 */

class DatabaseConnectionConfig extends GenericConfiguration {
	protected $SETTINGS = array(
		"CFG_DB_PATH" => array(
			"The path to where the database is stored",
			".",
		),
		"CFG_USE_HOSTNAME" => array(
			"Whether to use the machine's hostname as the database's filename",
			TRUE,
		),
		"CFG_CUSTOM_FILENAME" => array(
			"If USE_HOSTNAME is false, this is the filename to use for the database",
			TRUE,
		),
		"CFG_DEBUG_QUERY" => array(
			"If CFG_DEBUG_QUERY is true, each query will be outputted via var_dump()",
			TRUE,
		),
	);
}