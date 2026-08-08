<?php
ini_set('session.cookie_path', '/'); 
session_set_cookie_params(0, '/');
session_start(); 
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // --- REGISTRATION LOGIC ---
    if (isset($_POST['username'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Use try-catch to handle the duplicate entry exception
        try {
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Registration Successful! Please Login.'); window.location='../frontend/home.html';</script>";
            }
        } catch (mysqli_sql_exception $e) {
            // Check if the error code is 1062 (MySQL code for Duplicate Entry)
            if ($e->getCode() === 1062) {
                echo "<script>alert('User already exists! Please use a different email.'); window.history.back();</script>";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    } 
    
    // --- LOGIN LOGIC ---
    else {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $user['username'];
                
                session_write_close(); 
                header("Location: ../frontend/user.html");
                exit();
            } else {
                echo "<script>alert('Invalid Password'); window.location='../frontend/home.html';</script>";
            }
        } else {
            echo "<script>alert('No account found with that email'); window.location='../frontend/home.html';</script>";
        }
    }
}
$conn->close();
?>