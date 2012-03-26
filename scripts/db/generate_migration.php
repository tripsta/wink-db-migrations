<?php
require_once __DIR__ . '/../../init.php';

Application::bootstrap(array('path', 'database'));

$generate = new Wink_Db_Migration_Generate;
$generate->generate();
