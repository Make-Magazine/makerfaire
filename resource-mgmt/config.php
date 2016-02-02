<?php
/*
Site : http:www.smarttutorials.net
Author :muni
*/

require_once '../wp-config.php';
# Database Configuration
//define( 'DB_NAME', 'wp_makerfaire' );
/** MySQL database username */
//define('DB_USER', 'root');
/** MySQL database password */
//define('DB_PASSWORD', 'r00tp@$$');
/** MySQL hostname */
//define('DB_HOST', 'localhost:8889');
//define( 'DB_CHARSET', 'utf8' );
//define( 'DB_COLLATE', 'utf8_unicode_ci' );
$table_prefix = 'wp_';

$mysqli  = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
	echo("Failed to connect, the error message is : ". mysqli_connect_error());
	exit();
}

