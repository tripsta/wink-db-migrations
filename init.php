<?php
// define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', __DIR__ . '/application');

// define application environment
if (!defined('APPLICATION_ENV')) {
	$env = getenv('APPLICATION_ENV');
	define('APPLICATION_ENV', $env !== false ? $env :
		file_get_contents(APPLICATION_PATH . '/configs/application.id'));
}

// set include path
set_include_path(realpath(APPLICATION_PATH . '/../library')
	. PATH_SEPARATOR . get_include_path());

// include zend application
require_once 'Zend/Application.php';

class Application
{

	static public $env;

	static function bootstrap($resource = null)
	{
		$application = new Zend_Application(self::_getEnv(), self::_getConfig());
		return $application->getBootstrap()->bootstrap($resource);
	}

	static function run()
	{
		self::bootstrap()->run();
	}

	private static function _getEnv()
	{
		return self::$env ?: APPLICATION_ENV;
	}

	private static function _getConfig()
	{
		require_once 'Zend/Config/Ini.php';
		$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", self::_getEnv(), true);
		$config = $config->toArray();

		return $config;
	}
}
