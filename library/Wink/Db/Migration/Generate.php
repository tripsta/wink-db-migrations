<?php
class Wink_Db_Migration_Generate extends Wink_Db_Base
{
	public function __construct()
	{
		parent::__construct();
		$this->_addRules(array(
			'title|t=s' => 'Title of the migration (required)'
		));

		if ($this->_options->title === null) {
			die($this->_options->getUsageMessage());
		}
	}

	public function generate()
	{
		$fileName = $this->_getFileName();
		$separator = parent::DIRECTION_SEPARATOR;
		$path = $this->_getMigrationsRoot() . '/' . $fileName;
		$content = "-- Put your up migration here" . PHP_EOL
			. PHP_EOL
			. "{$separator}" . PHP_EOL
			. '-- Put your down migration here' . PHP_EOL
			. PHP_EOL;

		file_put_contents($path, $content);
		echo "Created new migration in {$path}" . PHP_EOL;
	}

	private function _getFileName()
	{
		$timestamp = gmdate('YmdHis');
		$fileNameParts = array($timestamp);

		foreach (explode(' ', $this->_options->title) as $titlePart) {
			$fileNameParts[] = strtolower($titlePart);
		}

		return join('_', $fileNameParts) . '.sql';
	}
}
