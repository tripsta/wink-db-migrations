<?php
require_once __DIR__ . '/../../init.php';

Application::bootstrap(array('path', 'database'));

$migrate = new Wink_Db_Migration_Migrate;
$migrate->migrate();
