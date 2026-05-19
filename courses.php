<?php
session_start();
$search_term = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : 'All';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended Courses - JobSure</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-6 h-16 flex items-center justify-between">
            <a href="index.php" class="flex items-center">
                <img src="logo.jpeg" alt="JobSure" class="h-8">
            </a>
            <div class="flex items-center gap-4">
                <span class="text-slate-500 hidden sm:inline">Welcome, <?php echo $_SESSION['username'] ?? 'User'; ?></span>
                <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium text-sm">Logout</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-10">
        
        <div class="text-center mb-12">
            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">Learning Path</span>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mt-4 mb-3">Recommended Courses for <span class="text-indigo-600"><?php echo $search_term; ?></span></h1>
            <p class="text-slate-500 max-w-2xl mx-auto">Based on your assessment, we recommend starting with these fundamental courses to build your skills.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="h-40 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white">
                    <i data-lucide="book-open" class="w-12 h-12 opacity-80"></i>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">Beginner</span>
                        <span class="flex items-center text-amber-500 text-sm font-bold"><i data-lucide="star" class="w-3 h-3 mr-1 fill-current"></i> 4.8</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Fundamentals of <?php echo $search_term; ?></h3>
                    <p class="text-slate-500 text-sm mb-4">Learn the core concepts and tools needed to start your career in this field.</p>
                    <button class="w-full py-2.5 border border-indigo-600 text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 transition-colors">View Details</button>
                </div>
            </div>

            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="h-40 bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white">
                    <i data-lucide="award" class="w-12 h-12 opacity-80"></i>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded">Certification</span>
                        <span class="flex items-center text-amber-500 text-sm font-bold"><i data-lucide="star" class="w-3 h-3 mr-1 fill-current"></i> 4.9</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Complete <?php echo $search_term; ?> Bootcamp</h3>
                    <p class="text-slate-500 text-sm mb-4">A comprehensive guide from basics to advanced practical applications.</p>
                    <button class="w-full py-2.5 border border-purple-600 text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition-colors">View Details</button>
                </div>
            </div>

            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="h-40 bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center text-white">
                    <i data-lucide="briefcase" class="w-12 h-12 opacity-80"></i>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">Practical</span>
                        <span class="flex items-center text-amber-500 text-sm font-bold"><i data-lucide="star" class="w-3 h-3 mr-1 fill-current"></i> 4.7</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">Real World <?php echo $search_term; ?> Projects</h3>
                    <p class="text-slate-500 text-sm mb-4">Build your portfolio by working on real-world scenarios and case studies.</p>
                    <button class="w-full py-2.5 border border-emerald-600 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition-colors">View Details</button>
                </div>
            </div>

        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>