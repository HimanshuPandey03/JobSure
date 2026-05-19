<?php
session_start();

// 1. AUTHENTICATION & COMPLETION CHECK
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || 
    !isset($_SESSION['quiz_completed']) || $_SESSION['quiz_completed'] != 1) {
    header("location: index.php");
    exit;
}

$user_name = $_SESSION['username'];
$result_field = $_SESSION['quiz_result'];
$score_percent = isset($_SESSION['quiz_score']) ? $_SESSION['quiz_score'] : 0;

// ========================================================
// 2. LOGIC: Determine Recommendation & Target Page based on Score
// ========================================================
if ($score_percent >= 76) {
    $recommendation = 'Job';
    $status_msg = "Excellent • Job Ready";
    $card_class = 'job';
    $target_page = "jobs.php";
    $btn_text = "Browse Full-Time Jobs";
    $status_icon = "trophy"; 
} elseif ($score_percent >= 41) {
    $recommendation = 'Internship';
    $status_msg = "Good • Internship Recommended";
    $card_class = 'internship';
    $target_page = "internships.php";
    $btn_text = "Find Internships";
    $status_icon = "rocket"; 
} else {
    $recommendation = 'Course';
    $status_msg = "Moderate • Recommend: Courses";
    $card_class = 'course';
    $target_page = "courses.php";
    $btn_text = "Start Learning Courses";
    $status_icon = "book-open"; 
}

// Prepare the Search URL for redirection
$cat_url = urlencode($result_field);
$final_redirect_url = "$target_page?search=$cat_url";

// ========================================================
// JOB ROLES ARRAY
// ========================================================
$summaries = [
    'Sales' => [
        'icon' => 'trending-up',
        'summary' => "You thrive on negotiation, persuasion, and closing deals. You are driven by targets and enjoy the psychology of influence.",
        'jobs' => ['Sales Executive', 'Sales Manager', 'Business Development Executive (BDE)', 'Key Account Manager', 'Inside Sales Representative']
    ],
    'Marketing' => [
        'icon' => 'megaphone',
        'summary' => "You understand people and markets. You know how to tell a story that sells and analyzing trends excites you.",
        'jobs' => ['Digital Marketing Executive', 'Social Media Manager', 'SEO Specialist', 'Brand Manager', 'Content Marketer']
    ],
    'Information Technology (IT)' => [
        'icon' => 'cpu', 
        'summary' => "You are a logical problem solver. Whether it's coding, fixing hardware, or analyzing data, you build the systems the world runs on.",
        'jobs' => ['Software Developer', 'Full Stack Developer', 'Data Analyst', 'QA Tester', 'System Administrator']
    ],
    'Content Creator' => [ 
        'icon' => 'video',
        'summary' => "You have a unique voice and love storytelling. You know how to create engaging media that captures attention and builds an audience.",
        'jobs' => ['Content Creator', 'YouTuber', 'Video Editor', 'Script Writer', 'Reels/Shorts Creator']
    ],
    'Influencer' => [ 
        'icon' => 'star',
        'summary' => "You have a magnetic personality and the ability to influence others. You thrive in the spotlight and know how to build a personal brand.",
        'jobs' => ['Social Media Influencer', 'Brand Ambassador', 'Content Strategist', 'Community Manager']
    ],
    'Graphic Design' => [ 
        'icon' => 'palette',
        'summary' => "You communicate through visuals. You have an eye for aesthetics, color, and layout, turning concepts into stunning visual realities.",
        'jobs' => ['Graphic Designer', 'Illustrator', 'Visual Designer', 'Logo Designer', 'Creative Director']
    ],
    'UI/UX Designer' => [ 
        'icon' => 'layout',
        'summary' => "You blend creativity with logic. You care about how a user feels and interacts with digital products, making them both beautiful and functional.",
        'jobs' => ['UI Designer', 'UX Researcher', 'Product Designer', 'Interaction Designer', 'Web Designer']
    ],
    'Finance & Accounts' => [
        'icon' => 'dollar-sign',
        'summary' => "Precision is your superpower. You are good with numbers, managing risks, and ensuring financial health for individuals or organizations.",
        'jobs' => ['Accountant', 'Financial Analyst', 'Auditor', 'Investment Advisor', 'Bank Officer']
    ],
    'Human Resources (HR)' => [
        'icon' => 'users',
        'summary' => "You are a people person. You care about culture, talent, and resolving conflicts to build strong, effective teams.",
        'jobs' => ['HR Recruiter', 'HR Manager', 'Talent Acquisition Specialist', 'Training & Development Officer']
    ],
    'Administration / Management' => [
        'icon' => 'clipboard-list',
        'summary' => "You are the backbone of the organization. You keep things organized, scheduled, and running smoothly amidst chaos.",
        'jobs' => ['Office Administrator', 'Operations Manager', 'Project Manager', 'Executive Assistant', 'Team Leader']
    ],
    'Customer Support' => [
        'icon' => 'headset',
        'summary' => "You have endless patience and empathy. You love helping people solve their immediate problems and turning frowns upside down.",
        'jobs' => ['Customer Care Executive', 'Helpdesk Support', 'Client Relationship Manager', 'Technical Support Specialist']
    ]
];

