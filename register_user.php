<?php
// register_user.php
require 'config.php';

// Only admins allowed
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
    $address = trim($_POST['address']);
    $jobDescription = trim($_POST['job_description']);
    $phone = trim($_POST['phone']);
    $about = trim($_POST['about']);

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO Users (Username, Email, PasswordHash, IsAdmin, Address, JobDescription, Phone, About) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisiss", $username, $email, $passwordHash, $isAdmin, $address, $jobDescription, $phone, $about);

    if ($stmt->execute()) {
        $msg = "User created successfully.";
    } else {
        $error = "Error creating user: " . $mysqli->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register New User - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <!-- Panel container -->
    <div class="container mt-5 panel form-panel">
        <div class="text-center panel-heading">
            <h2 class="title">Register New User</h2>
        </div>
        <div class="my-3">
            <img src="img/register_users.png" class="card-img-top mx-auto d-block" alt="Register User" style="height: 150px; object-fit: contain;">
        </div>
        <div class="panel-body">
            <?php if (isset($msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" action="register_user.php">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_admin" class="form-check-input">
                    <label class="form-check-label">Is Admin?</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Job Description</label>
                    <input type="text" name="job_description" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">About</label>
                    <textarea name="about" class="form-control"></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Create User</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>