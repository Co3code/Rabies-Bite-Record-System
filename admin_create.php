<?php
include "config/db.php";

$password = password_hash("admin123", PASSWORD_DEFAULT);

$sql = "INSERT INTO users (fullname, username, password, role)
        VALUES ('System Administrator', 'admin', '$password', 'admin')";

if (mysqli_query($conn, $sql)) {
    echo "Admin created successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
//admin - admin123
?>
