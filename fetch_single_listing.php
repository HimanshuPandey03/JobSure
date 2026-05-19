<?php
header('Content-Type: application/json');
require 'db_config.php'; // Use your connection file

$response = ['success' => false];
$id = $_GET['id'] ?? null; // Get ID from the URL

if ($id) {
    try {
        $sql = "SELECT * FROM jobs WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id); // "i" = integer
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['data'] = $result->fetch_assoc();
            $response['success'] = true;
        } else {
            $response['error'] = 'No listing found with that ID.';
        }
        $stmt->close();
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
} else {
    $response['error'] = 'No ID provided.';
}

$conn->close();
echo json_encode($response);
?>