<?php namespace org\core;
/*
 * @classname ClassLoader
 * @author apclapp
 * @description This class defines where classes are located within the current namespace, and how to load them.
 */

// Register this class as the global autoloader
spl_autoload_extensions(".php");
spl_autoload_register('org\core\ClassLoader::__autoload');

class ClassLoader {
	private static $LOADED_LIBRARIES = array();

	public static function __autoload($className) {

		$className = ltrim($className, '\\');
		$fileName = '';
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		$namespaceSteps = substr_count(trim(__NAMESPACE__, "\\"), "\\");
		$backString = str_repeat(".." . DIRECTORY_SEPARATOR, $namespaceSteps);
		$removedNamespace = trim(substr($fileName, strpos($fileName, DIRECTORY_SEPARATOR)), '\\');

		$fullPath = __DIR__ . DIRECTORY_SEPARATOR . $backString . $removedNamespace;

		// echo $fullPath;
		//var_dump($fileName);
		echo "<small>loaded</small> <font color=\"#cc0000\">'$fileName'</font> <br />";

		// var_dump($backString . $fileName);
		// echo $className;
		//var_dump($className);
		// var_dump(__NAMESPACE__);

		require $fullPath;
	}
}