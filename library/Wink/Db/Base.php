<?php
abstract class Wink_Db_Base
{
	const MIGRATIONS_TABLE = 'schema_migrations';
	const MIGRATIONS_DIR = 'db/migrations';
	const DIRECTION_SEPARATOR = '/*DOWN*/';

	protected $_options;

	function __construct()
	{
		$this->_options = new Zend_Console_Getopt(array());
	}

	protected function _addRules($rules)
	{
		$this->_options->addRules($rules);

		try {
			$this->_options->parse();
		} catch (Zend_Console_Getopt_Exception $e) {
			die($e->getUsageMessage());
		}
	}

	protected function _getMigrationsRoot()
	{
		return Zend_Registry::get('docRoot') . '/' . self::MIGRATIONS_DIR;
	}

	protected function _executeStatements($input)
	{
		$statements = $this->_splitStatements($input);

		foreach ($statements as $statement) {
			$this->_executeStatement($statement);
		}
	}

	private function _splitStatements($statements)
	{
		return preg_split('/;\r*\n/', $statements);
	}

	private function _executeStatement($statement)
	{
		$statement = trim($statement);

		if (!empty($statement)) {
			echo PHP_EOL . $statement . PHP_EOL;
			$from = new DateTime();
			echo ':: started: ' . $from->format('h:i:s');
			Zend_Registry::get('database')->closeConnection();
			$affectedRows = Zend_Registry::get('database')->exec($statement);

			$to = new DateTime();
			$interval = $from->diff($to);
			echo '=>'. $to->format('h:i:s') . ' min:sec '. $interval->format('%I:%S ') . PHP_EOL;
			echo ":: Affected rows: {$affectedRows}".PHP_EOL;
		}
	}

	protected function _showConnectionInformation()
	{
		$config = Zend_Registry::get('database')->getConfig();
		$host = $config['host'];
		$username = $config['username'];
		$dbname = $config['dbname'];
		$port = array_key_exists('port', $config) ? ":{$config['port']}" : null;

		echo "Connected as $username@$host$port/$dbname" . PHP_EOL;
	}
}
