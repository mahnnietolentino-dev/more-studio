<?php

$host = 'localhost';
$db   = 'more_db';
$user = 'root';
$pass = '1@mgr33nLantern';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('DB Connection Failed: ' . $conn->connect_error);
}
?>
