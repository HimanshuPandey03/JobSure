<?php
session_start();
require_once 'db_config.php';

// GET JSON INPUT
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data["email"]);

// 1. Check if email exists in database
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Email not found in our records."]);
    exit;
}
$stmt->close();

// 2. Generate OTP
$otp = rand(100000, 999999);

// 3. Store OTP and Email in Session
$_SESSION["reset_email"] = $email;
$_SESSION["reset_otp"]   = $otp;

// 4. Send OTP via PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "mithilesh9324958141@gmail.com"; // Your email
    $mail->Password = "yhvi volv rowr exbr"; // Your App Password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("mithilesh9324958141@gmail.com", "JobSure");
    $mail->addAddress($email);
    $mail->Subject = "Reset Your Password - JobSure";
    $mail->Body = "Your Password Reset OTP is: <b>$otp</b>";
    $mail->isHTML(true);

    $mail->send();

    echo json_encode(["success" => true, "message" => "OTP sent to your email."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Failed to send OTP. Error: " . $mail->ErrorInfo]);
}
?>