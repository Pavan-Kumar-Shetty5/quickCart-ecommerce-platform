<?php
session_start();
header('Content-Type: application/json');

// Using your port 3307 and database 'quickcart'
$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

$user_id = $_SESSION['user_id'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);
$cart_items = $data['cart_items'] ?? [];
$payment_method = $data['payment_method'] ?? 'COD';

if (!$user_id || empty($cart_items)) {
    die(json_encode(["status" => "error", "message" => "Session expired or cart is empty"]));
}

// 1. Fetch the shipping details from the most recent order record
$addrQuery = $conn->query("SELECT phone, address, pincode FROM orders WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
$addrData = $addrQuery ? $addrQuery->fetch_assoc() : null;

if (!$addrData || trim($addrData['address']) === "") {
    die(json_encode(["status" => "error", "message" => "Address is empty. Please update your profile first."]));
}
$phone = $addrData['phone'];
$address = $conn->real_escape_string($addrData['address']);
$pincode = $addrData['pincode'];

$success = true;

// 2. Loop through the items sent from the frontend
foreach ($cart_items as $item) {
    // Map product_id from your cart_items table
    $product_id = $item['product_id'] ?? $item['id'] ?? 0;
    $price = (float)($item['price'] ?? 0);
    $qty = (int)($item['quantity'] ?? 1);
    
    // Calculate total for this specific item
    $total_amount = $price * $qty;

    $sql = "INSERT INTO `orders` (`user_id`, `product_id`, `quantity`, `address`, `phone`, `pincode`, `payment_method`, `total_amount`) 
            VALUES ('$user_id', '$product_id', '$qty', '$address', '$phone', '$pincode', '$payment_method', '$total_amount')";
    
    if (!$conn->query($sql)) {
        $success = false;
        $error_msg = $conn->error;
        break;
    }
}

if ($success) {
    // 3. IMPORTANT: Clear the 'cart_items' table (not 'cart')
    $conn->query("DELETE FROM cart_items WHERE user_id = $user_id");
    echo json_encode(["status" => "success", "message" => "Order placed successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "SQL Error: " . $error_msg]);
}

$conn->close();
?>