<?php
// 1. START SESSION & SECURITY CHECK
session_start();

// If the session variable isn't set, redirect them to login immediately
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// 2. DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "quickcart", 3307);
if ($conn->connect_error) die("Connection Failed");

$view = $_GET['view'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QuickCart Admin | Dashboard</title>
    <link rel="stylesheet" href="../main/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <nav class="sidebar">
            <div class="sidebar-header">QuickCart <span>ADMIN</span></div>
            <ul>
                <li><a href="admin.php?view=dashboard" class="<?= $view=='dashboard'?'active':'' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="admin.php?view=users" class="<?= $view=='users'?'active':'' ?>"><i class="fas fa-user-friends"></i> Customers</a></li>
                <li><a href="admin.php?view=products" class="<?= $view=='products'?'active':'' ?>"><i class="fas fa-layer-group"></i> Inventory</a></li>
                <li><a href="admin.php?view=orders" class="<?= $view=='orders'?'active':'' ?>"><i class="fas fa-truck"></i> Orders</a></li>
                <li><a href="admin.php?view=chat" class="<?= $view=='chat'?'active':'' ?>"><i class="fas fa-comment-alt"></i> Support Chat</a></li>
            </ul>
            
            <a href="logout.php" class="logout-btn" onclick="return confirm('Logout from QuickCart Admin?')">
                <i class="fas fa-power-off"></i> Logout
            </a>
        </nav>

        <main class="content">
            <div class="top-bar">
                <h1><?= ucfirst($view) ?> Management</h1>
                <div class="user-info">Welcome, <?= $_SESSION['admin_user'] ?? 'Admin Pavan' ?></div>
            </div>

            <div class="container">
                <?php 
                    switch($view) {
                        case 'users': include 'manage-users.php'; break;
                        case 'products': include 'manage-products.php'; break;
                        case 'orders': include 'manage-orders.php'; break;
                        case 'chat': include '../main/admin-chat.html'; break;
                        default: include 'admin-stats.php'; break;
                    }
                ?>
            </div>
        </main>
    </div>
</body>
</html>