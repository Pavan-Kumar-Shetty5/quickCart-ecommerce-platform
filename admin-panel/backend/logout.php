<?php
session_start();

// 1. Clear all session data
session_unset();

// 2. Destroy the session entirely
session_destroy();

// 3. Redirect to the login page
header("Location: admin-login.php");
exit();
?>