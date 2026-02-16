<?php
    require "../config/db.php";
    require "../config/auth.php";

    require_admin(); // Only admin allowed

    $result = mysqli_query($conn, "SELECT id, fullname, username, role FROM users");

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">User Management</span>
    <a href="../dashboard.php" class="text-white">Back</a>
</nav>

<div class="container mt-4">
    <div class="card card-custom p-4">

        <div class="d-flex justify-content-between align-items-center">
            <h4>System Users</h4>
            <a href="create.php" class="btn btn-primary-custom">
                + Add New User
            </a>
        </div>

        <hr>


        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-info">
         <?php echo htmlspecialchars($_GET['message']); ?>
          </div>
        <?php endif; ?>

        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Fullname</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <?php if ($row['role'] === 'admin'): ?>
                            <span class="badge bg-primary">Admin</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Staff</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- delete form-->
                         <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="id"  value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>

                         </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
