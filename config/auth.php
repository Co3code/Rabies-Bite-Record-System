<?php
session_start();

if (! isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

function require_admin()
{
    if ($_SESSION['role'] !== 'admin') {
        die("Access Denied: Admins only.");
    }
}
