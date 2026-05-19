<?php
// We check for login OR register errors
session_start(); 

$login_error_message = '';
if (isset($_SESSION['login_error'])) {
    $login_error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

$register_error_message = '';
if (isset($_SESSION['register_error'])) {
    $register_error_message = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}

// --- NEW LOGIC: Check if Assessment is Pending ---
// If the user has completed the assessment (quiz_completed == 1), $show_assessment_alert stays false.
$show_assessment_alert = false;
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // If quiz_completed is 0 or NULL, show the prompt. If 1, do not show.
    if (!isset($_SESSION["quiz_completed"]) || $_SESSION["quiz_completed"] == 0) {
        $show_assessment_alert = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobSure - Find Your Dream Job</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--text-dark); line-height: 1.6; overflow-x: hidden; }
        .container { max-width: 1140px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        .section-heading { text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 48px; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse { 0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); } 70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); } 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); } }

        /* =================================
           2. Header & Navigation
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
        .cta-button { padding: 8px 24px; border-radius: 8px; font-weight: 600; font-size: 16px; border: 2px solid; transition: all 0.3s ease; margin-left: 10px; cursor: pointer; }
        .btn-outline-primary { background-color: var(--white); color: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-solid-primary { background-color: var(--primary-dark); color: var(--white); border-color: var(--primary-dark); }
        .cta-button:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .btn-outline-primary:hover { background-color: var(--primary-dark); color: var(--white); }
        .btn-solid-primary:hover { background-color: var(--primary-light); border-color: var(--primary-light); }
        
        /* Hero & Features */
        .hero { background: var(--gradient); color: var(--white); padding: 80px 0; text-align: center; }
        .hero h1 { font-size: 48px; font-weight: 700; margin-bottom: 16px; animation: fadeInUp 0.8s ease forwards; opacity: 0; }
        .hero p { font-size: 18px; max-width: 600px; margin: 0 auto 32px; animation: fadeInUp 0.8s ease 0.2s forwards; opacity: 0; }
        .btn-light { background: var(--white); color: var(--primary-dark); font-size: 16px; font-weight: 600; padding: 14px 28px; animation: pulse 2s infinite; border: none; border-radius: 8px; }
        .btn-light:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .features { padding: 80px 0; }
        .features-container { display: flex; justify-content: center; gap: 30px; }
        .feature-card { background: var(--white); border: 1px solid var(--border-color); border-radius: 12px; padding: 32px; text-align: center; box-shadow: var(--shadow); flex-basis: 350px; transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-hover); }
        .feature-card .icon { width: 48px; height: 48px; color: var(--primary-dark); margin-bottom: 24px; transition: all 0.3s ease; }
        .feature-card:hover .icon { transform: scale(1.15); color: var(--primary-light); }
        .feature-card h3 { font-size: 20px; font-weight: 600; margin-bottom: 12px; }
        .feature-card p { color: var(--text-light); font-size: 15px; }
        
        /* Modals */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: var(--white); border-radius: 12px; width: 100%; max-width: 450px; position: relative; padding: 32px 40px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); text-align: center; transform: translateY(20px); transition: transform 0.3s ease; }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-close { position: absolute; top: 12px; right: 12px; background: transparent; border: none; font-size: 28px; font-weight: 300; color: var(--text-light); cursor: pointer; line-height: 1; }
        .modal-close:hover { color: var(--text-dark); }
        .modal-content h2 { font-size: 28px; font-weight: 700; color: var(--text-dark); margin-bottom: 12px; }
        .modal-content .modal-subtitle { font-size: 16px; color: var(--text-light); margin-bottom: 28px; }
        #modal-error-message, #modal-register-error-message { color: #D90429; font-weight: 500; font-size: 14px; margin-bottom: 15px; text-align: center; min-height: 1.2em; }
        .modal-form { text-align: left; }
        .form-group { margin-bottom: 20px; }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 15px; transition: all 0.3s ease; background-color: var(--light-gray); }
        .form-input:focus { outline: none; border-color: var(--primary-dark); background-color: var(--white); box-shadow: 0 0 0 3px rgba(78, 46, 207, 0.2); }
        .form-forgot-link { text-align: right; margin-top: -10px; margin-bottom: 24px; }
        .form-forgot-link a { font-size: 14px; color: var(--primary-dark); font-weight: 500; cursor: pointer; }
        .form-forgot-link a:hover { text-decoration: underline; }
        .btn-gradient { width: 100%; padding: 12px 28px; border-radius: 8px; font-weight: 600; font-size: 16px; color: var(--white); border: none; cursor: pointer; background: var(--login-gradient); transition: all 0.3s ease; }
        .btn-gradient:hover { opacity: 0.9; box-shadow: var(--shadow-hover); transform: translateY(-2px); }
        .btn-gradient:disabled { opacity: 0.7; cursor: not-allowed; }
        .form-switch-link { text-align: center; margin-top: 24px; font-size: 15px; color: var(--text-light); }
        .form-switch-link a { color: var(--primary-dark); font-weight: 600; cursor: pointer; }
        .form-switch-link a:hover { text-decoration: underline; }
        .form-terms-text { font-size: 12px; margin-top: 15px; margin-bottom: 24px; text-align: center; color: var(--text-light); }

        /* Profile Dropdown */
        .profile-dropdown { position: relative; }
        .dropdown-trigger { width: 40px; height: 40px; border-radius: 50%; background: var(--primary-dark); color: var(--white); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s ease; }
        .dropdown-trigger:hover { background: var(--primary-light); box-shadow: var(--shadow-hover); }
        .dropdown-menu { display: none; position: absolute; top: 55px; right: 0; min-width: 240px; background: var(--white); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-hover); z-index: 1001; overflow: hidden; }
        .dropdown-menu.show { display: block; animation: fadeInUp 0.2s ease forwards; }
        .dropdown-header { padding: 15px 20px; border-bottom: 1px solid var(--border-color); }
        .dropdown-header strong { display: block; color: var(--text-dark); font-weight: 600; line-height: 1.2; }
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

        /* Admin Modal */
        .admin-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 2000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
        .admin-modal-overlay.show { display: flex; opacity: 1; }
        .admin-modal-content { background: var(--white); border-radius: 12px; width: 100%; max-width: 400px; box-shadow: var(--shadow-hover); transform: translateY(20px); transition: transform 0.3s ease; }
        .admin-modal-overlay.show .admin-modal-content { transform: translateY(0); }
        .admin-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid var(--border-color); }
        .admin-modal-header h2 { font-size: 20px; font-weight: 600; color: var(--text-dark); }
        .admin-modal-close { background: transparent; border: none; font-size: 28px; font-weight: 300; color: var(--text-light); cursor: pointer; line-height: 1; }
        .admin-modal-body { padding: 24px; }
        #admin-error-message { color: var(--danger-red); font-size: 14px; font-weight: 500; min-height: 1.2em; display: block; margin-top: 10px; }
        .btn-admin-login { width: 100%; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 16px; color: var(--white); border: none; cursor: pointer; background: var(--primary-dark); transition: all 0.3s ease; margin-top: 10px; }
        .btn-admin-login:hover { background: var(--primary-light); box-shadow: var(--shadow-hover); }
        
        /* AI Chat */
        #ai-chat-bubble { width: 75px; height: 75px; border-radius: 50%; background: linear-gradient(135deg, #6c4bff, #8f6dff); box-shadow: 0 10px 25px rgba(108, 75, 255, 0.45); display: flex; justify-content: center; align-items: center; position: fixed; bottom: 25px; right: 25px; border: none; cursor: pointer; transition: 0.25s ease; padding: 0; z-index: 9999; }
        #ai-chat-bubble:hover { transform: scale(1.1); box-shadow: 0 12px 40px rgba(108, 75, 255, 0.7); }
        #ai-chat-bubble .ai-icon { width: 48px; height: 48px; object-fit: contain; filter: brightness(1.15); }
        .chat-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 1000; display: none; align-items: flex-end; justify-content: flex-end; opacity: 0; transition: opacity 0.3s ease; padding: 0 30px 100px 0; }
        .chat-modal-overlay.show { display: flex; opacity: 1; }
        .chat-modal-content { background: var(--white); border-radius: 12px; width: 100%; max-width: 420px; height: 70vh; max-height: 600px; box-shadow: var(--shadow-hover); display: flex; flex-direction: column; overflow: hidden; transform: translateY(20px); transition: transform 0.3s ease; }
        .chat-modal-overlay.show .chat-modal-content { transform: translateY(0); }
        .chat-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: var(--gradient); color: var(--white); flex-shrink: 0; }
        .chat-modal-header h3 { font-size: 20px; font-weight: 600; }
        .chat-modal-close { background: transparent; border: none; font-size: 28px; font-weight: 300; color: rgba(255, 255, 255, 0.8); cursor: pointer; line-height: 1; }
        .chat-messages { flex-grow: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; background-color: var(--light-gray); }
        .chat-message { padding: 12px 16px; border-radius: 10px; max-width: 80%; line-height: 1.4; font-size: 15px; }
        .chat-message.ai { background: var(--white); color: var(--text-dark); border: 1px solid var(--border-color); align-self: flex-start; }
        .chat-message.user { background: var(--primary-dark); color: var(--white); align-self: flex-end; }
        .chat-message.ai.typing { font-style: italic; color: var(--text-light); }
        .chat-input-area { flex-shrink: 0; padding: 16px 20px; border-top: 1px solid var(--border-color); background: var(--white); display: flex; gap: 10px; }
        .chat-input { flex-grow: 1; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 14px; font-family: 'Poppins', sans-serif; font-size: 15px; transition: all 0.3s ease; }
        .chat-input:focus { outline: none; border-color: var(--primary-dark); box-shadow: 0 0 0 3px rgba(78, 46, 207, 0.2); }
        .chat-send-btn { width: 44px; height: 44px; border: none; background: var(--primary-dark); color: var(--white); border-radius: 8px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: background 0.3s ease; }
        .chat-send-btn:hover { background: var(--primary-light); }
        .chat-send-btn .icon { width: 22px; height: 22px; }

        /* =================================
           3. NEW Professional Footer Styles
           ================================= */
        .main-footer { 
            background-color: var(--text-dark); /* #2d3748 */
            color: var(--light-gray); /* #f9fafb */
            padding-top: 50px; 
            margin-top: 0; /* Remove old margin */
        }
        
        /* New main container for the columns */
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

        .footer-logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
            display: block;
            margin-bottom: 10px;
        }
        
        /* NEW STYLES FOR IMAGE LOGO IN FOOTER */
        .footer-logo-link {
            display: block; /* Ensure it behaves like a block element */
            margin-bottom: 10px;
        }
        
        .footer-img-logo {
            /* Ensure the image scales properly */
            height: 55px; /* Adjust height for the footer */
            width: auto;
            max-width: 150px;
            object-fit: contain;
            /* Invert colors for dark background (assuming logo.jpeg is bright) */
            filter: invert(1) hue-rotate(180deg); 
        }

        .brand-tagline {
            font-size: 14px;
            color: #a0aec0; /* Text-light equivalent for dark background */
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
            /* Desktop only style */
        }


        /* ==========================================================
           RESPONSIVE FIXES FOR MOBILE
           ========================================================== */
        
        /* HIDE hamburger by default on Desktop */
        .mobile-menu-btn {
            display: none;
        }

        /* MOBILE BREAKPOINT */
        @media (max-width: 900px) {

            /* --- Header Fixes --- */
            .main-header { padding: 10px 0; }
            .logo img { height: 32px; }
            .nav-right { gap: 10px; }
            .nav-buttons { display: flex; align-items: center; gap: 8px; }
            .cta-button { padding: 6px 12px; font-size: 12px; margin-left: 0; white-space: nowrap; }
            @media (max-width: 400px) { #open-register-modal { display: none; } }

            /* Navigation Dropdown */
            .nav-links {
                display: none;
                position: absolute;
                top: 75px; /* Adjusted based on header padding */
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
            .nav-links li { width: 100%; border-bottom: 1px solid #f0f0f0; padding: 0; }
            .nav-links a { display: block; padding: 15px 0; font-size: 16px; width: 100%; }
            .mobile-menu-btn { display: block; padding-left: 5px; }


            /* --- Content Layout Fixes --- */
            .hero { padding: 40px 0; }
            .hero h1 { font-size: 28px; margin-bottom: 15px; line-height: 1.3; }
            .hero p { font-size: 15px; padding: 0 10px; margin-bottom: 25px; }
            .features-container {
                flex-direction: column;
                gap: 20px;
                padding: 0 10px;
                margin-top: 30px;
            }
            
            /* --- Footer Mobile Fixes --- */
            .main-footer { padding-top: 40px; }
            
            .new-footer-container {
                flex-direction: column;
                gap: 35px; 
                padding-bottom: 30px;
                text-align: center; 
            }
            
            .footer-col { text-align: center; min-width: unset; }
            
            /* Center the new image logo */
            .footer-logo-link { text-align: center; margin: 0 auto 10px; } 

            .brand-tagline { margin: 0 auto 25px; }
            
            .social-links { justify-content: center; }

            .footer-links-list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 5px 15px;
            }
            
            .footer-links-list li {
                margin-bottom: 0;
            }

            .footer-bottom .footer-container {
                flex-direction: column;
                gap: 10px;
                padding-bottom: 60px; /* Space for chat bubble */
            }
            
            .developed-by { display: none; }
        }

        /* --- INNOVATIVE ASSESSMENT PROMPT CSS --- */
        @keyframes floatCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        
        .assessment-mission-card {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-left: 5px solid #6e48ff;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            padding: 20px 25px;
            border-radius: 16px;
            max-width: 340px;
            z-index: 9998;
            
            /* Updated for JS Loop Control: Start hidden */
            transform: translateY(150%);
            opacity: 0;
            /* Use transition instead of animation for repeated toggling */
            transition: transform 0.8s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.8s ease;
            
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Class to show the card */
        .assessment-mission-card.show {
            transform: translateY(0);
            opacity: 1;
        }

        .mission-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .mission-icon-bg {
            background: linear-gradient(135deg, #6e48ff, #977eff);
            width: 45px; height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 5px 15px rgba(110, 72, 255, 0.3);
            animation: floatCard 3s ease-in-out infinite;
        }
        .mission-title {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            line-height: 1.2;
        }
        .mission-desc {
            font-size: 13px;
            color: #5a677d;
            line-height: 1.4;
        }
        .mission-btn {
            background: linear-gradient(90deg, #6e48ff, #4e2ecf);
            color: white;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: transform 0.2s;
            display: block;
            margin-top: 5px;
        }
        .mission-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 5px 15px rgba(78, 46, 207, 0.25);
        }
        .mission-close {
            position: absolute; top: 8px; right: 10px;
            background: none; border: none; color: #a0aec0;
            cursor: pointer; font-size: 18px;
        }
        /* Hide on very small mobile if needed, or adjust position */
        @media(max-width: 480px) {
            .assessment-mission-card {
                left: 50%;
                transform: translateX(-50%) translateY(150%);
                bottom: 85px; /* Above mobile nav/footer */
                width: 90%;
            }
            .assessment-mission-card.show {
                transform: translateX(-50%) translateY(0);
            }
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
            <div class="nav-buttons">
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <div class="profile-dropdown">
                        <?php $initial = strtoupper(substr($_SESSION["username"], 0, 1)); ?>
                        <button id="profile-menu-trigger" class="dropdown-trigger">
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
                                <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
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
                    <a href="#" id="open-login-modal" class="cta-button btn-outline-primary">Login</a>
                    <a href="#" id="open-register-modal" class="cta-button btn-solid-primary">Register</a>
                <?php endif; ?>
            </div>
            <button id="mobile-menu-toggle" class="mobile-menu-btn">&#9776;</button>
        </div>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="container">
            <h1>Find Your Dream Job</h1>
            <p>We connect talented professionals with innovative companies. Start your search.</p>
            <a href="jobs.php" class="cta-button btn-light">Browse All Jobs</a>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="section-heading">Why JobSure?</h2>
            <div class="features-container">
                <div class="feature-card">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <h3>Curated Job Listings</h3>
                    <p>We remove the spam. Find high-quality, relevant job postings from verified companies.</p>
                </div>
                <div class="feature-card">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 11.186 0Z" />
                    </svg>
                    <h3>Save Jobs</h3>
                    <p>Bookmark jobs you're interested in and come back to them at any time.</p>
                </div>
                <div class="feature-card">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <h3>One-Click Apply</h3>
                    <p>Apply to jobs easily with our streamlined application form. No more endless sign-ups.</p>
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

<?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
    <div id="login-modal-overlay" class="modal-overlay">
        <div class="modal-content">
            <button id="close-login-modal" class="modal-close">&times;</button>
            <h2>Welcome Back</h2>
            <p class="modal-subtitle">Sign in to manage your job applications.</p>
            <div id="modal-error-message"><?php echo $login_error_message; ?></div>
            <form id="login-form" class="modal-form" action="login.php" method="POST">
                <div class="form-group">
                    <label for="login-email">Email (Username)</label>
                    <input name="email" id="login-email" type="email" class="form-input" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input name="password" id="login-password" type="password" class="form-input" placeholder="Must be at least 6 characters" required>
                </div>
                <div class="form-forgot-link"><a href="javascript:void(0)" onclick="openForgotModal()">Forgot password?</a></div>
                
                <button type="submit" class="btn-gradient">Login</button>
                <p class="form-switch-link">New to JobSure? <a id="switch-to-register">Create an account</a></p>
            </form>
        </div>
    </div>

    <div id="register-modal-overlay" class="modal-overlay">
        <div class="modal-content">
            <button id="close-register-modal" class="modal-close">&times;</button>
            <h2>Create Your Account</h2>
            <p class="modal-subtitle">Join thousands of job seekers today.</p>
            <div id="modal-register-error-message"><?php echo $register_error_message; ?></div>
            <form id="register-form" class="modal-form">
                <div id="register-step-1">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="register-first">First Name</label>
                            <input name="first_name" id="register-first" type="text" class="form-input" placeholder="First name" required>
                        </div>
                        <div class="form-group">
                            <label for="register-last">Last Name</label>
                            <input name="last_name" id="register-last" type="text" class="form-input" placeholder="Last name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input name="email" id="register-email" type="email" class="form-input" placeholder="you@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input name="password" id="register-password" type="password" class="form-input" placeholder="Must be at least 6 characters" required>
                    </div>
                    <p class="form-terms-text">By continuing you agree to our T&C</p>
                    <button type="submit" class="btn-gradient">Register</button>
                </div>
                <div id="register-step-2" style="display: none;">
                    <p class="modal-subtitle" id="otp-message">A verification code was sent to your email.</p>
                    <div class="form-group">
                        <label for="register-otp">Verification Code</label>
                        <input name="otp" id="register-otp" type="text" class="form-input" placeholder="6-digit Code">           
                    </div>
                    <button type="button" id="verify-otp-btn" class="btn-gradient">Verify & Create Account</button>
                </div>
            </form>
            <p class="form-switch-link">Already have an account? <a id="switch-to-login">Log in Instead</a></p>
        </div>
    </div>

    <script>
        const loginButton = document.getElementById('open-login-modal');
        const loginModalOverlay = document.getElementById('login-modal-overlay');
        const closeLoginModal = document.getElementById('close-login-modal');
        const registerButton = document.getElementById('open-register-modal');
        const registerModalOverlay = document.getElementById('register-modal-overlay');
        const closeRegisterModal = document.getElementById('close-register-modal');
        const switchToRegister = document.getElementById('switch-to-register');
        const switchToLogin = document.getElementById('switch-to-login');

        function showLoginModal() {
            registerModalOverlay.classList.remove('show');
            loginModalOverlay.classList.add('show');
        }
        function hideLoginModal() { loginModalOverlay.classList.remove('show'); }
        
        if (loginButton) {
            loginButton.addEventListener('click', function(e) { e.preventDefault(); showLoginModal(); });
            closeLoginModal.addEventListener('click', hideLoginModal);
            loginModalOverlay.addEventListener('click', function(e) { if (e.target === loginModalOverlay) hideLoginModal(); });
        }

        function showRegisterModal() {
            loginModalOverlay.classList.remove('show');
            registerModalOverlay.classList.add('show');
            document.getElementById('register-step-1').style.display = 'block';
            document.getElementById('register-step-2').style.display = 'none';
            document.getElementById('modal-register-error-message').textContent = '';
        }
        function hideRegisterModal() { registerModalOverlay.classList.remove('show'); }

        if (registerButton) {
            registerButton.addEventListener('click', function(e) { e.preventDefault(); showRegisterModal(); });
            closeRegisterModal.addEventListener('click', hideRegisterModal);
            registerModalOverlay.addEventListener('click', function(e) { if (e.target === registerModalOverlay) hideRegisterModal(); });
        }
        if (switchToRegister) switchToRegister.addEventListener('click', function(e) { e.preventDefault(); showRegisterModal(); });
        if (switchToLogin) switchToLogin.addEventListener('click', function(e) { e.preventDefault(); showLoginModal(); });

        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const loginErrorMessage = "<?php echo $login_error_message; ?>";
            const registerErrorMessage = "<?php echo $register_error_message; ?>";
            if (action === 'login') showLoginModal();
            else if (loginErrorMessage) showLoginModal();
            else if (registerErrorMessage) { showRegisterModal(); document.getElementById('modal-register-error-message').textContent = registerErrorMessage; }
        })();

        const registerForm = document.getElementById('register-form');
        const registerErrorMsg = document.getElementById('modal-register-error-message');
        const otpMessage = document.getElementById('otp-message');

        if (registerForm) {
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(registerForm);
                const data = { first_name: formData.get('first_name'), last_name: formData.get('last_name'), email: formData.get('email'), password: formData.get('password') };
                const submitBtn = registerForm.querySelector('button[type="submit"]');
                submitBtn.textContent = 'Registering...';
                submitBtn.disabled = true;
                
                try {
                    const response = await fetch('api_send_otp.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
                    const result = await response.json();
                    if (result.success) {
                        document.getElementById('register-step-1').style.display = 'none';
                        document.getElementById('register-step-2').style.display = 'block';
                        otpMessage.textContent = result.message;
                        registerErrorMsg.textContent = '';
                    } else {
                        registerErrorMsg.textContent = result.message;
                    }
                } catch (error) { registerErrorMsg.textContent = 'An error occurred. Please try again.'; }
                finally { submitBtn.textContent = 'Register'; submitBtn.disabled = false; }
            });
        }

        const verifyBtn = document.getElementById('verify-otp-btn');
        if (verifyBtn) {
            verifyBtn.addEventListener('click', async function() {
                const otp = document.getElementById('register-otp').value;
                verifyBtn.textContent = 'Verifying...';
                verifyBtn.disabled = true;
                try {
                    const response = await fetch('api_verify_otp.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ otp: otp }) });
                    const result = await response.json();
                    if (result.success) window.location.href = result.redirect;
                    else registerErrorMsg.textContent = result.message;
                } catch (error) { registerErrorMsg.textContent = 'An error occurred. Please try again.'; }
                finally { verifyBtn.textContent = 'Verify & Create Account'; verifyBtn.disabled = false; }
            });
        }
    </script>
<?php endif; ?>

<?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <script>
        const profileTrigger = document.getElementById('profile-menu-trigger');
        const profileMenu = document.getElementById('profile-menu');
        if (profileTrigger && profileMenu) {
            profileTrigger.addEventListener('click', function(e) { e.stopPropagation(); profileMenu.classList.toggle('show'); });
            window.addEventListener('click', function(e) { if (profileMenu.classList.contains('show') && !profileMenu.contains(e.target) && !profileTrigger.contains(e.target)) profileMenu.classList.remove('show'); });
        }
        const manageAccountToggle = document.getElementById('manage-account-toggle');
        const manageAccountSubmenu = document.getElementById('manage-account-submenu');
        if (manageAccountToggle && manageAccountSubmenu) {
            manageAccountToggle.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); manageAccountToggle.classList.toggle('open'); manageAccountSubmenu.classList.toggle('show'); });
        }
    </script>
