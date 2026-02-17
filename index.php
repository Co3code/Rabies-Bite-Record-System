<?php
session_start();

// Check if user is logged in
if (! isset($_SESSION['user'])) {
    // If not logged in, go to login page
    header("Location: auth/login.php");
    exit();
}

// If logged in, redirect based on role or choice
// Example: if you have roles stored in session
if ($_SESSION['role'] === 'admin') {
    header("Location: users/index.php");
} else {
    header("Location: patients/index.php");
}
exit();
