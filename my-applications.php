<?php
session_start(); 
require 'db_config.php'; 

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['login_error'] = "You must be logged in to view your applications.";
    header("location: index.php");
    exit;
}

$user_id = $_SESSION['id'];

// 2. FETCH APPLIED-TO LISTINGS
try {
    // MODIFIED: Fetched the new 'availability' column
    $sql = "SELECT jobs.*, applications.status, applications.applied_at, applications.availability 
            FROM jobs 
            INNER JOIN applications ON jobs.id = applications.job_id
            WHERE applications.user_id = ?
            ORDER BY applications.applied_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
} catch (Exception $e) {
    $applications = [];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - JobSure</title>
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
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--light-gray); 
            color: var(--text-dark); 
            line-height: 1.6; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .main-header { background-color: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .main-nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 700; color: var(--primary-dark); }
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
        main { flex-grow: 1; }
        .page-content-wrapper { max-width: 800px; margin: 0 auto; }
        .page-title { font-size: 42px; font-weight: 700; color: var(--text-dark); margin: 40px 0; position: relative; text-align: center; display: block; }
        .page-title::after { content: ''; position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); width: 200px; height: 6px; background: var(--gradient); border-radius: 3px; }
        .job-card { background: var(--white); border-radius: 12px; box-shadow: var(--shadow); padding: 24px; margin-bottom: 20px; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .job-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
        .job-card-title-wrap { flex: 1; }
        .job-card-title { font-size: 20px; font-weight: 600; color: var(--text-dark); display: inline-block; }
        .job-card-company { color: var(--text-light); font-weight: 500; margin-top: 4px; }
        .job-card-logo { width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--border-color); background-color: #eef2ff; color: var(--primary-dark); font-weight: 600; display: flex; align-items: center; justify-content: center; font-size: 18px; text-transform: uppercase; }
        .job-card-details { display: flex; flex-wrap: wrap; gap: 12px 24px; color: var(--text-light); font-size: 15px; margin-bottom: 20px; }
        .detail-item { display: flex; align-items: center; gap: 8px; }
        .detail-item i { width: 18px; height: 18px; stroke-width: 2.5; color: var(--text-light); }
        .job-card-footer { display: flex; justify-content: space-between; align-items: center; }
        .btn-apply-gradient { background: var(--login-gradient); color: var(--white); padding: 10px 24px; border-radius: 8px; font-weight: 500; font-size: 15px; transition: all 0.3s ease; border: none; }
        .status-tag {
            background-color: #dbeafe; color: #1e40af;
            padding: 4px 12px; border-radius: 20px; font-size: 13px;
            font-weight: 500;
        }
        .availability-info {
            font-size: 14px;
            color: var(--text-light);
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed var(--border-color);
        }
        .empty-state-container {
            text-align: center;
            padding: 100px 20px;
            background-color: var(--white);
            max-width: 800px; 
            margin: 40px auto; 
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }
        .empty-state-icon {
            color: var(--primary-dark);
            opacity: 0.6;
            margin-bottom: 30px;
        }
        .empty-state-container h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 12px;
        }
        .empty-state-container p {
            font-size: 16px;
            color: var(--text-light);
            max-width: 450px;
            margin: 0 auto 30px;
        }
        .btn-gradient {
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            color: var(--white);
            border: none;
            cursor: pointer;
            background: var(--login-gradient);
            transition: all 0.3s ease;
            display: inline-block;
        }
        .btn-gradient:hover {
            opacity: 0.9;
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }
        .main-footer { 
            background-color: #2d3748; 
            color: #a0aec0; 
            padding: 40px 0; 
            margin-top: 40px;
        }
        .footer-container { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .footer-container p { font-size: 15px; }
        .footer-links { 
            list-style: none; 
            padding-left: 0; 
            display: flex; 
            gap: 24px; 
        }
        .footer-links li { margin-bottom: 0; }
        .footer-links a { 
            font-size: 15px; 
            font-weight: 500; 
            color: #a0aec0; 
            transition: color 0.3s ease; 
        }
        .footer-links a:hover { 
            color: var(--white); 
            text-decoration: none; 
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
            <a href="index.php" class="logo">JobSure</a>
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
        <div class="page-content-wrapper">
            <h1 class="page-title">My Applications</h1>

            <section class="job-listings">

                <?php if (empty($applications)): ?>
                    <div class="empty-state-container">
                        <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 14 1.45-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 2 2v3.64a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5.24a2 2 0 0 1 1.79 1.1L13 7h7a2 2 0 0 1 2 2v1"/>
                        </svg>
                        <h1><?php echo htmlspecialchars($_SESSION["username"]); ?>, you don't have any applications yet.</h1>
                        <p>Start applying to boost your career with top hiring companies on JobSure.</p>
                        <a href="jobs.php" class="btn-gradient" style="margin-right: 10px;">Browse Jobs</a>
                        <a href="internships.php" class="btn-gradient">Browse Internships</a>
                    </div>

                <?php else: ?>
                    <?php foreach ($applications as $app): ?>
                        <div class="job-card">
                            <div class="job-card-header">
                                <div class="job-card-title-wrap">
                                    <span class="job-card-title"><?php echo htmlspecialchars($app['job_title']); ?></span>
                                    <div class="job-card-company"><?php echo htmlspecialchars($app['company_name']); ?></div>
                                </div>
                                <div class="job-card-logo">
                                    <?php echo htmlspecialchars(strtoupper(substr($app['company_name'], 0, 2))); ?>
                                </div>
                            </div>
                            <div class="job-card-details">
                                <?php if (!empty($app['location'])): ?>
                                    <span class="detail-item"><i data-lucide="map-pin"></i> <?php echo htmlspecialchars($app['location']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($app['pay_details'])): ?>
                                    <span class="detail-item"><i data-lucide="wallet-2"></i> <?php echo htmlspecialchars($app['pay_details']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($app['availability'])): ?>
                            <div class="availability-info">
                                <strong>Your availability:</strong> <?php echo htmlspecialchars($app['availability']); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="job-card-footer" style="margin-top: 20px;">
                                <span class="status-tag">
                                    Status: <?php echo htmlspecialchars($app['status']); ?>
                                </span>
                                <span class="posting-date">Applied on: <?php echo date('d M Y', strtotime($app['applied_at'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </section>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container footer-container">
            <p>&copy; 2025 JobSure. All rights reserved.</p>
            <ul class="footer-links">
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
    </footer>
    
    <script>
      lucide.createIcons();
      
      // (Dropdown JS is unchanged)
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
</body>
</html>