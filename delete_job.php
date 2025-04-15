<?php
require 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['jobid'])) {
    $jobID = intval($_GET['jobid']);

    $stmt = $mysqli->prepare("DELETE FROM Jobs WHERE JobID = ?");
    $stmt->bind_param("i", $jobID);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: dashboard.php?delete_success=1");
        exit;
    } else {
        $stmt->close();
        header("Location: dashboard.php?delete_error=1");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
