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
    // Collect and sanitize input fields
    $username = $mysqli->real_escape_string(trim($_POST['username']));
    $email = $mysqli->real_escape_string(trim($_POST['email']));
    $address = $mysqli->real_escape_string(trim($_POST['address']));
    $jobDescription = $mysqli->real_escape_string(trim($_POST['job_description']));
    $phone = $mysqli->real_escape_string(trim($_POST['phone']));
    $about = $mysqli->real_escape_string(trim($_POST['about']));

    // Update user details (IsAdmin is not updated)
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile - Admin - Cleaning App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>User Profile: <?php echo htmlspecialchars($user['Username']); ?></h2>
        <?php if (isset($msg)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="user_profile.php?userid=<?php echo $userID; ?>">
            <div class="mb-3">
                <label class="form-label">User ID</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['UserID']); ?>"
                    disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                    value="<?php echo htmlspecialchars($user['Username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                    value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Admin</label>
                <input type="checkbox" class="form-check-input" <?php echo $user['IsAdmin'] ? 'checked' : ''; ?>
                    disabled>
                <small class="form-text text-muted">Admin status cannot be changed here.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control"
                    value="<?php echo htmlspecialchars($user['Address']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Job Description</label>
                <input type="text" name="job_description" class="form-control"
                    value="<?php echo htmlspecialchars($user['JobDescription']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control"
                    value="<?php echo htmlspecialchars($user['Phone']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">About</label>
                <textarea name="about" class="form-control"><?php echo htmlspecialchars($user['About']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="users_list.php" class="btn btn-secondary">Back to User List</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>