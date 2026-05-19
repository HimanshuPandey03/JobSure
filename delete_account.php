<?php
session_start();
require_once 'db_config.php';

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

// 2. HANDLE ACCOUNT DELETION
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['confirm_delete']) || isset($_POST['delete_token']))) {
    
    if (isset($_POST['confirm_keyword']) && $_POST['confirm_keyword'] === 'DELETE') {
        
        $conn->begin_transaction();
        try {
            // Delete related data first
            $conn->query("DELETE FROM bookmarks WHERE user_id = $user_id");
            $conn->query("DELETE FROM applications WHERE user_id = $user_id");
            $conn->query("DELETE FROM profiles WHERE user_id = $user_id");
            
            // Delete the user
            $conn->query("DELETE FROM users WHERE id = $user_id");
            
            $conn->commit();
            
            // Destroy session and redirect
            $_SESSION = array();
            session_destroy();
            header("location: index.php?status=account_deleted");
            exit;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = "Error: Could not delete account. " . $e->getMessage();
        }
    } else {
        $error_msg = "You must type 'DELETE' exactly to confirm.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - JobSure</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script> 

    <style>
        /* =================================
           1. GLOBAL STYLES
           ================================= */
        :root {
            --primary-light: #6e48ff;
            --primary-dark: #4e2ecf;
            --white: #ffffff;
            --light-gray: #f9fafb;
            --border-color: #e7eaf3;
            --text-dark: #2d3748;
            --text-light: #5a677d;
            --danger-red: #D90429;
            --danger-bg: #fff1f2;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.1);
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
        
        main {
            flex: 1;                  
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }
        
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }

        /* =================================
           2. HEADER STYLES
           ================================= */
        .main-header { background-color: var(--white); border-bottom: 1px solid var(--border-color); padding: 20px 0; position: sticky; top: 0; z-index: 100; }
        .main-nav { display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; }
        .logo img { height: 70px; width: auto; max-width: 250px; object-fit: contain; }
        
        .nav-right { display: flex; align-items: center; gap: 28px; }
        .nav-links { display: flex; list-style: none; gap: 28px; }
        .nav-links a { color: var(--text-light); font-weight: 500; position: relative; padding-bottom: 6px; transition: color 0.3s ease; font-size: 17px; }
        .nav-links a::after { content: ''; position: absolute; bottom: 0; left: 50%; width: 0; height: 2px; background-color: var(--primary-dark); transition: all 0.3s ease; }
        .nav-links a:hover { color: var(--primary-dark); }
        .nav-links a:hover::after { width: 100%; left: 0; }
        
        .profile-dropdown { position: relative; }
        .dropdown-trigger { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-dark); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s ease; }
        .dropdown-menu { display: none; position: absolute; top: 55px; right: 0; min-width: 240px; background: var(--white); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-hover); z-index: 1001; overflow: hidden; }
        .dropdown-menu.show { display: block; animation: fadeInUp 0.2s ease forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-header { padding: 15px 20px; border-bottom: 1px solid var(--border-color); }
        .dropdown-header strong { display: block; color: var(--text-dark); font-weight: 600; }
        .dropdown-header small { color: var(--text-light); font-size: 13px; word-break: break-all; }
        .dropdown-item { display: block; padding: 12px 20px; color: var(--text-dark); font-size: 15px; font-weight: 500; transition: all 0.2s ease; }
        .dropdown-item:hover { background-color: var(--light-gray); color: var(--primary-dark); }
        .dropdown-divider { border: 0; height: 1px; background-color: var(--border-color); margin: 0; }
        .dropdown-item.dropdown-toggle { display: flex; justify-content: space-between; align-items: center; }
        .dropdown-submenu { display: none; background: var(--light-gray); }
        .dropdown-submenu.show { display: block; }
        .dropdown-submenu .dropdown-item { padding-left: 35px; }

        /* =================================
           3. DELETE CARD STYLES
           ================================= */
        .delete-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .delete-stripe { height: 8px; background-color: var(--danger-red); width: 100%; }
        .delete-body { padding: 40px; text-align: center; }

        .icon-circle {
            width: 80px; height: 80px;
            background-color: var(--danger-bg);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-circle i { color: var(--danger-red); width: 40px; height: 40px; }

        .delete-title { font-size: 24px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .delete-desc { color: var(--text-light); font-size: 14px; margin-bottom: 30px; line-height: 1.6; }
        
        .form-group { margin-bottom: 24px; }
        .input-label { display: block; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        
        .delete-input {
            width: 100%; padding: 14px; border: 2px solid var(--border-color);
            border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 18px;
            text-align: center; letter-spacing: 2px; text-transform: uppercase;
            font-weight: 600; color: var(--danger-red); transition: all 0.3s ease;
        }
        .delete-input:focus { outline: none; border-color: var(--danger-red); box-shadow: 0 0 0 4px rgba(217, 4, 41, 0.1); }

        .btn-actions { display: flex; flex-direction: column; gap: 12px; }
        
        .btn-delete {
            width: 100%; padding: 14px; border-radius: 10px; border: none;
            font-size: 16px; font-weight: 600; font-family: 'Poppins', sans-serif;
            cursor: pointer; transition: all 0.3s ease;
            background-color: #e2e8f0; color: #94a3b8;
        }
        
        .btn-delete.active {
            background-color: var(--danger-red); color: var(--white);
            box-shadow: 0 4px 12px rgba(217, 4, 41, 0.3);
        }
        .btn-delete.active:hover { transform: translateY(-2px); background-color: #b90324; }

        .btn-cancel {
            display: block; width: 100%; padding: 14px;
            border-radius: 10px; border: 1px solid var(--border-color);
            font-size: 15px; font-weight: 500; font-family: 'Poppins', sans-serif;
            background-color: var(--white); color: var(--text-light);
            text-align: center; transition: all 0.3s ease;
        }
        .btn-cancel:hover { background-color: var(--light-gray); color: var(--text-dark); }

        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .shake { animation: shake 0.3s ease-in-out 3; }
        
        @keyframes dissolve { to { opacity: 0; transform: scale(0.95); filter: blur(4px); } }
        .dissolving { animation: dissolve 1s forwards; pointer-events: none; }
        
        .error-box { background: var(--danger-bg); color: var(--danger-red); padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 20px; }

        /* =================================
           4. NEW MODAL (POPUP) STYLES
           ================================= */
        .confirm-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            display: none;
            align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .confirm-modal-overlay.show { display: flex; opacity: 1; }
        
        .confirm-modal {
            background: var(--white);
            border-radius: 12px;
            width: 90%; max-width: 400px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            transform: scale(0.9); transition: transform 0.3s ease;
        }
        .confirm-modal-overlay.show .confirm-modal { transform: scale(1); }
        
        .confirm-icon {
            width: 60px; height: 60px;
            background: #fee2e2; color: var(--danger-red);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .confirm-modal h3 { font-size: 20px; font-weight: 600; color: var(--text-dark); margin-bottom: 10px; }
        .confirm-modal p { color: var(--text-light); font-size: 14px; margin-bottom: 25px; line-height: 1.5; }
        
        .confirm-actions { display: flex; gap: 15px; }
        .btn-modal-cancel {
            flex: 1; padding: 12px; border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--white); color: var(--text-dark);
            font-weight: 500; cursor: pointer;
        }
        .btn-modal-confirm {
            flex: 1; padding: 12px; border-radius: 8px;
            border: none;
            background: var(--danger-red); color: var(--white);
            font-weight: 600; cursor: pointer;
        }
        .btn-modal-confirm:hover { background: #b90324; }

        /* =================================
           5. FOOTER STYLES
           ================================= */
        .main-footer { background-color: var(--text-dark); color: var(--light-gray); padding-top: 50px; margin-top: auto; }
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
        .social-links a { color: #a0aec0; transition: color 0.3s ease; }
        .social-links a:hover { color: var(--primary-light); transform: translateY(-2px); }
        .social-links svg { width: 20px; height: 20px; fill: currentColor; }
        .footer-bottom { padding: 20px 0; }
        .footer-bottom .footer-container { display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #a0aec0; }

        @media (max-width: 900px) {
            .nav-links { display: none; }
            .new-footer-container { flex-direction: column; text-align: center; gap: 35px; }
            .footer-col { text-align: center; min-width: unset; }
            .footer-logo-link, .brand-tagline { margin-left: auto; margin-right: auto; }
            .social-links { justify-content: center; }
            .footer-bottom .footer-container { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <nav class="container main-nav">
            <a href="index.php" class="logo">
                <img src="logo.jpeg" alt="JobSure Logo">
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
                        <a href="my-bookmarks.php" class="dropdown-item">Saved Jobs</a>
                        <a href="#" id="manage-account-toggle" class="dropdown-item dropdown-toggle">
                            <span>Manage Account</span>
                            <i data-lucide="chevron-down"></i>
                        </a>
                        <div id="manage-account-submenu" class="dropdown-submenu">
                            <a href="profile.php" class="dropdown-item">Edit Profile</a>
                            <a href="#" class="dropdown-item">Change Password</a>
                            <a href="delete_account.php" class="dropdown-item" style="color: #D90429;">Delete My Account</a>
                        </div>
                        <hr class="dropdown-divider">
                        <a href="logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div id="main-card" class="delete-card">
            <div class="delete-stripe"></div>
            <div class="delete-body">
                <div class="icon-circle">
                    <i data-lucide="ghost"></i>
                </div>
                <h1 class="delete-title">We're sorry to see you go</h1>
                <p class="delete-desc">
                    Deleting your account is <strong>permanent</strong>. You will lose your profile, saved jobs, and assessment certificates. This cannot be undone.
                </p>

                <?php if (isset($error_msg)): ?>
                    <div class="error-box shake">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <form id="delete-form" method="POST" action="delete_account.php">
                    <input type="hidden" name="delete_token" value="1">
                    
                    <div class="form-group">
                        <label class="input-label">Type "DELETE" to confirm</label>
                        <input type="text" id="confirm_keyword" name="confirm_keyword" 
                               class="delete-input" placeholder="DELETE" autocomplete="off">
                    </div>

                    <div class="btn-actions">
                        <button type="submit" name="confirm_delete" id="delete-btn" disabled class="btn-delete">
                            Delete My Account
                        </button>
                        <a href="profile.php" class="btn-cancel">
                            Cancel, keep my account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <div id="confirm-modal-overlay" class="confirm-modal-overlay">
        <div class="confirm-modal">
            <div class="confirm-icon">
                <i data-lucide="alert-triangle" style="width:32px; height:32px;"></i>
            </div>
            <h3>Are you absolutely sure?</h3>
            <p>This action cannot be undone. All your data including applications and saved jobs will be permanently removed.</p>
            <div class="confirm-actions">
                <button id="modal-cancel-btn" class="btn-modal-cancel">No, Cancel</button>
                <button id="modal-confirm-btn" class="btn-modal-confirm">Yes, Delete It</button>
            </div>
        </div>
    </div>

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

        // Dropdown Script
        const profileTrigger = document.getElementById('profile-menu-trigger');
        const profileMenu = document.getElementById('profile-menu');
        if (profileTrigger && profileMenu) {
            profileTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('show');
            });
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

        // Delete Functionality & Modal Logic
        const input = document.getElementById('confirm_keyword');
        const deleteBtn = document.getElementById('delete-btn');
        const form = document.getElementById('delete-form');
        const mainCard = document.getElementById('main-card');
        
        // Modal Elements
        const modalOverlay = document.getElementById('confirm-modal-overlay');
        const modalCancel = document.getElementById('modal-cancel-btn');
        const modalConfirm = document.getElementById('modal-confirm-btn');

        input.addEventListener('input', function() {
            if (this.value === 'DELETE') {
                deleteBtn.disabled = false;
                deleteBtn.classList.add('active');
                deleteBtn.innerText = "Confirm Deletion";
            } else {
                deleteBtn.disabled = true;
                deleteBtn.classList.remove('active');
                deleteBtn.innerText = "Delete My Account";
            }
        });

        // 1. Intercept form submit -> Show Modal
        form.addEventListener('submit', function(e) {
            if (input.value === 'DELETE') {
                e.preventDefault(); // Stop immediate submit
                modalOverlay.classList.add('show'); // Show the popup
            }
        });

        // 2. Handle Cancel
        modalCancel.addEventListener('click', function() {
            modalOverlay.classList.remove('show');
        });

        // 3. Handle Confirm -> Proceed with Deletion Animation
        modalConfirm.addEventListener('click', function() {
            modalOverlay.classList.remove('show'); // Hide modal
            
            // Update UI to show "Deleting..." state
            deleteBtn.innerText = "Deleting...";
            deleteBtn.style.opacity = '0.7';
            deleteBtn.style.cursor = 'wait';
            mainCard.classList.add('dissolving');
            
            // Wait for animation then submit
            setTimeout(() => { form.submit(); }, 1200);
        });
        
        // Close modal if clicking outside
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) modalOverlay.classList.remove('show');
        });
    </script>
</body>
</html>