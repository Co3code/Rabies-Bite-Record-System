<?php
    require "../config/db.php";
    require "../config/auth.php"; // login required
    $pageTitle = "Add Injection";
    include 'header.php';
    // Check patient ID
    if (! isset($_GET['patient_id']) || ! is_numeric($_GET['patient_id'])) {
    header("Location: index.php");
    exit();
    }

    $patient_id = $_GET['patient_id'];

    // Fetch patient info
    $stmt = mysqli_prepare($conn, "SELECT * FROM patients WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $result  = mysqli_stmt_get_result($stmt);
    $patient = mysqli_fetch_assoc($result);

    if (! $patient) {
    header("Location: index.php");
    exit();
    }

    // CSRF token
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

    // CSRF check
    if (! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $injection_date = trim($_POST['injection_date']);
    $dose           = trim($_POST['dose']);
    $vaccine        = trim($_POST['vaccine']);
    $remarks        = trim($_POST['remarks']);

    // Validation
    if (empty($injection_date) || empty($dose) || empty($vaccine)) {
        $errors[] = "Injection date, dose, and vaccine are required.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO injections (patient_id, injection_date, dose, vaccine, remarks)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "issss", $patient_id, $injection_date, $dose, $vaccine, $remarks);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: view.php?id=$patient_id&success=Injection record added");
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
    Add Injection
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">Add Injection</span>
    <a href="view.php?id=<?php echo $patient_id; ?>" class="text-white">Back</a>
</nav>

<div class="container mt-4">
    <div class="card card-custom p-4">

        <h4>Injection Record for <?php echo htmlspecialchars($patient['fullname']); ?></h4>

        <?php if (! empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label>Injection Date</label>
                <input type="date" name="injection_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Dose</label>
                <input type="text" name="dose" class="form-control" placeholder="e.g., 0.5 ml" required>
            </div>

            <div class="mb-3">
                <label>Vaccine</label>
                <input type="text" name="vaccine" class="form-control" placeholder="e.g., Rabies Vaccine XYZ" required>
            </div>

            <div class="mb-3">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100">Add Injection</button>

        </form>

    </div>
</div>

</body>
</html>
