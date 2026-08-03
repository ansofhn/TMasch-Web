<?php

$DB_HOST = getenv('MYSQLHOST')     ?: 'localhost';
$DB_PORT = getenv('MYSQLPORT')     ?: '3306';
$DB_USER = getenv('MYSQLUSER')     ?: 'root';
$DB_PASS = getenv('MYSQLPASSWORD') ?: '';
$DB_NAME = getenv('MYSQLDATABASE') ?: 'tmasch_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
