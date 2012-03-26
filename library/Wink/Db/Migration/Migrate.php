<?php
class Wink_Db_Migration_Migrate extends Wink_Db_Base
{
	function __construct()
	{
		parent::__construct();
		$this->_addRules(array(
			'down|d-i' => 'Migrates down the given number of steps (defaults to one)',
			'pending|p' => 'List pending up migrations'
		));
	}

	function migrate()
	{
		$this->_showConnectionInformation();
		$pendingMigrations = $this->_getPendingMigrations();

		if ($this->_options->pending) {
			$this->_listPendingMigrations($pendingMigrations);
		} else {
			$migrations = $this->_getMigrations($pendingMigrations);
			$this->_runMigrations($migrations);
		}
	}

	private function _getPendingMigrations()
	{
		$performedMigrations = $this->_getPerformedMigrations();

		if ($this->_isUp()) {
			$migrationScripts = $this->_getMigrationScripts();
			$pendingMigrations = array_diff($migrationScripts, $performedMigrations);
		} else {
			$pendingMigrations = array();
			$steps = (int) $this->_options->down;

			if ($steps <= 0) {
				$steps = 1;
			}

			if (count($performedMigrations) < $steps) {
				$steps = count($performedMigrations);
			}

			for ($i = 0; $i < $steps; ++$i) {
				$pendingMigrations[] = array_pop($performedMigrations);
			}
		}

		if (empty($pendingMigrations)) {
			die('Your schema is already at the end of the migration chain.' . PHP_EOL);
		}

		return $pendingMigrations;
	}

	private function _listPendingMigrations($pendingMigrations)
	{
		echo PHP_EOL . 'The following migrations are pending: ' . PHP_EOL . PHP_EOL;

		foreach ($pendingMigrations as $migration) {
			echo " * $migration" . PHP_EOL;
		}

		echo PHP_EOL;
	}

	private function _getMigrations($pendingMigrations)
	{
		$migrations = array();

		foreach ($pendingMigrations as $name) {
			$migrations[$name] = $this->_getMigration($name);
		}

		return $migrations;
	}

	private function _getPerformedMigrations()
	{
		$table = self::MIGRATIONS_TABLE;
		$db = Zend_Registry::get('database');

		try {
			return $db->fetchCol("SELECT name FROM {$table} ORDER BY dateCreated, id");
		} catch (Zend_Db_Exception $e) {
			die("ERROR! Could not get current schema metadata: {$e}" . PHP_EOL);
		}
	}

	private function _getMigrationScripts()
	{
		$migrationsDirectory = opendir($this->_getMigrationsRoot());
		$migrationScripts = array();

		while ($migration = readdir($migrationsDirectory)) {
			if (substr($migration, strlen($migration) - 4) == '.sql') {
				$migrationScripts[] = $migration;
			}
		}

		closedir($migrationsDirectory);
		sort($migrationScripts);
		return $migrationScripts;
	}

	private function _getMigration($name)
	{
		$file = file_get_contents($this->_getMigrationsRoot() . '/' . $name);
		$quotedSeparator = preg_quote(self::DIRECTION_SEPARATOR, '/');
		$parts = preg_split('/\n' . $quotedSeparator . '\r*\n/', $file);

		return $this->_isUp() ? $parts[0] : $parts[1];
	}

	private function _runMigrations($migrations)
	{
		foreach ($migrations as $name => $migration) {
			$this->_runMigration($name, $migration);
		}
	}

	private function _runMigration($name, $migration)
	{
		echo "Running {$name}...";

		$this->_executeStatements($migration);

		$db = Zend_Registry::get('database');
		$db->closeConnection();

		if ($this->_isUp()) {
			$db->insert(self::MIGRATIONS_TABLE, array('name' => $name));
		} else {
			$conditions = $db->quoteInto('name = ?', $name);
			$db->delete(self::MIGRATIONS_TABLE, $conditions);
		}

		echo "Finished running {$name}." . PHP_EOL . PHP_EOL;
	}

	private function _isUp()
	{
		return $this->_options->down === null;
	}
}
