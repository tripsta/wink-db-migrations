<?php
require_once __DIR__ . '/../../init.php';

Application::bootstrap(array('path', 'database'));

$bootstrap = new Wink_Db_Bootstrap;
$bootstrap->bootstrap();
