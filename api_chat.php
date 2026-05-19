<?php
// ==========================================================
// ERROR HANDLING
// ==========================================================
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// ==========================================================
// TIMEZONE + SESSION
// ==========================================================
date_default_timezone_set('Asia/Kolkata');
session_start();

header('Content-Type: application/json');

// ==========================================================
// LOGIN CHECK — BLOCK ASSISTANT FOR GUEST USERS
// ==========================================================
if (empty($_SESSION['username']) || empty($_SESSION['email'])) {
    echo json_encode([
        'success' => false,
        'reply'   => 'Please log in to your JobSure account to chat with the assistant.'
    ]);
    exit();
}

// Helper to send JSON response
function send_reply($message, $success = true) {
    echo json_encode([
        'success' => $success,
        'reply'   => $message
    ]);
    exit();
}

// ==========================================================
// DATABASE CONNECTION
// ==========================================================
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'jobsure_db');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (!isset($conn) || $conn->connect_error) {
    $err = $conn->connect_error ?? "Connection variable missing.";
    send_reply("DATABASE ERROR: Could not connect. " . $err, false);
}

// ==========================================================
// PARSE DATE/TIME + INSERT into DB
// ==========================================================
function schedule_pending_interview($conn, $user_text) {

    $text  = trim($user_text);
    $lower = strtolower($text);
    $dt = null;

    // --------------------------
    // CASE 1 — today/tomorrow
    // --------------------------
    if (preg_match('/\b(today|tomorrow)\b/i', $lower, $match)) {

        $base = new DateTime($match[1]); // today or tomorrow

        // Look for time (e.g., "5 PM", "2:30 pm")
        if (preg_match('/(\d{1,2})\s*:\s*(\d{2})\s*([ap]m)?/i', $text, $tm)) {
            $hour = (int)$tm[1];
            $min  = (int)$tm[2];
            $ampm = strtolower($tm[3] ?? "");

            if ($ampm === 'pm' && $hour < 12) $hour += 12;
            if ($ampm === 'am' && $hour == 12) $hour = 0;

            $base->setTime($hour, $min, 0);
        } else {
            $base->setTime(10, 0, 0); // default time
        }

        $dt = $base;
    }

    // --------------------------
    // CASE 2 — DD/MM/YYYY or DD-MM-YYYY
    // --------------------------
    elseif (preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})/', $text, $dm)) {

        $day   = (int)$dm[1];
        $month = (int)$dm[2];
        $year  = (int)$dm[3];

        $hour = 10;
        $min  = 0;

        if (preg_match('/(\d{1,2})\s*:\s*(\d{2})\s*([ap]m)?/i', $text, $tm)) {
            $hour = (int)$tm[1];
            $min  = (int)$tm[2];
            $ampm = strtolower($tm[3] ?? "");

            if ($ampm === 'pm' && $hour < 12) $hour += 12;
            if ($ampm === 'am' && $hour == 12) $hour = 0;
        }

        $dt = new DateTime();
        $dt->setDate($year, $month, $day);
        $dt->setTime($hour, $min, 0);
    }

    // --------------------------
    // Unsupported format
    // --------------------------
    else {
        send_reply(
            "I'm sorry, I couldn't understand that date or time. " .
            "Try: 'Tomorrow at 3 PM' or '25/11/2025 at 2:00 PM'.",
            false
        );
    }

    // Final safety check
    if (!$dt) {
        send_reply("Invalid date or time. Please try again.", false);
    }

    // Must be future date
    if ($dt->getTimestamp() < time()) {
        send_reply("That time is in the past. Please give a future time.", false);
    }

    // Format for DB
    $mysql_time    = $dt->format('Y-m-d H:i:s');
    $friendly_time = $dt->format('l, F jS \a\t g:i A');

    // User info from login session
    $candidate_name  = $_SESSION["username"];
    $candidate_email = $_SESSION["email"];

    // Insert into your table (without status column)
    $stmt = $conn->prepare(
        "INSERT INTO scheduled_interviews (candidate_name, candidate_email, requested_time)
         VALUES (?, ?, ?)"
    );

    if ($stmt === false) {
        send_reply("DATABASE ERROR: Could not prepare statement. " . $conn->error, false);
    }

    $stmt->bind_param("sss", $candidate_name, $candidate_email, $mysql_time);

    if ($stmt->execute()) {
        $msg =
            "Perfect! I’ve tentatively scheduled you for **$friendly_time**.\n\n" .
            "A confirmation email will be sent to **$candidate_email**.\n\n" .
            "Is there anything else I can help with?";
        $_SESSION['step'] = 'end';
        send_reply($msg, true);
    } else {
        send_reply("DATABASE ERROR: Could not save. " . $conn->error, false);
    }

    $stmt->close();
}

// ==========================================================
// MAIN CHAT LOGIC
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $raw  = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) $data = $_POST;
    if (!is_array($data)) $data = [];

    $user_message = trim($data['message'] ?? "");

    if ($user_message === "") {
        send_reply("Please type a message.", false);
    }

    $lower = strtolower($user_message);
    $step  = $_SESSION['step'] ?? 'start';

    $match = function($words) use ($lower) {
        foreach ($words as $w) {
            if (strpos($lower, $w) !== false) return true;
        }
        return false;
    };

    if ($match(['start','yes','ok','schedule']) && $step !== 'start') {
        $step = 'start';
    }

    switch ($step) {

        case 'start':
            if ($match(['yes','ok','start','schedule'])) {
                $_SESSION['step'] = 'awaiting_datetime';
                send_reply(
                    "Great! What date and time would you like?\n" .
                    "Try: 'tomorrow at 3 PM' or '25/11/2025 at 10 AM'"
                );
            } else {
                send_reply("Hi! I can help you schedule an interview. Would you like to get started?");
            }
            break;

        case 'awaiting_datetime':
            schedule_pending_interview($conn, $user_message);
            break;

        case 'end':
            if ($match(['no','bye','stop'])) {
                session_destroy();
                send_reply("Alright! Have a great day 😊");
            } else {
                send_reply("You're all set! If you need changes, contact support. Have a great day!");
            }
            break;
    }

} else {
    send_reply("Invalid request method.", false);
}

$conn->close();