<?php
// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'LocalFloof'); // Replace with your actual database username
define('DB_PASSWORD', 'thisisthesqlpassword'); // Replace with your actual database password
define('DB_NAME', 'mywebsite');     // Replace with your actual database name

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
