<?php

DEFINE('DB_HOST', 'localhost');
DEFINE('DB_USER', 'root');
DEFINE('DB_PASS', 'jikipol');
DEFINE('DB_TABLE', 'fantasy');
DEFINE('DB_PORT', ini_get('mysqli.default_port'));

require_once($_SERVER['DOCUMENT_ROOT'].$root."modules/classes/Database.class.php");
$db = new Database();

?>