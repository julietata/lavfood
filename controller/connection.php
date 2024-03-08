<?php

$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "lavfood_core";

$conn = new mysqli($db_host, $db_username, $db_password, $db_database);

if($conn->connect_error){
    die('error database ' . $conn->connect_error);
}