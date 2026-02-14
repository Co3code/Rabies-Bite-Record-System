<?php
    session_start();

    // Protect page
    if (! isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- <nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Rabies System</span>
    <span class="text-white">
        Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?> |
        <a href="auth/logout.php" class="text-warning">Logout</a>
    </span>
</nav> -->
<!-- <nav class="navbar navbar-expand-lg navbar-custom px-3">
    <a class="navbar-brand">Rabies System</a>

    <div class="ms-auto text-white">
        Welcome,  echo htmlspecialchars($_SESSION['fullname']);  |
        <a href="auth/logout.php" class="text-warning">Logout</a>
    </div>
</nav> -->
<nav class="navbar navbar-expand-lg navbar-custom px-3">
    <a class="navbar-brand text-white">Rabies System</a>

    <div class="ms-auto text-white">
        Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>
        | <a href="patients/index.php" class="text-info">Patients</a>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            | <a href="users/index.php" class="text-info">Manage Users</a>
        <?php endif; ?>

        | <a href="auth/logout.php" class="text-warning">Logout</a>
    </div>
</nav>


<div class="container mt-5">
    <!-- <div class="card p-4 shadow"> -->
        <div class="card card-custom p-4">
        <h3>Dashboard</h3>
        <p>Login successful </p>
    </div>
</div>

</body>
</html>
