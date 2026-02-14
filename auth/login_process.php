<?php
session_start();
require "../config/db.php";

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// CSRF Token Validation
if (! isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

// Get and sanitize input
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Basic validation
if (empty($username) || empty($password)) {
    header("Location: login.php?error=All fields are required");
    exit();
}

// Prepare statement (SQL Injection protection)
$sql  = "SELECT id, fullname, username, password, role FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {

        // Verify hashed password
        if (password_verify($password, $user['password'])) {

            // Regenerate session ID (prevent session fixation)
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role']     = $user['role'];

            header("Location: ../dashboard.php");
            exit();

        } else {
            header("Location: login.php?error=Invalid username or password");
            exit();
        }

    } else {
        header("Location: login.php?error=Invalid username or password");
        exit();
    }

} else {
    die("Database error");
}
