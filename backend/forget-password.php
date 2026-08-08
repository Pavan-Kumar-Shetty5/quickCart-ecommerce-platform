<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../frontend/style.css"> </head>
<body>
    <div class="auth-container">
        <form action="../backend/send-link.php" method="POST" class="auth-form">
            <h2>Reset Password</h2>
            <p>Enter your email and we'll send you a link to reset your password.</p>
            
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="name@example.com" required>
            </div>
            
            <button type="submit" class="auth-submit-btn">Send Reset Link</button>
            <div style="margin-top: 15px;">
                <a href="login.php" class="forgot-link">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>