<?php
// backend/manage-users.php
$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

// Handle User Deletion
// Handle User Deletion
if(isset($_GET['del_user'])) {
    $u_id = (int)$_GET['del_user']; // Cast to int for security

    // 1. Delete user's cart items first
    $conn->query("DELETE FROM cart_items WHERE user_id = $u_id");

    // 2. Optional: If you want to delete their orders too (uncomment if needed)
    // $conn->query("DELETE FROM orders WHERE user_id = $u_id");

    // 3. Now safely delete the user
    $conn->query("DELETE FROM users WHERE id = $u_id");

    header("Location: admin.php?view=users");
    exit();
}

$users = $conn->query("SELECT id, username, email FROM users ORDER BY id DESC");
?>
<div class="card">
    <h3>Customer Registry</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td><?php echo $u['username']; ?></td>
                <td><?php echo $u['email']; ?></td>
                <td>
                    <a href="admin.php?view=users&del_user=<?php echo $u['id']; ?>" 
                       class="text-danger" 
                       onclick="return confirm('Delete this user?')">Remove</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>