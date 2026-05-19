<?php
// 1. AUTHENTICATION
session_start();
require 'db_config.php'; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['login_error'] = "You must be logged in to take the assessment.";
    header("location: index.php");
    exit;
}
$user_id = $_SESSION["id"];

// 2. CHECK IF ALREADY COMPLETED
$is_assessment_completed = $_SESSION["quiz_completed"] ?? 0;
if ($is_assessment_completed == 1) {
    header("location: assessment_result.php");
    exit;
}

// =======================================================================
// 3. FETCH USER'S SELECTED INTERESTS
// =======================================================================
$selected_interests = []; 
$display_name = htmlspecialchars($_SESSION['username'] ?? 'User');

if (isset($conn)) {
    $sql_fetch_interests = "SELECT areas_of_interest FROM profiles WHERE user_id = ?";
    if ($stmt_fetch = $conn->prepare($sql_fetch_interests)) {
        $stmt_fetch->bind_param("i", $user_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($row = $result->fetch_assoc()) {
            $interests_string = trim($row['areas_of_interest'] ?? '');
            if (!empty($interests_string)) {
                $selected_interests = array_filter(array_map('trim', explode(',', $interests_string)));
            }
        }
        $stmt_fetch->close();
    }
}

if (empty($selected_interests)) {
    $_SESSION['profile_status'] = "⚠️ You need to select your Areas of Interest first!";
    header("location: profile.php");
    exit;
}

// ========================================================
// 4. FETCH QUESTIONS (EXACT MATCH FIX)
// ========================================================
$final_questions = [];
$num_interests = count($selected_interests); // e.g., 2, 3, 4, or 5

// MATH: Total 25 questions divided by number of interests
$limit_per_cat = floor(25 / $num_interests); 
$remainder = 25 % $num_interests;

foreach ($selected_interests as $index => $cat) {
    // LOGIC: Distribute the remainder. 
    $this_cat_limit = $limit_per_cat + ($index < $remainder ? 1 : 0);
    
    // FIX: Use Exact string match (Removed %)
    $cat_search = $conn->real_escape_string($cat);
    
    // FIX: Changed 'LIKE' to '=' so Marketing doesn't grab Digital Marketing
    $sql = "SELECT q.id, q.category, q.section, q.question_text 
            FROM assessment_questions q 
            WHERE q.category = ? 
            ORDER BY q.section ASC 
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cat_search, $this_cat_limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $q_id = $row['id'];
        
        // Fetch Options
        $opts_sql = "SELECT id, option_text, option_label FROM assessment_options WHERE question_id = $q_id ORDER BY option_label ASC";
        $opts_res = $conn->query($opts_sql);
        $options = [];
        while ($opt = $opts_res->fetch_assoc()) {
            $options[$opt['option_label']] = $opt['option_text']; 
        }
        $row['options'] = $options;
        $final_questions[] = $row;
    }
}

// 5. MERGER & SHUFFLE LOGIC
// First, shuffle everything so categories are mixed randomly
shuffle($final_questions);

// Then, SORT BY SECTION (A -> B -> C -> D -> E)
usort($final_questions, function($a, $b) {
    return strcmp($a['section'], $b['section']);
});

