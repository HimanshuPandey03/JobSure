<?php
session_start(); 
require 'db_config.php'; 

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['login_error'] = "You must be logged in to view that page.";
    header("location: index.php");
    exit;
}

// 2. QUIZ GATEKEEPER CHECK
if (!isset($_SESSION['quiz_completed']) || $_SESSION['quiz_completed'] != 1) {
    header("location: assessment.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Jobs - JobSure</title>
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
            --login-gradient: linear-gradient(to right, #00d0ff, #7c3aed);
            --danger-red: #D90429;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* --- FOOTER FIX: Make body a flex container --- */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--light-gray); 
            color: var(--text-dark); 
            line-height: 1.6;
            display: flex;            
            flex-direction: column;   
            min-height: 100vh;        
            overflow-x: hidden;
        }
        
        /* --- FOOTER FIX: Main grows to fill space --- */
        main {
            flex: 1;                  
            width: 100%;             
        }
        
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* =================================
           2. Header & Navigation
           ================================= */
        .main-header { background-color: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .main-nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { 
            display: flex; 
            align-items: center; 
        }
        .logo img {
            height: 70px;  
            width: auto;   
            max-width: 250px; 
            object-fit: contain;
        }
        .nav-right { display: flex; align-items: center; gap: 28px; }
        .nav-links { display: flex; list-style: none; gap: 28px; }
        .nav-links a {
            color: var(--text-light);
            font-weight: 500;
            position: relative;
            padding-bottom: 6px;
            transition: color 0.3s ease;
            font-size: 17px; 
        }
        .nav-links a::after { content: ''; position: absolute; bottom: 0; left: 50%; width: 0; height: 2px; background-color: var(--primary-dark); transition: all 0.3s ease; }
        .nav-links a:hover { color: var(--primary-dark); }
        .nav-links a:hover::after { width: 100%; left: 0; }
        
        /* ACTIVE LINK FOR JOBS PAGE */
        .nav-links a[href*="jobs.php"] { color: var(--primary-dark); font-weight: 600; }
        
        /* Profile Dropdown CSS (Reused) */
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
           3. Page Content & Filters
           ================================= */
        .page-content-wrapper { max-width: 800px; margin: 0 auto; }
        .page-title { font-size: 42px; font-weight: 700; color: var(--text-dark); margin: 40px 0; position: relative; text-align: center; display: block; }
        .page-title::after { content: ''; position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); width: 200px; height: 6px; background: var(--gradient); border-radius: 3px; }
        
        .filter-bar {
            background: var(--white); border-radius: 12px; box-shadow: var(--shadow);
            padding: 20px 24px; margin-bottom: 30px; display: flex;
            flex-wrap: wrap; gap: 20px; align-items: flex-end; 
        }
        .filter-group { flex: 1; min-width: 150px; }
        .filter-group.filter-search { flex-grow: 1.5; }
        .filter-label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text-dark); margin-bottom: 8px;
        }
        .filter-search-input,
        .filter-select {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border-color);
            border-radius: 8px; background-color: var(--light-gray);
            font-family: 'Poppins', sans-serif; font-size: 15px;
            color: var(--text-dark); transition: all 0.3s ease;
        }
        .filter-select {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%235a677d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center;
            background-size: 18px; cursor: pointer; padding-right: 40px;
        }
        .filter-search-input:focus,
        .filter-select:focus {
            outline: none; border-color: var(--primary-light);
            background-color: var(--white); 
            box-shadow: 0 0 0 2px rgba(110, 72, 255, 0.2);
        }

        .job-card-link {
            display: block;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .job-card-link:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }
        .job-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
        .job-card-title-wrap { flex: 1; }
        .job-card-title { font-size: 20px; font-weight: 600; color: var(--text-dark); display: inline-block; }
        .job-card-company { color: var(--text-light); font-weight: 500; margin-top: 4px; }
        .job-card-logo { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--border-color); background-color: #eef2ff; color: var(--primary-dark); font-weight: 600; display: flex; align-items: center; justify-content: center; font-size: 18px; text-transform: uppercase; }
        .job-card-details { display: flex; flex-wrap: wrap; gap: 12px 24px; color: var(--text-light); font-size: 15px; margin-bottom: 20px; }
        .detail-item { display: flex; align-items: center; gap: 8px; }
        .detail-item i { width: 18px; height: 18px; stroke-width: 2.5; color: var(--text-light); }
        .job-card-tags { display: flex; flex-wrap: wrap; gap: 8px; }
        .tag { background: var(--light-gray); color: var(--text-light); padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 500; }
        
        /* =================================
           4. NEW Professional Footer Styles
           ================================= */
        .main-footer { 
            background-color: var(--text-dark); /* #2d3748 */
            color: var(--light-gray); /* #f9fafb */
            padding-top: 50px; 
            margin-top: 0;
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

        /* Mobile Responsive adjustments (Added for Footer) */
        @media (max-width: 900px) {
            /* Header Mobile Fixes */
             .logo img { height: 50px; }
            .nav-links { display: none; }
            .nav-right { gap: 10px; }
            
            /* Filters Mobile Fixes */
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group {
                min-width: 100%;
            }

            /* Footer Mobile Fixes */
            .main-footer { padding-top: 40px; }
            
            .new-footer-container {
                flex-direction: column;
                gap: 35px; 
                padding-bottom: 30px;
                text-align: center; 
            }
            
            .footer-col { text-align: center; min-width: unset; }
            
            /* Add centering for image logo on mobile */
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
        /* --- NEW CARD STYLES (Add to <style>) --- */

/* 1. Wrapper behaves like a link but is a div */
.job-card-wrapper {
    background: var(--white);
    border-radius: 12px;
    box-shadow: var(--shadow);
    padding: 24px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    position: relative;
    cursor: pointer; /* Shows hand cursor */
}

.job-card-wrapper:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary-light);
}

