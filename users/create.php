<?php
    require "../config/db.php";
    require "../config/auth.php";

    require_admin(); // Only admin can access

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF Validation
    if (! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    // Validation
    if (empty($fullname) || empty($username) || empty($password) || empty($role)) {
        $errors[] = "All fields are required.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Check duplicate username
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check, "s", $username);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $errors[] = "Username already exists.";
    }

    if (empty($errors)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn,
            "INSERT INTO users (fullname, username, password, role)
             VALUES (?, ?, ?, ?)");

        mysqli_stmt_bind_param($stmt, "ssss",
            $fullname,
            $username,
            $hashed_password,
            $role
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?success=User created successfully");
            exit();
        } else {
            $errors[] = "Something went wrong.";
        }
    }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">Create User</span>
    <a href="index.php" class="text-white">Back</a>
</nav>

<div class="container mt-4">
    <div class="card card-custom p-4">

        <h4>Add New User</h4>

        <?php if (! empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden" name="csrf_token"
                   value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="fullname"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100">
                Create Account
            </button>

        </form>

    </div>
</div>

</body>
</html>
