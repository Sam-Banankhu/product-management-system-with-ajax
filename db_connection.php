<?php
// Database configuration
$host = 'localhost'; 
$dbName = 'pms'; 
$username = 'root'; 
$password = ''; 

// database connection
$conn = new mysqli($host, $username, $password, $dbName);

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