<?php endif; ?>

<div id="admin-modal-overlay" class="admin-modal-overlay">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h2>Admin Access</h2>
            <button id="admin-modal-close" class="admin-modal-close">&times;</button>
        </div>
        <div class="admin-modal-body">
            <form id="admin-login-form">
                <div class="form-group">
                    <label for="admin-password">Password</label>
                    <input type="password" id="admin-password" class="form-input" required>
                    <span id="admin-error-message"></span>
                </div>
                <button type="submit" class="btn-admin-login">Login</button>
            </form>
        </div>
    </div>
</div>

<button id="ai-chat-bubble" title="Chat with AI Assistant">
    <img src="robot.png" class="ai-icon" alt="AI Assistant">
</button>

<div id="ai-chat-modal" class="chat-modal-overlay">
    <div class="chat-modal-content">
        <div class="chat-modal-header">
            <h3>AI Assistant</h3>
            <button id="ai-chat-close" class="chat-modal-close">&times;</button>
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="chat-message ai">Hi! I'm the JobSure assistant. I can help you find jobs or answer questions.</div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chat-input" class="chat-input" placeholder="Type your message...">
            <button id="chat-send-btn" class="chat-send-btn" title="Send">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L6 12Zm0 0h7.5" />
                </svg>
            </button>
        </div>
    </div>
