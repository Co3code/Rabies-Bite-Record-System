<?php

    require "../config/db.php";
    require "../config/auth.php"; // login required
    $pageTitle = 'Patient Details';
    include '../header.php';

    // Check patient ID
    if (! isset($_GET['id']) || ! is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
    }

    $patient_id = $_GET['id'];

    // Fetch patient info
    $stmt = mysqli_prepare($conn, "SELECT * FROM patients WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $result  = mysqli_stmt_get_result($stmt);
    $patient = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (! $patient) {
    header("Location: index.php");
    exit();
    }

    // Fetch bite records
    $stmt = mysqli_prepare($conn, "SELECT * FROM bite_records WHERE patient_id = ? ORDER BY bite_date DESC");
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $bites = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    // Fetch injection records
    $stmt = mysqli_prepare($conn, "SELECT * FROM injections WHERE patient_id = ? ORDER BY injection_date DESC");
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $injections = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    // Calculate last injection date & status
    $last_injection_date = null;
    $status              = "No injections yet";

    if (mysqli_num_rows($injections) > 0) {
    $last                = mysqli_fetch_assoc($injections);
    $last_injection_date = $last['injection_date'];

    //  Create DateTime objects here
    $today     = new \DateTime();
    $last_date = new \DateTime($last_injection_date);

    $diff_days = $today->diff($last_date)->days;

    if ($diff_days < 30) {
        $status = "Up to date";
    } elseif ($diff_days < 365) {
        $status = "Needs booster";
    } else {
        $status = "Overdue";
    }

    // Reset injections pointer
    mysqli_data_seek($injections, 0);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">Patient Details</span>
    <a href="index.php" class="text-white">Back</a>
</nav>

<div class="container mt-4">

    <div class="card card-custom p-4 mb-4">
        <h4><?php echo htmlspecialchars($patient['fullname']); ?></h4>
        <p>Age: <?php echo htmlspecialchars($patient['age']); ?> | Sex: <?php echo htmlspecialchars($patient['sex']); ?></p>
        <p>Contact: <?php echo htmlspecialchars($patient['contact']); ?></p>
        <p>Address: <?php echo htmlspecialchars($patient['address']); ?></p>
        <p>Last Injection: <?php echo $last_injection_date ? htmlspecialchars($last_injection_date) : 'N/A'; ?></p>
        <p>Status: <strong><?php echo $status; ?></strong></p>
    </div>
    <!-- Success Message -->
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5>Bite History</h5>
        <a href="add_bite.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary-custom btn-sm">+ Add Bite</a>
    </div>

    <table class="table table-bordered table-hover mb-4">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Animal</th>
                <th>Category</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($bites) > 0): ?>
                <?php while ($bite = mysqli_fetch_assoc($bites)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bite['bite_date']); ?></td>
                        <td><?php echo htmlspecialchars($bite['animal']); ?></td>
                        <td><?php echo htmlspecialchars($bite['category']); ?></td>
                        <td><?php echo htmlspecialchars($bite['location']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No bite records yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5>Injection History</h5>
        <a href="add_injection.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary-custom btn-sm">+ Add Injection</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Dose</th>
                <th>Vaccine</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($injections) > 0): ?>
                <?php while ($inj = mysqli_fetch_assoc($injections)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($inj['injection_date']); ?></td>
                        <td><?php echo htmlspecialchars($inj['dose']); ?></td>
                        <td><?php echo htmlspecialchars($inj['vaccine']); ?></td>
                        <td><?php echo htmlspecialchars($inj['remarks']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No injections yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>
