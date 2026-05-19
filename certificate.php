<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();
ob_clean(); // Clean any previous output

// 1. CHECK FOR FPDF AND REQUIRE IT
if (!file_exists('fpdf186/fpdf.php')) {
    ob_clean();
    die("CRITICAL ERROR: FPDF library not found. Make sure the 'fpdf186' folder is in the same directory.");
}
require('fpdf186/fpdf.php');

// 2. GATEKEEPER
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
    !isset($_SESSION['quiz_completed']) || $_SESSION['quiz_completed'] != 1) {
    header("location: index.php");
    exit;
}

// 3. GET USER DATA
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Participant';
$result_field = isset($_SESSION['quiz_result']) ? $_SESSION['quiz_result'] : 'General';

// --- PDF GENERATION ---

$logo_file = 'CLogo.png';
$sign_file = 'Sign.png'; 
$stamp_file = 'Stamp.png';

$pdf = new FPDF('L', 'mm', 'A4'); 
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);
$pdf->SetTitle("Certificate of Career Assessment");

// Add Fonts
$pdf->AddFont('Times', 'B', 'timesb.php');
$pdf->AddFont('Times', 'I', 'timesi.php');
$pdf->AddFont('Times', 'BI', 'timesbi.php'); 
$pdf->AddFont('Times', '', 'times.php');

// Page dimensions
$pageW = 297;
$pageH = 210;
$margin = 15; 

// Colors
$accent = [102, 51, 255];        
$accent_light = [198, 185, 255]; 
$text_dark = [35, 35, 35];
$text_muted = [110, 110, 110];

// --- Border ---
$pdf->SetLineWidth(1.5);
$pdf->SetDrawColor($accent[0], $accent[1], $accent[2]);
$pdf->Rect($margin, $margin, $pageW - ($margin * 2), $pageH - ($margin * 2)); 
$pdf->SetLineWidth(0.5);
$pdf->SetDrawColor($accent_light[0], $accent_light[1], $accent_light[2]);
$pdf->Rect($margin + 3, $margin + 3, $pageW - ($margin * 2) - 6, $pageH - ($margin * 2) - 6);

// --- Content ---

// 1. --- TOP LOGO (MOVED UP) ---
if (file_exists($logo_file)) {
    $logo_width = 85; 
    $logo_x = ($pageW - $logo_width) / 2; 
    // FIX: Moved Y up to 5 (was 10) to give just a bit more clearance at the top
    $logo_y = 5; 
    
    $pdf->Image($logo_file, $logo_x, $logo_y, $logo_width, 0); 
}

// Main Title (Maintained position at 65)
$pdf->SetFont('Helvetica', 'B', 32);
$pdf->SetTextColor($text_dark[0], $text_dark[1], $text_dark[2]);
$pdf->SetY(65); 
$pdf->Cell(0, 14, 'Certificate of Career Assessment', 0, 1, 'C');

// Presented to
$pdf->SetFont('Arial', '', 16);
$pdf->SetTextColor($text_muted[0], $text_muted[1], $text_muted[2]);
$pdf->SetY(88); 
$pdf->Cell(0, 8, 'This certificate is proudly presented to', 0, 1, 'C');

// Recipient Name
$pdf->SetFont('Times', 'BI', 48); 
$pdf->SetTextColor($accent[0], $accent[1], $accent[2]);
$pdf->SetY(105); 
$pdf->Cell(0, 22, $user_name, 0, 1, 'C');

// Supporting line
$pdf->SetFont('Arial', '', 15);
$pdf->SetTextColor($text_muted[0], $text_muted[1], $text_muted[2]);
$pdf->SetY(132); 
$pdf->Cell(0, 8, 'Presented in recognition of completing the assessment for the', 0, 1, 'C');

// Assessment Field
$pdf->SetFont('Helvetica', 'B', 20); 
$pdf->SetTextColor($text_dark[0], $text_dark[1], $text_dark[2]);
$pdf->SetY(145);
$pdf->Cell(0, 10, trim($result_field . ' Career Assessment'), 0, 1, 'C');

// Thin divider line
$pdf->SetDrawColor($accent_light[0], $accent_light[1], $accent_light[2]);
$pdf->SetLineWidth(0.5);
$pdf->Line($pageW / 2 - 50, 158, $pageW / 2 + 50, 158);

// --- Bottom Date, Stamp, & Signature ---
$bottom_line_y = 185; 
$bottom_label_y = 190; 
$line_width = 60; 
$col_spacing = 15; 

$left_col_x = $margin + $col_spacing; 
$center_col_x = ($pageW / 2) - ($line_width / 2); 
$right_col_x = $pageW - $margin - $col_spacing - $line_width; 

// DATE
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor($text_dark[0], $text_dark[1], $text_dark[2]);
$pdf->SetXY($left_col_x, $bottom_line_y - 6);
$pdf->Cell($line_width, 0, date('F j, Y'), 0, 1, 'C'); 

$pdf->SetDrawColor($accent_light[0], $accent_light[1], $accent_light[2]);
$pdf->Line($left_col_x, $bottom_line_y, $left_col_x + $line_width, $bottom_line_y); 

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor($text_muted[0], $text_muted[1], $text_muted[2]);
$pdf->SetXY($left_col_x, $bottom_label_y);
$pdf->Cell($line_width, 0, 'Date Issued', 0, 1, 'C'); 

// STAMP (MOVED DOWN)
if (file_exists($stamp_file)) {
    $stamp_size = 32; 
    $pdf->Image(
        $stamp_file, 
        $center_col_x + ($line_width / 2) - ($stamp_size / 2), 
        // FIX: Changed -36 to -32 to move it DOWN closer to the line
        $bottom_line_y - 32, 
        $stamp_size, 
        0
    ); 
}

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor($text_dark[0], $text_dark[1], $text_dark[2]);
$pdf->SetXY($center_col_x, $bottom_line_y - 3);
$pdf->Cell($line_width, 0, 'Official Seal', 0, 1, 'C');

$pdf->Line($center_col_x, $bottom_line_y, $center_col_x + $line_width, $bottom_line_y);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor($text_muted[0], $text_muted[1], $text_muted[2]);
$pdf->SetXY($center_col_x, $bottom_label_y);
$pdf->Cell($line_width, 0, 'Certification Stamp', 0, 1, 'C'); 

// SIGNATURE (MOVED DOWN)
if (file_exists($sign_file)) {
    $sig_size = 45; 
    $pdf->Image(
        $sign_file, 
        $right_col_x + ($line_width / 2) - ($sig_size / 2), 
        // FIX: Changed -35 to -28 to move it DOWN closer to the line
        $bottom_line_y - 28, 
        $sig_size, 
        0 
    ); 
}

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor($text_dark[0], $text_dark[1], $text_dark[2]);
$pdf->SetXY($right_col_x, $bottom_line_y - 3);
$pdf->Cell($line_width, 0, 'JobSure Director', 0, 1, 'C'); 

$pdf->Line($right_col_x, $bottom_line_y, $right_col_x + $line_width, $bottom_line_y);

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor($text_muted[0], $text_muted[1], $text_muted[2]);
$pdf->SetXY($right_col_x, $bottom_label_y);
$pdf->Cell($line_width, 0, 'Authorized Signature', 0, 1, 'C');

$pdf->Output('D', 'JobSure_Certificate.pdf');

ob_end_flush();
exit;
?>