// Fallback logic
if (!isset($summaries[$result_field])) {
    foreach ($summaries as $key => $val) {
        if (stripos($key, $result_field) !== false || stripos($result_field, $key) !== false) {
            $result_field = $key;
            break;
        }
    }
    if (!isset($summaries[$result_field])) {
        $result_field = 'Sales'; // Final Fallback
    }
}

$result_data = $summaries[$result_field];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Result - JobSure</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

<style>
    :root {
        --purple: #6e48ff;
        --purple-dark: #4e2ecf;
        --gradient: linear-gradient(90deg, #6e48ff, #4e2ecf);
        --light-gray: #f5f6ff;
        --white: #ffffff;
        --text-dark: #2d2d2d;
        --text-light: #5a5a5a;
        --success-bg: #dcfce7; --success-text: #166534;
        --info-bg: #dbeafe; --info-text: #1e40af;
        --warn-bg: #fef9c3; --warn-text: #854d0e;
    }
    body {
        font-family: "Poppins", sans-serif;
        background: var(--light-gray);
        text-align: center;
        padding: 40px 20px;
        overflow-x: hidden;
    }
    /* Popup Styles */
    #congrats-popup {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.45); display: flex; justify-content: center; align-items: center;
        animation: fadeIn .5s ease-out; z-index: 9999;
    }
    .popup-box {
        background: white; padding: 40px 60px; border-radius: 14px;
        text-align: center; animation: popIn .6s ease-out;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    .popup-box h2 { color: var(--purple-dark); font-size: 28px; margin-bottom: 10px; }
    
    /* Result Container */
    .result-container {
        max-width: 700px; margin: auto; background: white; padding: 50px;
        border-radius: 18px; box-shadow: 0 6px 22px rgba(0,0,0,0.08);
        animation: popIn .6s ease-out; position: relative; z-index: 10;
    }
    .result-icon {
        width: 70px; height: 70px; border-radius: 50%; padding: 15px;
        background: var(--gradient); color: white; margin-bottom: 18px;
        stroke-width: 1.3; animation: popIn .5s ease-out .3s backwards;
    }
    h1 { font-size: 18px; color: var(--text-light); letter-spacing: 1px; text-transform: uppercase; }
    h2 { font-size: 34px; color: var(--purple-dark); margin-top: 10px; font-weight: 700; }
    
    .summary { margin-top: 20px; font-size: 16px; color: var(--text-light); line-height: 1.7; padding: 0 20px; }
    
    /* SCORE CARD STYLES */
    .score-card {
        margin: 25px 0; padding: 25px; border-radius: 12px;
        display: flex; flex-direction: column; align-items: center; gap: 10px;
    }
    .score-card.job { background: var(--success-bg); color: var(--success-text); border: 1px solid #bbf7d0; }
    .score-card.internship { background: var(--info-bg); color: var(--info-text); border: 1px solid #bfdbfe; }
    .score-card.course { background: var(--warn-bg); color: var(--warn-text); border: 1px solid #fde047; }
    
    .status-icon-large { width: 48px; height: 48px; margin-bottom: 5px; }
    .score-rec { font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Tags & Buttons */
    .job-tag {
        background: #f4f0ff; border: 1px solid #d8caff; color: var(--purple-dark);
        padding: 7px 16px; border-radius: 22px; font-size: 14px; font-weight: 500;
    }
    .job-tag-list { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-top: 15px; }
    
    .button-row { margin-top: 35px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
    .btn {
        padding: 12px 28px; border-radius: 8px; font-size: 16px; font-weight: 600;
        border: none; cursor: pointer; text-decoration: none; transition: all 0.3s ease;
    }
    .btn-primary { background: var(--gradient); color: white; }
    .btn-primary:hover { opacity: .85; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(110,72,255,0.4); }
    .btn-secondary { background: white; color: var(--purple-dark); border: 2px solid #d7ccff; }
    .btn-secondary:hover { background: #f1ebff; }
    
    #confetti-canvas { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 999; }
    
    @keyframes popIn { 0% { transform: scale(.6); opacity: 0; } 70% { transform: scale(1.06); opacity: 1; } 100% { transform: scale(1); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity:1; } }
</style>
</head>

<body>

<div id="congrats-popup">
    <div class="popup-box">
        <h2>🎉 Congratulations, <?php echo htmlspecialchars($user_name); ?>!</h2>
        <p>You have completed the JobSure Career Assessment!</p>
    </div>
</div>

<canvas id="confetti-canvas"></canvas>

<div class="result-container">
    <i data-lucide="<?php echo $result_data['icon']; ?>" class="result-icon"></i>
    <h1>Your Career Profile</h1>
    <h2><?php echo $result_field; ?></h2>
    
    <div class="score-card <?php echo $card_class; ?>">
        <i data-lucide="<?php echo $status_icon; ?>" class="status-icon-large"></i>
        <div class="score-rec"><?php echo $status_msg; ?></div>
    </div>

    <p class="summary">
        Hi <strong><?php echo htmlspecialchars($user_name); ?></strong>!  
        <?php echo $result_data['summary']; ?>
    </p>

    <h3 style="margin-top:30px; color:var(--text-dark);">Ideal roles for you:</h3>
    <div class="job-tag-list">
        <?php foreach ($result_data['jobs'] as $job): ?>
            <span class="job-tag"><?php echo $job; ?></span>
        <?php endforeach; ?>
    </div>

    <div class="button-row">
        <button onclick="downloadAndRedirect('<?php echo $final_redirect_url; ?>')" class="btn btn-secondary">
            Download Certificate
        </button>
        
        <button onclick="downloadAndRedirect('<?php echo $final_redirect_url; ?>')" class="btn btn-primary">
            <?php echo $btn_text; ?> <i data-lucide="arrow-right" style="width:16px; height:16px; vertical-align:middle; margin-left:5px;"></i>
        </button>
    </div>
</div>

<script>
    lucide.createIcons();
    
    // DUAL ACTION: Download PDF -> Wait 1.5 Seconds -> Redirect Page
    function downloadAndRedirect(targetUrl) {
        // 1. Create a temporary link to trigger the download
        const link = document.createElement('a');
        link.href = 'certificate.php';
        link.setAttribute('download', 'JobSure_Certificate.pdf'); // Force download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // 2. Wait 1.5 seconds and then redirect
        setTimeout(() => { 
            window.location.href = targetUrl; 
        }, 1500);
    }
    
    // Hide Popup after 2.5 seconds
    setTimeout(() => {
        document.getElementById("congrats-popup").style.display = "none";
    }, 2500);

    // CONFETTI ANIMATION
    const canvas = document.getElementById("confetti-canvas");
    const ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const confettiPieces = [];
    const totalPieces = 120;
    const colors = ["#6e48ff", "#4e2ecf", "#b49aff", "#dcd2ff", "#38bdf8", "#4ade80"];
    
    for (let i = 0; i < totalPieces; i++) {
        confettiPieces.push({
            x: Math.random() * canvas.width,
            y: Math.random() * -canvas.height,
            size: 8 + Math.random() * 6,
            speed: 1 + Math.random() * 3,
            color: colors[Math.floor(Math.random() * colors.length)],
            rotation: Math.random() * 360
        });
    }
    
    function animateConfetti() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        confettiPieces.forEach(p => {
            ctx.save();
            ctx.fillStyle = p.color;
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
            ctx.restore();
            p.y += p.speed;
            p.rotation += 2;
            if (p.y > canvas.height) {
                p.y = -10;
                p.x = Math.random() * canvas.width;
            }
        });
        requestAnimationFrame(animateConfetti);
    }
    animateConfetti();
    
    // Handle Resize
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
</script>
</body>
</html>