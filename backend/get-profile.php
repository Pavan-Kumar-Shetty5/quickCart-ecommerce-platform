<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

// 1. Get username/email from the users table
$userQuery = $conn->query("SELECT username, email FROM users WHERE id = $user_id");
$userData = $userQuery->fetch_assoc();

// 2. Get ONLY the most recent shipping info from the orders table
// Using ORDER BY id DESC ensures we get the last address you used
$orderQuery = $conn->query("SELECT phone, address, pincode FROM orders WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");

$shippingData = $orderQuery ? $orderQuery->fetch_assoc() : null;

echo json_encode([
    "status" => "success",
    "username" => $userData['username'],
    "email" => $userData['email'],
    // If no order exists yet, these return as empty strings
    "phone" => $shippingData['phone'] ?? '',
    "address" => $shippingData['address'] ?? '',
    "pincode" => $shippingData['pincode'] ?? ''
]);

$conn->close();
?>