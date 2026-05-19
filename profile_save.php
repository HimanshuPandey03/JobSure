<?php
session_start();
require 'db_config.php'; 

// 1. Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["id"])) {
    header("location: index.php"); 
    exit;
}

$user_id = $_SESSION["id"];
$upload_dir = "uploads/resumes/"; 
$errors = []; 
$resume_path_to_db = null;

// Default redirect (stay on profile)
$redirect_url = "profile.php"; 

// Check if manually flagged as new
$is_new_user_flow = false;
if (isset($_SESSION['is_new_user']) && $_SESSION['is_new_user'] === true) $is_new_user_flow = true;
if (isset($_POST['is_new_user_flag']) && $_POST['is_new_user_flag'] == '1') $is_new_user_flow = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Get data
    $full_name = trim($_POST['full_name'] ?? '');
    $phone_no = trim($_POST['phone_no'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $gender = $_POST['gender'] ?? null;
    $education = trim($_POST['education'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $work_experience = trim($_POST['work_experience'] ?? '');
    $currently_looking_for = trim($_POST['currently_looking_for'] ?? '');
    $work_mode = trim($_POST['work_mode'] ?? '');
    $areas_of_interest = trim($_POST['areas_of_interest'] ?? '');

    // 3. Validation
    if (empty($full_name)) $errors[] = "Full Name is required.";
    if (!empty($phone_no) && !preg_match('/^\d{10}$/', $phone_no)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    // Resume Validation
    if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] == UPLOAD_ERR_OK) {
        $file_name = $_FILES['resume_file']['name'];
        $file_size = $_FILES['resume_file']['size'];
        $file_tmp_name = $_FILES['resume_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        
        if (!in_array($file_ext, $allowed_extensions)) $errors[] = "Invalid resume file type.";
        if ($file_size > 5000000) $errors[] = "Resume file is too large (Max 5MB).";
    }

    if (!empty($errors)) {
        $_SESSION['profile_status'] = "Error: " . implode("<br>", $errors);
        header("location: profile.php");
        exit;
    }

    try {
        // Update user name
        $stmt_user = $conn->prepare("UPDATE users SET first_name = ? WHERE id = ?");
        $stmt_user->bind_param("si", $full_name, $user_id);
        $stmt_user->execute();
        $stmt_user->close();
        
        // --- CHECK EXISTING DATA ---
        // We capture the state of the profile BEFORE this update
        $stmt_check = $conn->prepare("SELECT user_id, resume_path, skills, education FROM profiles WHERE user_id = ?");
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $existing_data = $result->fetch_assoc();
        $stmt_check->close();
        
        $profile_exists = ($result->num_rows > 0);
        $resume_path_to_db = $existing_data['resume_path'] ?? null; 

        // Handle File Upload
        if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] == UPLOAD_ERR_OK) {
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $target_file = $upload_dir . "user_" . $user_id . "_" . uniqid() . "." . $file_ext;
            if (move_uploaded_file($file_tmp_name, $target_file)) {
                $resume_path_to_db = $target_file; 
            }
        }
        
        // Insert or Update DB
        if ($profile_exists) {
            $stmt = $conn->prepare("UPDATE profiles SET phone_no=?, location=?, gender=?, education=?, skills=?, work_experience=?, resume_path=?, currently_looking_for=?, work_mode=?, areas_of_interest=? WHERE user_id=?");
            $stmt->bind_param("ssssssssssi", $phone_no, $location, $gender, $education, $skills, $work_experience, $resume_path_to_db, $currently_looking_for, $work_mode, $areas_of_interest, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO profiles (user_id, phone_no, location, gender, education, skills, work_experience, resume_path, currently_looking_for, work_mode, areas_of_interest) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssss", $user_id, $phone_no, $location, $gender, $education, $skills, $work_experience, $resume_path_to_db, $currently_looking_for, $work_mode, $areas_of_interest);
        }
        
        if ($stmt->execute()) {
            
            // --- LOGIC TO DETECT "INCOMPLETE" PROFILE ---
            $was_incomplete = false;
            
            // If profile didn't exist, OR if it lacked core details (Resume OR Skills OR Education)
            if (!$profile_exists) {
                $was_incomplete = true;
            } elseif (empty($existing_data['resume_path']) || empty($existing_data['skills']) || empty($existing_data['education'])) {
                // If any of these were empty before, we assume the user is finishing their signup
                $was_incomplete = true;
            }

            // REDIRECT LOGIC
            if ($is_new_user_flow || $was_incomplete) {
                // 1. Set success message
                $_SESSION['profile_status'] = "Profile completed! Please log in to continue.";
                
                // 2. FORCE LOGOUT (Important: clears session so login.php doesn't bounce them back)
                unset($_SESSION['loggedin']);
                unset($_SESSION['id']);
                unset($_SESSION['username']);
                unset($_SESSION['is_new_user']);
                
                // 3. Redirect to Login
                $redirect_url = "login.php"; 
            } else {
                $_SESSION['profile_status'] = "Changes saved successfully!";
            }

        } else {
            $_SESSION['profile_status'] = "Error updating profile: " . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        $_SESSION['profile_status'] = "An error occurred: " . $e->getMessage();
    }

    header("location: " . $redirect_url);
    exit;

} else {
    header("location: profile.php");
    exit;
}
?>