<?php
// 1. MUST MATCH LOGIN.PHP EXACTLY
ini_set('session.cookie_path', '/'); 
session_set_cookie_params(0, '/');
session_start();

header('Content-Type: application/json');

// 2. DATABASE (Using your 3307 Port)
$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Fail"]));
}

// 3. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please login"]);
    exit;
}

$u_id = $_SESSION['user_id'];

// --- GET: VIEW CART ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Make sure ci.product_id is included in the list
$sql = "SELECT c.id as cart_id,c.product_id, p.name as productName, p.price, p.image_path as image, c.quantity 
    FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = $u_id";

    $res = $conn->query($sql);
    $data = [];
    while($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
    exit;
}

// --- POST: ADD / UPDATE / REMOVE ---
$input = json_decode(file_get_contents("php://input"), true);

if ($input) {
    // Determine the action (Default to 'add' if not specified)
    $action = $input['action'] ?? 'add';

    if ($action === 'add') {
        // Support both old format (product_id) and new format (item -> id)
        $p_id = $input['product_id'] ?? $input['item']['id'];
        $qty = $input['quantity'] ?? $input['item']['quantity'] ?? 1;

        $check = $conn->query("SELECT id FROM cart_items WHERE user_id = $u_id AND product_id = $p_id");
        if ($check->num_rows > 0) {
            $sql = "UPDATE cart_items SET quantity = quantity + $qty WHERE user_id = $u_id AND product_id = $p_id";
        } else {
            $sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ($u_id, $p_id, $qty)";
        }
        $conn->query($sql);
        echo json_encode(["status" => "success"]);
    }
    // --- UPDATE ACTION (Increment / Decrement) ---
// --- UPDATE ACTION (Increment / Decrement) ---
// --- UPDATE ACTION (Increment / Decrement) ---
    if ($action === 'update') {
        // Use $input because that's where your json_decode is saved
        $cart_id = (int)($input['cart_id'] ?? 0);
        $change = (int)($input['change'] ?? 0); 

        if ($cart_id > 0) {
            // Update quantity but ensure it stays at minimum 1
            $sql = "UPDATE cart_items SET quantity = GREATEST(1, quantity + $change) 
                    WHERE id = $cart_id AND user_id = $u_id";
            
            if ($conn->query($sql)) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        }
        exit;
    }
    
    // --- REMOVE ACTION ---
    if ($action === 'remove') {
        $cart_id = (int)($input['cart_id'] ?? 0);
        $conn->query("DELETE FROM cart_items WHERE id = $cart_id AND user_id = $u_id");
        echo json_encode(["status" => "success"]);
        exit;
    }
    
    if ($action === 'remove') {
        $cart_id = $input['cart_id'];
        $conn->query("DELETE FROM cart_items WHERE id = $cart_id AND user_id = $u_id");
        echo json_encode(["status" => "success"]);
    }
}
?>