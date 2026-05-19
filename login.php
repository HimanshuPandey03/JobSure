<?php
session_start();
require_once 'db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    $error_user_not_found = "Username does not exist.";
    $error_password_mismatch = "Password does not match.";

    // --- UPDATED SQL: Also select the new quiz columns ---
    $sql = "SELECT id, first_name, email, password, quiz_completed, quiz_result FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_email);
        $param_email = $email;

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // --- UPDATED: Bind the new columns ---
                $stmt->bind_result($id, $first_name, $email_from_db, $hashed_password, $quiz_completed, $quiz_result);
                
                if ($stmt->fetch()) {
                    
                    if (password_verify($password, $hashed_password)) {
                        // --- SUCCESS ---
                        session_regenerate_id();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $first_name; 
                        $_SESSION["email"] = $email_from_db;
                        
                        // --- NEW: Save quiz status to session ---
                        $_SESSION["quiz_completed"] = $quiz_completed;
                        $_SESSION["quiz_result"] = $quiz_result;
                        // --- END NEW ---
                        
                        header("location: index.php"); 
                        exit;
                    } else {
                        $_SESSION['login_error'] = $error_password_mismatch;
                        header("location: index.php"); 
                        exit;
                    }
                }
            } else {
                $_SESSION['login_error'] = $error_user_not_found;
                header("location: index.php"); 
                exit;
            }
        } else {
            $_SESSION['login_error'] = "An unexpected error occurred.";
            header("location: index.php");
            exit;
        }
        $stmt->close();
    }
    $conn->close();
} else {
    header("location: index.php");
    exit;
}
?><?php
session_start();
require_once 'db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    $error_user_not_found = "Username does not exist.";
    $error_password_mismatch = "Password does not match.";

    // --- UPDATED SQL: Also select the new quiz columns ---
    $sql = "SELECT id, first_name, email, password, quiz_completed, quiz_result FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_email);
        $param_email = $email;

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // --- UPDATED: Bind the new columns ---
                $stmt->bind_result($id, $first_name, $email_from_db, $hashed_password, $quiz_completed, $quiz_result);
                
                if ($stmt->fetch()) {
                    
                    if (password_verify($password, $hashed_password)) {
                        // --- SUCCESS ---
                        session_regenerate_id();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $first_name; 
                        $_SESSION["email"] = $email_from_db;
                        
                        // --- NEW: Save quiz status to session ---
                        $_SESSION["quiz_completed"] = $quiz_completed;
                        $_SESSION["quiz_result"] = $quiz_result;
                        // --- END NEW ---
                        
                        header("location: index.php"); 
                        exit;
                    } else {
                        $_SESSION['login_error'] = $error_password_mismatch;
                        header("location: index.php"); 
                        exit;
                    }
                }
            } else {
                $_SESSION['login_error'] = $error_user_not_found;
                header("location: index.php"); 
                exit;
            }
        } else {
            $_SESSION['login_error'] = "An unexpected error occurred.";
            header("location: index.php");
            exit;
        }
        $stmt->close();
    }
    $conn->close();
} else {
    header("location: index.php");
    exit;
}
?>