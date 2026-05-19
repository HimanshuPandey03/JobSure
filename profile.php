<?php
session_start();
require_once 'db_config.php';

// 1. AUTHENTICATION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['login_error'] = "Please log in to access your profile.";
    header("location: index.php");
    exit;
}

// Check if this is a new user
$is_new_user = false;
if (isset($_SESSION['is_new_user']) && $_SESSION['is_new_user'] === true) {
    $is_new_user = true;
}

// Variables from session
$user_id = $_SESSION["id"];
$user_name = $_SESSION["username"] ?? "User";
$user_email = $_SESSION["email"] ?? "Email Not Found";

// Initialize profile data array
$profile_data = [
    'full_name' => $user_name,
    'phone_no' => '',
    'location' => '', 
    'gender' => '', 
    'education' => '', 
    'skills' => '', 
    'work_experience' => '', 
    'currently_looking_for' => '',
    'work_mode' => '',
    'areas_of_interest' => '',
    'resume_path' => ''
];
$status_message = ''; 

// Function to safely output profile data
function profile_value($key, $data) {
    return htmlspecialchars($data[$key] ?? '');
}

// 2. FETCH EXISTING PROFILE DATA
$sql_fetch = "SELECT * FROM profiles WHERE user_id = ?";
if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $user_id);
    if ($stmt_fetch->execute()) {
        $result = $stmt_fetch->get_result();
        if ($result->num_rows == 1) {
            $profile_data = array_merge($profile_data, $result->fetch_assoc());
        }
    }
    $stmt_fetch->close();
}

// If the phone number is empty, force "New User" mode
if (empty($profile_data['phone_no'])) {
    $is_new_user = true;
}

// Check for status message
if (isset($_SESSION['profile_status'])) {
    $status_message = $_SESSION['profile_status'];
    unset($_SESSION['profile_status']);
}

// Helper data for JS
$looking_for_arr = array_filter(explode(',', $profile_data['currently_looking_for']));
$work_mode_arr = array_filter(explode(',', $profile_data['work_mode']));
$areas_of_interest_arr = array_filter(explode(',', $profile_data['areas_of_interest']));

