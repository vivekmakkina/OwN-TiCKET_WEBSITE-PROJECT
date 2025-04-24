<?php
$host = "localhost";
$user = "root";
$password = "root"; // for MAMP; leave blank for WAMP
$dbname = "movie_booking";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
