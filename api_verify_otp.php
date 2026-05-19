<?php
session_start();
require_once "db_config.php";

$data = json_decode(file_get_contents("php://input"), true);
$user_otp = trim($data["otp"]);

// ▼▼▼ THIS IS THE ONLY LINE ADDED ▼▼▼
// This will write the OTPs to your Apache error log for debugging.
error_log("Verifying OTP - User entered: " . $user_otp . " | Session OTP: " . ($_SESSION["reg_otp"] ?? "NOT SET"));

// Check OTP
if (!isset($_SESSION["reg_otp"]) || $user_otp != $_SESSION["reg_otp"]) {
    echo json_encode(["success" => false, "message" => "Incorrect OTP."]);
    exit;
}

// Create user account
$sql = "INSERT INTO users (first_name, last_name, email, password, email_verified)
        VALUES (?, ?, ?, ?, 1)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssss",
    $_SESSION["reg_first_name"],
    $_SESSION["reg_last_name"],
    $_SESSION["reg_email"],
    $_SESSION["reg_password"]
);
$stmt->execute();

$user_id = $conn->insert_id;

// Clear OTP session
unset($_SESSION["reg_otp"]);

// Auto Login
$_SESSION["loggedin"] = true;
$_SESSION["id"] = $user_id;
$_SESSION["username"] = $_SESSION["reg_first_name"];
$_SESSION["email"] = $_SESSION["reg_email"];

// SUCCESS RESPONSE
echo json_encode([
    "success" => true,
    "redirect" => "profile.php"
]);
?>