$total_questions = count($final_questions); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Assessment - JobSure</title>
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
        }
        * { box-sizing: border-box; }
        @keyframes bgMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        body {
            background: linear-gradient(120deg, #f6f0ff, #e9f6ff, #fff0f5);
            background-size: 250% 250%;
            animation: bgMove 12s ease infinite;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
            padding: 40px 20px;
        }
        
        /* --- Animations --- */
        .floating-shape { position: absolute; width: 110px; opacity: 0.18; animation: floaty 8s infinite ease-in-out; pointer-events: none; }
        @keyframes floaty { 0% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-35px) rotate(15deg); } 100% { transform: translateY(0) rotate(0deg); } }
        .floating-emoji { position: absolute; font-size: 55px; opacity: 0.28; animation: bobble 6s infinite ease-in-out; pointer-events: none; }
        @keyframes bobble { 0% { transform: translateY(0); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0); } }
        
        /* --- Quiz Layout --- */
        .quiz-container { width: 100%; max-width: 600px; margin-top: 20px; animation: popIn 0.6s ease-out; position: relative; z-index: 1; }
        @keyframes popIn { 0% { transform: scale(.6); opacity: 0; } 70% { transform: scale(1.02); opacity: 1; } 100% { transform: scale(1); } }
        .quiz-form { background: var(--white); border-radius: 12px; box-shadow: var(--shadow); border: 1px solid var(--border-color); overflow: hidden; }
        .quiz-header { padding: 30px 40px; text-align: center; border-bottom: 1px solid var(--border-color); margin-top:1px; }
        .quiz-header h1 { font-size: 24px; font-weight: 600; color: var(--text-dark); margin-bottom: 0px; }
        .quiz-header p { font-size: 15px; color: var(--text-light); margin-top: 0px; }
        
        /* --- Slider --- */
        .question-slider { display: flex; width: 100%; transition: transform 0.4s ease-in-out; }
        .question-slide { width: 100%; flex-shrink: 0; padding: 40px; min-height: 480px; display: flex; flex-direction: column; }
        
        /* --- Question Elements --- */
        .quiz-illustration { text-align: center; margin-bottom: 20px; animation: float 6s ease-in-out infinite; }
        .quiz-illustration i { width: 80px; height: 80px; color: var(--primary-dark); stroke-width: 1.5; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .question-slide > * { animation: fadeIn 0.5s ease-out 0.3s; animation-fill-mode: backwards; }
        .question-slide > *:nth-child(2) { animation-delay: 0.4s; }
        .question-slide > *:nth-child(3) { animation-delay: 0.5s; }
        .question-slide > *:nth-child(4) { animation-delay: 0.6s; }
        .question-slide > *:nth-child(5) { animation-delay: 0.7s; }
        .question-counter { font-size: 14px; font-weight: 600; color: var(--primary-dark); margin-bottom: 10px; }
        .question-text { font-size: 20px; font-weight: 500; margin-bottom: 24px; }
        
        /* --- Options --- */
        .answer-options { display: flex; flex-direction: column; gap: 15px; flex-grow: 1; }
        .answer-option { display: block; background: var(--white); border: 1px solid var(--border-color); padding: 15px 20px; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; font-weight: 500; color: var(--text-dark); position: relative; }
        .answer-option:hover { transform: translateY(-2px) scale(1.02); border-color: var(--primary-dark); background: #eef2ff; }
        .answer-option input { position: absolute; opacity: 0; width: 0; height: 0; cursor: pointer; }
        .answer-option:has(input:checked) { background: #eef2ff; color: var(--primary-dark); border-color: var(--primary-dark); font-weight: 600; box-shadow: 0 0 0 1px var(--primary-dark); }
        
        /* --- Progress & Buttons --- */
        .progress-bar-container { padding: 0px 40px 20px; }
        .progress-bar { width: 0%; height: 10px; background: var(--gradient); border-radius: 10px; transition: width 0.4s ease; }
        @keyframes pulse { 0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(110, 72, 255, 0.3); } 70% { transform: scale(1.03); box-shadow: 0 0 0 10px rgba(110, 72, 255, 0); } 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(110, 72, 255, 0); } }
        .btn-submit, .btn-start-assessment { width: 100%; padding: 15px; border-radius: 8px; font-weight: 600; font-size: 16px; color: var(--white); border: none; cursor: pointer; background: var(--gradient); transition: all 0.3s ease; }
        .btn-submit:hover, .btn-start-assessment:hover { opacity: 0.9; box-shadow: 0 4px 15px rgba(110, 72, 255, 0.3); transform: scale(1.03); }
        .btn-start-assessment { animation: pulse 2s infinite; }
        .quiz-nav { display: flex; justify-content: space-between; gap: 10px; margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 30px; }
        .btn-nav { padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 15px; border: 1px solid var(--border-color); background: var(--white); color: var(--text-dark); cursor: pointer; transition: all 0.3s ease; }
        .btn-nav:hover { background: var(--light-gray); border-color: var(--text-dark); transform: translateY(-2px); }
        .btn-next { background: var(--primary-dark); color: var(--white); border-color: var(--primary-dark); }
        .btn-next:hover { background: var(--primary-light); color: var(--white); border-color: var(--primary-light); }
        .quiz-nav .btn-nav, .quiz-nav .btn-submit { flex: 1; width: 100%; }

        /* --- POPUP STYLES --- */
        .milestone-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(45, 35, 66, 0.7); 
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: none; 
            justify-content: center; align-items: center;
            animation: fadeInOverlay 0.4s ease-out;
        }
        .milestone-popup {
            background: white;
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            position: relative;
            box-shadow: 0 20px 60px rgba(110, 72, 255, 0.4);
            animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            border: 4px solid #fff;
            background-clip: padding-box;
        }
        .milestone-popup::before {
            content: ''; position: absolute; top: -4px; bottom: -4px; left: -4px; right: -4px;
            background: var(--gradient); border-radius: 28px; z-index: -1;
        }
        .milestone-emoji {
            font-size: 64px; display: block; margin-bottom: 10px;
            animation: wiggle 1s ease-in-out infinite;
        }
        .milestone-title {
            font-size: 26px; font-weight: 700;
            background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        .milestone-text {
            font-size: 16px; color: var(--text-light); margin-bottom: 25px; line-height: 1.5;
        }
        .milestone-text b {
            color: var(--primary-dark);
            font-weight: 600;
        }
        .btn-milestone {
            background: var(--gradient); color: white; border: none;
            padding: 12px 35px; border-radius: 50px; font-weight: 600; font-size: 16px;
            cursor: pointer; transition: all 0.3s; box-shadow: 0 8px 20px rgba(110, 72, 255, 0.3);
        }
        .btn-milestone:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 12px 30px rgba(110, 72, 255, 0.5); }
        
        @keyframes fadeInOverlay { from { opacity: 0; } to { opacity: 1; } }
        @keyframes bounceIn { 0% { transform: scale(0.3); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        @keyframes wiggle { 0%, 100% { transform: rotate(-5deg); } 50% { transform: rotate(5deg); } }
    </style>
</head>
<body>

<img src="https://cdn-icons-png.flaticon.com/512/742/742751.png" class="floating-shape" style="top:5%; left:7%; animation-duration: 6.5s;">
<img src="https://cdn-icons-png.flaticon.com/512/742/742831.png" class="floating-shape" style="top:70%; left:80%; animation-duration: 9s;">
<img src="https://cdn-icons-png.flaticon.com/512/742/742925.png" class="floating-shape" style="top:40%; left:90%; animation-duration: 7.5s;">
<img src="https://cdn-icons-png.flaticon.com/512/742/742774.png" class="floating-shape" style="top:80%; left:10%; animation-duration: 10s;">
<div class="floating-emoji" style="top:15%; left:50%;">🌈</div>
<div class="floating-emoji" style="top:30%; left:75%;">✨</div>
<div class="floating-emoji" style="top:30%; left:15%;">🫧</div>
<div class="floating-emoji" style="top:80%; left:55%;">🎨</div>
<div class="floating-emoji" style="top:60%; left:30%;">💡</div>
<div class="floating-emoji" style="top:3%; left:95%;">🧸</div>

<div id="milestone-overlay" class="milestone-overlay">
    <div class="milestone-popup">
        <span id="milestone-emoji" class="milestone-emoji">🚀</span>
        <h2 id="milestone-title" class="milestone-title">Great Start!</h2>
        <p id="milestone-text" class="milestone-text">You've crushed the first section. Keep going!</p>
        <button class="btn-milestone" onclick="closeMilestone()">Continue ✨</button>
    </div>
</div>

<div class="quiz-container">
    <div class="quiz-header">
        <h1>Career Assessment</h1>
        <p>Answer these questions to find your path!</p>
    </div>
    
    <form class="quiz-form" action="save_assessment.php" method="POST">
        <div class="question-slider" id="question-slider">

            <div class="question-slide welcome-slide" data-slide="0">
                <div class="quiz-illustration"><i data-lucide="lightbulb"></i></div>
                <h2 class="question-text" style="text-align: center; font-size: 24px; font-weight: 700; margin-top: 10px;">
                    Welcome, <?php echo $display_name; ?>!
                </h2>
                <p style="color: var(--text-light); margin-bottom: 10px;">This assessment is customized for your interests: <strong style="color:var(--primary-dark); font-weight:700;">(<?php echo implode(', ', $selected_interests); ?>)</strong>. We'll recommend a career field that's right for you.</p>
                <p style="color: var(--text-light); margin-bottom: 2px;">There are no wrong answers! Just pick the one that feels most like you.</p>
                <p style="color: var(--text-light); margin-bottom: 0px;"><u>Note:</u> Don't refresh the page after the assessment starts. Complete all <?php echo $total_questions; ?> questions in one go.</p>

                <div style="flex-grow: 1;"></div>
                <button type="button" class="btn-start-assessment" style="margin-top: 30px;" onclick="nextSlide(false)">Start Assessment</button>
            </div>
            
            <?php foreach ($final_questions as $q_index => $question): ?>
            <div class="question-slide" data-slide="<?php echo $q_index + 1; ?>">
                <p class="question-counter">Question <?php echo $q_index + 1; ?> / <?php echo $total_questions; ?></p>
                
                <p class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>
                
                <input type="hidden" name="cat_<?php echo $q_index + 1; ?>" value="<?php echo $question['category']; ?>">

                <div class="answer-options">
                    <?php
                    $option_labels = ['A', 'B', 'C', 'D'];
                    foreach ($option_labels as $label):
                        if (isset($question['options'][$label])):
                    ?>
                        <label class="answer-option">
                            <input type="radio" name="q<?php echo $q_index + 1; ?>" value="<?php echo $label; ?>" required> 
                            <?php echo htmlspecialchars($question['options'][$label]); ?>
                        </label>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <div class="quiz-nav">
                    <button type="button" class="btn-nav" onclick="prevSlide()" <?php echo ($q_index == 0) ? 'style="visibility:hidden;"' : ''; ?>>Previous</button>
                    <button type="button" class="btn-nav btn-next" onclick="nextSlide()">
                        <?php echo ($q_index == $total_questions - 1) ? 'Finish' : 'Next'; ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="question-slide" data-slide="<?php echo $total_questions + 1; ?>">
                <div class="quiz-illustration"><i data-lucide="party-popper"></i></div>
                <h2 class="question-text" style="text-align: center;">You're all done!</h2>
                <p style="color: var(--text-light); margin-bottom: 20px;">You've answered all the questions. Click the button below to see your personalized career recommendation!</p>
                <div style="flex-grow: 1;"></div>
                <div class="quiz-nav"><button type="button" class="btn-nav" onclick="prevSlide()">Previous</button><button type="submit" class="btn-submit">See My Result</button></div>
            </div>
            
        </div>
        
        <div class="progress-bar-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
    </form>
</div>

<script>
    let currentSlide = 0;
    const totalQuestions = <?php echo $total_questions; ?>;
    const totalSlides = totalQuestions + 2; 
    const slider = document.getElementById('question-slider');
    const progressBar = document.getElementById('progress-bar');
    
    const overlay = document.getElementById('milestone-overlay');
    const mEmoji = document.getElementById('milestone-emoji');
    const mTitle = document.getElementById('milestone-title');
    const mText = document.getElementById('milestone-text');

    const milestones = {
        5: {
            emoji: "💖",
            title: "Vibe Check Passed! ✨",
            text: "You just aced the <b>Personal & Motivation</b> section. Loving the energy! Let's see what you know..."
        },
        10: {
            emoji: "🧠",
            title: "Big Brain Energy ⚡",
            text: "<b>Knowledge & Interest</b>? Done. You're crushing the technical side! Ready for some real-world drama?"
        },
        15: {
            emoji: "🕵️‍♀️",
            title: "Problem Solver Mode 🧩",
            text: "<b>Practical Situations</b> handled like a pro. You know how to navigate tricky spots. Next up: Social Skills!"
        },
        20: {
            emoji: "💬",
            title: "Smooth Operator 🤝",
            text: "<b>Communication & Behavioral</b> section complete! Just one last stretch to define your <b>Future Goals</b>. Finish strong!"
        }
    };

    function nextSlide(validate = true) {
        if (validate && currentSlide > 0 && currentSlide <= totalQuestions) {
            const radios = document.querySelector(`[data-slide="${currentSlide}"]`).querySelectorAll('input[type="radio"]');
            if (![...radios].some(r => r.checked)) {
                alert('Please select an answer to continue.');
                return;
            }
        }
        
        if (validate && currentSlide < totalQuestions && milestones[currentSlide]) {
            showMilestone(currentSlide);
            return; 
        }

        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateSlide();
        }
    }
    
    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide--;
            updateSlide();
        }
    }
    
    function updateSlide() {
        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        let percent = 0;
        if(currentSlide > 0 && currentSlide <= totalQuestions) {
            percent = (currentSlide) / totalQuestions * 100;
        } else if (currentSlide > totalQuestions) {
            percent = 100;
        }
        progressBar.style.width = `${percent}%`;
    }

    function showMilestone(index) {
        const data = milestones[index];
        mEmoji.textContent = data.emoji;
        mTitle.textContent = data.title;
        mText.innerHTML = data.text; 
        overlay.style.display = "flex"; 
    }

    function closeMilestone() {
        overlay.style.display = "none"; 
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateSlide();
        }
    }

    lucide.createIcons();
    updateSlide(); 
</script>
</body>
</html>