<?php
require 'config.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['jobid'])) {
    die("Job ID not provided.");
}

$jobID = intval($_GET['jobid']);

// fetch job details
$stmt = $mysqli->prepare("SELECT j.JobID, j.ClientName, j.JobDescription, j.ServiceDate, j.Location, j.EstimatedDuration, u.Username 
                          FROM Jobs j 
                          INNER JOIN Users u ON j.UserID = u.UserID 
                          WHERE j.JobID = ?");
$stmt->bind_param("i", $jobID);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

if (!$job) {
    die("Job not found.");
}

// fetch associated job items
$stmt2 = $mysqli->prepare("SELECT ji.QuantityUsed, i.ItemName, i.Description, i.ImagePath 
                           FROM JobItems ji 
                           INNER JOIN Items i ON ji.ItemID = i.ItemID 
                           WHERE ji.JobID = ?");
$stmt2->bind_param("i", $jobID);
$stmt2->execute();
$jobItemsResult = $stmt2->get_result();
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Job Details - Cleaning App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="styles.css" rel="stylesheet">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5 panel">
        <div class="text-center panel-heading">
            <h2 class="title">Job Details</h2>
        </div>
        <div class="p-4 panel-body">
            <div class="text-center mb-4">
                <img src="img/CleaningLogoBlack.png" alt="Cleaning Logo" class="img-fluid" style="max-width: 500px;">
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Job Information</strong>
                </div>
                <div class="card-body">
                    <p><strong>Job ID:</strong> <?php echo htmlspecialchars($job['JobID']); ?></p>
                    <p><strong>Client Name:</strong> <?php echo htmlspecialchars($job['ClientName']); ?></p>
                    <p><strong>Job Description:</strong> <?php echo htmlspecialchars($job['JobDescription']); ?></p>
                    <p><strong>Service Date:</strong> <?php echo htmlspecialchars($job['ServiceDate']); ?></p>
                    <p><strong>Created By:</strong> <?php echo htmlspecialchars($job['Username']); ?></p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Additional Details</strong>
                </div>
                <div class="card-body">
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['Location']); ?></p>
                    <p><strong>Estimated Duration:</strong> <?php echo htmlspecialchars($job['EstimatedDuration']); ?></p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Selected Items</strong>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Image</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>Quantity Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $jobItemsResult->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['ImagePath']): ?>
                                            <img src="<?php echo htmlspecialchars($item['ImagePath']); ?>" alt="<?php echo htmlspecialchars($item['ItemName']); ?>" width="50" height="50">
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Description']); ?></td>
                                    <td><?php echo htmlspecialchars($item['QuantityUsed']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
                <button type="button" class="btn btn-success" onclick="window.print();">Print Form</button>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>