<?php
session_start();
require 'db_config.php';

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];
$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_msg = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_msg = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_msg = "New password must be at least 6 characters long.";
    } else {
        $sql = "SELECT password FROM users WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();
                if (password_verify($current_password, $hashed_password)) {
                    $new_param_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                    if ($update_stmt = $conn->prepare($update_sql)) {
                        $update_stmt->bind_param("si", $new_param_password, $user_id);
                        if ($update_stmt->execute()) {
                            $success_msg = "Password changed successfully!";
                        } else {
                            $error_msg = "Something went wrong. Please try again.";
                        }
                        $update_stmt->close();
                    }
                } else {
                    $error_msg = "Incorrect current password.";
                }
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - JobSure</title>
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
            --danger-red: #D90429;
            --success-green: #10b981;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            color: var(--text-dark); 
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            
            /* --- NEW BACKGROUND EFFECT --- */
            background-color: #f8f9fc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(110, 72, 255, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(217, 4, 41, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(110, 72, 255, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            background-size: 100% 100%;
        }
        
        main { flex: 1; width: 100%; display: flex; justify-content: center; align-items: center; padding: 60px 20px; }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* =================================
           2. Header & Navigation
           ================================= */
        .main-header { background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-color); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .main-nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; }
        .logo img { height: 70px; width: auto; max-width: 250px; object-fit: contain; }
        .nav-right { display: flex; align-items: center; gap: 28px; }
        .nav-links { display: flex; list-style: none; gap: 28px; }
        .nav-links a { color: var(--text-light); font-weight: 500; position: relative; padding-bottom: 6px; transition: color 0.3s ease; font-size: 17px; }
        .nav-links a:hover { color: var(--primary-dark); }
        
        /* Profile Dropdown */
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
           3. Footer Styles
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

        /* =================================
           4. Form Card Styles + NEW ANIMATIONS
           ================================= */
        @keyframes cardEntrance {
            0% { opacity: 0; transform: translateY(30px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95); /* Slightly transparent */
            backdrop-filter: blur(12px); /* Glass effect */
            padding: 40px;
            border-radius: 20px; /* Rounder corners */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06); /* Softer, deeper shadow */
            width: 100%;
            max-width: 480px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            
            /* Entrance Animation */
            animation: cardEntrance 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .auth-title { font-size: 24px; font-weight: 700; color: var(--text-dark); text-align: center; margin-bottom: 10px; }
        .auth-subtitle { font-size: 14px; color: var(--text-light); text-align: center; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-wrapper { position: relative; }
        .input-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); width: 20px; height: 20px; }
        .form-input { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; font-family: 'Poppins', sans-serif; transition: all 0.3s ease; background: #f8f9fc; }
        .form-input:focus { outline: none; border-color: var(--primary-light); background: var(--white); box-shadow: 0 0 0 3px rgba(110, 72, 255, 0.1); }
        .password-toggle { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-light); }
        
        .btn-primary { width: 100%; background: var(--gradient); color: var(--white); border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: 10px; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(110, 72, 255, 0.3); }
        
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-error { background-color: #fee2e2; color: var(--danger-red); border: 1px solid #fecaca; }
        .alert-success { background-color: #d1fae5; color: var(--success-green); border: 1px solid #a7f3d0; }

        /* =================================
           5. Cancel Button Style (FIXED)
           ================================= */
        .cancel-link {
            display: block !important;
            width: 100%;
            text-align: center;
            padding: 12px 0;
            margin-top: 15px;
            background-color: #f3f4f6;
            color: #4b5563;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .cancel-link:hover {
            background-color: #e5e7eb;
            color: #111827; 
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* Mobile Fixes */
        @media (max-width: 900px) {
            .nav-links { display: none; }
            .logo img { height: 50px; }
            .new-footer-container { flex-direction: column; text-align: center; }
            .footer-logo-link { margin: 0 auto 10px; }
            .brand-tagline { margin: 0 auto 25px; }
            .social-links { justify-content: center; }
            .footer-bottom .footer-container { flex-direction: column; gap: 10px; }
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
                        <a href="#" id="manage-account-toggle" class="dropdown-item dropdown-toggle" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Manage Account</span>
                            <i data-lucide="chevron-down" style="width:16px;"></i>
                        </a>
                        <div id="manage-account-submenu" class="dropdown-submenu">
                            <a href="profile.php" class="dropdown-item">Edit Profile</a>
                            <a href="change_password.php" class="dropdown-item" style="background-color: #f3f4f6; color: var(--primary-dark); font-weight: 600;">Change Password</a>
                            <a href="delete_account.php" class="dropdown-item" style="color: #dc2626;">Delete My Account</a>
                        </div>
                        <hr class="dropdown-divider">
                        <a href="logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="auth-card">
            <div class="text-center" style="margin-bottom: 25px;">
                 <div style="width:60px; height:60px; background:#eef2ff; color:var(--primary-dark); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; border:1px solid #e0e7ff;">
                    <i data-lucide="lock-keyhole" style="width:30px; height:30px;"></i>
                </div>
                <h1 class="auth-title">Change Password</h1>
                <p class="auth-subtitle">Create a new, strong password for your account.</p>
            </div>

            <?php if($error_msg): ?>
                <div class="alert alert-error">
                    <i data-lucide="alert-circle" width="20"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <?php if($success_msg): ?>
                <div class="alert alert-success">
                    <i data-lucide="check-circle-2" width="20"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <div class="input-wrapper">
                        <i data-lucide="key" class="input-icon"></i>
                        <input type="password" name="current_password" class="form-input" placeholder="Enter current password" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <div class="input-wrapper">
                        <i data-lucide="lock" class="input-icon"></i>
                        <input type="password" name="new_password" id="newPass" class="form-input" placeholder="Min. 6 characters" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('newPass', 'eye1')">
                            <i data-lucide="eye" id="eye1" width="18"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <div class="input-wrapper">
                        <i data-lucide="check-circle" class="input-icon"></i>
                        <input type="password" name="confirm_password" id="confPass" class="form-input" placeholder="Re-enter new password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confPass', 'eye2')">
                            <i data-lucide="eye" id="eye2" width="18"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Update Password</button>
                
                <a href="index.php" class="cancel-link">Cancel & Go Back</a>
                
            </form>
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

        // Dropdown Toggle
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

        // Password Visibility Toggle
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const iconElement = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                iconElement.setAttribute('data-lucide', 'eye-off'); 
            } else {
                input.type = "password";
                iconElement.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>     