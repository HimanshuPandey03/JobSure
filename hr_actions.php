<?php
header('Content-Type: application/json');
ini_set('display_errors', 0); 
error_reporting(E_ALL);

// --- PHPMailer Includes ---
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $conn = new mysqli("localhost", "root", "", "jobsure_db");
    if ($conn->connect_error) throw new Exception("DB Connection Failed");
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

function sendResponse($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action_type'] ?? '';
    $app_id = intval($_POST['app_id'] ?? 0);

    if ($app_id <= 0) sendResponse(false, 'Invalid Application ID');

    // 1. STATUS UPDATE LOGIC ONLY (No Schedule/Offer)
    if ($action === 'update_status') {
        $status = $_POST['status'];
        
        // Update DB
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $app_id);
        
        if ($stmt->execute()) {
            
            // ▼▼▼ SEND EMAIL (Only if Shortlisted) ▼▼▼
            if ($status === 'Shortlisted') {
                
                // Fetch User Name, Email AND Job Title
                $user_sql = "SELECT u.email, u.first_name, u.last_name, j.job_title 
                             FROM applications a 
                             JOIN users u ON a.user_id = u.id 
                             JOIN jobs j ON a.job_id = j.id
                             WHERE a.id = ?";
                             
                $u_stmt = $conn->prepare($user_sql);
                $u_stmt->bind_param("i", $app_id);
                $u_stmt->execute();
                $u_result = $u_stmt->get_result();
                
                if ($row = $u_result->fetch_assoc()) {
                    $to_email = $row['email'];
                    $full_name = $row['first_name'] . ' ' . $row['last_name'];
                    $job_title = $row['job_title']; 
                    
                    $mail = new PHPMailer(true);
                    
                    // SSL Fix
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    try {
                        // SMTP Settings
                        $mail->isSMTP();
                        $mail->Host = "smtp.gmail.com";
                        $mail->SMTPAuth = true;
                        $mail->Username = "mithilesh9324958141@gmail.com"; 
                        $mail->Password = "yhvi volv rowr exbr"; 
                        $mail->SMTPSecure = "tls";
                        $mail->Port = 587;

                        $mail->setFrom("mithilesh9324958141@gmail.com", "JobSure HR");
                        $mail->addAddress($to_email);
                        $mail->Subject = "Update on your application for $job_title";

                        // Embed Logo
                        $logo_path = 'logo.jpeg'; 
                        $cid_string = '';
                        if (file_exists($logo_path)) {
                            $mail->addEmbeddedImage($logo_path, 'logo_cid_123');
                            $cid_string = '<img src="cid:logo_cid_123" alt="JobSure Logo" style="width: 120px; margin-bottom: 25px;">';
                        }

                        // --- PROFESSIONAL EMAIL CONTENT (Candidate word removed) ---
                        $mail->Body = '
                        <div style="background-color: #f3f4f6; padding: 40px 20px; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
                            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center;">
                                
                                ' . $cid_string . '
                                
                                <h2 style="color: #111827; margin: 0 0 10px 0; font-size: 24px; font-weight: 700;">Good News!</h2>
                                <h3 style="color: #4f46e5; margin: 0 0 25px 0; font-size: 18px; font-weight: 500;">You\'ve been shortlisted.</h3>
                                
                                <p style="font-size: 15px; color: #374151; line-height: 1.6; text-align: left;">
                                    Dear <b>' . $full_name . '</b>,
                                </p>
                                
                                <p style="font-size: 15px; color: #374151; line-height: 1.6; text-align: left;">
                                    We are pleased to inform you that your profile stood out to us. We have shortlisted <b>your application</b> for the position of <b>' . $job_title . '</b>.
                                </p>
                                
                                <div style="background-color: #f9fafb; border-left: 4px solid #4f46e5; padding: 15px; margin: 25px 0; text-align: left;">
                                    <p style="margin: 0; font-size: 14px; color: #4b5563;">
                                        <strong>What happens next?</strong><br>
                                        Our recruitment team is currently finalizing interview slots. You will receive a separate email or a phone call within the next 24-48 hours to schedule a discussion.
                                    </p>
                                </div>
                                
                                <p style="font-size: 15px; color: #374151; line-height: 1.6; text-align: left;">
                                    In the meantime, please feel free to review the job description and prepare any questions you might have for us.
                                </p>
                                
                                <p style="font-size: 15px; color: #374151; line-height: 1.6; text-align: left; margin-top: 30px;">
                                    Best regards,<br>
                                    <strong>The JobSure Team</strong>
                                </p>
                                
                                <div style="border-top: 1px solid #e5e7eb; margin-top: 30px; padding-top: 20px;">
                                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">
                                        This is an automated message. Please do not reply directly to this email.
                                    </p>
                                </div>
                            </div>
                        </div>';
                        
                        $mail->isHTML(true);
                        $mail->send();
                        
                    } catch (Exception $e) {
                        // Error handling (silent)
                    }
                }
            }
            // ▲▲▲ END EMAIL LOGIC ▲▲▲

            sendResponse(true, "Status updated to $status");
        }
        else sendResponse(false, "DB Error");
    }
}
?>