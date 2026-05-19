<?php
header('Content-Type: application/json');
require 'db_config.php';

try {
    $sql = "SELECT * FROM jobs ORDER BY id DESC";
    $result = $conn->query($sql);
    $jobs = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($jobs);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
$conn->close();
?>