/* 2. New Footer area for the button */
.job-card-footer {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: flex-end; /* Aligns button to right */
}

/* 3. The "Pill" Save Button */
.card-save-btn {
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: 50px; /* Pill shape */
    padding: 6px 16px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 10; /* Ensures it sits on top */
}

.card-save-btn i {
    width: 16px;
    height: 16px;
    transition: fill 0.2s ease;
}

/* Hover State */
.card-save-btn:hover {
    border-color: var(--primary-light);
    color: var(--primary-light);
    background-color: #f5f3ff; /* Light purple tint */
}

/* Active/Saved State */
.card-save-btn.saved {
    background-color: var(--primary-light);
    border-color: var(--primary-light);
    color: var(--white);
}
.card-save-btn.saved i {
    fill: var(--white);
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
                                <a href="change_password.php" class="dropdown-item">Change Password</a>
                                <a href="delete_account.php" class="dropdown-item" style="color: #dc2626;">Delete My Account</a>
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
        <div class="page-content-wrapper">
            <h1 class="page-title">Find Your Next Job</h1>

            <form id="filter-form">
                <aside class="filter-bar">
                    <div class="filter-group filter-search">
                        <label for="filter-search" class="filter-label">Search</label>
                        <input type="text" id="filter-search" name="search" class="filter-search-input" placeholder="Job title or company...">
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-location" class="filter-label">Location</label>
                        <select id="filter-location" name="location" class="filter-select">
                            <option value="">All Locations</option>
                            <option value="Work from home">Work from home</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Mumbai">Mumbai</option>
                            <option value="Bangalore">Bangalore</option>
                            <option value="Hyderabad">Hyderabad</option>
                            <option value="Chennai">Chennai</option>
                            <option value="Kolkata">Kolkata</option>
                            <option value="Pune">Pune</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-experience" class="filter-label">Experience</label>
                        <select id="filter-experience" name="experience" class="filter-select">
                            <option value="">All Experiences</option>
                            <option value="Fresher">Fresher</option>
                            <option value="1-3 Years">1-3 Years</option>
                            <option value="3+ Years">3+ Years</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-salary" class="filter-label">Salary (Monthly/Annual)</label>
                        <select id="filter-salary" name="salary" class="filter-select">
                            <option value="">Any Salary</option>
                            <option value="0-10000">0 - 10,000</option>
                            <option value="10000-25000">10,000 - 25,000</option>
                            <option value="25000-50000">25,000 - 50,000</option>
                            <option value="50000-100000">50,000 - 1,00,000</option>
                            <option value="100000+">1,00,000+</option>
                        </select>
                    </div>
                </aside>
            </form>

            <section class="job-listings" id="job-listings-container">
                <a href="#" class="job-card-link">
                    <div class="job-card-header">
                        <p style="text-align: center; color: var(--text-light);">Loading jobs...</p>
                    </div>
                </a>
            </section>
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
    <script>
      lucide.createIcons();
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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filter-form');
            const listingsContainer = document.getElementById('job-listings-container');
            
            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('search');
            
            if (searchTerm) {
                const searchInput = document.getElementById('filter-search');
                if (searchInput) {
                    searchInput.value = searchTerm;
                }
            }

            function createJobCard(job) {
                let tagsHTML = '';
                if (job.skills) {
                    job.skills.split(',').forEach(skill => {
                        if (skill.trim()) tagsHTML += `<span class="tag">${skill.trim()}</span>`;
                    });
                }
                const logoInitial = job.company_name ? job.company_name.substring(0, 2).toUpperCase() : '??';

                return `
                    <a href="view_listing.php?id=${job.id}" class="job-card-link">
                        <div class="job-card-header">
                            <div class="job-card-title-wrap">
                                <span class="job-card-title">${job.job_title}</span>
                                <div class="job-card-company">${job.company_name}</div>
                            </div>
                            <div class="job-card-logo">${logoInitial}</div>
                        </div>
                        <div class="job-card-details">
                            ${job.location ? `<span class="detail-item"><i data-lucide="map-pin"></i> ${job.location}</span>` : ''}
                            ${job.pay_details ? `<span class="detail-item"><i data-lucide="wallet-2"></i> ${job.pay_details}</span>` : ''}
                            ${job.experience ? `<span class="detail-item"><i data-lucide="briefcase"></i> ${job.experience}</span>` : ''}
                        </div>
                        <div class="job-card-tags">
                            ${tagsHTML}
                        </div>
                    </a>
                `;
            }

            async function fetchListings() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                try {
                    const response = await fetch(`api_fetch_jobs.php?${params.toString()}`);
                    const listings = await response.json();
                    listingsContainer.innerHTML = ''; 

                    if (!listings || listings.length === 0 || listings.error) {
                        
                        // --- FALLBACK LOGIC START ---
                        const currentSearch = formData.get('search');
                        
                        if (currentSearch && currentSearch.trim() !== '') {
                            // Display a friendly message (Styles updated to standard CSS)
                            listingsContainer.innerHTML = `
                                <div style="padding: 20px; margin-bottom: 24px; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; color: #1e40af; display: flex; align-items: flex-start; gap: 15px;">
                                    <i data-lucide="info" style="width:24px; height:24px; flex-shrink:0; stroke-width: 2;"></i>
                                    <div>
                                        <p style="font-weight: 600; margin-bottom: 5px;">No exact matches found for "<strong>${currentSearch}</strong>".</p>
                                        <p style="font-size: 14px;">Don't worry! We are showing <strong>all available jobs</strong> instead.</p>
                                    </div>
                                </div>
                            `;
                            
                            // FETCH ALL JOBS (Empty params)
                            const fallbackResponse = await fetch(`api_fetch_jobs.php`);
                            const fallbackListings = await fallbackResponse.json();
                            
                            if (fallbackListings && fallbackListings.length > 0) {
                                fallbackListings.forEach(listing => {
                                    const cardHTML = createJobCard(listing);
                                    listingsContainer.insertAdjacentHTML('beforeend', cardHTML);
                                });
                                lucide.createIcons();
                                return; 
                            }
                        }
                        // --- FALLBACK LOGIC END ---

                        // If standard filter (or even fallback failed)
                        listingsContainer.insertAdjacentHTML('beforeend', `
                            <div class="job-card-link" style="text-align: center; color: var(--text-light); cursor: default; border-color: transparent;">
                                No jobs found matching your criteria.
                            </div>
                        `);
                        return;
                    }

                    listings.forEach(listing => {
                        const cardHTML = createJobCard(listing);
                        listingsContainer.insertAdjacentHTML('beforeend', cardHTML);
                    });
                    lucide.createIcons();
                } catch (error) {
                    console.error('Error fetching listings:', error);
                    listingsContainer.innerHTML = `
                        <div class="job-card-link" style="text-align: center; color: var(--danger-red); cursor: default; border-color: transparent;">
                            Failed to load listings. Please try again.
                        </div>
                    `;
                }
            }

            let debounceTimer;
            function debouncedFetch() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchListings, 300); 
            }
            filterForm.querySelector('input[name="search"]').addEventListener('input', debouncedFetch);
            filterForm.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', fetchListings);
            });

            fetchListings();
        });
    </script>
</body>
</html>