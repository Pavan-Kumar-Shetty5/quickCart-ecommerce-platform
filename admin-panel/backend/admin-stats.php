<?php
// backend/admin-stats.php
$u_total = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$p_total = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$o_total = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
?>
<div class="stats-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
    <div class="card" style="border-left: 5px solid #007bff;">
        <h4 style="margin:0; color:#888;">Total Users</h4>
        <h2 style="margin:10px 0;"><?php echo $u_total; ?></h2>
    </div>
    <div class="card" style="border-left: 5px solid #4bb543;">
        <h4 style="margin:0; color:#888;">Products</h4>
        <h2 style="margin:10px 0;"><?php echo $p_total; ?></h2>
    </div>
    <div class="card" style="border-left: 5px solid #ffc107;">
        <h4 style="margin:0; color:#888;">Total Orders</h4>
        <h2 style="margin:10px 0;"><?php echo $o_total; ?></h2>
    </div>
</div>
<div class="card" style="margin-top:20px;">
    <h3>Quick Actions</h3>
    <p>Welcome to the <b>QuickCart</b> Admin Panel. Use the sidebar to manage your store inventory and view customer data.</p>
</div>