<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mobile_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Secret key for generating password reset tokens
define('SECRET_KEY', 'a_very_long_and_random_secret_key_for_your_project');
