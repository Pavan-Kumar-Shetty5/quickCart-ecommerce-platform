<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

// Database connection on Port 3307
$conn = new mysqli("localhost", "root", "", "quickcart", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];

// Check if user exists using Prepared Statements (Better for your project marks!)
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pavankumarshetty100@gmail.com'; 
        $mail->Password   = 'yyotegysmpcggiei'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('pavankumarshetty100@gmail.com', 'QuickCart Support');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Link - QuickCart';
        
        $link = "http://localhost/final-year-project/backend/reset-page.php?email=" . urlencode($email);
        
        // Professional HTML Email Body
        $mail->Body = "
            <div style='background: #121212; color: #ffffff; padding: 20px; font-family: sans-serif; border-radius: 10px;'>
                <h2 style='color: #007bff;'>QuickCart Password Recovery</h2>
                <p>We received a request to reset your password. Click the button below to proceed:</p>
                <a href='$link' style='display: inline-block; padding: 10px 20px; background: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                <p style='font-size: 12px; color: #888; margin-top: 20px;'>If you did not request this, please ignore this email.</p>
            </div>";

        $mail->send();
        
        // Display Styled Success Page instead of JSON
        renderStatusPage("Success!", "A reset link has been sent to your email. Please check your inbox.", "success", $email);

    } catch (Exception $e) {
        renderStatusPage("Mail Failed", "Error: " . $mail->ErrorInfo, "error", $email);
    }
} else {
    renderStatusPage("Account Not Found", "The email address entered is not registered with QuickCart.", "error", $email);
}

// Function to display the styled card
function renderStatusPage($title, $msg, $type, $email) {
    $color = ($type == "success") ? "#4bb543" : "#ff4444";
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?> | QuickCart</title>
        <link rel="stylesheet" href="../style.css"> </head>
    <body>
        <div class="auth-container">
            <div class="status-card" style="text-align: center; padding: 20px;">
                <div style="font-size: 50px; color: <?php echo $color; ?>; margin-bottom: 15px;">
                    <?php echo ($type == "success") ? "✔" : "✖"; ?>
                </div>
                <h2 style="color: #fff; margin-bottom: 10px;"><?php echo $title; ?></h2>
                <p style="color: #b0b0b0; font-size: 14px; margin-bottom: 25px;"><?php echo $msg; ?></p>
                <a href="../frontend/home.html" class="auth-submit-btn" style="text-decoration: none; display: block;">Back to Login</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>