// --- UPDATED LIST: EXACTLY MATCHING THE 11 ASSESSMENT CATEGORIES ---
$popular_interests = [
    'Sales', 
    'Marketing', 
    'Information Technology (IT)', 
    'Content Creator', 
    'Influencer',  
    'Digital Marketing',
    'Finance & Accounts', 
    'Human Resources (HR)', 
    'Administration / Management', 
    'Customer Support'
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - JobSure</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                'primary-dark': '#4e2ecf',
                'primary-light': '#6e48ff',
                'teal-custom': '#08a488', 
                'error-red': '#ef4444',
                'warn-orange': '#f59e0b',
              },
              fontFamily: {
                'sans': ['Poppins', 'system-ui', 'sans-serif'],
              },
            }
          }
        }
    </script>

    <style>
        :root {
            --primary-dark: #4e2ecf;
            --border-color: #e7eaf3;
            --teal-custom: #08a488;
        }
        .form-input, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-dark);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(78, 46, 207, 0.2);
        }
        .form-select {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .nav-link {
            color: #5a677d;
            font-weight: 500;
            position: relative;
            padding-bottom: 6px;
            transition: color 0.3s ease;
            text-decoration: none;
        }
        .nav-link:hover { color: var(--primary-dark); }

        /* --- PREFERENCE BUTTONS --- */
        .pref-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 9999px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .pref-btn:hover {
            border-color: #a1a1aa;
        }
        .pref-btn.selected {
            background-color: var(--teal-custom);
            border-color: var(--teal-custom);
            color: #ffffff;
        }
        .pref-btn .icon-plus,
        .pref-btn.selected .icon-check { display: inline-block; }
        .pref-btn .icon-check,
        .pref-btn.selected .icon-plus { display: none; }

        /* --- TAG INPUTS --- */
        .tag-input-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: #f9fafb;
            cursor: text;
        }
        .tag-input-container:focus-within {
             border-color: var(--primary-dark);
             background-color: white;
             box-shadow: 0 0 0 3px rgba(78, 46, 207, 0.2);
        }
        .tag-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: var(--teal-custom);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        .tag-remove-btn {
            cursor: pointer;
            width: 16px;
            height: 16px;
            color: rgba(255, 255, 255, 0.8);
            min-width: 16px;
            flex-shrink: 0;
        }
        .tag-remove-btn:hover { color: white; }
        #area-search-input {
            flex-grow: 1;
            border: none;
            outline: none;
            padding: 4px;
            background-color: transparent;
            min-width: 200px;
        }
        .popular-tag-btn {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 9999px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            color: #374151;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .popular-tag-btn:hover {
            background-color: #f3f4f6;
            border-color: #a1a1aa;
        }
        .limit-warning {
            color: #ef4444;
            font-size: 12px;
            margin-left: 8px;
            font-weight: 500;
            display: none;
        }
        .attention-text {
            color: #d97706; 
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 4px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800">

    <header class="bg-white shadow-sm sticky top-0 z-40" style="border-bottom: 1px solid var(--border-color);">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="index.php" class="inline-flex items-center">
                    <img src="logo.jpeg" alt="JobSure Logo" class="h-8 sm:h-10"> 
                </a>
                <div class="flex items-center space-x-6">
                    <span class="text-slate-500 hidden sm:inline">
                        Welcome, <?php echo htmlspecialchars($user_name); ?>
                    </span>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto p-4 sm:p-6 lg:p-8">

        <?php if ($status_message): ?>
            <div id="status-message" class="max-w-4xl mx-auto p-4 mb-6 text-sm <?php echo strpos($status_message, 'Error') !== false ? 'text-red-700 bg-red-100' : 'text-green-700 bg-green-100'; ?> rounded-lg" role="alert">
                <span class="font-medium"><?php echo strpos($status_message, 'Error') !== false ? 'Error!' : 'Success!'; ?></span> <?php echo $status_message; ?>
            </div>
        <?php endif; ?>

        <section id="page-profile">
            <div class="max-w-4xl mx-auto">
                
                <div class="text-center mb-8">
                    <?php if ($is_new_user): ?>
                        <h1 class="text-4xl font-bold text-slate-900 mb-2">Hi there! 👋 Let's get started</h1>
                        <p class="text-lg text-slate-500">Complete your profile to find the best career opportunities.</p>
                    <?php else: ?>
                        <h1 class="text-4xl font-bold text-slate-900 mb-2">My Profile</h1>
                        <p class="text-lg text-slate-500">Update your personal and professional information.</p>
                    <?php endif; ?>
                </div>
                
                <form id="profile-form" action="profile_save.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md border border-slate-200 mb-6">
                        <h2 class="text-xl font-semibold text-primary-dark mb-6">Personal Details</h2>
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="profile-name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                                    <input type="text" id="profile-name" name="full_name" class="form-input" value="<?php echo profile_value('full_name', $profile_data); ?>" required >
                                </div>
                                <div>
                                    <label for="profile-email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                                    <input type="email" id="profile-email" name="email" required class="form-input bg-slate-200 cursor-not-allowed" readonly value="<?php echo htmlspecialchars($user_email); ?>">
                                    <p class="text-xs text-slate-500 mt-1">Email cannot be changed.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="profile-phone" class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                                    <input type="tel" id="profile-phone" name="phone_no" class="form-input" placeholder="10 digits e.g. 1234567890" value="<?php echo profile_value('phone_no', $profile_data); ?>" pattern="\d{10}" title="Phone number must be exactly 10 digits.">
                                </div>
                                <div>
                                    <label for="profile-location" class="block text-sm font-medium text-slate-700 mb-1">Current City</label>
                                    <input type="text" id="profile-location" name="location" class="form-input" placeholder="e.g., Mumbai, MH" value="<?php echo profile_value('location', $profile_data); ?>">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Gender</label>
                                <div class="flex items-center gap-6 pt-2"> 
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="gender" value="Male" class="h-4 w-4 text-primary-dark border-slate-300 focus:ring-primary-dark" <?php echo ($profile_data['gender'] === 'Male') ? 'checked' : ''; ?>>
                                        <span class="ml-2 text-sm text-slate-700">Male</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="gender" value="Female" class="h-4 w-4 text-primary-dark border-slate-300 focus:ring-primary-dark" <?php echo ($profile_data['gender'] === 'Female') ? 'checked' : ''; ?>>
                                        <span class="ml-2 text-sm text-slate-700">Female</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md border border-slate-200 mb-6">
                        <h2 class="text-xl font-semibold text-primary-dark mb-6">Your Preferences</h2>
                        <div class="space-y-5">
                            
                            <input type="hidden" name="currently_looking_for" id="currently_looking_for" value="<?php echo profile_value('currently_looking_for', $profile_data); ?>">
                            <input type="hidden" name="work_mode" id="work_mode" value="<?php echo profile_value('work_mode', $profile_data); ?>">

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Currently looking for</label>
                                <div class="flex flex-wrap gap-3">
                                    <div class="pref-btn" data-group="looking_for" data-value="Jobs">
                                        <span>Jobs</span>
                                        <i data-lucide="plus" class="icon-plus w-4 h-4 ml-1.5"></i>
                                        <i data-lucide="check" class="icon-check w-4 h-4 ml-1.5"></i>
                                    </div>
                                    <div class="pref-btn" data-group="looking_for" data-value="Internships">
                                        <span>Internships</span>
                                        <i data-lucide="plus" class="icon-plus w-4 h-4 ml-1.5"></i>
                                        <i data-lucide="check" class="icon-check w-4 h-4 ml-1.5"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Work mode</label>
                                <div class="flex flex-wrap gap-3">
                                    <div class="pref-btn" data-group="work_mode" data-value="In-office">
                                        <span>In-office</span>
                                        <i data-lucide="plus" class="icon-plus w-4 h-4 ml-1.5"></i>
                                        <i data-lucide="check" class="icon-check w-4 h-4 ml-1.5"></i>
                                    </div>
                                    <div class="pref-btn" data-group="work_mode" data-value="Work from home">
                                        <span>Work from home</span>
                                        <i data-lucide="plus" class="icon-plus w-4 h-4 ml-1.5"></i>
                                        <i data-lucide="check" class="icon-check w-4 h-4 ml-1.5"></i>
                                    </div>
                                </div>
                            </div>
                    
                        </div>
                    </div>

                    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md border border-slate-200">
                        <h2 class="text-xl font-semibold text-primary-dark mb-6">Professional Details</h2>
                        <div class="space-y-5">
                            
                            <div>
                                <label for="profile-education" class="block text-sm font-medium text-slate-700 mb-1">Highest Education</label>
                                <?php $current_edu = profile_value('education', $profile_data); ?>
                                <select id="profile-education" name="education" class="form-select">
                                    <option value="" <?php echo ($current_edu == '') ? 'selected' : ''; ?>>-- Select Education Level --</option>
                                    <option value="SSC" <?php echo ($current_edu == 'SSC') ? 'selected' : ''; ?>>SSC (10th)</option>
                                    <option value="HSC" <?php echo ($current_edu == 'HSC') ? 'selected' : ''; ?>>HSC (12th)</option>
                                    <option value="Diploma" <?php echo ($current_edu == 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                                    <option value="Under Graduate" <?php echo ($current_edu == 'Under Graduate') ? 'selected' : ''; ?>>Under Graduate</option>
                                    <option value="Graduate" <?php echo ($current_edu == 'Graduate') ? 'selected' : ''; ?>>Graduate (Degree)</option>
                                    <option value="Post Graduate" <?php echo ($current_edu == 'Post Graduate') ? 'selected' : ''; ?>>Post Graduate (Masters)</option>
                                    <option value="Doctorate" <?php echo ($current_edu == 'Doctorate') ? 'selected' : ''; ?>>Doctorate (Ph.D)</option>
                                    <option value="Other" <?php echo ($current_edu == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="area-search-input" class="block text-sm font-medium text-slate-700">
                                        Area(s) of interest <span class="text-slate-500 font-normal">(Max 5)</span>
                                    </label>
                                    <span id="interest-counter" class="text-xs font-medium text-primary-dark">0/5 selected</span>
                                </div>
                                
                                <p class="attention-text">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> 
                                    Please select ONLY the areas you are genuinely interested in. Take this seriously, as your entire career assessment will be based on these choices.
                                </p>

                                <span id="limit-warning" class="limit-warning">Maximum 5 interests allowed!</span>

                                <input type="hidden" id="profile-areas-of-interest" name="areas_of_interest" value="<?php echo profile_value('areas_of_interest', $profile_data); ?>">
                                
                                <div class="tag-input-container" onclick="document.getElementById('area-search-input').focus();">
                                    <div id="area-tags-container" class="flex flex-wrap gap-2"></div>
                                    <input type="text" id="area-search-input" class="form-input" placeholder="Type or select below">
                                </div>
                                
                                <label class="block text-sm font-medium text-slate-700 mt-4 mb-2">Popular career interests</label>
                                <div id="popular-tags-container" class="flex flex-wrap gap-2">
                                    <?php foreach ($popular_interests as $interest): ?>
                                        <button type="button" class="popular-tag-btn" data-value="<?php echo htmlspecialchars($interest); ?>">
                                            <span><?php echo htmlspecialchars($interest); ?></span>
                                            <i data-lucide="plus" class="w-4 h-4 ml-1.5 text-slate-500"></i>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div>
                                <label for="profile-skills" class="block text-sm font-medium text-slate-700 mb-1">Skills</label>
                                <input type="text" id="profile-skills" name="skills" class="form-input" placeholder="e.g., JavaScript, React, Node.js, Python" value="<?php echo profile_value('skills', $profile_data); ?>">
                                <p class="text-xs text-slate-500 mt-1">Enter skills separated by commas.</p>
                            </div>
                            <div>
                                <label for="profile-experience" class="block text-sm font-medium text-slate-700 mb-1">Work Experience</label>
                                <textarea id="profile-experience" name="work_experience" rows="5" class="form-input" placeholder="e.g., Senior Developer @ Google (2020-Present)&#10;- Worked on..."><?php echo profile_value('work_experience', $profile_data); ?></textarea>
                            </div>

                            <div>
                                <label for="profile-resume" class="block text-sm font-medium text-slate-700 mb-1">Resume</label>
                                
                                <input type="file" id="profile-resume" name="resume_file" 
                                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                                       accept="application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                       onchange="updateResumeStatus(this)">

                                <div id="resume-status-container" class="mt-2">
                                    <?php if (!empty($profile_data['resume_path'])): ?>
                                        <div class="flex items-center p-2 bg-green-50 border border-green-200 rounded-md">
                                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-2"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-green-700">Upload Done ✅</p>
                                                <p class="text-xs text-green-600">Current file: <?php echo basename($profile_data['resume_path']); ?></p>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center p-2 bg-slate-50 border border-slate-200 rounded-md">
                                            <i data-lucide="alert-circle" class="w-5 h-5 text-slate-400 mr-2"></i>
                                            <p class="text-sm text-slate-500">Not yet uploaded.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="text-right border-t pt-5 mt-3">
                                <button type="submit" class="bg-primary-dark text-white font-medium px-6 py-3 rounded-lg shadow hover:bg-primary-light transition-colors">
                                    <?php echo $is_new_user ? 'Save Profile & Continue' : 'Save Changes'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>
    
    <script>
        lucide.createIcons();
        
        // Hide status message after 4 seconds
        const statusMessage = document.getElementById('status-message');
        if (statusMessage) {
            setTimeout(() => {
                statusMessage.style.transition = 'opacity 0.5s';
                statusMessage.style.opacity = '0';
                setTimeout(() => statusMessage.style.display = 'none', 500);
            }, 4000);
        }

        // --- NEW: Function to update Resume UI immediately on file select ---
        function updateResumeStatus(input) {
            const container = document.getElementById('resume-status-container');
            
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                
                // Update the UI to show "Selected" status (Blue)
                container.innerHTML = `
                    <div class="flex items-center p-2 bg-blue-50 border border-blue-200 rounded-md">
                        <i data-lucide="file-up" class="w-5 h-5 text-blue-600 mr-2"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-700">File Selected (Ready to Save)</p>
                            <p class="text-xs text-blue-600">${fileName}</p>
                        </div>
                    </div>
                `;
                // Refresh icons for the new HTML
                lucide.createIcons();
            }
        }

        /* --- JAVASCRIPT FOR INTERACTIVE ELEMENTS --- */
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Preference Buttons ---
            const prefButtons = document.querySelectorAll('.pref-btn');
            const hiddenInputs = {
                looking_for: document.getElementById('currently_looking_for'),
                work_mode: document.getElementById('work_mode')
            };
            
            const selectedValues = {
                looking_for: new Set(hiddenInputs.looking_for.value.split(',').filter(Boolean)),
                work_mode: new Set(hiddenInputs.work_mode.value.split(',').filter(Boolean))
            };

            // Initialize buttons
            prefButtons.forEach(btn => {
                const group = btn.dataset.group;
                const value = btn.dataset.value;
                if (selectedValues[group] && selectedValues[group].has(value)) {
                    btn.classList.add('selected');
                }
            });

            // Click listeners
            prefButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const group = btn.dataset.group;
                    const value = btn.dataset.value;
                    
                    btn.classList.toggle('selected');
                    
                    if (btn.classList.contains('selected')) {
                        selectedValues[group].add(value);
                    } else {
                        selectedValues[group].delete(value);
                    }
                    hiddenInputs[group].value = Array.from(selectedValues[group]).join(',');
                });
            });


            // --- Area of Interest Tags (WITH 5 TAG LIMIT) ---
            const tagsContainer = document.getElementById('area-tags-container');
            const tagsHiddenInput = document.getElementById('profile-areas-of-interest');
            const tagSearchInput = document.getElementById('area-search-input');
            const popularTagsContainer = document.getElementById('popular-tags-container');
            const limitWarning = document.getElementById('limit-warning');
            const interestCounter = document.getElementById('interest-counter');
            
            const MAX_TAGS = 5;
            let areaTags = new Set(tagsHiddenInput.value.split(',').filter(Boolean));

            function updateCounter() {
                const count = areaTags.size;
                interestCounter.textContent = `${count}/${MAX_TAGS} selected`;
                if (count >= MAX_TAGS) {
                    interestCounter.classList.add('text-error-red');
                    interestCounter.classList.remove('text-primary-dark');
                } else {
                    interestCounter.classList.remove('text-error-red');
                    interestCounter.classList.add('text-primary-dark');
                }
            }

            function renderTags() {
                tagsContainer.innerHTML = '';
                areaTags.forEach(tag => {
                    const tagEl = document.createElement('div');
                    tagEl.className = 'tag-item';
                    tagEl.innerHTML = `<span>${tag}</span><i data-lucide="x" class="tag-remove-btn flex-shrink-0" data-value="${tag}"></i>`;
                    tagsContainer.appendChild(tagEl);
                });
                tagsHiddenInput.value = Array.from(areaTags).join(',');
                
                updateCounter();
                lucide.createIcons({ root: tagsContainer });
            }

            function addTag(tag) {
                tag = tag.trim();
                if (!tag) return;

                if (areaTags.size >= MAX_TAGS && !areaTags.has(tag)) {
                    // Show warning
                    limitWarning.style.display = 'block';
                    setTimeout(() => {
                        limitWarning.style.display = 'none';
                    }, 3000);
                    tagSearchInput.value = '';
                    return;
                }

                if (tag.length > 1 && !areaTags.has(tag)) {
                    areaTags.add(tag);
                    renderTags();
                }
                tagSearchInput.value = '';
            }

            function removeTag(tag) {
                areaTags.delete(tag);
                renderTags();
            }

            // Event delegation for tag removal
            tagsContainer.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.tag-remove-btn');
                if (removeBtn) {
                    e.stopPropagation();
                    const val = removeBtn.dataset.value;
                    removeTag(val);
                }
            });

            // Initial Render
            renderTags();

            tagSearchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    addTag(tagSearchInput.value);
                }
            });
            
            tagSearchInput.addEventListener('blur', () => {
                 addTag(tagSearchInput.value);
            });

            popularTagsContainer.querySelectorAll('.popular-tag-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    addTag(btn.dataset.value);
                });
            });
        });
    </script>
</body>
</html>