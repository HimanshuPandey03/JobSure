<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

$mail = new PHPMailer(true);

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
    // 1. ENABLE VERBOSE DEBUG OUTPUT
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPAuth   = true;

    // 2. YOUR CREDENTIALS
    $mail->Username   = "mithilesh9324958141@gmail.com";
    $mail->Password   = "yhvi volv rowr exbr"; // Your App Password

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 3. SENDER AND RECIPIENT
    $mail->setFrom("mithilesh9324958141@gmail.com", "SMTP Test");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "SMTP Test";
    $mail->Body    = "If you receive this, SMTP works.";

    echo "<h3>Sending Test Mail…</h3>";

    if ($mail->send()) {
        echo "<h3 style='color:green;'>SUCCESS: Mail Sent</h3>";
    } else {
        echo "<h3 style='color:red;'>FAILED: " . $mail->ErrorInfo . "</h3>";
    }

} catch (Exception $e) {
    echo "<h3 style='color:red;'>EXCEPTION: " . $mail->ErrorInfo . "</h3>";
}
?>