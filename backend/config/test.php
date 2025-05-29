<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'databaseConfig.php';

if ($conn->connect_error) {
    die("Falló la conexión: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM students");

while ($row = $result->fetch_assoc()) {
    print_r($row);
}
