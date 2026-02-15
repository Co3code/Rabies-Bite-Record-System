<?php
    require "config/db.php";
    require "config/auth.php";

    // Protect page
    if (! isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
    }

    // Summary counts
    $total_patients   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM patients"))['count'];
    $total_bites      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bite_records"))['count'];
    $total_injections = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM injections"))['count'];

    // Overdue injections
    $overdue_result = mysqli_query($conn,
    // "SELECT p.id
    //  FROM patients p
    //  LEFT JOIN injections i ON p.id = i.patient_id
    //  GROUP BY p.id
    //  HAVING MAX(i.injection_date) IS NULL OR DATEDIFF(CURDATE(), MAX(i.injection_date)) > 365"
    //if i add the patient without bite/injection it will not show as a overdue
    "SELECT p.id
    FROM patients p
    JOIN injections i ON p.id = i.patient_id
    GROUP BY p.id
    HAVING DATEDIFF(CURDATE(), MAX(i.injection_date)) > 365"

    );
    $overdue_count = mysqli_num_rows($overdue_result);
    $overdue_class = $overdue_count > 0 ? 'bg-danger text-white' : 'bg-success text-white';

    // Fetch recent bites (latest 5)
    $recent_bites = mysqli_query($conn,
    "SELECT b.*, p.fullname
     FROM bite_records b
     JOIN patients p ON b.patient_id = p.id
     ORDER BY b.bite_date DESC
     LIMIT 5"
    );

    // Fetch recent injections (latest 5)
    $recent_injections = mysqli_query($conn,
    "SELECT i.*, p.fullname
     FROM injections i
     JOIN patients p ON i.patient_id = p.id
     ORDER BY i.injection_date DESC
     LIMIT 5"
    );

    $search_query   = '';
    $search_results = [];

    if (isset($_GET['search']) && ! empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $stmt         = mysqli_prepare($conn, "SELECT id, fullname, age, sex, contact FROM patients WHERE fullname LIKE ?");
    $like_search  = "%" . $search_query . "%";
    mysqli_stmt_bind_param($stmt, "s", $like_search);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($res)) {
        $search_results[] = $row;
    }
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
 <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex align-items-center">
                <input
                    type="text"
                    name="search"
                    class="form-control me-2 rounded-pill border-primary"
                    placeholder="Search by patient name"
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                >
                <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
                    Search
                </button>
            </form>
        </div>
    </div>
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 text-center">
                <h5>Total Patients</h5>
                <h2><?php echo $total_patients; ?></h2>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 text-center">
                <h5>Total Bites</h5>
                <h2><?php echo $total_bites; ?></h2>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 text-center">
                <h5>Total Injections</h5>
                <h2><?php echo $total_injections; ?></h2>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 text-center <?php echo $overdue_class; ?>">
                <h5>Overdue Injections</h5>
                <h2><?php echo $overdue_count; ?></h2>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <?php if (! empty($search_results)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h5>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h5>
                <table class="table table-bordered table-hover table-sm text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Patient Name</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($patient['age']); ?></td>
                                <td><?php echo htmlspecialchars($patient['sex']); ?></td>
                                <td><?php echo htmlspecialchars($patient['contact']); ?></td>
                                <td>
                                    <a href="patients/view.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (isset($_GET['search'])): ?>
        <div class="alert alert-warning mt-4">
            No patients found for "<?php echo htmlspecialchars($search_query); ?>"
        </div>
    <?php endif; ?>

    <!-- Recent Bites & Injections -->
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <h5>Recent Bites</h5>
            <table class="table table-bordered table-hover table-sm text-center">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Animal</th>
                        <th>Category</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_bites) > 0): ?>
                        <?php while ($bite = mysqli_fetch_assoc($recent_bites)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bite['bite_date']); ?></td>
                                <td><?php echo htmlspecialchars($bite['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($bite['animal']); ?></td>
                                <td><?php echo htmlspecialchars($bite['category']); ?></td>
                                <td><?php echo htmlspecialchars($bite['location']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No bite records yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6 mb-3">
            <h5>Recent Injections</h5>
            <table class="table table-bordered table-hover table-sm text-center">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Dose</th>
                        <th>Vaccine</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_injections) > 0): ?>
                        <?php while ($inj = mysqli_fetch_assoc($recent_injections)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inj['injection_date']); ?></td>
                                <td><?php echo htmlspecialchars($inj['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($inj['dose']); ?></td>
                                <td><?php echo htmlspecialchars($inj['vaccine']); ?></td>
                                <td><?php echo htmlspecialchars($inj['remarks']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No injection records yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
