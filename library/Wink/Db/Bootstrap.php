<?php
class Wink_Db_Bootstrap extends Wink_Db_Base
{
	private $_rootDb;

	function __construct()
	{
		parent::__construct();
		$this->_addRules(array(
			'rootpwd|r-w' => 'Root password, can be empty (required)',
			'remotehost|h-w' => 'Client host (for which you want to grant access)',
		));

		if ($this->_options->rootpwd === null || $this->_options->remotehost === null) {
			die($this->_options->getUsageMessage());
		}
	}

	function bootstrap()
	{
		$this->_showConnectionInformation();
		$this->_createDatabase();
		$this->_grantRights();
		$this->_createMigrationsTable();
	}

	private function _createDatabase()
	{
		$config = Zend_Registry::get('database')->getConfig();
		$rootDb = $this->_getRootDbConnection();
		$sql = "CREATE DATABASE {$config['dbname']} CHARACTER SET = utf8 COLLATE utf8_general_ci";

		echo "Creating database: $sql";
		$rootDb->exec($sql);
		echo '...done.' . PHP_EOL;
	}

	private function _grantRights()
	{
		$config = Zend_Registry::get('database')->getConfig();
		$rootDb = $this->_getRootDbConnection();
		$dbname = $config['dbname'];
		$username = $config['username'];
		$password = $config['password'];
		$remoteHost = $this->_options->remotehost;
		$sql = "GRANT ALL ON $dbname.* TO '$username'@'$remoteHost' IDENTIFIED BY '$password'";

		echo "Granting rights: $sql";
		$rootDb->exec($sql);
		echo '...done.' . PHP_EOL;
	}

	private function _createMigrationsTable()
	{
		$table = self::MIGRATIONS_TABLE;
		$db = Zend_Registry::get('database');
		$sql = <<<CREATE_TABLE
CREATE TABLE $table (
	id INTEGER AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255),
	dateCreated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE = InnoDB
CREATE_TABLE;

		echo "Creating schema migrations table: $sql";
		$db->exec($sql);
		echo '...done.' . PHP_EOL;
	}

	private function _getRootDbConnection()
	{
		if ($this->_rootDb === null) {
			$config = Zend_Registry::get('database')->getConfig();
			$host = $config['host'];
			$port = array_key_exists('port', $config) ? ";port={$config['port']}" : null;
			$password = $this->_options->rootpwd;
			$this->_rootDb = new PDO("mysql:host=$host$port", 'root', $password);
		}

		return $this->_rootDb;
	}
}
