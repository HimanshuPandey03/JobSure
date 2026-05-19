<?php
session_start();
header('Content-Type: application/json');
require 'db_config.php';

$response = ['success' => false, 'message' => 'An error occurred.'];

// 1. Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["id"])) {
    $response['message'] = 'You must be logged in to apply.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION["id"];
$job_id = $_POST['job_id'] ?? null;
$availability = $_POST['availability'] ?? 'Not specified'; // Get new availability data

if (!$job_id) {
    $response['message'] = 'Invalid Job ID.';
    echo json_encode($response);
    exit;
}

try {
    // 2. Try to insert the new application
    $sql_insert = "INSERT INTO applications (user_id, job_id, status, availability) VALUES (?, ?, 'Applied', ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iis", $user_id, $job_id, $availability);
    
    if ($stmt_insert->execute()) {
        $response['success'] = true;
        $response['message'] = 'Application submitted!';
    } else {
        $response['message'] = 'You have already applied for this job.';
    }
    $stmt_insert->close();

} catch (mysqli_sql_exception $e) {
    // Catch the "Duplicate entry" error
    if ($e->getCode() == 1062) { // 1062 is the MySQL code for duplicate unique key
        $response['message'] = 'You have already applied for this job.';
    } else {
        $response['message'] = $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>