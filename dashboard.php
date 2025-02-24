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
        <!-- Four Admin Panels Cards -->
        <div class="text-center mb-4">
            <h2>Admin Dashboard</h2>
        </div>
        <div class="row mb-4 text-center">
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="register_user.php">
                        <img src="img/register_users.png" class="card-img-top" alt="Register User"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="register_user.php"
                                class="text-decoration-none text-dark">Register User</a></h5>
                        <p class="card-text">Create new admin and cleaning staff accounts.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="register_client.php">
                        <img src="img/register_client.png" class="card-img-top" alt="Register Client"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="register_client.php"
                                class="text-decoration-none text-dark">Register Client</a></h5>
                        <p class="card-text">Manually register new client details.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="register_item.php">
                        <img src="img/register_item.png" class="card-img-top" alt="Register Item"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="register_item.php"
                                class="text-decoration-none text-dark">Register Item</a></h5>
                        <p class="card-text">Add new cleaning items and images.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="update_items.php">
                        <img src="img/update_items.png" class="card-img-top" alt="Update Items"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="update_items.php" class="text-decoration-none text-dark">Update
                                Items</a></h5>
                        <p class="card-text">Manage inventory levels and update quantities.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="job_form_admin.php">
                        <img src="img/job_form_admin.png" class="card-img-top" alt="Update Items"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="job_form_admin.php" class="text-decoration-none text-dark">Add a
                                Job</a></h5>
                        <p class="card-text">Create and assign a Job.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 d-inline-block">
                <div class="card h-100">
                    <a href="users_list.php">
                        <img src="img/users_list.png" class="card-img-top" alt="Update Items"
                            style="height: 150px; object-fit: contain;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title"><a href="users_list.php" class="text-decoration-none text-dark">List of
                                Users</a></h5>
                        <p class="card-text">View and manage all registered users.</p>
                    </div>
                </div>
            </div>

        </div>
        <!-- End of Admin Panels Cards -->

        <!-- Job Table Section -->

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