<?php
// user_profile.php
require 'config.php';

// Only admin users should access this page
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

// Get user ID from GET parameter
if (!isset($_GET['userid'])) {
    die("User ID not specified.");
}

$userID = intval($_GET['userid']);

// Process form submission to update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $mysqli->real_escape_string(trim($_POST['username']));
    $email = $mysqli->real_escape_string(trim($_POST['email']));
    $address = $mysqli->real_escape_string(trim($_POST['address']));
    $jobDescription = $mysqli->real_escape_string(trim($_POST['job_description']));
    $phone = $mysqli->real_escape_string(trim($_POST['phone']));
    $about = $mysqli->real_escape_string(trim($_POST['about']));

    $stmt = $mysqli->prepare("UPDATE Users SET Username = ?, Email = ?, Address = ?, JobDescription = ?, Phone = ?, About = ? WHERE UserID = ?");
    $stmt->bind_param("ssssssi", $username, $email, $address, $jobDescription, $phone, $about, $userID);
    if ($stmt->execute()) {
        $msg = "User profile updated successfully.";
    } else {
        $error = "Error updating profile: " . $mysqli->error;
    }
    $stmt->close();
}

// Retrieve user details
$stmt = $mysqli->prepare("SELECT UserID, Username, Email, IsAdmin, Address, JobDescription, Phone, About FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("User not found.");
}
$user = $result->fetch_assoc();
$stmt->close();

// Pagination settings for assigned jobs
$jobsPerPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $jobsPerPage;

// Fetch total number of assigned jobs
$totalJobsQuery = "SELECT COUNT(*) AS total FROM Jobs WHERE UserID = ?";
$stmt = $mysqli->prepare($totalJobsQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$totalJobsRow = $result->fetch_assoc();
$totalJobs = $totalJobsRow['total'];
$totalPages = ceil($totalJobs / $jobsPerPage);
$stmt->close();

// Fetch assigned jobs with pagination
$query = "SELECT JobID, ClientName, JobDescription, ServiceDate FROM Jobs WHERE UserID = ? ORDER BY DateCreated DESC LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iii", $userID, $jobsPerPage, $offset);
$stmt->execute();
$assignedJobsResult = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile - Admin - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <!-- Panel container -->
    <div class="container mt-5 panel">
        <div class="panel-heading text-center">
            <h2 class="title">User Profile: <?php echo htmlspecialchars($user['Username']); ?></h2>
        </div>
        <div class="panel-body">
            <?php if (isset($msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" action="user_profile.php?userid=<?php echo $userID; ?>">
                <div class="mb-3">
                    <label class="form-label">User ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['UserID']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Admin</label>
                    <input type="checkbox" class="form-check-input" <?php echo $user['IsAdmin'] ? 'checked' : ''; ?> disabled>
                    <small class="form-text text-muted">Admin status cannot be changed here.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['Address']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Job Description</label>
                    <input type="text" name="job_description" class="form-control" value="<?php echo htmlspecialchars($user['JobDescription']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['Phone']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">About</label>
                    <textarea name="about" class="form-control"><?php echo htmlspecialchars($user['About']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="users_list.php" class="btn btn-secondary">Back to User List</a>
            </form>
            <!-- Assigned Jobs Section -->
            <h3 class="mt-5">Assigned Jobs</h3>
            <table class="table table-bordered">
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
                    <?php while ($job = $assignedJobsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['JobID']); ?></td>
                            <td><?php echo htmlspecialchars($job['ClientName']); ?></td>
                            <td><?php echo htmlspecialchars($job['JobDescription']); ?></td>
                            <td><?php echo htmlspecialchars($job['ServiceDate']); ?></td>
                            <td>
                                <a href="job_details.php?jobid=<?php echo $job['JobID']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-folder-open"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Pagination for Assigned Jobs -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="user_profile.php?userid=<?php echo $userID; ?>&page=<?php echo ($page - 1); ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="user_profile.php?userid=<?php echo $userID; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="user_profile.php?userid=<?php echo $userID; ?>&page=<?php echo ($page + 1); ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>