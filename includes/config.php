<?php 
// Connecting to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mobile_store";

// Creating a connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Die if connection gets fail
if (!$conn) { 
    die("Connection failed" . mysqli_connect_error());
}
else { 
    echo "Connection Succesfull <br>";
}

?>