</div>

<?php if ($show_assessment_alert): ?>
<div class="assessment-mission-card" id="assessment-mission-card">
    <button type="button" class="mission-close" id="mission-close-btn">&times;</button>
    <div class="mission-header">
        <div class="mission-icon-bg">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.1 2.76-.46 3.39-.09a2.18 2.18 0 0 1 0 2.9A2.19 2.19 0 0 0 9 12z"></path><path d="M15 12a2.18 2.18 0 0 0 .94-1.85c.38-.63 1-1.78-.09-3.4-1-1.45-4-2-4-2s.56 3.25 2 4.75"></path></svg>
        </div>
        <div>
            <div class="mission-title">Career Mission Pending</div>
            <div class="mission-desc">Unlock your perfect job match.</div>
        </div>
    </div>
    <a href="assessment.php" class="mission-btn">
        🚀 Launch Assessment
    </a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const missionCard = document.getElementById('assessment-mission-card');
    const closeBtn = document.getElementById('mission-close-btn');
    
    // Time in milliseconds (60000 = 1 minute)
    const REAPPEAR_TIME = 60000; 

    if (missionCard && closeBtn) {
        
        // Show the popup
        const showPopup = () => {
            // Add class to trigger CSS transition
            missionCard.classList.add('show');
        };

        // Hide the popup and schedule it to come back
        const hidePopup = (e) => {
            if(e) { e.preventDefault(); e.stopPropagation(); }
            
            // Remove class to hide
            missionCard.classList.remove('show');
            
            // Set timer to show it again after 1 minute
            setTimeout(showPopup, REAPPEAR_TIME);
        };

        // Initial Show (2 seconds after load)
        setTimeout(showPopup, 2000);

        // Attach Click Event to X button
        closeBtn.addEventListener('click', hidePopup);
    }
});
</script>
<?php endif; ?>

