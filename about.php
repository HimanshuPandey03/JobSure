<?php
session_start(); // Start the session to check for login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - JobSure</title> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script> 
    <style>
        /* =================================
           1. Global Styles & Variables
           ================================= */
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
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* --- SCROLLBAR FIX: Prevents horizontal shifting --- */
        html { overflow-y: scroll; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden; 
        }
        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px;
        }
        a { text-decoration: none; color: inherit; }
        .section-heading {
            text-align: center;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 48px;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* =================================
           2. Header & Navigation 
           ================================= */
        .main-header { 
            background-color: var(--white); 
            border-bottom: 1px solid var(--border-color); 
            padding: 20px 0; 
            position: sticky; 
            top: 0; 
            z-index: 100; 
        }
        .main-nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        
        /* Logo Styles */
        .logo { display: flex; align-items: center; }
        .logo img {
            height: 70px;  /* KEPT AT 70PX AS PER USER REQUEST NOT TO CHANGE HEADER */
            width: auto;
            max-width: 250px;
            object-fit: contain;
        }

        /* Nav Right Wrapper */
        .nav-right { 
            display: flex; 
            align-items: center; 
            gap: 28px; 
        }
        
        .nav-links { 
            display: flex; 
            list-style: none; 
            gap: 28px; 
        }
        .nav-links a {
            color: var(--text-light);
            font-weight: 500;
            position: relative;
            padding-bottom: 6px;
            transition: color 0.3s ease;
            font-size: 17px; 
        }
        .nav-links a[href="about.php"] { 
            color: var(--primary-dark);
            font-weight: 600;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--primary-dark);
            transition: all 0.3s ease;
        }
        .nav-links a:hover { color: var(--primary-dark); }
        .nav-links a:hover::after { width: 100%; left: 0; }
        
        /* Login/Register Buttons */
        .cta-button { padding: 8px 24px; border-radius: 8px; font-weight: 600; font-size: 16px; border: 2px solid; transition: all 0.3s ease; margin-left: 10px; cursor: pointer; }
        .btn-outline-primary { background-color: var(--white); color: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-solid-primary { background-color: var(--primary-dark); color: var(--white); border-color: var(--primary-dark); }
        .cta-button:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .btn-outline-primary:hover { background-color: var(--primary-dark); color: var(--white); }
        .btn-solid-primary:hover { background-color: var(--primary-light); border-color: var(--primary-light); }

        /* Profile Dropdown CSS (Reused from Index) */
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

        /* =================================
           3. Page Specific Content
           ================================= */
        .page-hero {
            background: var(--gradient);
            color: var(--white);
            padding: 40px 0;
            text-align: center;
        }
        .page-hero h1 {
            font-size: 36px;
            font-weight: 700;
            animation: fadeInUp 0.8s ease forwards;
        }
        .content-section {
            padding: 80px 0;
            background-color: var(--white); 
        }
        .content-wrapper {
            max-width: 800px;
            margin: 0 auto;
            color: var(--text-light);
            font-size: 17px;
        }
        .content-wrapper h2 {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
        }
        .content-wrapper p { margin-bottom: 20px; }
        .features {
            padding: 80px 0;
            background-color: var(--light-gray);
        }
        .features-container {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .feature-card {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            box-shadow: var(--shadow);
            flex-basis: 350px; 
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }
        .team-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 4px solid var(--border-color);
        }
        .feature-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .feature-card p {
            color: var(--primary-dark);
            font-size: 15px;
            font-weight: 500;
        }

        /* =================================
           4. NEW Professional Footer Styles
           ================================= */
        .main-footer { 
            background-color: var(--text-dark); /* #2d3748 */
            color: var(--light-gray); /* #f9fafb */
            padding-top: 50px; 
        }
        
        .new-footer-container { 
            display: flex; 
            justify-content: space-between; 
            gap: 30px; 
            padding-bottom: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-col {
            flex: 1; 
            min-width: 150px;
        }
        
        /* Existing text logo class */
        .footer-logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
            display: block;
            margin-bottom: 10px;
        }
        
        /* NEW STYLES FOR IMAGE LOGO IN FOOTER */
        .footer-logo-link {
            display: block; 
            margin-bottom: 10px;
        }
        
        .footer-img-logo {
            height: 55px; /* Set to 55px */
            width: auto;
            max-width: 200px;
            object-fit: contain;
            /* Invert colors for dark background to ensure visibility */
            filter: invert(1) hue-rotate(180deg); 
        }

        .brand-tagline {
            font-size: 14px;
            color: #a0aec0; 
            margin-bottom: 25px;
            max-width: 200px;
        }

        .footer-heading {
            font-size: 18px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 20px;
        }

        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links-list li {
            margin-bottom: 12px;
        }

        .footer-links-list a {
            color: #a0aec0;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .footer-links-list a:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }
        
        /* Social Media Icons */
        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: #a0aec0;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .social-links svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        
        /* Footer Bottom (Copyright) */
        .footer-bottom {
            padding: 20px 0;
        }
        
        .footer-bottom .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #a0aec0;
        }
        .developed-by {
            /* For desktop */
        }

        /* Mobile Responsive adjustments */
        @media (max-width: 900px) {
            /* Header Mobile Fixes */
             .logo img { height: 50px; }
            .nav-links { display: none; }
            .nav-right { gap: 10px; }
            .cta-button { padding: 6px 12px; font-size: 12px; margin-left: 0; }
            
            /* Footer Mobile Fixes */
            .main-footer { padding-top: 40px; }
            
            .new-footer-container {
                flex-direction: column;
                gap: 35px; 
                padding-bottom: 30px;
                text-align: center; 
            }
            
            .footer-col { text-align: center; min-width: unset; }
            
            /* Centering for image logo on mobile */
            .footer-logo-link { text-align: center; margin: 0 auto 10px; } 
            
            .footer-logo { text-align: center; margin: 0 auto 10px; }
            
            .brand-tagline { margin: 0 auto 25px; }
            
            .social-links { justify-content: center; }

            .footer-links-list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 5px 15px;
            }
            
            .footer-links-list li { margin-bottom: 0; }

            .footer-bottom .footer-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .developed-by { display: none; }
        }
        /* ==========================================================
           RESPONSIVE FIXES FOR MOBILE (MATCHING INDEX.PHP)
           ========================================================== */
        
        /* HIDE hamburger by default on Desktop */
        .mobile-menu-btn { display: none; }

        /* MOBILE BREAKPOINT */
        @media (max-width: 900px) {

            /* --- Header Fixes --- */
            .main-header { padding: 10px 0; }
            
            .logo img {
                height: 28px; 
                width: auto;
            }

            .nav-right { gap: 5px; }
            
            /* Nav Links - Mobile Menu */
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--white);
                padding: 0;
                border-top: 1px solid var(--border-color);
                flex-direction: column;
                text-align: center;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                z-index: 999;
            }

            .nav-links.show { display: flex; }
            
            .nav-links li { width: 100%; border-bottom: 1px solid #f0f0f0; }
            .nav-links a { display: block; padding: 15px 0; font-size: 16px; width: 100%; }

            /* Hamburger Icon */
            .mobile-menu-btn {
                display: block;
                font-size: 26px;
                background: none;
                border: none;
                cursor: pointer;
                color: var(--text-dark);
                padding-left: 5px;
            }

            /* --- Page Content Fixes --- */
            .features-container {
                display: flex;
                flex-direction: column;
                gap: 20px;
                padding: 0 10px;
            }
            .feature-card {
                flex-basis: auto;
                width: 100%; 
                max-width: 100%;
            }
            
            /* Text Padding */
            .content-section { padding: 40px 0; }
            .content-wrapper { padding: 0 10px; text-align: left; }
            
            /* Footer */
            .footer-container {
                display: flex;
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }
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
                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
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
                                <a href="change_password.php" class="dropdown-item">Change Password</a>
                                <a href="delete_account.php" class="dropdown-item" style="color: #dc2626;">Delete My Account</a>
                            </div>
                                <hr class="dropdown-divider">
                                <a href="logout.php" class="dropdown-item">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                         <a href="index.php?action=login" class="cta-button btn-outline-primary">Login</a>
                         <a href="index.php?action=register" class="cta-button btn-solid-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="page-hero">
            <div class="container">
                <h1>About JobSure</h1>
            </div>
        </section>
        <section class="content-section">
            <div class="container content-wrapper">
                <h2>Our Mission</h2>
                <p>At JobSure, our mission is to build a bridge between exceptional talent and innovative companies. We believe that finding a fulfilling job shouldn't be a chore. That's why we've created a platform that is clean, fast, and free of spam.</p>
                <p>We focus on quality listings and a seamless user experience, ensuring that both job seekers and employers find exactly what they're looking for. We're passionate about helping people grow their careers and helping companies build great teams.</p>
            </div>
        </section>
       
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
    <script>
      lucide.createIcons();
      
      // Only run this script if the user is logged in
      <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
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
      <?php endif; ?>
    </script>
    </body>
</html>
<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- STRICT CHECK: HIDE POPUP IF ASSESSMENT IS DONE ---
$show_assessment_alert = false;
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Check if explicitly marked as completed (1)
    $is_completed = isset($_SESSION["quiz_completed"]) && $_SESSION["quiz_completed"] == 1;

    // Only show if NOT completed
    if (!$is_completed) {
        $show_assessment_alert = true;
    }
}
?>

