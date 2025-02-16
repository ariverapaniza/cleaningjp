<?php
// dashboard.php
require 'config.php';

// Only admins should access this page
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

// Fetch jobs joined with user details
$query = "SELECT j.JobID, j.ClientName, j.JobDescription, j.ServiceDate, u.Username
          FROM Jobs j
          INNER JOIN Users u ON j.UserID = u.UserID
          ORDER BY j.DateCreated DESC";

$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Cleaning App</title>
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
        <h2 class="mb-4">Admin Dashboard</h2>
        <div class="mb-3">
            <input type="text" id="searchInput" onkeyup="searchJobs()" placeholder="Search jobs..."
                class="form-control">
        </div>
        <table class="table table-bordered" id="jobsTable">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Client Name</th>
                    <th>Job Description</th>
                    <th>Service Date</th>
                    <th>Created By</th>
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
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    <td>
                        <a href="job_details.php?jobid=<?php echo $row['JobID']; ?>" class="btn btn-primary btn-sm">Open
                            Form</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>