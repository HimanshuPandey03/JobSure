<?php
// Start the session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Set an error message before redirecting
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['login_error'] = "Please log in to access this page.";
    header("location: login.html");
    exit;
}

// Set variables for easy use in other pages
$user_id = $_SESSION["id"];
$user_name = $_SESSION["username"];
?>