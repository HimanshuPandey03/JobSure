<?php
session_start();

// Security Check
if (!isset($_SESSION["loggedin"]) || !isset($_SESSION['assessment_breakdown'])) {
    header("location: index.php");
    exit;
}

// $breakdown contains: ['Sales' => 80, 'IT' => 40]
$breakdown = $_SESSION['assessment_breakdown'];
$user_name = htmlspecialchars($_SESSION['username']);

// Helper for Labels & "Tasty" Colors
function getAnalysisStyle($percent) {
    if ($percent >= 76) {
        return [
            'label' => 'Excellent 🌟',
            'color' => 'text-emerald-600', 
            'bg_bar' => 'bg-gradient-to-r from-emerald-300 to-emerald-400',
            'bg_card' => 'bg-emerald-50/90',
            'border' => 'border-emerald-200',
            'icon_bg' => 'bg-emerald-100'
        ];
    } elseif ($percent >= 41) {
        return [
            'label' => 'Good 🚀',
            'color' => 'text-indigo-600', 
            'bg_bar' => 'bg-gradient-to-r from-indigo-300 to-purple-400', // Blueberry/Grape vibe
            'bg_card' => 'bg-indigo-50/90',
            'border' => 'border-indigo-200',
            'icon_bg' => 'bg-indigo-100'
        ];
    } else {
        return [
            'label' => 'Moderate 🌱',    
            'color' => 'text-orange-600', 
            'bg_bar' => 'bg-gradient-to-r from-orange-300 to-amber-400', // Bakery vibe
            'bg_card' => 'bg-orange-50/90',
            'border' => 'border-orange-200',
            'icon_bg' => 'bg-orange-100'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Skills - JobSure</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            /* Soft "Sugar" Gradient Background */
            background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
            background-image: linear-gradient(to top, #fff1eb 0%, #ace0f9 100%);
            overflow-x: hidden;
            color: #4a4a4a;
        }
        h1, h2, h3, .fun-font { font-family: 'Fredoka', sans-serif; }
        
        /* Floating Food Animation */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-25px) rotate(10deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .float-food {
            position: absolute;
            font-size: 3.5rem;
            opacity: 0.7; /* Make them clearer */
            animation: float 7s ease-in-out infinite;
            z-index: -1;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
            pointer-events: none;
        }

        /* Card Pop Animation */
        .card-pop { animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; opacity: 0; transform: scale(0.9); }
        @keyframes popIn { to { opacity: 1; transform: scale(1); } }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        /* Yummy Glassmorphism */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 relative">

    <div class="float-food" style="top: 8%; left: 8%; animation-duration: 8s;">🍩</div>
    <div class="float-food" style="top: 15%; right: 12%; animation-duration: 6s; font-size: 4.5rem;">🍕</div>
    <div class="float-food" style="bottom: 20%; left: 15%; animation-duration: 9s;">🍦</div>
    <div class="float-food" style="bottom: 10%; right: 8%; animation-duration: 7s;">🧁</div>
    <div class="float-food" style="top: 45%; left: 85%; animation-duration: 10s;">🍔</div>
    <div class="float-food" style="top: 55%; left: 5%; animation-duration: 7.5s; font-size: 3rem;">🍿</div>
    <div class="float-food" style="top: 5%; left: 50%; animation-duration: 11s;">🍪</div>

    <div class="text-center mb-10 card-pop z-10">
        <h1 class="text-4xl font-bold text-slate-800 mb-2 tracking-wide">Skills Menu 🧾</h1>
        <p class="text-slate-500">Serving up your results, <b><?php echo $user_name; ?></b>!</p>
    </div>

    <div class="w-full max-w-lg space-y-5 z-10">
        <?php 
        $i = 0;
        foreach ($breakdown as $category => $percent): 
            $style = getAnalysisStyle($percent);
            $delay = "delay-" . (++$i * 100);
        ?>
        <div class="card-pop <?php echo $delay; ?> glass-card <?php echo $style['bg_card']; ?> border <?php echo $style['border']; ?> p-6 rounded-3xl flex items-center justify-between transition-transform hover:scale-[1.02]">
            
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full <?php echo $style['icon_bg']; ?> flex items-center justify-center text-2xl shadow-sm border border-white">
                    <?php 
                        // Dynamic Icon based on Category
                        if(stripos($category, 'Market') !== false) echo '📣';
                        elseif(stripos($category, 'Design') !== false) echo '🎨';
                        elseif(stripos($category, 'IT') !== false || stripos($category, 'Web') !== false) echo '💻';
                        elseif(stripos($category, 'Sales') !== false) echo '🤝';
                        elseif(stripos($category, 'Financ') !== false) echo '💳';
                        elseif(stripos($category, 'Influenc') !== false) echo '✨';
                        else echo '🔥';
                    ?>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xl fun-font"><?php echo htmlspecialchars($category); ?></h3>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider opacity-80">Skill Level</p>
                </div>
            </div>

            <div class="text-right">
                <span class="block font-bold text-lg <?php echo $style['color']; ?> fun-font mb-1">
                    <?php echo $style['label']; ?>
                </span>
                
                <div class="w-28 h-3 bg-white rounded-full overflow-hidden shadow-inner ml-auto border border-white">
                    <div class="h-full rounded-full <?php echo $style['bg_bar']; ?>" style="width: <?php echo $percent; ?>%"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-12 text-center card-pop delay-300 z-10">
        <a href="assessment_result.php" class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-indigo-500 font-pj rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:bg-indigo-600 shadow-xl hover:-translate-y-1">
            See My Best Match
            <div class="absolute -top-3 -right-3 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full animate-bounce shadow-md">
                Ready!
            </div>
            <i data-lucide="arrow-right" class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>