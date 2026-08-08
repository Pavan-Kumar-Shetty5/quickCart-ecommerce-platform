<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Session expired"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit;
}

$phone    = $conn->real_escape_string($data['phone']);
$address  = $conn->real_escape_string($data['address']);
$pincode  = $conn->real_escape_string($data['pincode']);
$password = $data['password'] ?? '';

// --- PART 1: Update 'orders' table (Shipping Info) ---
$sqlOrder = "UPDATE orders SET phone = '$phone', address = '$address', pincode = '$pincode' WHERE user_id = $user_id";
$conn->query($sqlOrder);

// --- PART 2: Update 'users' table (Password only if changed) ---
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sqlUser = "UPDATE users SET password = '$hashedPassword' WHERE id = $user_id";
    
    if (!$conn->query($sqlUser)) {
        echo json_encode(["status" => "error", "message" => "Shipping saved, but password update failed"]);
        exit;
    }
}

echo json_encode(["status" => "success", "message" => "Profile and Password updated!"]);

$conn->close();
?>