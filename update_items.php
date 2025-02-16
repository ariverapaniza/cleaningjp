<?php
// update_items.php
require 'config.php';

// Only admin users should access this page
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

$msg = '';
$error = '';

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $itemID = intval($_POST['item_id']);
    $newQuantity = intval($_POST['new_quantity']);

    // Update the Items table with the new quantity
    $stmt = $mysqli->prepare("UPDATE Items SET Quantity = ? WHERE ItemID = ?");
    $stmt->bind_param("ii", $newQuantity, $itemID);
    if ($stmt->execute()) {
        $msg = "Item updated successfully!";
    } else {
        $error = "Error updating item: " . $mysqli->error;
    }
    $stmt->close();
}

// Fetch all items from the Items table
$query = "SELECT * FROM Items ORDER BY DateCreated DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Items - Cleaning App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2 class="mb-4">Update Items Inventory</h2>
        <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Current Quantity</th>
                    <th>Update Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['ItemID']); ?></td>
                    <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                    <td><?php echo htmlspecialchars($item['Description']); ?></td>
                    <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                    <td>
                        <!-- Form to update the quantity for this item -->
                        <form method="post" action="update_items.php" class="d-flex">
                            <input type="hidden" name="item_id" value="<?php echo $item['ItemID']; ?>">
                            <input type="number" name="new_quantity" class="form-control me-2"
                                value="<?php echo $item['Quantity']; ?>" min="0" required>
                            <button type="submit" name="update_item" class="btn btn-primary">Update</button>
                        </form>
                    </td>
                    <td>
                        <!-- Optional: You could add additional actions here (e.g., edit details or delete) -->
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>