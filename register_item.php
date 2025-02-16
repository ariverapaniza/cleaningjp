<?php
// register_item.php
require 'config.php';

// Only admins allowed
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemName = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        // Ensure the uploads folder exists and is writable
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2>Register New Item</h2>
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
            <button class="btn btn-primary" type="submit">Add Item</button>
        </form>
    </div>
</body>

</html>