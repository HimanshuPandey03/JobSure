<?php
session_start(); 
require 'db_config.php'; 

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['login_error'] = "You must be logged in to view that page.";
    header("location: index.php");
    exit;
}
$user_id = $_SESSION['id'];
$user_name = $_SESSION['username'];

// 2. GET LISTING ID FROM URL
$listing_id = $_GET['id'] ?? null;
if (!$listing_id) {
    echo "No listing specified.";
    exit;
}

// 3. FETCH ALL LISTING DETAILS
try {
    $sql = "SELECT 
                j.*, 
                (b.id IS NOT NULL) AS is_bookmarked,
                (a.id IS NOT NULL) AS is_applied
            FROM jobs j
            LEFT JOIN bookmarks b ON j.id = b.job_id AND b.user_id = ?
            LEFT JOIN applications a ON j.id = a.job_id AND a.user_id = ?
            WHERE j.id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $user_id, $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo "Listing not found.";
        exit;
    }
    $listing = $result->fetch_assoc();
    $stmt->close();
    
    // 4. NEW: FETCH USER'S PROFILE (for resume)
    $profile_sql = "SELECT resume_path FROM profiles WHERE user_id = ?";
    $profile_stmt = $conn->prepare($profile_sql);
    $profile_stmt->bind_param("i", $user_id);
    $profile_stmt->execute();
    $profile_result = $profile_stmt->get_result();
    $profile_data = $profile_result->fetch_assoc();
    $profile_stmt->close();

} catch (Exception $e) {
    echo "Error fetching listing: " . $e->getMessage();
    exit;
}
$conn->close();

