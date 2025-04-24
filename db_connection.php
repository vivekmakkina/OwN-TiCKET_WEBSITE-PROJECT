<?php
// Ensure the correct database connection credentials are used
$servername = "localhost";
$username = "root";  // Default username for MAMP
$password = "root";  // Default password for MAMP
$database = "movie_booking";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