<script>
    (function() {
        const logo = document.querySelector('.logo');
        let clickCount = 0;
        const requiredClicks = 7;
        const adminModal = document.getElementById('admin-modal-overlay');
        const closeAdminModalBtn = document.getElementById('admin-modal-close');
        const adminForm = document.getElementById('admin-login-form');
        const adminPasswordInput = document.getElementById('admin-password');
        const adminErrorMsg = document.getElementById('admin-error-message');
        // NEW: Footer admin link
        const openAdminModalFooter = document.getElementById('open-admin-modal-footer');


        function showAdminModal() { adminModal.classList.add('show'); adminPasswordInput.focus(); }
        function hideAdminModal() { adminModal.classList.remove('show'); adminPasswordInput.value = ''; adminErrorMsg.textContent = ''; clickCount = 0; }

        if (logo) {
            logo.addEventListener('click', function(e) { e.preventDefault(); clickCount++; if (clickCount === requiredClicks) showAdminModal(); });
        }
        // NEW: Footer admin link event
        if (openAdminModalFooter) {
            openAdminModalFooter.addEventListener('click', function(e) { 
                e.preventDefault(); 
                hideAdminModal(); 
                showAdminModal(); 
            });
        }
        if(adminForm) {
            adminForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (adminPasswordInput.value === "admin123") window.location.href = 'admin_dashboard.php';
                else { adminErrorMsg.textContent = 'Incorrect password.'; adminPasswordInput.value = ''; adminPasswordInput.focus(); }
            });
        }
        if(closeAdminModalBtn) closeAdminModalBtn.addEventListener('click', hideAdminModal);
        if(adminModal) adminModal.addEventListener('click', function(e) { if (e.target === adminModal) hideAdminModal(); });

        // AI Chat
        const chatBubble = document.getElementById('ai-chat-bubble');
        const chatModal = document.getElementById('ai-chat-modal');
        const closeChatModal = document.getElementById('ai-chat-close');
        const chatMessages = document.getElementById('chat-messages');
        const chatInput = document.getElementById('chat-input');
        const chatSendBtn = document.getElementById('chat-send-btn');
        
        function showChatModal() { chatModal.classList.add('show'); chatInput.focus(); }
        function hideChatModal() { chatModal.classList.remove('show'); }
        function addMessageToUI(sender, message) {
            const messageEl = document.createElement('div');
            messageEl.className = `chat-message ${sender}`;
            messageEl.textContent = message;
            chatMessages.appendChild(messageEl);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        function sendMessage() {
            const message = chatInput.value.trim();
            if (message === '') return;
            addMessageToUI('user', message);
            chatInput.value = '';
            addMessageToUI('ai typing', '...');
            const typingIndicator = chatMessages.lastChild;

            fetch('api_chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                chatMessages.removeChild(typingIndicator);
                if (data.reply) addMessageToUI('ai', data.reply);
                else addMessageToUI('ai', 'Sorry, an error occurred.');
             })
            .catch(error => {
                chatMessages.removeChild(typingIndicator);
                addMessageToUI('ai', 'Sorry, I am having trouble connecting.');
            });
        }

        if (chatBubble) chatBubble.addEventListener('click', showChatModal);
        if (closeChatModal) closeChatModal.addEventListener('click', hideChatModal);
        if (chatModal) chatModal.addEventListener('click', function(e) { if (e.target === chatModal) hideChatModal(); });
        if (chatSendBtn) chatSendBtn.addEventListener('click', sendMessage);
        if (chatInput) chatInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') sendMessage(); });

        document.getElementById("mobile-menu-toggle").onclick = function () {
            document.querySelector(".nav-links").classList.toggle("show");
        };
    })();
