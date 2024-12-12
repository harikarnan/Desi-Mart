<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'desimart';

// Create a database connection
$db = new mysqli($host, $username, $password, $database);

// Check connection
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}
?>
