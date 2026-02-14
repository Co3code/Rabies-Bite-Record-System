<?php
    session_start();

    // Redirect if already logged in
    if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
    }

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rabies Bite Record System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1d3557, #457b9d);
            height: 100vh;
        }
        .login-box {
            width: 400px;
            margin: auto;
            margin-top: 8%;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.2);
        }
        .system-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #1d3557;
        }
    </style>
</head>
<body class="login-bg">

<div class="login-box">
    <h4 class="system-title">Rabies Bite Record System</h4>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login_process.php">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100">Login</button>
    </form>
</div>

</body>
</html>
