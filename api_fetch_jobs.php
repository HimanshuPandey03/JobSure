<?php
session_start(); 
header('Content-Type: application/json');
require 'db_config.php';

// --- SIMPLIFIED QUERY ---
$sql = "SELECT * FROM jobs WHERE (listing_type = 'job' OR listing_type = 'Job')";

$params = [];
$types = "";

// 1. Search Filter
if (!empty($_GET['search'])) {
    $searchTerm = "%" . $_GET['search'] . "%";
    $sql .= " AND (job_title LIKE ? OR company_name LIKE ? OR skills LIKE ?)";
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
    $types .= "sss";
}

// 2. SMART LOCATION FILTER
// This maps broad cities to their specific local areas
$locationMappings = [
    'Mumbai' => ['Mumbai', 'Malad', 'Andheri', 'Bandra', 'Borivali', 'Dadar', 'Thane', 'Navi Mumbai', 'Powai', 'Goregaon', 'Kurla', 'Vashi'],
    'Delhi' => ['Delhi', 'New Delhi', 'Noida', 'Gurgaon', 'Gurugram', 'Faridabad', 'Ghaziabad'],
    'Bangalore' => ['Bangalore', 'Bengaluru', 'Whitefield', 'Electronic City', 'Indiranagar', 'Koramangala', 'HSR Layout'],
    'Pune' => ['Pune', 'Hinjewadi', 'Viman Nagar', 'Kothrud', 'Magarpatta'],
    'Hyderabad' => ['Hyderabad', 'Secunderabad', 'Gachibowli', 'Madhapur', 'Jubilee Hills']
];

if (!empty($_GET['location']) && $_GET['location'] !== 'All Locations') {
    $selectedLoc = $_GET['location'];
    
    // Check if the selected location has known sub-areas (like Malad inside Mumbai)
    if (isset($locationMappings[$selectedLoc])) {
        $subAreas = $locationMappings[$selectedLoc];
        $likeClauses = [];
        foreach ($subAreas as $area) {
            $likeClauses[] = "location LIKE ?";
            array_push($params, "%" . $area . "%");
            $types .= "s";
        }
        // (location LIKE '%Mumbai%' OR location LIKE '%Malad%' OR ...)
        $sql .= " AND (" . implode(" OR ", $likeClauses) . ")";
    } else {
        // Standard search for other states/cities
        $sql .= " AND location LIKE ?";
        array_push($params, "%" . $selectedLoc . "%");
        $types .= "s";
    }
}

// 3. Experience Filter
if (!empty($_GET['experience']) && $_GET['experience'] !== 'All Experiences') {
    $expTerm = "%" . $_GET['experience'] . "%";
    $sql .= " AND experience LIKE ?";
    array_push($params, $expTerm);
    $types .= "s";
}

// 4. SMART SALARY FILTER (Handles "10000" AND "6lpa")
if (!empty($_GET['salary']) && $_GET['salary'] !== 'Any Salary') {
    $salaryRange = $_GET['salary'];
    
    // Logic: If DB says "6lpa", treat as 50000 (approx monthly). If "10000", treat as 10000.
    $salaryCalc = "
        CASE 
            WHEN LOWER(pay_details) LIKE '%lpa%' THEN CAST(pay_details AS DECIMAL(10,2)) * 8333 
            ELSE CAST(REPLACE(REPLACE(pay_details, ',', ''), ' ', '') AS UNSIGNED)
        END
    ";

    if (strpos($salaryRange, '+') !== false) {
        // Handle "100000+"
        $min = (int)str_replace('+', '', $salaryRange);
        $sql .= " AND $salaryCalc >= ?";
        array_push($params, $min);
        $types .= "i";
    } else {
        // Handle ranges like "10000-25000"
        $parts = explode('-', $salaryRange);
        if (count($parts) == 2) {
            $min = (int)$parts[0];
            $max = (int)$parts[1];
            $sql .= " AND $salaryCalc BETWEEN ? AND ?";
            array_push($params, $min, $max);
            $types .= "ii";
        }
    }
}

$sql .= " ORDER BY posted_at DESC";

try {
    if (empty($params)) {
        $result = $conn->query($sql);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params); 
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }
    
    $jobs = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $jobs = ["error" => $e->getMessage()];
}

$conn->close();
echo json_encode($jobs);
?>