</script>

<div id="forgotPasswordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index: 3000; justify-content:center; align-items:center;">
    <div style="background:white; padding:40px; width:100%; max-width:450px; border-radius:12px; text-align:center; position:relative; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        
        <span onclick="document.getElementById('forgotPasswordModal').style.display='none'" style="position:absolute; top:15px; right:20px; cursor:pointer; font-size:28px; color:#5a677d;">&times;</span>
        
        <h2 style="margin-bottom:10px; color:#2d3748;">Reset Password</h2>
        <p id="fp_subtitle" style="color:#5a677d; margin-bottom:20px;">Follow the steps to recover your account.</p>

        <div id="step1_email">
            <div class="form-group" style="text-align:left;">
                <label style="font-weight:600; font-size:14px;">Enter your registered email</label>
                <input type="email" id="fp_email" class="form-input" placeholder="example@gmail.com" style="width:100%; padding:12px; border:1px solid #e7eaf3; border-radius:8px; margin-top:5px;">
            </div>
            <button onclick="sendResetOTP(event)" class="btn-gradient" style="width:100%; margin-top:10px;">Send OTP</button>
        </div>

        <div id="step2_otp" style="display:none;">
            <div class="form-group" style="text-align:left;">
                <label style="font-weight:600; font-size:14px;">Enter OTP sent to email</label>
                <input type="text" id="fp_otp" class="form-input" placeholder="6-digit Code" style="width:100%; padding:12px; border:1px solid #e7eaf3; border-radius:8px; margin-top:5px;">
            </div>
            <button onclick="verifyResetOTP()" class="btn-gradient" style="width:100%; margin-top:10px;">Verify OTP</button>
        </div>

        <div id="step3_password" style="display:none;">
            <div class="form-group" style="text-align:left;">
                <label style="font-weight:600; font-size:14px;">New Password</label>
                <input type="password" id="fp_new_pass" class="form-input" placeholder="New Password" style="width:100%; padding:12px; border:1px solid #e7eaf3; border-radius:8px; margin-top:5px;">
            </div>
             <div class="form-group" style="text-align:left; margin-top:15px;">
                <label style="font-weight:600; font-size:14px;">Confirm Password</label>
                <input type="password" id="fp_confirm_pass" class="form-input" placeholder="Confirm Password" style="width:100%; padding:12px; border:1px solid #e7eaf3; border-radius:8px; margin-top:5px;">
            </div>
            <button onclick="updatePassword()" class="btn-gradient" style="width:100%; margin-top:10px;">Change Password</button>
        </div>

        <p id="fp_message" style="color:#D90429; margin-top:15px; font-weight:500; font-size:14px; min-height:20px;"></p>
    </div>
