<?php
// ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "2008", "jobsure_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// FETCH LIVE STATS (Only Pending, Shortlisted, Rejected)
$stats = ['pending' => 0, 'shortlisted' => 0, 'rejected' => 0];

$check = $conn->query("SHOW TABLES LIKE 'applications'");
if ($check && $check->num_rows > 0) {
    $result = $conn->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status");
    while ($row = $result->fetch_assoc()) {
        $s = trim($row['status']);
        
        // 1. Pending
        if (stripos($s, 'Applied') !== false || stripos($s, 'Pending') !== false) {
            $stats['pending'] += $row['count'];
        }
        // 2. Shortlisted (Accepted)
        if (stripos($s, 'Shortlisted') !== false) {
            $stats['shortlisted'] += $row['count'];
        }
        // 3. Rejected
        if (stripos($s, 'Rejected') !== false) {
            $stats['rejected'] += $row['count'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment Dashboard - JobSure</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #6366f1; 
            --secondary-bg: #f8fafc; --white: #ffffff;
            --text-main: #0f172a; --text-light: #64748b;
            --border: #e2e8f0; --danger: #ef4444; --success: #10b981;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--secondary-bg); color: var(--text-main); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: var(--white); padding: 20px 30px; border-radius: 12px; border: 1px solid var(--border); }
        .brand h1 { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .btn-header { text-decoration: none; padding: 8px 16px; border: 1px solid var(--border); border-radius: 6px; color: var(--text-main); font-size: 14px; }
        .btn-header:hover { background: #f1f5f9; }

        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--white); padding: 24px; border-radius: 12px; border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .stat-info h3 { font-size: 32px; font-weight: 700; margin-bottom: 5px; }
        .stat-info p { color: var(--text-light); font-size: 14px; }

        /* Table */
        .table-wrapper { background: var(--white); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; text-align: left; padding: 18px 24px; font-size: 13px; font-weight: 600; color: var(--text-light); border-bottom: 1px solid var(--border); }
        td { padding: 20px 24px; border-bottom: 1px solid var(--border); font-size: 14px; vertical-align: middle; }
        
        /* Badges */
        .badge { padding: 6px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .badge-pending { background: #fff7ed; color: #ea580c; }
        .badge-shortlisted { background: #eff6ff; color: #2563eb; }
        .badge-rejected { background: #fef2f2; color: #dc2626; }

        /* Buttons */
        .action-group { display: flex; gap: 8px; justify-content: flex-end; }
        .btn-action { padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
        
        .btn-resume { background: white; border: 1px solid var(--border); color: var(--text-main); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-resume:hover { border-color: var(--primary); color: var(--primary); }

        .btn-accept { background: #dcfce7; color: #15803d; }
        .btn-accept:hover { background: #bbf7d0; }

        .btn-reject { background: #fee2e2; color: #b91c1c; }
        .btn-reject:hover { background: #fecaca; }

    </style>
</head>
<body>

<div class="container">
    <header class="header">
        <div class="brand"><h1><i data-lucide="layout-dashboard"></i> Recruitment</h1></div>
        <a href="admin_dashboard.php" class="btn-header">Back to Admin</a>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info"><h3><?php echo $stats['pending']; ?></h3><p>Pending Review</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3><?php echo $stats['shortlisted']; ?></h3><p>Shortlisted</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-info"><h3><?php echo $stats['rejected']; ?></h3><p>Rejected</p></div>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Candidate</th>
                    <th style="width: 25%;">Role</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 30%; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch User Name, Email, Job Title, and Resume
                $sql = "SELECT 
                            a.id AS app_id, 
                            a.status, 
                            a.applied_at, 
                            j.job_title, 
                            u.first_name, 
                            u.last_name, 
                            u.email,
                            p.resume_path
                        FROM applications a 
                        JOIN jobs j ON a.job_id = j.id 
                        JOIN users u ON a.user_id = u.id 
                        LEFT JOIN profiles p ON a.user_id = p.user_id
                        ORDER BY a.id DESC";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        
                        $status = trim($row['status']);
                        $fullName = $row['first_name'] . ' ' . $row['last_name'];
                        
                        // Badge Logic
                        $badgeClass = 'badge-pending';
                        if(stripos($status, 'Shortlisted') !== false) $badgeClass = 'badge-shortlisted';
                        if(stripos($status, 'Rejected') !== false) $badgeClass = 'badge-rejected';

                        echo "<tr>";
                        
                        // Col 1: Candidate
                        echo "<td>
                                <div style='font-weight:600;'>$fullName</div>
                                <div style='font-size:12px; color:var(--text-light);'>{$row['email']}</div>
                              </td>";

                        // Col 2: Role
                        echo "<td>{$row['job_title']}</td>";

                        // Col 3: Status
                        echo "<td><span class='badge $badgeClass'>$status</span></td>";

                        // Col 4: Actions
                        echo "<td><div class='action-group'>";

                        // Logic: Only show buttons if Pending or Applied
                        if (stripos($status, 'Applied') !== false || stripos($status, 'Pending') !== false) {
                            
                            // View Resume
                            if(!empty($row['resume_path'])) {
                                echo "<a href='{$row['resume_path']}' target='_blank' class='btn-action btn-resume' title='View Resume'><i data-lucide='eye' size='14'></i></a>";
                            }
                            
                            // Accept (Shortlist)
                            echo "<button onclick='updateStatus({$row['app_id']}, \"Shortlisted\")' class='btn-action btn-accept'>Accept</button>";
                            
                            // Reject
                            echo "<button onclick='updateStatus({$row['app_id']}, \"Rejected\")' class='btn-action btn-reject'>Reject</button>";
                        }
                        // If already Shortlisted
                        elseif (stripos($status, 'Shortlisted') !== false) {
                            echo "<span style='font-size:12px; color:#15803d; font-weight:600; display:flex; align-items:center; gap:5px;'><i data-lucide='check' size='14'></i> Mail Sent</span>";
                        }
                        // If Rejected
                        elseif (stripos($status, 'Rejected') !== false) {
                            echo "<span style='font-size:12px; color:var(--text-light);'>Closed</span>";
                        }

                        echo "</div></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding:30px;'>No applications found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    lucide.createIcons();

    async function updateStatus(id, status) {
        if(!confirm('Are you sure you want to change status to ' + status + '?')) return;
        
        const fd = new FormData();
        fd.append('action_type', 'update_status');
        fd.append('app_id', id);
        fd.append('status', status);

        try {
            const res = await fetch('hr_actions.php', { method: 'POST', body: fd });
            const data = await res.json();
            
            if(data.success) {
                // Reload to see changes
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            console.error(err);
            alert('Server connection failed');
        }
    }
</script>

</body>
</html>