<?php
session_start();

// If already logged in, skip to dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded credentials as requested
    if ($username === "admin" && $password === "admin@123") {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = "Administrator";
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QuickCart | Admin Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #0f0f0f;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            background: #1a1a1a;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            width: 350px;
            border: 1px solid #333;
        }
        .login-card h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #4bb543;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #aaa;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #444;
            background: #222;
            color: #fff;
            box-sizing: border-box;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #4bb543;
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #3e9e37;
        }
        .error-msg {
            color: #ff4d4d;
            background: rgba(255, 77, 77, 0.1);
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>QuickCart Admin</h2>
    
    <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Enter admin username">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
        </div>
        <button type="submit" class="login-btn">LOGIN</button>
    </form>
</div>

</body>
</html>