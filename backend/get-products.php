<?php
// Include the CLEAN connection file
include 'db_connect.php'; 

header('Content-Type: application/json');

// Your product query
$sql = "SELECT p.id, p.name, p.price, p.image_path, c.category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id";

$result = $conn->query($sql);

$products = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);

// Close it here at the very end of the specific script
$conn->close();
?>