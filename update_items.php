<?php
require 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    elseif (isset($_POST['update_item'])) {
        $itemID = intval($_POST['item_id']);
        $newQuantity = intval($_POST['new_quantity']);
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

// fetch all items from the Items table
$query = "SELECT * FROM Items ORDER BY DateCreated DESC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Items - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
    <style>
        #itemSearchInput {
            margin-bottom: 15px;
        }

        .item-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .low-stock {
            background-color: rgb(230, 115, 125);
        }
    </style>
    <script>
        function searchItems() {
            var input = document.getElementById("itemSearchInput").value.toLowerCase();
            var table = document.getElementById("itemsTable");
            var tr = table.getElementsByTagName("tr");
            for (var i = 1; i < tr.length; i++) {
                var tdName = tr[i].getElementsByClassName("item-name")[0];
                var tdDesc = tr[i].getElementsByClassName("item-desc")[0];
                if (tdName && tdDesc) {
                    var txtValueName = tdName.textContent || tdName.innerText;
                    var txtValueDesc = tdDesc.textContent || tdDesc.innerText;
                    tr[i].style.display = (txtValueName.toLowerCase().indexOf(input) > -1 || txtValueDesc.toLowerCase().indexOf(input) > -1) ? "" : "none";
                }
            }
        }
    </script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5 panel">
        <div class="panel-heading text-center">
            <h2 class="title">Update Items Inventory</h2>
        </div>
        <div class="my-3">
            <img src="img/update_items.png" class="card-img-top mx-auto d-block" alt="Register User" style="height: 150px; object-fit: contain;">
        </div>
        <div class="panel-body">
            <?php if ($msg): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <!-- Search Bar -->
            <input type="text" id="itemSearchInput" onkeyup="searchItems()" placeholder="Search by Item Name or Description..." class="form-control search-input">
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
                        <?php $rowClass = ($item['Quantity'] < 10) ? 'low-stock' : ''; ?>
                        <tr class="<?php echo $rowClass; ?>">
                            <td><?php echo htmlspecialchars($item['ItemID']); ?></td>
                            <td>
                                <?php if ($item['ImagePath']): ?>
                                    <img src="<?php echo htmlspecialchars($item['ImagePath']); ?>" alt="<?php echo htmlspecialchars($item['ItemName']); ?>" class="item-img">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td class="item-name"><?php echo htmlspecialchars($item['ItemName']); ?></td>
                            <td class="item-desc"><?php echo htmlspecialchars($item['Description']); ?></td>
                            <td><?php echo htmlspecialchars($item['Quantity']); ?>
                                <?php if ($item['Quantity'] < 10): ?>
                                    <span class="badge bg-danger ms-2">Low stock!</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" action="update_items.php" class="d-flex">
                                    <input type="hidden" name="item_id" value="<?php echo $item['ItemID']; ?>">
                                    <input type="number" name="new_quantity" class="form-control me-2" value="<?php echo $item['Quantity']; ?>" min="0" required>
                                    <button type="submit" name="update_item" class="btn btn-primary"><i class="fa fa-sync"></i></button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="update_items.php" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    <input type="hidden" name="item_id" value="<?php echo $item['ItemID']; ?>">
                                    <button type="submit" name="delete_item" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <div class="panel-footer text-center">
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>