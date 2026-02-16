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
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
     header("Location: index.php?message=" . urlencode("User deleted successfuly."));
    exit();
}else{
    header("location: index.php");
    exit();
}