<?php if ($show_assessment_alert): ?>
<style>
    @keyframes floatCard { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    .assessment-mission-card {
        position: fixed; bottom: 30px; left: 30px;
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6); border-left: 5px solid #6e48ff;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15); padding: 20px 25px;
        border-radius: 16px; max-width: 340px; z-index: 9998;
        
        /* HIDDEN INITIALLY */
        transform: translateY(150%); opacity: 0;
        transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.5s ease;
        
        display: flex; flex-direction: column; gap: 12px;
    }
    .assessment-mission-card.show { transform: translateY(0); opacity: 1; }
    
    .mission-header { display: flex; align-items: center; gap: 12px; }
    .mission-icon-bg {
        background: linear-gradient(135deg, #6e48ff, #977eff); width: 45px; height: 45px;
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        color: white; box-shadow: 0 5px 15px rgba(110, 72, 255, 0.3);
        animation: floatCard 3s ease-in-out infinite;
    }
    .mission-title { font-size: 16px; font-weight: 700; color: #2d3748; line-height: 1.2; }
    .mission-desc { font-size: 13px; color: #5a677d; line-height: 1.4; }
    .mission-btn {
        background: linear-gradient(90deg, #6e48ff, #4e2ecf); color: white;
        text-align: center; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 14px;
        text-decoration: none; transition: transform 0.2s; display: block; margin-top: 5px;
    }
    .mission-btn:hover { transform: scale(1.03); box-shadow: 0 5px 15px rgba(78, 46, 207, 0.25); }
    .mission-close {
        position: absolute; top: 8px; right: 10px; background: none; border: none; color: #a0aec0;
        cursor: pointer; font-size: 18px;
    }
    @media(max-width: 480px) {
        .assessment-mission-card { left: 50%; transform: translateX(-50%) translateY(150%); bottom: 85px; width: 90%; }
        .assessment-mission-card.show { transform: translateX(-50%) translateY(0); }
    }
</style>

<div class="assessment-mission-card" id="assessment-mission-card">
    <button class="mission-close" id="mission-close-btn">&times;</button>
    <div class="mission-header">
        <div class="mission-icon-bg">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.1 2.76-.46 3.39-.09a2.18 2.18 0 0 1 0 2.9A2.19 2.19 0 0 0 9 12z"></path><path d="M15 12a2.18 2.18 0 0 0 .94-1.85c.38-.63 1-1.78-.09-3.4-1-1.45-4-2-4-2s.56 3.25 2 4.75"></path></svg>
        </div>
        <div>
            <div class="mission-title">Career Mission Pending</div>
            <div class="mission-desc">Unlock your perfect job match.</div>
        </div>
    </div>
    <a href="assessment.php" class="mission-btn">🚀 Launch Assessment</a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const missionCard = document.getElementById('assessment-mission-card');
    const closeBtn = document.getElementById('mission-close-btn');
    
    // Time in milliseconds (60000 = 1 minute)
    const REAPPEAR_TIME = 60000;

    if (missionCard && closeBtn) {
        
        // Function to show
        const showPopup = () => { missionCard.classList.add('show'); };

        // Function to hide and loop
        const hidePopup = (e) => { 
            if(e) { e.preventDefault(); e.stopPropagation(); }
            
            missionCard.classList.remove('show'); 
            
            // Reappear after 1 minute
            setTimeout(showPopup, REAPPEAR_TIME); 
        };

        // Initial Show (2 seconds after load)
        setTimeout(showPopup, 2000); 

        // Attach listener
        closeBtn.addEventListener('click', hidePopup);
    }
});
</script>
<?php endif; ?>