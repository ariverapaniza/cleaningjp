<?php
// edit_job.php
require 'config.php';

// Only admin users should access this page
if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

// Get job details
if (!isset($_GET['jobid'])) {
    die("Job ID not provided.");
}

$jobID = intval($_GET['jobid']);

// Fetch job data
$stmt = $mysqli->prepare("SELECT * FROM Jobs WHERE JobID = ?");
$stmt->bind_param("i", $jobID);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

// Fetch clients for dropdown
$clientsResult = $mysqli->query("SELECT ClientID, ClientName FROM Clients ORDER BY ClientName ASC");

// Fetch all available items
$itemsResult = $mysqli->query("SELECT * FROM Items");

// Fetch currently selected items for the job
$jobItems = [];
$jobItemsQuery = $mysqli->prepare("SELECT ItemID, QuantityUsed FROM JobItems WHERE JobID = ?");
$jobItemsQuery->bind_param("i", $jobID);
$jobItemsQuery->execute();
$jobItemsResult = $jobItemsQuery->get_result();
while ($row = $jobItemsResult->fetch_assoc()) {
    $jobItems[$row['ItemID']] = $row['QuantityUsed']; // Store items with their quantities
}
$jobItemsQuery->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect updated job data
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

    // Update the job record
    $stmt = $mysqli->prepare("UPDATE Jobs SET ClientID = ?, ClientName = ?, JobDescription = ?, ServiceDate = ?, Location = ?, EstimatedDuration = ? WHERE JobID = ?");
    $stmt->bind_param("isssssi", $clientID, $clientName, $jobDescription, $serviceDate, $location, $estimatedDuration, $jobID);
    $stmt->execute();
    $stmt->close();

    // Clear previous JobItems entries
    $stmt = $mysqli->prepare("DELETE FROM JobItems WHERE JobID = ?");
    $stmt->bind_param("i", $jobID);
    $stmt->execute();
    $stmt->close();

    // Loop through posted data for each selected item and insert new JobItems entries
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'item_') === 0 && $value === "on") {
            $itemID = str_replace('item_', '', $key);
            $qtyField = 'qty_' . $itemID;
            $quantityUsed = isset($_POST[$qtyField]) ? intval($_POST[$qtyField]) : 0;

            if ($quantityUsed > 0) {
                // Insert new item selection
                $stmt = $mysqli->prepare("INSERT INTO JobItems (JobID, ItemID, QuantityUsed) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $jobID, $itemID, $quantityUsed);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    header("Location: dashboard.php?edit_success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Job - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
    <script>
        // Function to filter items based on search input (by item name and description)
        function searchItems() {
            var input = document.getElementById("itemSearchInput").value.toLowerCase();
            var table = document.getElementById("itemsTable");
            var tr = table.getElementsByTagName("tr");

            // Loop through all table rows (skip the header row)
            for (var i = 1; i < tr.length; i++) {
                var tdName = tr[i].getElementsByTagName("td")[2]; // Item Name column
                var tdDesc = tr[i].getElementsByTagName("td")[3]; // Description column
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
    <!-- Wrap edit job content inside a panel -->
    <div class="container mt-5 panel">
        <div class="text-center panel-heading">
            <h2 class="title">Edit Job</h2>
        </div>
        <div class="p-4 panel-body">
            <form method="post" action="edit_job.php?jobid=<?php echo $jobID; ?>">
                <div class="mb-3">
                    <label class="form-label">Client Name</label>
                    <select name="client_id" class="form-select" required>
                        <?php while ($client = $clientsResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($client['ClientID']); ?>"
                                <?php echo ($client['ClientID'] == $job['ClientID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client['ClientName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Job Description</label>
                    <textarea name="job_description" class="form-control" required><?php echo htmlspecialchars($job['JobDescription']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Service Date</label>
                    <input type="date" name="service_date" class="form-control" value="<?php echo htmlspecialchars($job['ServiceDate']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($job['Location']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estimated Duration</label>
                    <input type="text" name="estimated_duration" class="form-control" value="<?php echo htmlspecialchars($job['EstimatedDuration']); ?>" required>
                </div>
                <!-- Items Section -->
                <h4>Select Items</h4>
                <input type="text" id="itemSearchInput" onkeyup="searchItems()" placeholder="Search items..." class="form-control mb-3 search-input">
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
                                    <input type="checkbox" name="item_<?php echo $item['ItemID']; ?>" value="on"
                                        <?php echo isset($jobItems[$item['ItemID']]) ? 'checked' : ''; ?>>
                                </td>
                                <td>
                                    <?php if ($item['ImagePath']): ?>
                                        <img src="<?php echo htmlspecialchars($item['ImagePath']); ?>" width="50" height="50" alt="<?php echo htmlspecialchars($item['ItemName']); ?>">
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                                <td><?php echo htmlspecialchars($item['Description']); ?></td>
                                <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                                <td>
                                    <input type="number" name="qty_<?php echo $item['ItemID']; ?>" min="0" class="form-control" placeholder="0"
                                        value="<?php echo isset($jobItems[$item['ItemID']]) ? $jobItems[$item['ItemID']] : ''; ?>">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success me-2">Update Job</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>