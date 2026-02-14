<?php
    require "../config/db.php";
    require "../config/auth.php"; // login required only

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF check
    if (! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $fullname = trim($_POST['fullname']);
    $age      = trim($_POST['age']);
    $sex      = trim($_POST['sex']);
    $address  = trim($_POST['address']);
    $contact  = trim($_POST['contact']);

    // Validation
    if (empty($fullname) || empty($age) || empty($sex)) {
        $errors[] = "Full name, age, and sex are required.";
    }

    if (! is_numeric($age) || $age <= 0) {
        $errors[] = "Age must be a valid number.";
    }

    if (empty($errors)) {

        $stmt = mysqli_prepare($conn,
            "INSERT INTO patients (fullname, age, sex, address, contact)
             VALUES (?, ?, ?, ?, ?)");

        mysqli_stmt_bind_param($stmt, "sisss",
            $fullname,
            $age,
            $sex,
            $address,
            $contact
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?success=Patient added successfully");
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
    <title>Add Patient</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">Add Patient</span>
    <a href="index.php" class="text-white">Back</a>
</nav>

<div class="container mt-4">
    <div class="card card-custom p-4">

        <h4>New Patient</h4>

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
                <label>Age</label>
                <input type="number" name="age"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Sex</label>
                <select name="sex" class="form-control" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Address</label>
                <textarea name="address"
                          class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label>Contact</label>
                <input type="text" name="contact"
                       class="form-control">
            </div>

            <button type="submit"
                    class="btn btn-primary-custom w-100">
                Save Patient
            </button>

        </form>

    </div>
</div>

</body>
</html>
