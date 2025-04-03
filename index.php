<?php
// index.php
require 'config.php';

// If user is already logged in, redirect accordingly.
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute statement
    $stmt = $mysqli->prepare("SELECT UserID, Username, PasswordHash, IsAdmin FROM Users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($UserID, $Username, $PasswordHash, $IsAdmin);

    if ($stmt->num_rows > 0 && $stmt->fetch()) {
        // Verify password
        if (password_verify($password, $PasswordHash)) {
            // Set session variables
            $_SESSION['UserID'] = $UserID;
            $_SESSION['Username'] = $Username;
            $_SESSION['IsAdmin'] = $IsAdmin;
            session_regenerate_id();

            // Redirect based on role
            if ($IsAdmin) {
                header("Location: dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>

    <!-- Logo Section (100px margin below the logo) -->
    <div class="container text-center mt-5" style="margin-bottom: 100px;">
        <img src="img/CleaningLogoBlack.png" alt="Cleaning Logo" class="img-fluid" style="max-width: 500px;">
    </div>

    <!-- Center the login card horizontally -->
    <div class="container d-flex justify-content-center">
        <!-- Login Card -->
        <div class="login-card p-4">
            <!-- Optional User Icon -->
            <div class="login-icon text-center mb-3">
                <img src="img/userlogin.png" alt="User Icon" style="width:80px;">
            </div>
            <!-- Title -->
            <h2 class="text-center mb-4" style="color: #9b59b6;">LOGIN</h2>

            <!-- Display error message if login fails -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post" action="index.php">
                <div class="mb-3">
                    <label class="form-label" style="color: #9b59b6;">Username</label>
                    <input type="text" name="username" class="form-control login-input"
                        placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="color: #9b59b6;">Password</label>
                    <input type="password" name="password" class="form-control login-input"
                        placeholder="Password" required>
                </div>
                <div class="text-center">
                    <button class="btn login-btn" type="submit">LOGIN</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>