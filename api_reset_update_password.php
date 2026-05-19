<?php
session_start();
require_once 'db_config.php';

$data = json_decode(file_get_contents("php://input"), true);
$new_password = $data["new_password"];

// Security Check
if (!isset($_SESSION["reset_email"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized request."]);
    exit;
}

$email = $_SESSION["reset_email"];
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update Password
$sql = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    // Clear session data
    unset($_SESSION["reset_email"]);
    unset($_SESSION["reset_otp"]);
    
    echo json_encode(["success" => true, "message" => "Password changed successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Database error."]);
}
?>