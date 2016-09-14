<?php
/*

*/

require_once '../../wp-config.php';

$table_prefix = 'wp_';

$mysqli  = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$mysqli->set_charset("utf8");
if (mysqli_connect_errno()) {
	echo("Failed to connect, the error message is : ". mysqli_connect_error());
	exit();
}

