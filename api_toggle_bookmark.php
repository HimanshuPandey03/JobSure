<?php
// 1. Start output buffering immediately
ob_start();

session_start();
require 'db_config.php';

// 2. CLEAN THE BUFFER: This deletes any accidental spaces/newlines 
// created by db_config.php or whitespace before this tag.
ob_clean(); 

header('Content-Type: application/json');

$response = ['success' => false, 'status' => 'error', 'message' => ''];

// Authentication Check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["id"])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to bookmark items.']);
    exit;
}

$user_id = $_SESSION["id"];
$job_id = $_POST['job_id'] ?? null;

if (!$job_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid job ID.']);
    exit;
}

try {
    // Check existence
    $sql_check = "SELECT id FROM bookmarks WHERE user_id = ? AND job_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $job_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $stmt_check->close();

    if ($result->num_rows > 0) {
        // Remove
        $sql_delete = "DELETE FROM bookmarks WHERE user_id = ? AND job_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $user_id, $job_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        $response['success'] = true;
        $response['status'] = 'removed';
        $response['message'] = 'Bookmark removed.';
    } else {
        // Add
        $sql_insert = "INSERT INTO bookmarks (user_id, job_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $user_id, $job_id);
        $stmt_insert->execute();
        $stmt_insert->close();
        
        $response['success'] = true;
        $response['status'] = 'added';
        $response['message'] = 'Bookmark added.';
    }

} catch (Exception $e) {
    $response['message'] = "Database error: " . $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>