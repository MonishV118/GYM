<?php
$servername = "localhost";
$username = "root";       // Your MySQL username
$password = "Vuma@1234";           // Your MySQL password
$dbname = "gym_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
