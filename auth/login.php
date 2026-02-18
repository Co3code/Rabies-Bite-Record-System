<?php
    session_start();

    $pageTitle = "Rabies Bite Record System - Login";
    include '../header.php';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1d3557;
            --accent-blue: #457b9d;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* Image Side */
        .login-image {
            flex: 1;
            background: #f1f4f9 url('../images/hospital.jpg') center/cover no-repeat;            display: none; /* Hidden on mobile */
        }

        /* Form Side */
        .login-form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            padding: 40px;
        }

        .form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .brand-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .welcome-text {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin-top: 5px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.2);
            border-color: var(--accent-blue);
        }

        .btn-login {
            background-color: var(--primary-dark);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: var(--accent-blue);
            transform: translateY(-1px);
        }

        /* Desktop responsiveness */
        @media (min-width: 992px) {
            .login-image {
                display: block;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-image">
        </div>

    <div class="login-form-section">
        <div class="form-wrapper">

            <div class="mb-4">
                <h2 class="brand-logo">Rabies Bite Record System</h2>
                <p class="welcome-text">Please enter your credentials to access the secure database.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="login_process.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. admin_Co Untian" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-login w-100">Sign In</button>

                <div class="mt-4 text-center">
                    <small class="text-muted">&copy; 2024 Health Department. All rights reserved.</small>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>