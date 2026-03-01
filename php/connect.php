<?php
$db = new mysqli(
    getenv('MYSQL_HOST'),
    getenv('MYSQL_USER'),
    getenv('MYSQL_PASSWORD'),
    getenv('MYSQL_DATABASE')
);

if ($db->connect_error) {
    die('Database connection error: ' . $db->connect_error);
}