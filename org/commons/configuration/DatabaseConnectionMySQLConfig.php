<?php namespace org\commons\configuration;
/*
 * @classname DatabaseConnectionMySQLConfig
 * @author apclapp
 * @description This class defines configuration settings for MySQL database connections.
 */

class DatabaseConnectionMySQLConfig extends GenericConfiguration {
	protected $SETTINGS = array(
		"CFG_DB_ADDRESS" => array(
			"The address where the database is located",
			"localhost",
		),
		"CFG_DB_PORT" => array(
			"The port to connect to the database on",
			3306,
		),
		"CFG_DB_SCHEMA" => array(
			"The schema that should be connected to",
			"justcrave",
		),
		"CFG_DB_USERNAME" => array(
			"The username credential to use for connection",
			"root",
		),
		"CFG_DB_PASSWORD" => array(
			"The password credential to use for connection",
			"root",
		),
	);
}