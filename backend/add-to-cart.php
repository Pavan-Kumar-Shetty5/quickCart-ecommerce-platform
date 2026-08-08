<?php
header('Content-Type: application/json');
$host = "localhost";
$user = "root";
$pass = "";
$db = "quickcart";

$conn = new mysqli($host, $user, $pass, $db);

// Get the data sent from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['product_id']) && isset($data['quantity'])) {
    $p_id = $data['product_id'];
    $qty = $data['quantity'];

    // For now, we use a hardcoded user_id (e.g., 1) until you build your login system
    $u_id = 1; 

    // Check if product is already in cart
    $check = "SELECT id, quantity FROM cart_items WHERE user_id = $u_id AND product_id = $p_id";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        // Update existing quantity
        $sql = "UPDATE cart_items SET quantity = quantity + $qty WHERE user_id = $u_id AND product_id = $p_id";
    } else {
        // Insert new item
        $sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ($u_id, $p_id, $qty)";
    }

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Added to cart"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}
$conn->close();
?>