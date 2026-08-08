<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

// Assuming your table has 'order_id', 'order_date', 'amount', 'address', 'pincode'
// Update column names if they differ in your database
$sql = "SELECT 
            o.order_date, 
            o.total_amount, 
            o.status, 
            o.address, 
            o.pincode, 
            p.image_path,
            c.category_name 
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN categories c ON p.category_id = c.id 
        WHERE o.user_id = $user_id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);
$orders = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode(["status" => "success", "orders" => $orders]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>