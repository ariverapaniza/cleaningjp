<?php
// user_dashboard.php
require 'config.php';

// Only non-admin (cleaning staff) users should access this page
if (!isLoggedIn() || isAdmin()) {
    header("Location: index.php");
    exit;
}

// Fetch jobs created by the logged-in cleaning staff member
$stmt = $mysqli->prepare("SELECT JobID, ClientName, JobDescription, ServiceDate FROM Jobs WHERE UserID = ? ORDER BY DateCreated DESC");
$stmt->bind_param("i", $_SESSION['UserID']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Cleaning App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function searchJobs() {
            var input = document.getElementById("searchInput").value.toLowerCase();
            var rows = document.getElementById("jobsTable").getElementsByTagName("tr");
            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName("td");
                var found = false;
                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? "" : "none";
            }
        }
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>





    <div class="container mt-5">
        <h2 class="mb-4 text-center">Dashboard</h2>
        <div class="row mb-4 text-center">
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="job_form.php">
                        <img src="img/job_form_admin.png" class="card-img-top" alt="Update Items"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="job_form.php" class="text-decoration-none text-dark">Add a
                                Job</a></h5>
                        <p class="card-text">Create a New Job.</p>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="mb-4 text-center">My Job Forms</h2>
        <div class="mb-3">
            <input type="text" id="searchInput" onkeyup="searchJobs()" placeholder="Search my jobs..."
                class="form-control">
        </div>
        <table class="table table-bordered" id="jobsTable">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Client Name</th>
                    <th>Job Description</th>
                    <th>Service Date</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['JobID']); ?></td>
                        <td><?php echo htmlspecialchars($row['ClientName']); ?></td>
                        <td><?php echo htmlspecialchars($row['JobDescription']); ?></td>
                        <td><?php echo htmlspecialchars($row['ServiceDate']); ?></td>
                        <td>
                            <a href="job_details.php?jobid=<?php echo $row['JobID']; ?>" class="btn btn-primary btn-sm">Open
                                Form</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center">No job forms found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>