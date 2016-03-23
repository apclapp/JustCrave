<?php namespace org\commons\connection;
/*
 * @classname DatabaseConnection
 * @author apclapp
 * @description This abstract class defines how database connections are connected and queried
 */

interface DatabaseConnection {

	function connect();

	function close();

	function query($query_string);

	function execute($query_string);

	function getLastRowCount();

	function getLastInsertId();

	function escapeString($string);
}

?>