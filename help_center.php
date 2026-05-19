<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - JobSure</title> 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script> 
    <style>
        /* =================================
           1. Global Styles & Variables (Full Set from Index)
           ================================= */
        :root {
            --primary-light: #6e48ff; --primary-dark: #4e2ecf;
            --gradient: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
            --white: #ffffff; --light-gray: #f9fafb; --border-color: #e7eaf3;
            --text-dark: #2d3748; --text-light: #5a677d;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.05); --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.1);
            --login-gradient: linear-gradient(to right, #00d0ff, #7c3aed);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { overflow-y: scroll; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--text-dark); line-height: 1.6; overflow-x: hidden; display: flex; flex-direction: column; min-height: 100vh; }
        main { flex: 1; width: 100%; }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* =================================
           2. HEADER & NAVIGATION (MATCHES INDEX.PHP)
           ================================= */
        .main-header { background-color: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .main-nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; }
        .logo img { height: 65px; width: auto; max-width: 250px; object-fit: contain; }
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

        /* Highlight current page: Help Center */
        .nav-links a[href="help_center.php"] { color: var(--primary-dark); font-weight: 600; }
        
        /* Buttons/Dropdown Styles */
        .nav-buttons a { padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 15px; border: 2px solid; }
        .btn-outline-primary { color: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-solid-primary { background-color: var(--primary-dark); color: var(--white); border-color: var(--primary-dark); }
        .profile-dropdown { position: relative; }
        .dropdown-trigger { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-dark); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; border: none; cursor: pointer; }

        /* --- Mobile Nav Toggle (Hidden on desktop) --- */
        .mobile-menu-btn { display: none; font-size: 26px; background: none; border: none; cursor: pointer; color: var(--text-dark); padding-left: 5px; }

        /* PAGE SPECIFIC CONTENT STYLES */
        .page-hero { background: var(--gradient); color: var(--white); padding: 40px 0; text-align: center; }
        .page-hero h1 { font-size: 36px; font-weight: 700; }
        .content-section { padding: 60px 0; background-color: var(--white); }
        .help-center-wrapper { max-width: 850px; margin: 0 auto; }
        .search-bar-wrap { margin-bottom: 40px; text-align: center; }
        .search-input { width: 100%; max-width: 600px; padding: 15px 20px; border: 2px solid var(--border-color); border-radius: 50px; font-size: 16px; transition: all 0.3s; }
        .search-input:focus { outline: none; border-color: var(--primary-light); box-shadow: 0 0 0 4px rgba(110, 72, 255, 0.1); }

        .faq-category h2 { font-size: 24px; font-weight: 700; color: var(--primary-dark); margin-top: 30px; margin-bottom: 25px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; }

        .faq-item { background: var(--light-gray); border-radius: 8px; margin-bottom: 15px; overflow: hidden; }
        .faq-question { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; font-size: 17px; font-weight: 600; color: var(--text-dark); cursor: pointer; transition: background 0.3s; }
        .faq-question:hover { background: #e0e7ff; }
        .faq-answer { padding: 0 20px; font-size: 15px; color: var(--text-light); max-height: 0; transition: max-height 0.3s ease-out, padding 0.3s ease-out; overflow: hidden; }
        .faq-answer p { padding-bottom: 15px; margin-top: 5px; }
        .faq-item.active .faq-answer { max-height: 300px; /* Needs to be larger than content */ padding-top: 10px; }
        .faq-item.active .faq-question { background: var(--primary-dark); color: var(--white); }
        .faq-item.active .faq-question i { transform: rotate(180deg); color: var(--white); }
        .faq-question i { color: var(--primary-dark); transition: transform 0.3s; }

        /* FOOTER STYLES (MATCHES INDEX.PHP) */
        .main-footer { background-color: var(--text-dark); color: var(--light-gray); padding-top: 50px; }
        .new-footer-container { display: flex; justify-content: space-between; gap: 30px; padding-bottom: 40px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .footer-col { flex: 1; min-width: 150px; }
        
        /* Existing text logo class */
        .footer-logo { font-size: 28px; font-weight: 700; color: var(--white); display: block; margin-bottom: 10px; }
        
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

        .brand-tagline { font-size: 14px; color: #a0aec0; margin-bottom: 25px; max-width: 200px; }
        .footer-heading { font-size: 18px; font-weight: 600; color: var(--white); margin-bottom: 20px; }
        .footer-links-list { list-style: none; padding: 0; margin: 0; }
        .footer-links-list li { margin-bottom: 12px; }
        .footer-links-list a { color: #a0aec0; font-size: 15px; transition: color 0.3s ease; }
        .footer-links-list a[href*="help_center.php"] { color: var(--primary-light); text-decoration: underline; font-weight: 600; }
        .footer-links-list a:hover { color: var(--primary-light); text-decoration: underline; }
        .social-links { display: flex; gap: 15px; }
        .social-links a { color: #a0aec0; transition: color 0.3s ease, transform 0.3s ease; }
        .social-links a:hover { color: var(--primary-light); transform: translateY(-2px); }
        .social-links svg { width: 20px; height: 20px; fill: currentColor; }
        .footer-bottom { padding: 20px 0; }
        .footer-bottom .footer-container { display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #a0aec0; }
        
        /* MOBILE RESPONSIVE FIXES */
        @media (max-width: 900px) {
            .logo img { height: 32px; }
            .nav-right { gap: 10px; }
            .nav-buttons a { padding: 6px 12px; font-size: 12px; }
            .nav-links { display: none; position: absolute; top: 65px; left: 0; width: 100%; background: var(--white); padding: 0; border-top: 1px solid var(--border-color); flex-direction: column; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 99; }
            .nav-links.show { display: flex; }
            .nav-links li { width: 100%; border-bottom: 1px solid #f0f0f0; padding: 0; }
            .nav-links a { display: block; padding: 15px 0; font-size: 16px; width: 100%; }
            .mobile-menu-btn { display: block; }
            .new-footer-container { flex-direction: column; gap: 35px; padding-bottom: 30px; text-align: center; }
            .footer-col { text-align: center; min-width: unset; }
            
            /* Center the new image logo on mobile */
            .footer-logo-link { text-align: center; margin: 0 auto 10px; } 

            .footer-logo { margin: 0 auto 10px; }
            .brand-tagline { margin: 0 auto 25px; }
            .social-links { justify-content: center; }
            .footer-links-list { display: flex; flex-wrap: wrap; justify-content: center; gap: 5px 15px; }
            .footer-links-list li { margin-bottom: 0; }
            .footer-bottom .footer-container { flex-direction: column; gap: 10px; }
            .developed-by { display: none; }
            .search-input { padding: 12px 20px; }
            .faq-question { font-size: 16px; padding: 12px 15px; }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <nav class="container main-nav">
            <a href="index.php" class="logo">
                <img src="logo.jpeg" alt="JobSure Logo">
            </a>
            
            <ul class="nav-links" id="desktop-nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="internships.php">Internships</a></li>
                <li><a href="jobs.php">Jobs</a></li>
            </ul>

            <div class="nav-right">
                <div class="nav-buttons">
                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <div class="profile-dropdown">
                            <?php $initial = strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                            <button id="profile-menu-trigger" class="dropdown-trigger" title="My Account"><?php echo htmlspecialchars($initial); ?></button>
                            </div>
                        <a href="logout.php" class="cta-button btn-outline-primary">Logout</a>
                    <?php else: ?>
                        <a href="index.php?action=login" class="cta-button btn-outline-primary">Login</a>
                        <a href="index.php?action=register" class="cta-button btn-solid-primary">Register</a>
                    <?php endif; ?>
                </div>
                 <button id="mobile-menu-toggle" class="mobile-menu-btn">&#9776;</button>
            </div>
        </nav>
    </header>

    <main>
        <section class="page-hero">
            <div class="container">
                <h1>JobSure Help Center</h1>
            </div>
        </section>
        <section class="content-section">
            <div class="container help-center-wrapper">
                
                <div class="search-bar-wrap">
                    <input type="text" id="help-search" class="search-input" placeholder="Search for FAQs, jobs, or accounts...">
                </div>

                <div class="faq-category">
                    <h2>Account & Registration</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I create an account? <i data-lucide="chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Click the 'Register' button in the top right corner. You will need to provide your name, email, and a secure password. You must also complete a brief assessment to access job listings.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            Why is the assessment mandatory? <i data-lucide="chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>The assessment ensures that only serious and qualified candidates access our curated job listings, helping us maintain high quality for both job seekers and employers.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            I forgot my password. How do I reset it? <i data-lucide="chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>On the login modal, click the 'Forgot password?' link. You will be prompted to enter your registered email to receive an OTP for verification.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-category">
                    <h2>Jobs & Applications</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I apply for a job? <i data-lucide="chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Once you find a job you like, click 'Apply Now' on the job details page. Your saved profile and resume will be used for a fast, one-click application process.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            How do I save a job for later? <i data-lucide="chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>On the job listing page, click the 'Bookmark' or 'Save Job' icon. You can view all saved jobs under the 'My Bookmarks' section in your profile dropdown menu.</p>
                        </div>
                    </div>
                </div>

                <p style="text-align: center; margin-top: 40px; font-size: 16px;">
                    Can't find your answer? <a href="contact.php" style="color: var(--primary-dark); font-weight: 600;">Contact our support team</a>.
                </p>

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
        // Placeholder/simplified dropdown JS for legal pages
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
        
        // FAQ Accordion functionality
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const faqItem = button.closest('.faq-item');
                const answer = faqItem.querySelector('.faq-answer');
                const isActive = faqItem.classList.contains('active');

                // Close all other open FAQs
                document.querySelectorAll('.faq-item.active').forEach(item => {
                    if (item !== faqItem) {
                        item.classList.remove('active');
                    }
                });

                // Toggle current FAQ
                if (!isActive) {
                    faqItem.classList.add('active');
                } else {
                    faqItem.classList.remove('active');
                }
            });
        });
        // Mobile menu toggle logic
        document.getElementById("mobile-menu-toggle").onclick = function () {
            document.getElementById("desktop-nav-links").classList.toggle("show");
        };
    </script>
</body>
</html>