function formatSkills($skills) {
    if (empty($skills)) return '';
    $skills_array = explode(',', $skills);
    $html = '';
    foreach ($skills_array as $skill) {
        $html .= '<span class="skill-tag">' . htmlspecialchars(trim($skill)) . '</span>';
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($listing['job_title']); ?> - JobSure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary-light: #6e48ff;
            --primary-dark: #4e2ecf;
            --gradient: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
            --white: #ffffff;
            --light-gray: #f9fafb;
            --border-color: #e7eaf3;
            --text-dark: #2d3748;
            --text-light: #5a677d;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.1);
            --login-gradient: linear-gradient(to right, #00d0ff, #7c3aed);
            --danger-red: #D90429;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--text-dark); line-height: 1.6; }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* (Header CSS) */
        .main-header { background-color: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .main-nav { display: flex; justify-content: space-between; align-items: center; }
        
        /* UPDATED LOGO STYLE */
        .logo { display: flex; align-items: center; }
        .logo img { height: 70px; width: auto; max-width: 250px; object-fit: contain; }

        .nav-right { display: flex; align-items: center; gap: 28px; }
        .nav-links { display: flex; list-style: none; gap: 28px; }
        .nav-links a { color: var(--text-light); font-weight: 500; position: relative; padding-bottom: 6px; transition: color 0.3s ease; }
        .nav-links a::after { content: ''; position: absolute; bottom: 0; left: 50%; width: 0; height: 2px; background-color: var(--primary-dark); transition: all 0.3s ease; }
        .nav-links a:hover { color: var(--primary-dark); }
        .nav-links a:hover::after { width: 100%; left: 0; }
        .profile-dropdown { position: relative; }
        .dropdown-trigger { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-dark); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s ease; }
        .dropdown-menu { display: none; position: absolute; top: 55px; right: 0; min-width: 240px; background: var(--white); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-hover); z-index: 1001; overflow: hidden; }
        .dropdown-menu.show { display: block; animation: fadeInUp 0.2s ease forwards; }
        .dropdown-header { padding: 15px 20px; border-bottom: 1px solid var(--border-color); }
        .dropdown-header strong { display: block; color: var(--text-dark); font-weight: 600; }
        .dropdown-header small { color: var(--text-light); font-size: 13px; word-break: break-all; }
        .dropdown-item { display: block; padding: 12px 20px; color: var(--text-dark); font-size: 15px; font-weight: 500; transition: all 0.2s ease; }
        .dropdown-item:hover { background-color: var(--light-gray); color: var(--primary-dark); }
        .dropdown-divider { border: 0; height: 1px; background-color: var(--border-color); margin: 0; }
        .dropdown-item.dropdown-toggle { display: flex; justify-content: space-between; align-items: center; }
        .dropdown-arrow { width: 16px; height: 16px; transition: transform 0.2s ease; stroke-width: 2.5; }
        .dropdown-item.dropdown-toggle.open .dropdown-arrow { transform: rotate(180deg); }
        .dropdown-submenu { display: none; background: var(--light-gray); }
        .dropdown-submenu.show { display: block; }
        .dropdown-submenu .dropdown-item { padding-left: 35px; }

        /* (Page Layout CSS) */
        .listing-layout {
            max-width: 900px;
            margin: 40px auto;
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .listing-header {
            padding: 32px 40px;
            border-bottom: 1px solid var(--border-color);
        }
        .listing-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        .listing-header h2 {
            font-size: 18px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        .listing-actions {
            margin-top: 24px;
            display: flex;
            gap: 16px;
        }
        
        /* --- IMPROVED BUTTON STYLES --- */
        
        /* Apply Button */
        .btn-apply-gradient { 
            background: var(--login-gradient); 
            color: var(--white); 
            padding: 12px 30px; 
            border-radius: 8px; 
            font-weight: 600; 
            font-size: 16px; 
            transition: all 0.3s ease; 
            border: none; 
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(110, 72, 255, 0.3);
        }
        .btn-apply-gradient:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(110, 72, 255, 0.4);
            opacity: 0.95; 
        }
        /* Disabled State (Applied) */
        .btn-apply-gradient.applied {
            background: #e2e8f0; /* Light Grey */
            color: #94a3b8;      /* Darker Grey Text */
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
            border: 1px solid #cbd5e1;
        }
        
        /* Save Button */
        .bookmark-btn {
            background: var(--white); 
            border: 1px solid var(--primary-dark); /* Border matches primary color */
            color: var(--primary-dark); 
            border-radius: 8px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer; 
            transition: all 0.3s ease;
        }
        .bookmark-btn i { width: 20px; height: 20px; stroke-width: 2; }
        
        .bookmark-btn:hover { 
            background-color: #f5f3ff; /* Very light purple tint on hover */
        }
        
        /* Active State (Saved) */
        .bookmark-btn.bookmarked {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(78, 46, 207, 0.3);
        }
        .bookmark-btn.bookmarked i { 
            fill: var(--white); 
            stroke: var(--white);
        }


        .listing-info-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            padding: 24px 40px;
            background: var(--light-gray);
            border-bottom: 1px solid var(--border-color);
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .info-item i {
            width: 20px;
            height: 20px;
            color: var(--text-light);
            stroke-width: 2.5;
        }
        .info-item-text strong {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
        }
        .info-item-text span {
            font-size: 15px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .listing-details {
            padding: 32px 40px;
        }
        .listing-section {
            margin-bottom: 32px;
        }
        .listing-section h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 12px;
        }
        .listing-section p, .listing-section li {
            font-size: 15px;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        .listing-section ul {
            padding-left: 20px;
            list-style: disc;
        }
        .skill-tag {
            display: inline-block;
            background: var(--light-gray);
            color: var(--text-light);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        /* =================================
           NEW FOOTER STYLES (FROM INDEX.PHP)
           ================================= */
        .main-footer { background-color: var(--text-dark); color: var(--light-gray); padding-top: 50px; margin-top: 0; }
        .new-footer-container { display: flex; justify-content: space-between; gap: 30px; padding-bottom: 40px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .footer-col { flex: 1; min-width: 150px; }
        .footer-logo-link { display: block; margin-bottom: 10px; }
        .footer-img-logo { height: 55px; width: auto; max-width: 200px; object-fit: contain; filter: invert(1) hue-rotate(180deg); }
        .brand-tagline { font-size: 14px; color: #a0aec0; margin-bottom: 25px; max-width: 200px; }
        .footer-heading { font-size: 18px; font-weight: 600; color: var(--white); margin-bottom: 20px; }
        .footer-links-list { list-style: none; padding: 0; margin: 0; }
        .footer-links-list li { margin-bottom: 12px; }
        .footer-links-list a { color: #a0aec0; font-size: 15px; transition: color 0.3s ease; }
        .footer-links-list a:hover { color: var(--primary-light); text-decoration: underline; }
        .social-links { display: flex; gap: 15px; }
        .social-links a { color: #a0aec0; transition: color 0.3s ease, transform 0.3s ease; }
        .social-links a:hover { color: var(--primary-light); transform: translateY(-2px); }
        .social-links svg { width: 20px; height: 20px; fill: currentColor; }
        .footer-bottom { padding: 20px 0; }
        .footer-bottom .footer-container { display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #a0aec0; }

        /* Mobile Fixes */
        @media (max-width: 900px) {
            .logo img { height: 50px; }
            .new-footer-container { flex-direction: column; text-align: center; }
            .footer-logo-link { margin: 0 auto 10px; }
            .brand-tagline { margin: 0 auto 25px; }
            .social-links { justify-content: center; }
            .footer-bottom .footer-container { flex-direction: column; gap: 10px; }
        }
        
        /* =================================
           NEW: APPLY MODAL CSS
           ================================= */
        .apply-modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .apply-modal-overlay.show {
            display: flex;
            opacity: 1;
        }
        .apply-modal-content {
            background: var(--white);
            border-radius: 12px;
            width: 100%;
            max-width: 700px;
            box-shadow: var(--shadow-hover);
            transform: translateY(20px);
            transition: transform 0.3s ease;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        .apply-modal-overlay.show .apply-modal-content {
            transform: translateY(0);
        }
        .apply-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
        }
        .apply-modal-header h2 {
            font-size: 18px;
            font-weight: 500;
            color: var(--text-light);
        }
        .apply-modal-close {
            background: transparent; border: none; font-size: 28px;
            font-weight: 300; color: var(--text-light); cursor: pointer;
            line-height: 1;
        }
        
        .apply-modal-body {
            padding: 24px 32px;
            overflow-y: auto;
            flex-grow: 1;
        }
        
        /* Job Summary inside Modal */
        .job-summary {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }
        .job-summary-info { flex: 1; }
        .job-summary-info h3 {
            font-size: 22px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .job-summary-info p {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 12px;
        }
        .job-summary-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 15px;
            color: var(--text-light);
        }
        .job-summary-logo {
            width: 50px; height: 50px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background-color: #eef2ff; color: var(--primary-dark);
            font-weight: 600; display: flex;
            align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        
        /* Form Sections */
        .apply-section {
            margin-bottom: 24px;
        }
        .apply-section h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 12px;
        }
        
        /* Resume Section */
        .resume-box {
            background: var(--light-gray);
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            padding: 16px;
        }
        .resume-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .resume-box-header strong {
            font-weight: 600;
            color: var(--text-dark);
        }
        .resume-box-header a {
            font-size: 14px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        .resume-box-header a:hover { text-decoration: underline; }
        .resume-box p {
            font-size: 14px;
            color: var(--text-light);
        }
        
        /* Availability Section */
        .availability-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .availability-option {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        .availability-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-dark);
        }
        .availability-option label {
            font-size: 15px;
            color: var(--text-dark);
            cursor: pointer;
        }
        
        .apply-modal-footer {
            padding: 20px 32px;
            border-top: 1px solid var(--border-color);
            background: var(--white);
            text-align: right;
        }

    </style>
</head>
<body>
    
    <header class="main-header">
        <nav class="container main-nav">
            <a href="index.php" class="logo">
                <img src="logo.jpeg" alt="JobSure Logo" class="logo-img">
            </a>
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="internships.php">Internships</a></li>
                    <li><a href="jobs.php">Jobs</a></li>
                </ul>
                <div class="nav-buttons">
                    <div class="profile-dropdown">
                        <?php $initial = strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                        <button id="profile-menu-trigger" class="dropdown-trigger" title="My Account">
                            <?php echo htmlspecialchars($initial); ?>
                        </button>
                        <div id="profile-menu" class="dropdown-menu">
                            <div class="dropdown-header">
                                <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>
                                <small><?php echo htmlspecialchars($_SESSION["email"]); ?></small>
                            </div>
                            <a href="my-applications.php" class="dropdown-item">My Applications</a>
                            <a href="my-bookmarks.php" class="dropdown-item">My Bookmarks</a>
                            <a href="#" id="manage-account-toggle" class="dropdown-item dropdown-toggle">
                                <span>Manage Account</span>
                                <i data-lucide="chevron-down" class="dropdown-arrow"></i>
                            </a>
                            <div id="manage-account-submenu" class="dropdown-submenu">
                                <a href="profile.php" class="dropdown-item">Edit Profile</a>
                                <a href="#" class="dropdown-item">Change Password</a>
                                <a href="#" class="dropdown-item">Delete My Account</a>
                            </div>
                            <hr class="dropdown-divider">
                            <a href="logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="listing-layout">
            <div class="listing-header">
                <h1><?php echo htmlspecialchars($listing['job_title']); ?></h1>
                <h2><?php echo htmlspecialchars($listing['company_name']); ?></h2>
                
                <div class="listing-actions">
                    <button class="btn-apply-gradient <?php echo $listing['is_applied'] ? 'applied' : ''; ?>" id="apply-btn" <?php echo $listing['is_applied'] ? 'disabled' : ''; ?>>
                        <?php echo $listing['is_applied'] ? 'Applied' : 'Apply Now'; ?>
                    </button>
                    <button class="bookmark-btn <?php echo $listing['is_bookmarked'] ? 'bookmarked' : ''; ?>" id="bookmark-btn">
                        <i data-lucide="bookmark" <?php echo $listing['is_bookmarked'] ? 'fill="currentColor"' : 'fill="none"'; ?>></i>
                        <span><?php echo $listing['is_bookmarked'] ? 'Saved' : 'Save'; ?></span>
                    </button>
                </div>
            </div>

            <div class="listing-info-bar">
                <?php if ($listing['listing_type'] == 'internship' && !empty($listing['pay_details'])): ?>
                <div class="info-item">
                    <i data-lucide="wallet-2"></i>
                    <div class="info-item-text"><strong>Stipend</strong><span><?php echo htmlspecialchars($listing['pay_details']); ?></span></div>
                </div>
                <?php elseif ($listing['listing_type'] == 'job' && !empty($listing['pay_details'])): ?>
                <div class="info-item">
                    <i data-lucide="wallet-2"></i>
                    <div class="info-item-text"><strong>Salary</strong><span><?php echo htmlspecialchars($listing['pay_details']); ?></span></div>
                </div>
                <?php endif; ?>

                <?php if (!empty($listing['location'])): ?>
                <div class="info-item">
                    <i data-lucide="map-pin"></i>
                    <div class="info-item-text"><strong>Location</strong><span><?php echo htmlspecialchars($listing['location']); ?></span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($listing['listing_type'] == 'internship' && !empty($listing['duration'])): ?>
                <div class="info-item">
                    <i data-lucide="calendar"></i>
                    <div class="info-item-text"><strong>Duration</strong><span><?php echo htmlspecialchars($listing['duration']); ?></span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($listing['listing_type'] == 'job' && !empty($listing['experience'])): ?>
                <div class="info-item">
                    <i data-lucide="briefcase"></i>
                    <div class="info-item-text"><strong>Experience</strong><span><?php echo htmlspecialchars($listing['experience']); ?></span></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="listing-details">
                <?php if (!empty($listing['process_details'])): ?>
                <div class="listing-section">
                    <h3>About the <?php echo $listing['listing_type']; ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($listing['process_details'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($listing['skills'])): ?>
                <div class="listing-section">
                    <h3>Skill(s) required</h3>
                    <div class="skills-container">
                        <?php echo formatSkills($listing['skills']); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($listing['qualification'])): ?>
                <div class="listing-section">
                    <h3>Who can apply</h3>
                    <ul>
                        <li>Must have a qualification of: <?php echo htmlspecialchars($listing['qualification']); ?></li>
                        <li>Gender: <?php echo htmlspecialchars($listing['gender']); ?></li>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($listing['contact_person'])): ?>
                <div class="listing-section">
                    <h3>Contact</h3>
                    <p>
                        <strong><?php echo htmlspecialchars($listing['contact_person']); ?></strong>
                        <?php if (!empty($listing['contact_phone'])): ?>
                        <br><?php echo htmlspecialchars($listing['contact_phone']); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container new-footer-container">
            
            <div class="footer-col brand-col">
                <a href="index.php" class="footer-logo-link">
                    <img src="logo.jpeg" alt="JobSure Logo" class="footer-img-logo">
                </a>
                <p class="brand-tagline">Connecting talent with opportunity.</p>
                <div class="social-links">
                    <a href="#" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.298-4 0v5.604h-3v-11h3v1.765c1.396-2.423 6-2.355 6 3.197v6.038z"/></svg></a>
                    <a href="#" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.791-1.574 2.162-2.722-.951.555-2.005.959-3.127 1.184-1.203-1.272-2.904-2.073-4.793-2.073-3.623 0-6.561 2.939-6.561 6.563 0 .514.057 1.016.173 1.496-5.45-2.744-10.29-5.787-13.535-9.186-.563.987-.88 2.155-.88 3.39 0 2.278 1.159 4.28 2.915 5.464-.852-.027-1.65-.262-2.352-.648v.083c0 3.196 2.274 5.86 5.29 6.471-.555.152-1.137.23-1.734.23-.424 0-.834-.041-1.234-.117.844 2.622 3.284 4.536 6.182 4.588-2.251 1.767-5.088 2.825-8.15 2.825-.531 0-1.05-.031-1.564-.094 2.917 1.867 6.368 2.955 10.05 2.955 12.067 0 18.675-9.878 18.675-18.681 0-.301-.006-.598-.018-.895 1.282-.924 2.398-2.075 3.288-3.398z"/></svg></a>
                    <a href="#" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.353c-.761 0-.877.309-.877.747v1.171h2.234l-.291 2.28h-1.943v6.171h-3v-6.171h-1.899v-2.28h1.899v-1.525c0-2.227 1.189-3.329 3.298-3.329l1.699.006v2.071z"/></svg></a>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Job Seekers</h4>
                <ul class="footer-links-list">
                    <li><a href="jobs.php">Browse All Jobs</a></li>
                    <li><a href="internships.php">Internships</a></li>
                    <li><a href="my-applications.php">My Applications</a></li>
                    <li><a href="my-bookmarks.php">Saved Jobs</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4 class="footer-heading">Company</h4>
                <ul class="footer-links-list">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4 class="footer-heading">Legal</h4>
                <ul class="footer-links-list">
                    <li><a href="privacy_policy.php">Privacy Policy</a></li>
                    <li><a href="terms_of_service.php">Terms of Service</a></li>
                    <li><a href="security_policy.php">Security Policy</a></li>
                    <li><a href="help_center.php">Help Center</a></li>
                </ul>
            </div>

        </div>
        
        <div class="footer-bottom">
            <div class="container footer-container">
                <p>&copy; 2025 JobSure. All rights reserved.</p>
                <p class="developed-by">Developed with ❤️ in India</p>
            </div>
        </div>
    </footer>
    
    <div id="apply-modal" class="apply-modal-overlay">
        <div class="apply-modal-content">
            <div class="apply-modal-header">
                <h2>Applying to <?php echo htmlspecialchars($listing['job_title']); ?></h2>
                <button id="close-apply-modal" class="apply-modal-close">&times;</button>
            </div>
            
            <form id="apply-form">
                <div class="apply-modal-body">
                    <div class="job-summary">
                        <div class="job-summary-info">
                            <h3><?php echo htmlspecialchars($listing['job_title']); ?></h3>
                            <p><?php echo htmlspecialchars($listing['company_name']); ?></p>
                            <div class="job-summary-details">
                                <?php if (!empty($listing['pay_details'])): ?>
                                    <span><i data-lucide="wallet-2" style="width:16px; height:16px; vertical-align: middle; margin-right: 5px;"></i> <?php echo htmlspecialchars($listing['pay_details']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($listing['location'])): ?>
                                    <span><i data-lucide="map-pin" style="width:16px; height:16px; vertical-align: middle; margin-right: 5px;"></i> <?php echo htmlspecialchars($listing['location']); ?></span>
                                <?php endif; ?>
                                <?php if ($listing['listing_type'] == 'internship' && !empty($listing['duration'])): ?>
                                    <span><i data-lucide="calendar" style="width:16px; height:16px; vertical-align: middle; margin-right: 5px;"></i> <?php echo htmlspecialchars($listing['duration']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="job-summary-logo">
                            <?php echo htmlspecialchars(strtoupper(substr($listing['company_name'], 0, 2))); ?>
                        </div>
                    </div>
                    
                    <div class="apply-section">
                        <h4>Your resume</h4>
                        <div class="resume-box">
                            <div class="resume-box-header">
                                <strong><?php echo htmlspecialchars($user_name); ?>'s Resume</strong>
                                <a href="profile.php">Edit resume</a>
                            </div>
                            <?php if (!empty($profile_data['resume_path'])): ?>
                                <p>Your current resume (<?php echo htmlspecialchars(basename($profile_data['resume_path'])); ?>) will be submitted.</p>
                            <?php else: ?>
                                <p style="color: var(--danger-red); font-weight: 500;">You have not uploaded a resume. Please go to your profile to add one.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="apply-section">
                        <h4>Confirm your availability</h4>
                        <div class="availability-group">
                            <div class="availability-option">
                                <input type="radio" id="avail-1" name="availability" value="Yes, I am available to join immediately" checked>
                                <label for="avail-1">Yes, I am available to join immediately</label>
                            </div>
                            <div class="availability-option">
                                <input type="radio" id="avail-2" name="availability" value="No, I am currently on notice period">
                                <label for="avail-2">No, I am currently on notice period</label>
                            </div>
                            <div class="availability-option">
                                <input type="radio" id="avail-3" name="availability" value="No, I will have to serve notice period">
                                <label for="avail-3">No, I will have to serve notice period</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="apply-modal-footer">
                    <button type="submit" id="submit-application-btn" class="btn-apply-gradient">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
    <script>
      lucide.createIcons();
      
      // (Dropdown JS)
      const profileTrigger = document.getElementById('profile-menu-trigger');
      const profileMenu = document.getElementById('profile-menu');
      if (profileTrigger && profileMenu) {
          profileTrigger.addEventListener('click', (e) => { e.stopPropagation(); profileMenu.classList.toggle('show'); });
          window.addEventListener('click', (e) => {
              if (profileMenu.classList.contains('show') && !profileMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
                  profileMenu.classList.remove('show');
              }
          });
      }
      const manageAccountToggle = document.getElementById('manage-account-toggle');
      const manageAccountSubmenu = document.getElementById('manage-account-submenu');
      if (manageAccountToggle && manageAccountSubmenu) {
          manageAccountToggle.addEventListener('click', (e) => {
              e.preventDefault(); e.stopPropagation();
              manageAccountToggle.classList.toggle('open');
              manageAccountSubmenu.classList.toggle('show');
          });
      }
      
      // --- UPDATED: Apply & Bookmark JS ---
      
      const jobId = "<?php echo $listing['id']; ?>";

      const applyBtn = document.getElementById('apply-btn');
      const bookmarkBtn = document.getElementById('bookmark-btn');
      const applyModal = document.getElementById('apply-modal');
      const closeApplyModalBtn = document.getElementById('close-apply-modal');
      const applyForm = document.getElementById('apply-form');
      
      function showApplyModal() { applyModal.classList.add('show'); }
      function hideApplyModal() { applyModal.classList.remove('show'); }

      if (applyBtn) {
          applyBtn.addEventListener('click', () => {
              if (applyBtn.disabled) return;
              showApplyModal(); 
          });
      }
      
      if (closeApplyModalBtn) closeApplyModalBtn.addEventListener('click', hideApplyModal);
      
      if (applyModal) {
          applyModal.addEventListener('click', (e) => {
              if (e.target === applyModal) hideApplyModal();
          });
      }
      
      if (applyForm) {
          applyForm.addEventListener('submit', async (e) => {
              e.preventDefault();
              const formData = new FormData(applyForm);
              formData.append('job_id', jobId);
              
              try {
                  const response = await fetch('api_apply_job.php', { method: 'POST', body: formData });
                  const text = await response.text(); // Get raw text first
                  try {
                      const result = JSON.parse(text); // Try to parse it
                      if (result.success) {
                          hideApplyModal();
                          applyBtn.textContent = 'Applied';
                          applyBtn.classList.add('applied');
                          applyBtn.disabled = true;
                      } else {
                          alert(result.message);
                      }
                  } catch (jsonError) {
                      console.error("Server Error (Non-JSON response):", text);
                      alert("Server error. Check console for details.");
                  }
              } catch (error) { 
                  console.error(error);
                  alert('Network error occurred.'); 
              }
          });
      }
      
      // --- Bookmark Click (Improved Error Handling) ---
      if (bookmarkBtn) {
          bookmarkBtn.addEventListener('click', async () => {
              const formData = new FormData();
              formData.append('job_id', jobId);
              
              try {
                  const response = await fetch('api_toggle_bookmark.php', { method: 'POST', body: formData });
                  
                  // 1. Read response as text first to debug
                  const text = await response.text();
                  
                  // 2. Try to parse JSON
                  let result;
                  try {
                      result = JSON.parse(text);
                  } catch (e) {
                      console.error("CRITICAL: Server returned invalid JSON:", text);
                      throw new Error("Server response was not valid JSON. See console.");
                  }
                  
                  if (result.success) {
                      const icon = bookmarkBtn.querySelector('i');
                      const textSpan = bookmarkBtn.querySelector('span');
                      if (result.status === 'added') {
                          bookmarkBtn.classList.add('bookmarked');
                          textSpan.textContent = 'Saved';
                          icon.setAttribute('fill', 'currentColor'); // Fix for Lucide
                      } else {
                          bookmarkBtn.classList.remove('bookmarked');
                          textSpan.textContent = 'Save';
                          icon.setAttribute('fill', 'none'); // Fix for Lucide
                      }
                      // Re-render icons if needed, though setAttribute usually handles SVG
                      lucide.createIcons(); 
                  } else {
                      alert(result.message);
                  }
              } catch (error) { 
                  console.error("Fetch error:", error);
                  // Only alert if it's a real error, not just a UI glitch
                  // alert('An error occurred: ' + error.message); 
              }
          });
      }
    </script>
</body>
</html>