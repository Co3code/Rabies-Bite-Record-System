<?php
    require "../config/db.php";
    require "../config/auth.php"; // only login required (not admin only)

    // Search logic
    $search = "";

    if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM patients WHERE fullname LIKE ? ORDER BY created_at DESC"
    );

    $like = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "s", $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    } else {

    $result = mysqli_query(
        $conn,
        "SELECT * FROM patients ORDER BY created_at DESC"
    );
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patients</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-custom px-3">
    <span class="navbar-brand text-white">Patient Management</span>
    <a href="../dashboard.php" class="text-white">Back</a>
</nav>

<div class="container mt-4">
    <div class="card card-custom p-4">

        <div class="d-flex justify-content-between align-items-center">
            <h4>Patients</h4>
            <a href="create.php" class="btn btn-primary-custom">
                + Add Patient
            </a>
        </div>

        <hr>

        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-control"
                    placeholder="Search patient by name..."
                >
                <button class="btn btn-primary-custom">Search</button>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Sex</th>
                    <th>Contact</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['sex']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>

                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-primary-custom me-1">
                                    Edit
                                </a>

                                <a href="delete.php?id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-danger me-1"
                                   onclick="return confirm('Are you sure you want to delete this patient?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            No patients found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
