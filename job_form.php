<?php
// job_form.php
require 'config.php';

// Only logged in cleaning staff (non-admin) should access this page
if (!isLoggedIn() || isAdmin()) {
    header("Location: index.php");
    exit;
}

// Fetch available items from the Items table
$itemsResult = $mysqli->query("SELECT * FROM Items");
// Fetch clients for the dropdown
$clientsResult = $mysqli->query("SELECT ClientID, ClientName FROM Clients ORDER BY ClientName ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize job data
    $clientID = intval($_POST['client_id']);
    $jobDescription = $mysqli->real_escape_string(trim($_POST['job_description']));
    $serviceDate = $mysqli->real_escape_string(trim($_POST['service_date']));
    $location = $mysqli->real_escape_string(trim($_POST['location']));
    $estimatedDuration = $mysqli->real_escape_string(trim($_POST['estimated_duration']));

    // Fetch the actual client name based on ClientID
    $stmt = $mysqli->prepare("SELECT ClientName FROM Clients WHERE ClientID = ?");
    $stmt->bind_param("i", $clientID);
    $stmt->execute();
    $stmt->bind_result($clientName);
    $stmt->fetch();
    $stmt->close();

    // Insert into Jobs table with ClientID and ClientName
    $stmt = $mysqli->prepare("INSERT INTO Jobs (UserID, ClientID, ClientName, JobDescription, ServiceDate, Location, EstimatedDuration) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $_SESSION['UserID'], $clientID, $clientName, $jobDescription, $serviceDate, $location, $estimatedDuration);
    if ($stmt->execute()) {
        $jobID = $stmt->insert_id;
        $stmt->close();

        // Loop through posted data for each item checkbox (names starting with "item_")
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'item_') === 0 && $value === "on") {
                $itemID = str_replace('item_', '', $key);
                $qtyField = 'qty_' . $itemID;
                $quantityUsed = isset($_POST[$qtyField]) ? intval($_POST[$qtyField]) : 0;
                if ($quantityUsed > 0) {
                    // Insert into JobItems table
                    $stmt2 = $mysqli->prepare("INSERT INTO JobItems (JobID, ItemID, QuantityUsed) VALUES (?, ?, ?)");
                    $stmt2->bind_param("iii", $jobID, $itemID, $quantityUsed);
                    $stmt2->execute();
                    $stmt2->close();
                    // Deduct quantity from Items table
                    $stmt3 = $mysqli->prepare("UPDATE Items SET Quantity = Quantity - ? WHERE ItemID = ?");
                    $stmt3->bind_param("ii", $quantityUsed, $itemID);
                    $stmt3->execute();
                    $stmt3->close();
                }
            }
        }
        header("Location: job_form.php?success=1");
        exit;
    } else {
        $error = "Error creating job form.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Job Form - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
    <style>
        #itemSearchInput {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <!-- Panel container -->
    <div class="container mt-5 panel">
        <div class="text-center panel-heading">
            <h2 class="title">Create New Job Form</h2>
        </div>
        <div class="my-3">
            <img src="img/job_form_admin.png" class="card-img-top mx-auto d-block" alt="Register User" style="height: 150px; object-fit: contain;">
        </div>
        <div class="panel-body">
            <form method="post" action="job_form.php">
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <!-- Title is in panel heading -->
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary" type="submit">Submit Job Form</button>
                    </div>
                </div>
                <!-- Job Details Fields -->
                <div class="mb-3">
                    <label class="form-label">Client Name</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Select a client...</option>
                        <?php while ($client = $clientsResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($client['ClientID']); ?>">
                                <?php echo htmlspecialchars($client['ClientName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Job Description</label>
                    <textarea name="job_description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Service Date</label>
                    <input type="date" name="service_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estimated Duration</label>
                    <input type="text" name="estimated_duration" class="form-control" required placeholder="e.g., 3 hours">
                </div>
                <!-- Display Success or Error Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Job form submitted successfully!</div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <!-- Items Section -->
                <h4>Select Items</h4>
                <input type="text" id="itemSearchInput" onkeyup="searchItems()"
                    placeholder="Search items by name or description..." class="form-control search-input">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Image</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Quantity Left</th>
                            <th>Quantity Used</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $itemsResult->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="item_<?php echo $item['ItemID']; ?>" value="on">
                                </td>
                                <td>
                                    <?php if ($item['ImagePath']): ?>
                                        <img src="<?php echo htmlspecialchars($item['ImagePath']); ?>" width="50" height="50"
                                            alt="<?php echo htmlspecialchars($item['ItemName']); ?>">
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td class="item-name"><?php echo htmlspecialchars($item['ItemName']); ?></td>
                                <td class="item-desc"><?php echo htmlspecialchars($item['Description']); ?></td>
                                <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                                <td>
                                    <input type="number" name="qty_<?php echo $item['ItemID']; ?>" min="0" class="form-control" placeholder="0">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>

</html>