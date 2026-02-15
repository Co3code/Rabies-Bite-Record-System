<?php
    require "../config/db.php";
    require "../config/auth.php"; // login required

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

    $bite_date = trim($_POST['bite_date']);
    $animal    = trim($_POST['animal']);
    $category  = trim($_POST['category']);
    $location  = trim($_POST['location']);

    // Validation
    if (empty($bite_date) || empty($animal) || empty($category) || empty($location)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO bite_records (patient_id, bite_date, animal, category, location)
             VALUES (?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "issss", $patient_id, $bite_date, $animal, $category, $location);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: view.php?id=$patient_id&success=Bite record added");
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
    <title>Add Bite Record</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">Add Bite Record</span>
    <a href="view.php?id=<?php echo $patient_id; ?>" class="text-white">Back</a>
</nav>

<div class="container mt-4">
    <div class="card card-custom p-4">

        <h4>Bite Record for <?php echo htmlspecialchars($patient['fullname']); ?></h4>

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
                <label>Bite Date</label>
                <input type="date" name="bite_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Animal</label>
                <input type="text" name="animal" class="form-control" placeholder="Dog, Cat, etc." required>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="">Select</option>
                    <option value="Category I">Category I</option>
                    <option value="Category II">Category II</option>
                    <option value="Category III">Category III</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Location of Bite</label>
                <input type="text" name="location" class="form-control" placeholder="Arm, Leg, etc." required>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100">Add Bite Record</button>

        </form>

    </div>
</div>

</body>
</html>
