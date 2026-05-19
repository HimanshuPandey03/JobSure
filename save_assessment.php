<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php"); exit;
}
$user_id = $_SESSION["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Array to store: 'CategoryName' => ['score' => 0, 'max_score' => 0]
    $results = []; 

    // 1. CALCULATE SCORES
    // We loop through 25 questions to find whatever categories were used
    for ($i = 1; $i <= 25; $i++) {
        if (isset($_POST["q$i"]) && isset($_POST["cat_$i"])) {
            $selected_label = $_POST["q$i"]; // A, B, C, D
            $category = $_POST["cat_$i"];    // Sales, IT, etc.

            $points = 0;
            if ($selected_label == 'A') $points = 4;
            elseif ($selected_label == 'B') $points = 3;
            elseif ($selected_label == 'C') $points = 2;
            elseif ($selected_label == 'D') $points = 1;

            if (!isset($results[$category])) {
                $results[$category] = ['score' => 0, 'max_score' => 0];
            }

            $results[$category]['score'] += $points;
            $results[$category]['max_score'] += 4; 
        }
    }

    // 2. PROCESS RESULTS FOR ANALYSIS
    $highest_percent = -1;
    $winner_category = "";
    $final_breakdown = []; // This will hold data for the new Analysis Page

    foreach ($results as $cat => $data) {
        if ($data['max_score'] > 0) {
            $percent = ($data['score'] / $data['max_score']) * 100;
            
            // Store breakdown for the next page
            $final_breakdown[$cat] = $percent;

            if ($percent > $highest_percent) {
                $highest_percent = $percent;
                $winner_category = $cat;
            }
        }
    }

    // 3. DETERMINE RECOMMENDATION (For the Winner)
    $recommendation = "Course"; 
    if ($highest_percent >= 76) {
        $recommendation = "Job";
    } elseif ($highest_percent >= 41) {
        $recommendation = "Internship";
    }

    // 4. SAVE TO DATABASE (Only the winner goes to DB)
    $sql = "UPDATE users SET quiz_completed = 1, quiz_result = ?, quiz_score_percent = ?, quiz_recommendation = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $winner_category, $highest_percent, $recommendation, $user_id);
    
    if ($stmt->execute()) {
        // 5. SAVE SESSION DATA
        $_SESSION['quiz_completed'] = 1;
        $_SESSION['quiz_result'] = $winner_category;
        $_SESSION['quiz_score'] = $highest_percent;
        $_SESSION['quiz_rec'] = $recommendation;
        
        // IMPORTANT: Save the full breakdown for the Analysis Page
        $_SESSION['assessment_breakdown'] = $final_breakdown; 
        
        // REDIRECT TO THE ANALYSIS PAGE FIRST (Then to Result)
        header("location: assessment_analysis.php"); 
        exit;
    } else {
        echo "Error saving results.";
    }

} else {
    header("location: assessment.php");
}
?>