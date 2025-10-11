<?php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "test";

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Connection Failed: ' . $mysqli->connect_error);
}

// throw exceptions on errors to simplify error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli->set_charset('utf8mb4');