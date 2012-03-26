<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * @var string
	 */
	protected $_docRoot;

	protected function _initPath()
	{
		$this->_docRoot = realpath(APPLICATION_PATH . '/../');
		Zend_Registry::set('docRoot', $this->_docRoot);
	}

	protected function _initDatabase()
	{
		if (!Zend_Registry::isRegistered('database')) {
			$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini',
				APPLICATION_ENV);

			$db = Zend_Db::factory($config);
			Zend_Registry::set('database', $db);
		}
	}
}

