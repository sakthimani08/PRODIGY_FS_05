<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// your DB connection code here
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "social_app";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