</div>

<script>
    function openForgotModal() {
        document.getElementById('login-modal-overlay').classList.remove('show');
        document.getElementById('forgotPasswordModal').style.display = 'flex';
        document.getElementById('step1_email').style.display = 'block';
        document.getElementById('step2_otp').style.display = 'none';
        document.getElementById('step3_password').style.display = 'none';
        document.getElementById('fp_message').innerText = "";
        document.getElementById('fp_email').value = "";
    }

    function sendResetOTP(event) {
        let email = document.getElementById('fp_email').value;
        if(email === "") { 
            document.getElementById('fp_message').innerText = "Please enter your email."; 
            return; 
        }
        let btn = event.target;
        btn.innerText = "Sending...";
        btn.disabled = true;

        fetch('api_reset_send_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerText = "Send OTP";
            btn.disabled = false;
            if(data.success) {
                document.getElementById('fp_message').innerText = "";
                document.getElementById('step1_email').style.display = 'none';
                document.getElementById('step2_otp').style.display = 'block';
            } else {
                document.getElementById('fp_message').innerText = data.message;
            }
        })
        .catch(err => {
             btn.innerText = "Send OTP";
             btn.disabled = false;
             document.getElementById('fp_message').innerText = "Connection error.";
        });
    }

    function verifyResetOTP() {
        let otp = document.getElementById('fp_otp').value;
        fetch('api_reset_verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ otp: otp })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('fp_message').innerText = "";
                document.getElementById('step2_otp').style.display = 'none';
                document.getElementById('step3_password').style.display = 'block';
            } else {
                document.getElementById('fp_message').innerText = data.message;
            }
        });
    }

    function updatePassword() {
        let p1 = document.getElementById('fp_new_pass').value;
        let p2 = document.getElementById('fp_confirm_pass').value;
        if(p1 === "" || p2 === "") {
            document.getElementById('fp_message').innerText = "Please fill in all fields.";
            return;
        }
        if(p1 !== p2) {
            document.getElementById('fp_message').innerText = "Passwords do not match!";
            return;
        }
        fetch('api_reset_update_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ new_password: p1 })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Password Changed Successfully! Please Login.");
                window.location.href = 'index.php?action=login';
            } else {
                document.getElementById('fp_message').innerText = data.message;
            }
        });
    }
