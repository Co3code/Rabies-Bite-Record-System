<?php
require "../config/db.php";
require "../config/auth.php";
require_admin(); // Only admin can delete

if (! isset($_GET['id']) || ! is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Delete patient securely
$stmt = mysqli_prepare($conn, "DELETE FROM patients WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: index.php?success=Patient deleted successfully");
exit();
