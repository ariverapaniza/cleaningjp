<?php
require 'config.php';

// only admins allowed
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clientName = trim($_POST['client_name']);
    $address = trim($_POST['address']);
    $contactDetails = trim($_POST['contact_details']);

    $stmt = $mysqli->prepare("INSERT INTO Clients (ClientName, Address, ContactDetails) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $clientName, $address, $contactDetails);

    if ($stmt->execute()) {
        $msg = "Client registered successfully.";
    } else {
        $error = "Error registering client: " . $mysqli->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Client - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5 panel form-panel">
        <div class="text-center panel-heading">
            <h2 class="title">Register Client</h2>
        </div>
        <div class="my-3">
            <img src="img/register_client.png" class="card-img-top mx-auto d-block" alt="Register User" style="height: 150px; object-fit: contain;">
        </div>
        <div class="panel-body">
            <?php if (isset($msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" action="register_client.php">
                <div class="mb-3">
                    <label class="form-label">Client Name</label>
                    <input type="text" name="client_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Details</label>
                    <input type="text" name="contact_details" class="form-control">
                </div>
                <button class="btn btn-primary" type="submit">Register Client</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>