</script>
<?php if (isset($_GET['status']) && $_GET['status'] === 'account_deleted'): ?>
    <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2d3748;
            color: #ffffff;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateX(120%);
            animation: slideInToast 0.5s forwards;
            border-left: 5px solid #D90429;
            font-family: 'Poppins', sans-serif;
        }
        .toast-icon {
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .toast-content h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        .toast-content p {
            margin: 0;
            font-size: 13px;
            color: #cbd5e0;
        }
        .toast-close {
            background: none;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            font-size: 20px;
            margin-left: 10px;
        }
        .toast-close:hover { color: #fff; }

        @keyframes slideInToast {
            to { transform: translateX(0); }
        }
    </style>

    <div id="goodbye-toast" class="toast-notification">
        <div class="toast-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div class="toast-content">
            <h4>Account Deleted</h4>
            <p>We're sorry to see you go.</p>
        </div>
        <button onclick="this.parentElement.remove()" class="toast-close">✕</button>
    </div>

    <script>
        window.history.replaceState({}, document.title, window.location.pathname);
        setTimeout(() => {
            const toast = document.getElementById('goodbye-toast');
            if(toast) {
                toast.style.transition = "opacity 0.5s, transform 0.5s";
                toast.style.opacity = "0";
                toast.style.transform = "translateX(100%)";
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
<?php endif; ?>

</body>
</html>