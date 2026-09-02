<?php
require_once '../../wp-config.php';

$table_prefix = 'wp_';

global $wpdb;
$mysqli = $wpdb->dbh;   // reuse WordPress's existing mysqli connection

if (!($mysqli instanceof mysqli) || mysqli_connect_errno()) {
    http_response_code(500);
    exit('DB connection unavailable: ' . mysqli_connect_error());
}