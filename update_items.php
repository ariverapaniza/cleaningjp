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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete functionality
    if (isset($_POST['delete_item'])) {
        $itemID = intval($_POST['item_id']);
        $stmt = $mysqli->prepare("DELETE FROM Items WHERE ItemID = ?");
        $stmt->bind_param("i", $itemID);
        if ($stmt->execute()) {
            $msg = "Item deleted successfully!";
        } else {
            $error = "Error deleting item: " . $mysqli->error;
        }
        $stmt->close();
    }
    // Update functionality
    elseif (isset($_POST['update_item'])) {
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
    <style>
    /* Optional: style for the search input */
    #itemSearchInput {
        margin-bottom: 15px;
    }

    .item-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
    </style>
    <script>
    // Function to filter items based on search input (by item name and description)
    function searchItems() {
        var input = document.getElementById("itemSearchInput").value.toLowerCase();
        var table = document.getElementById("itemsTable");
        var tr = table.getElementsByTagName("tr");

        // Loop through all table rows (skip the header row)
        for (var i = 1; i < tr.length; i++) {
            var tdName = tr[i].getElementsByClassName("item-name")[0];
            var tdDesc = tr[i].getElementsByClassName("item-desc")[0];
            if (tdName && tdDesc) {
                var txtValueName = tdName.textContent || tdName.innerText;
                var txtValueDesc = tdDesc.textContent || tdDesc.innerText;
                if (txtValueName.toLowerCase().indexOf(input) > -1 || txtValueDesc.toLowerCase().indexOf(input) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    </script>
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

        <!-- Search Bar -->
        <input type="text" id="itemSearchInput" onkeyup="searchItems()"
            placeholder="Search by Item Name or Description..." class="form-control">

        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Current Quantity</th>
                    <th>Update Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['ItemID']); ?></td>
                    <td>
                        <?php if ($item['ImagePath']): ?>
                        <img src="<?php echo htmlspecialchars($item['ImagePath']); ?>"
                            alt="<?php echo htmlspecialchars($item['ItemName']); ?>" class="item-img">
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                    </td>
                    <td class="item-name"><?php echo htmlspecialchars($item['ItemName']); ?></td>
                    <td class="item-desc"><?php echo htmlspecialchars($item['Description']); ?></td>
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
                        <!-- Form to delete the item -->
                        <form method="post" action="update_items.php"
                            onsubmit="return confirm('Are you sure you want to delete this item?');">
                            <input type="hidden" name="item_id" value="<?php echo $item['ItemID']; ?>">
                            <button type="submit" name="delete_item" class="btn btn-danger">Delete</button>
                        </form>
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