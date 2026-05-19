<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$user_otp = trim($data["otp"]);

// Check if session exists
if (!isset($_SESSION["reset_otp"])) {
    echo json_encode(["success" => false, "message" => "Session expired. Please try again."]);
    exit;
}

// Verify OTP
if ($user_otp == $_SESSION["reset_otp"]) {
    echo json_encode(["success" => true, "message" => "OTP Verified."]);
} else {
    echo json_encode(["success" => false, "message" => "Incorrect OTP."]);
}
?>