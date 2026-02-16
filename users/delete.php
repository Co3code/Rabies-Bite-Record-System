<?php

require "../config/db.php";
require "../config/auth.php";

require_admin(); // ony admin can delete
if (isset($_POST['id'])) {
    $user_id = intval($_POST['id']);

    //prevent deleting self
    if ($user_id === $_SESSION['user_id']) {
        header("Location: index.php?message=" . urlencode("You cannot delete yourself"));

        exit();
    }
    // Prepare and execute deletion
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=" . urlencode("User deleted successfully.") . "&type=success");
    } else {
        header("Location: index.php?message=" . urlencode("Failed to delete user.") . "&type=error");
    }
    mysqli_stmt_close($stmt);
    exit();
} else {
    // No ID provided
    header("Location: index.php?message=" . urlencode("No user selected for deletion.") . "&type=error");
    exit();
}
