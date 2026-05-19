<?php
session_start(); 
header('Content-Type: application/json');
require 'db_config.php';

// --- SIMPLIFIED QUERY ---
$sql = "SELECT * FROM jobs WHERE (listing_type = 'internship' OR listing_type = 'Internship')";

$params = [];
$types = "";

// 1. Search Filter
if (!empty($_GET['search'])) {
    $searchTerm = "%" . $_GET['search'] . "%";
    $sql .= " AND (job_title LIKE ? OR company_name LIKE ? OR skills LIKE ?)";
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
    $types .= "sss";
}

// 2. SMART LOCATION FILTER (Same mappings as jobs)
$locationMappings = [
    'Mumbai' => ['Mumbai', 'Malad', 'Andheri', 'Bandra', 'Borivali', 'Dadar', 'Thane', 'Navi Mumbai', 'Powai', 'Goregaon', 'Kurla', 'Vashi'],
    'Delhi' => ['Delhi', 'New Delhi', 'Noida', 'Gurgaon', 'Gurugram', 'Faridabad', 'Ghaziabad'],
    'Bangalore' => ['Bangalore', 'Bengaluru', 'Whitefield', 'Electronic City', 'Indiranagar', 'Koramangala', 'HSR Layout'],
    'Pune' => ['Pune', 'Hinjewadi', 'Viman Nagar', 'Kothrud', 'Magarpatta'],
    'Hyderabad' => ['Hyderabad', 'Secunderabad', 'Gachibowli', 'Madhapur', 'Jubilee Hills']
];

if (!empty($_GET['location']) && $_GET['location'] !== 'All Locations') {
    $selectedLoc = $_GET['location'];
    if (isset($locationMappings[$selectedLoc])) {
        $subAreas = $locationMappings[$selectedLoc];
        $likeClauses = [];
        foreach ($subAreas as $area) {
            $likeClauses[] = "location LIKE ?";
            array_push($params, "%" . $area . "%");
            $types .= "s";
        }
        $sql .= " AND (" . implode(" OR ", $likeClauses) . ")";
    } else {
        $sql .= " AND location LIKE ?";
        array_push($params, "%" . $selectedLoc . "%");
        $types .= "s";
    }
}

// 3. Stipend Filter
if (!empty($_GET['stipend']) && $_GET['stipend'] !== 'Any Stipend') {
    if ($_GET['stipend'] === 'Unpaid') {
         $sql .= " AND (pay_details LIKE '%Unpaid%' OR pay_details = '0' OR pay_details = '')";
    } else {
        // Clean numeric calculation for stipend
        $stipendCalc = "CAST(REPLACE(REPLACE(pay_details, ',', ''), ' ', '') AS UNSIGNED)";
        
        $stipendRange = $_GET['stipend'];
        if (strpos($stipendRange, '+') !== false) {
            $min = (int)str_replace('+', '', $stipendRange);
            $sql .= " AND $stipendCalc >= ?";
            array_push($params, $min);
            $types .= "i";
        } else {
            $parts = explode('-', $stipendRange);
            if (count($parts) == 2) {
                $min = (int)$parts[0];
                $max = (int)$parts[1];
                $sql .= " AND $stipendCalc BETWEEN ? AND ?";
                array_push($params, $min, $max);
                $types .= "ii";
            }
        }
    }
}

// 4. Duration Filter
if (!empty($_GET['duration']) && $_GET['duration'] !== 'Any Duration') {
    $durationTerm = "%" . intval($_GET['duration']) . "%"; 
    $sql .= " AND duration LIKE ?";
    array_push($params, $durationTerm);
    $types .= "s";
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
    $internships = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $internships = ["error" => $e->getMessage()];
}

$conn->close();
echo json_encode($internships);
?>