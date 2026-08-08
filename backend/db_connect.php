<?php
// db_connect.php - Only the connection
$host = "127.0.0.1";
$port = 3307;
$db_user = "root";
$db_pass = "";
$db_name = "quickcart";

// Create connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
// DO NOT put $conn->close() here!
?>