<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "quickcart", 3307);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

// --- CASE 1: LOADING THE DATA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userQuery = $conn->query("SELECT username, email FROM users WHERE id = $user_id");
    $userData = $userQuery->fetch_assoc();

    $orderQuery = $conn->query("SELECT phone, address, pincode FROM orders WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
    $shippingData = $orderQuery->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "username" => $userData['username'],
        "email" => $userData['email'],
        "phone" => $shippingData['phone'] ?? '',
        "address" => $shippingData['address'] ?? '',
        "pincode" => $shippingData['pincode'] ?? ''
    ]);
} 

// --- CASE 2: SAVING THE DATA (POST) ---
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $phone = $conn->real_escape_string($data['phone']);
    $address = $conn->real_escape_string($data['address']);
    $pincode = $conn->real_escape_string($data['pincode']);

    // Check if user already has a row in orders
    $check = $conn->query("SELECT id FROM orders WHERE user_id = $user_id LIMIT 1");

    if ($check->num_rows > 0) {
        // UPDATE existing row
        $sql = "UPDATE orders SET phone='$phone', address='$address', pincode='$pincode' WHERE user_id=$user_id";
    } else {
        // INSERT first row (dummy product/qty so it doesn't break your table structure)
        $sql = "INSERT INTO orders (user_id, phone, address, pincode, product_id, quantity) 
                VALUES ($user_id, '$phone', '$address', '$pincode', 0, 0)";
    }

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Profile updated in database!"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

$conn->close();
?>