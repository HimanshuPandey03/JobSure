<?php
session_start();
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // --- Validation ---
    
    // Check if email already exists
    $sql_check = "SELECT id FROM users WHERE email = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $_SESSION['register_error'] = "An account with this email already exists.";
            header("location: index.php");
            exit;
        }
        $stmt_check->close();
    }
    
    // Check password length
    if (strlen($password) < 6) {
        $_SESSION['register_error'] = "Password must be at least 6 characters.";
        header("location: index.php");
        exit;
    }
    
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql_insert = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    
    if ($stmt_insert = $conn->prepare($sql_insert)) {
        $stmt_insert->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
        
        if ($stmt_insert->execute()) {
            // SUCCESS: Automatically log the user in
            session_regenerate_id();
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $conn->insert_id; 
            $_SESSION["username"] = $first_name;
            $_SESSION["email"] = $email;
            
            // --- NEW ---
            // Set a flag that this is a brand new user
            $_SESSION['is_new_user'] = true; 
            // --- END NEW ---
            
            header("location: profile.php"); // Redirect to profile
            exit;
        } else {
            $_SESSION['register_error'] = "Something went wrong. Please try again.";
            header("location: index.php");
            exit;
        }
        $stmt_insert->close();
    }
    $conn->close();
    
} else {
    header("location: index.php");
    exit;
}
?>