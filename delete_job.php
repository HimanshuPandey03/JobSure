<?php
header('Content-Type: application/json');
require 'db_config.php'; // <-- USES YOUR FILE

$response = ['success' => false];

// Get the ID from the POST request
$id = $_POST['id'] ?? null;

if ($id) {
    try {
        // --- CHANGED to MySQLi Prepared Statement ---
        $sql = "DELETE FROM jobs WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id); // "i" = integer
        $stmt->execute();
        
        // Check if a row was actually deleted
        if ($stmt->affected_rows > 0) { // <-- CHANGED
            $response['success'] = true;
        } else {
            $response['error'] = 'No job found with that ID.';
        }
        $stmt->close();
        // --- END CHANGE ---

    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
} else {
    $response['error'] = 'No ID provided.';
}

$conn->close();
echo json_encode($response);
?>