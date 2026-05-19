<?php
header('Content-Type: application/json');
require 'db_config.php'; // <-- USES YOUR FILE

$response = ['success' => false];

try {
    // Get all data, including new fields
    $listing_type = $_POST['listing_type'] ?? 'job';
    $job_title = $_POST['job_title'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $location = $_POST['location'] ?? '';
    $pay_details = $_POST['pay_details'] ?? '';
    // Only save duration for internships
    $duration = ($listing_type === 'internship') ? ($_POST['duration'] ?? '') : null; 
    $qualification = $_POST['qualification'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $process_details = $_POST['process_details'] ?? '';

    // Updated SQL INSERT statement
    $sql = "INSERT INTO jobs (listing_type, job_title, company_name, location, pay_details, duration, qualification, experience, skills, gender, contact_person, contact_phone, process_details) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // --- CHANGED to MySQLi Prepared Statement ---
    $stmt = $conn->prepare($sql);
    
    // "s" = string. We have 13 string parameters.
    $stmt->bind_param(
        "sssssssssssss", 
        $listing_type,
        $job_title, 
        $company_name, 
        $location, 
        $pay_details, 
        $duration,
        $qualification, 
        $experience, 
        $skills, 
        $gender, 
        $contact_person, 
        $contact_phone,
        $process_details
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = $stmt->error;
    }
    $stmt->close();
    // --- END CHANGE ---

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>