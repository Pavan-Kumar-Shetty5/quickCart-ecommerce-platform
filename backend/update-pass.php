<?php
// Include your actual database connection file here
require_once 'db_connect.php'; 

$message = "";
$status_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture values from the form
    $email = $_POST['email'] ?? '';
    $new_pass = $_POST['new_pass'] ?? '';

    if (empty($email) || empty($new_pass)) {
        $message = "Invalid request. Missing email or password.";
    } else {
        try {
            // 1. Hash the new password securely
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

            // 2. Prepare the statement using MySQLi syntax (?)
            // Assuming your connection variable is $conn
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            
            if ($stmt) {
                // 3. Bind parameters ("ss" means both parameters are strings)
                $stmt->bind_param("ss", $hashed_password, $email);
                
                // 4. Execute the query
                $stmt->execute();

                // 5. Check if any rows were affected (updated)
                if ($stmt->affected_rows > 0) {
                    $message = "Password updated successfully!";
                    $status_class = "success"; 
                } else {
                    $message = "Account not found or password is unchanged.";
                }
                
                $stmt->close();
            } else {
                $message = "Failed to prepare the database statement.";
            }

        } catch (Exception $e) {
            $message = "Something went wrong. Please try again later.";
        }
    }
} else {
    header("Location: reset-page.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Status | QuickCart</title>
    <link rel="stylesheet" href="../frontend/update-pass.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>
                <?php echo ($status_class === "success") ? "Success!" : "Notice"; ?>
            </h2>
            
            <p style="color: <?php echo ($status_class === 'success') ? '#22c55e' : '#ef4444'; ?>; font-size: 14px; margin-bottom: 25px;">
                <?php echo htmlspecialchars($message); ?>
            </p>
            
            <div class="footer-links">
                <a href="../frontend/home.html" class="back-link">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>