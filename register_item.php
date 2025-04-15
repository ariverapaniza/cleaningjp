<?php
require 'config.php';

// only admins allowed
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemName = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);

    // handle image upload hopefully
    $imagePath = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $fileName = basename($_FILES['item_image']['name']);
        $targetFile = $uploadDir . time() . "_" . $fileName;
        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $mysqli->prepare("INSERT INTO Items (ItemName, Description, Quantity, ImagePath) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $itemName, $description, $quantity, $imagePath);

    if ($stmt->execute()) {
        $msg = "Item added successfully.";
    } else {
        $error = "Error adding item: " . $mysqli->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Item - Cleaning App</title>
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
            <h2 class="title">Register New Item</h2>
        </div>
        <div class="my-3">
            <img src="img/register_item.png" class="card-img-top mx-auto d-block" alt="Register User" style="height: 150px; object-fit: contain;">
        </div>
        <div class="panel-body">
            <?php if (isset($msg)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post" action="register_item.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item Image (50x50 recommended)</label>
                    <input type="file" name="item_image" class="form-control">
                </div>
                <button class="btn btn-success" type="submit">Add Item</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>