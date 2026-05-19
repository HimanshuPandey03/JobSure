<?php
session_start(); // Start the session to check for login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - JobSure</title> 
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
        
        /* --- SCROLLBAR FIX --- */
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
           2. Header & Navigation (MATCHES INDEX.PHP)
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
        .nav-links a[href="contact.php"] { 
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

        /* Profile Dropdown CSS */
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
        .contact-section { padding: 80px 0; }
        .contact-grid {
            display: flex;
            gap: 40px;
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }
        .contact-form-wrapper { flex: 2; }
        .contact-info-wrapper {
            flex: 1;
            background: var(--light-gray);
            border-radius: 8px;
            padding: 30px;
        }
        .contact-form-wrapper h2,
        .contact-info-wrapper h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 24px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 3px rgba(78, 46, 207, 0.2);
        }
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }
        .info-item .icon {
            width: 24px;
            height: 24px;
            color: var(--primary-dark);
            margin-top: 3px;
        }
        .info-item h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 2px;
        }
        .info-item p, .info-item a {
            font-size: 15px;
            color: var(--text-light);
            word-break: break-word;
        }
        .info-item a:hover { color: var(--primary-dark); }
        
        /* =================================
           4. NEW Professional Footer Styles
           ================================= */
        .main-footer { 
            background-color: var(--text-dark); /* #2d3748 */
            color: var(--light-gray); /* #f9fafb */
            padding-top: 50px; 
            /* margin-top: 0; (Overwritten from previous file to match design) */
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
            .cta-button { padding: 6px 12px; font-size: 12px; margin-left: 0; }
            
            /* Page Specific Mobile Fixes */
            .contact-grid {
                flex-direction: column;
                gap: 30px;
                padding: 20px;
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
                <h1>Get In Touch</h1>
            </div>
        </section>
        <section class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <div class="contact-form-wrapper">
                        <h2>Send us a message</h2>
                        <form action="#" method="POST">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" class="form-input" required></textarea>
                            </div>
                            <button type="submit" class="cta-button btn-solid-primary">
                                Send Message
                            </button>
                        </form>
                    </div>
                    <div class="contact-info-wrapper">
                        <h2>Contact Information</h2>
                        <div class="info-item">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <div>
                                <h3>Our Office</h3>
                                <p>513, Magic Square, Dwarkadhish Road, Malad East, Mumbai- 400097</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <div>
                                <h3>Email Us</h3>
                                <a href="mailto:work.jobsure@gmail.com">work.jobsure@gmail.com</a>
                            </div>
                        </div>
                        <div class="info-item">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.211-.998-.552-1.348l-3.675-3.675a2.25 2.25 0 0 0-3.182 0l-1.99 1.99-1.562-1.562a16.5 16.5 0 0 1-6.26-6.26l1.562-1.562 1.99-1.99a2.25 2.25 0 0 0 0-3.182L6.14 3.052A2.25 2.25 0 0 0 5.798 2.5H4.5A2.25 2.25 0 0 0 2.25 4.75v2Z" />
                            </svg>
                            <div>
                                <h3>Call Us</h3>
                                <a href="tel:+919867172527">+91 98671 72527</a>
                            </div>
                        </div>
                    </div>
                </div>
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