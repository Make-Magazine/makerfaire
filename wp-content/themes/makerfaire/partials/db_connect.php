<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../../wp-config.php';

$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} 
?>
