<?php
session_start();
require_once 'db_config.php';

// GET JSON INPUT
$data = json_decode(file_get_contents("php://input"), true);

$first_name = trim($data["first_name"]);
$last_name  = trim($data["last_name"]);
$email      = trim($data["email"]);
$password   = $data["password"];

// 1. Check if email already registered
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered."]);
    exit;
}
$stmt->close();

// 2. Generate OTP
$otp = rand(100000, 999999);

// 3. Store data temporarily in session
$_SESSION["reg_first_name"] = $first_name;
$_SESSION["reg_last_name"]  = $last_name;
$_SESSION["reg_email"]      = $email;
$_SESSION["reg_password"]   = password_hash($password, PASSWORD_DEFAULT);
$_SESSION["reg_otp"]        = $otp;

// 4. Send OTP via email (PHPMailer recommended)
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();

// ▼▼▼ ADD THIS BLOCK TO FIX THE ERROR ▼▼▼
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
// ▲▲▲ END OF BLOCK TO ADD ▲▲▲

try {
    // SMTP SETTINGS
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "mithilesh9324958141@gmail.com"; // Your email
    $mail->Password = "yhvi volv rowr exbr";  // Your new Gmail App Password
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    // EMAIL CONTENT
    $mail->setFrom("mithilesh9324958141@gmail.com", "JobSure");
    $mail->addAddress($email);
    $mail->Subject = "Your JobSure OTP Verification Code";
    $mail->Body = "Your verification OTP is: <b>$otp</b>";
    $mail->isHTML(true);

    $mail->send();

    echo json_encode(["success" => true, "message" => "OTP sent to your email."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Failed to send OTP."]);
}
?>
