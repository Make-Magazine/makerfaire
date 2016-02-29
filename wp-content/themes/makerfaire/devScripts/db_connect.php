<?php
/*
 * this file pulls in the wordPress config file for use in devScripts
 * this lessens the amount of overhead used vs including wp-load.php and
 * allows test scripts to run raster
 */
$wp_config_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
include $wp_config_path.'/wp-config.php';

$host         = DB_HOST;
$user         = DB_USER;
$password     = DB_PASSWORD;
$database     = DB_NAME;

$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
