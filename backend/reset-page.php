<?php
// backend/reset-page.php
$email = isset($_GET['email']) ? $_GET['email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | QuickCart</title>
    <link rel="stylesheet" href="../frontend/update-pass.css">
</head>
<body>
    <div class="auth-container">
        <form method="POST" action="update-pass.php" class="auth-form">
            <h2>New Password</h2>
            <p>Setting password for: <b><?php echo htmlspecialchars($email); ?></b></p>
            
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            
            <div class="input-group">
                <input type="password" name="new_pass" placeholder="Enter new password" required>
            </div>

            <button type="submit" class="auth-submit-btn">Update Password</button>
            
            <div class="footer-links">
                <a href="../frontend/home.html" class="back-link">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>