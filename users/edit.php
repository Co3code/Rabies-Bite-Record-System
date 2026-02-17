<?php
    require "../config/db.php";
    require "../config/auth.php";
    $pageTitle = "Edit User";
    include '../header.php';

    require_admin(); // Only admin can edit

    if (! isset($_GET['id'])) {
    header("Location: index.php");
    exit();
    }

    $user_id = intval($_GET['id']);
    $result  = mysqli_query($conn, "SELECT id, fullname, username, role FROM users WHERE id = $user_id");
    $user    = mysqli_fetch_assoc($result);

    if (! $user) {
    header("Location: index.php?message=User not found&type=error");
    exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $role     = $_POST['role'];

    $stmt = mysqli_prepare($conn, "UPDATE users SET fullname = ?, username = ?, role = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $fullname, $username, $role, $user_id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php?message=User updated successfully&type=success");
    exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Edit User</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Fullname</label>
            <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="staff" <?php if ($user['role'] === 'staff') {
                                              echo 'selected';
                                      }
                                      ?>>Staff</option>
                <option value="admin" <?php if ($user['role'] === 'admin') {
                                              echo 'selected';
                                      }
                                      ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
