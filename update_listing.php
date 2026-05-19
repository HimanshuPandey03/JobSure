<?php
header('Content-Type: application/json');
require 'db_config.php'; // Use your connection file

$response = ['success' => false];

try {
    // Get all data, INCLUDING the id
    $id = $_POST['id'] ?? null;
    $listing_type = $_POST['listing_type'] ?? 'job';
    $job_title = $_POST['job_title'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $location = $_POST['location'] ?? '';
    $pay_details = $_POST['pay_details'] ?? '';
    $duration = ($listing_type === 'internship') ? ($_POST['duration'] ?? '') : null;
    $qualification = $_POST['qualification'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $process_details = $_POST['process_details'] ?? '';

    if (!$id) {
        throw new Exception('Listing ID is missing.');
    }

    // SQL UPDATE statement
    $sql = "UPDATE jobs SET 
                listing_type = ?, 
                job_title = ?, 
                company_name = ?, 
                location = ?, 
                pay_details = ?, 
                duration = ?, 
                qualification = ?, 
                experience = ?, 
                skills = ?, 
                gender = ?, 
                contact_person = ?, 
                contact_phone = ?, 
                process_details = ?
            WHERE id = ?"; // The WHERE clause is critical
    
    $stmt = $conn->prepare($sql);
    
    // Bind all 13 fields + the ID at the end (14 total)
    $stmt->bind_param(
        "sssssssssssssi", // 13 strings, 1 integer
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
        $process_details,
        $id // Bind the ID for the WHERE clause
    );
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = $stmt->error;
    }
    $stmt->close();

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>