<?php
// index.php
require 'config.php';

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
                header("Location: job_form.php");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2 class="mb-4">Login</h2>
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="index.php">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>