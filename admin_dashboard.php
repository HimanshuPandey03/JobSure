<?php
session_start();

// 1. DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. FETCH STATS FOR DASHBOARD CARDS
$pending_count = 0;
$total_jobs = 0;
$total_internships = 0;

// Check tables exist to prevent crash on fresh install
$app_table_check = $conn->query("SHOW TABLES LIKE 'applications'");
$job_table_check = $conn->query("SHOW TABLES LIKE 'jobs'");

if ($app_table_check && $app_table_check->num_rows > 0) {
    $res = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'Pending'");
    if ($res) $pending_count = $res->fetch_assoc()['count'];
}

if ($job_table_check && $job_table_check->num_rows > 0) {
    $res_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE listing_type = 'job'");
    if ($res_jobs) $total_jobs = $res_jobs->fetch_assoc()['count'];

    $res_int = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE listing_type = 'internship'");
    if ($res_int) $total_internships = $res_int->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JobSure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #6e48ff;
            --primary-dark: #4e2ecf;
            --secondary-bg: #f3f4f6;
            --white: #ffffff;
            --text-main: #1f2937;
            --text-light: #6b7280;
            --border: #e5e7eb;
            --danger: #ef4444;
            --success: #10b981;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --gradient: linear-gradient(135deg, #6e48ff 0%, #9d7aff 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* --- HEADER --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            background: var(--white);
            padding: 15px 25px;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .brand h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .nav-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-light);
        }
        .btn-ghost:hover {
            background: var(--secondary-bg);
            color: var(--text-main);
        }

        .btn-outline {
            background: var(--white);
            border: 1px solid var(--border);
            color: var(--text-main);
            position: relative;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #fdfcff;
        }

        .btn-primary {
            background: var(--gradient);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(110, 72, 255, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(110, 72, 255, 0.35);
        }

        .badge {
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            position: absolute;
            top: -5px;
            right: -5px;
            font-weight: 700;
        }

        /* --- STATS CARDS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
            border: 1px solid var(--border);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            border-color: #dbeafe;
        }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-main);
        }

        .stat-info p {
            color: var(--text-light);
            font-size: 14px;
            font-weight: 500;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .bg-purple { background: #f3f0ff; color: var(--primary); }
        .bg-green { background: #ecfdf5; color: var(--success); }
        .bg-red { background: #fef2f2; color: var(--danger); }

        /* --- TABLE SECTION --- */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .table-container {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9fafb;
            text-align: left;
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-main);
        }

        tr:last-child td { border-bottom: none; }
        
        tr:hover { background-color: #f9fafb; }

        .job-title {
            font-weight: 600;
            color: var(--primary-dark);
        }
        
        .company-name {
            color: var(--text-light);
            font-size: 13px;
        }

        .type-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-job { background: #eff6ff; color: #1d4ed8; }
        .type-intern { background: #ecfdf5; color: #047857; }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit { background: #f3f4f6; color: var(--text-main); }
        .btn-edit:hover { background: #e5e7eb; }
        
        .btn-delete { background: #fef2f2; color: var(--danger); }
        .btn-delete:hover { background: #fee2e2; }

        /* --- MODAL --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.show { display: flex; opacity: 1; }
        
        .modal-content {
            background: var(--white);
            border-radius: 16px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid var(--border);
        }
        .modal-overlay.show .modal-content { transform: scale(1); }

        .modal-header {
            padding: 20px 30px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 { font-size: 20px; font-weight: 600; }
        
        .close-btn {
            background: none; border: none; font-size: 24px;
            color: var(--text-light); cursor: pointer;
        }
        
        .modal-body { padding: 30px; }
        
        .form-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
        }
        .col-2 { grid-column: span 2; }
        
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text-main); margin-bottom: 6px;
        }
        
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 1px solid var(--border); border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            transition: border 0.2s;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(110, 72, 255, 0.1);
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #f9fafb;
            border-radius: 0 0 16px 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 15px; align-items: flex-start; }
            .nav-actions { width: 100%; justify-content: space-between; }
            .form-grid { grid-template-columns: 1fr; }
            .col-2 { grid-column: span 1; }
        }
    </style>
</head>
<body>

    <div class="container">
       
        <header class="header">
            <div class="brand">
                <div class="stat-icon bg-purple" style="width: 40px; height: 40px; border-radius: 8px; font-size: 20px;">
                    <i data-lucide="layout-dashboard"></i>
                </div>
                <h1>Admin Portal</h1>
            </div>
            
            <div class="nav-actions">
                <a href="index.php" class="btn btn-ghost">
                    <i data-lucide="home" size="18"></i> Home
                </a>
                
                <a href="hr_dashboard.php" class="btn btn-outline">
                    <i data-lucide="users" size="18"></i> HR Panel
                    <?php if($pending_count > 0): ?>
                        <span class="badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </a>

                <a href="logout.php" class="btn btn-outline" style="color: var(--danger); border-color: #fee2e2; background: #fef2f2;">
                    <i data-lucide="log-out" size="18"></i> Logout
                </a>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_jobs; ?></h3>
                    <p>Active Jobs</p>
                </div>
                <div class="stat-icon bg-purple">
                    <i data-lucide="briefcase"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_internships; ?></h3>
                    <p>Active Internships</p>
                </div>
                <div class="stat-icon bg-green">
                    <i data-lucide="graduation-cap"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $pending_count; ?></h3>
                    <p>Pending Applications</p>
                </div>
                <div class="stat-icon bg-red">
                    <i data-lucide="file-clock"></i>
                </div>
            </div>
        </div>

        <div class="content-header">
            <h2>Current Listings</h2>
            <button id="add-job-btn" class="btn btn-primary">
                <i data-lucide="plus-circle"></i> Add New Listing
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Listing Details</th>
                        <th>Type</th>
                        <th>Compensation</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="jobs-table-body">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                            <i data-lucide="loader-2" class="animate-spin"></i> Loading data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="listing-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Create New Listing</h2>
                <button id="close-modal-btn" class="close-btn">&times;</button>
            </div>
            <form id="listing-form">
                <input type="hidden" id="edit-listing-id" name="id" value="">
                
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="listing_type">What type of listing is this?</label>
                        <select id="listing_type" name="listing_type" class="form-control" required>
                            <option value="job">Full Time Job</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="job_title">Title</label>
                            <input type="text" id="job_title" name="job_title" class="form-control" placeholder="e.g. Frontend Developer" required>
                        </div>
                        <div class="form-group">
                            <label for="company_name">Company</label>
                            <input type="text" id="company_name" name="company_name" class="form-control" placeholder="e.g. Google" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control" placeholder="City or Remote" required>
                        </div>
                        
                        <div class="form-group">
                            <label id="pay-label" for="pay_details">Salary / Stipend</label>
                            <input type="text" id="pay_details" name="pay_details" class="form-control" placeholder="e.g. 5 LPA">
                        </div>

                        <div class="form-group" id="duration-group" style="display: none;">
                            <label for="duration">Duration</label>
                            <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g. 3 Months">
                        </div>
                        
                        <div class="form-group">
                            <label for="qualification">Qualification</label>
                            <select id="qualification" name="qualification" class="form-control">
                                <option value="Any">Any</option>
                                <option value="HSC">HSC</option>
                                <option value="Graduate">Graduate</option>
                                <option value="Post-Graduate">Post-Graduate</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience">Experience</label>
                            <select id="experience" name="experience" class="form-control">
                                <option value="Fresher">Fresher</option>
                                <option value="0-1 Year">0-1 Year</option>
                                <option value="1-3 Years">1-3 Years</option>
                                <option value="3+ Years">3+ Years</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender Preference</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="Male / Female">Any</option>
                                <option value="Male Only">Male Only</option>
                                <option value="Female Only">Female Only</option>
                            </select>
                        </div>

                        <div class="form-group col-2">
                            <label for="skills">Skills (Comma separated)</label>
                            <input type="text" id="skills" name="skills" class="form-control" placeholder="e.g. React, Node.js, SQL">
                        </div>

                        <div class="form-group">
                            <label for="contact_person">Contact Name</label>
                            <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="HR Name">
                        </div>
                        <div class="form-group">
                            <label for="contact_phone">Contact Phone</label>
                            <input type="tel" id="contact_phone" name="contact_phone" class="form-control" placeholder="Phone Number">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancel-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Listing</button>
                </div>
            </form>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // --- Elements ---
        const modal = document.getElementById('listing-modal');
        const addJobBtn = document.getElementById('add-job-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const listingForm = document.getElementById('listing-form');
        const tableBody = document.getElementById('jobs-table-body');
        const modalTitle = document.getElementById('modal-title');
        const hiddenEditId = document.getElementById('edit-listing-id');
        
        // --- Form Interaction ---
        const listingTypeSelect = document.getElementById('listing_type');
        const payLabel = document.getElementById('pay-label');
        const payInput = document.getElementById('pay_details');
        const durationGroup = document.getElementById('duration-group');

        let isEditMode = false;

        // Toggle fields based on type
        function updateFormFields() {
            const type = listingTypeSelect.value;
            if (type === 'internship') {
                if(!isEditMode) modalTitle.textContent = 'Create New Internship';
                payLabel.textContent = 'Stipend Amount';
                payInput.placeholder = 'e.g. ₹5,000 /month';
                durationGroup.style.display = 'block';
            } else {
                if(!isEditMode) modalTitle.textContent = 'Create New Job';
                payLabel.textContent = 'Annual Salary (LPA)';
                payInput.placeholder = 'e.g. ₹15 - 25 LPA';
                durationGroup.style.display = 'none';
            }
        }
        
        // --- Fetch and Render Listings ---
        async function fetchJobs() {
            try {
                const response = await fetch('fetch_jobs.php');
                const jobs = await response.json();
                
                tableBody.innerHTML = ''; 

                if (!jobs || jobs.length === 0 || jobs.error) {
                    tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:30px; color:#9ca3af;">No active listings found.</td></tr>`;
                    return;
                }

                jobs.forEach(job => {
                    const row = document.createElement('tr');
                    
                    const badgeClass = job.listing_type === 'job' ? 'type-job' : 'type-intern';
                    const typeLabel = job.listing_type === 'job' ? 'Full Time' : 'Internship';

                    row.innerHTML = `
                        <td>
                            <div class="job-title">${job.job_title}</div>
                            <div class="company-name">${job.company_name}</div>
                        </td>
                        <td><span class="type-badge ${badgeClass}">${typeLabel}</span></td>
                        <td>${job.pay_details}</td>
                        <td>${job.location}</td>
                        <td class="actions">
                            <button class="action-btn btn-edit" data-id="${job.id}" title="Edit Listing">
                                <i data-lucide="pencil" size="16"></i>
                            </button>
                            <button class="action-btn btn-delete" data-id="${job.id}" title="Delete Listing">
                                <i data-lucide="trash-2" size="16"></i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                lucide.createIcons(); 

            } catch (error) {
                console.error('Fetch error:', error);
                tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:red;">Failed to load data.</td></tr>`;
            }
        }

        // --- Modal Logic ---
        function showModal() { modal.classList.add('show'); }
        function hideModal() { 
            modal.classList.remove('show'); 
            listingForm.reset(); 
            hiddenEditId.value = ''; 
            isEditMode = false; 
        }

        addJobBtn.addEventListener('click', () => {
            isEditMode = false;
            listingForm.reset();
            hiddenEditId.value = '';
            modalTitle.textContent = 'Create New Listing';
            updateFormFields();
            showModal();
        });

        [closeModalBtn, cancelBtn].forEach(btn => btn.addEventListener('click', hideModal));
        modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(); });
        listingTypeSelect.addEventListener('change', updateFormFields);

        // --- Save Listing ---
        listingForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const url = isEditMode ? 'update_listing.php' : 'add_job.php';
            const formData = new FormData(listingForm);
            
            try {
                const response = await fetch(url, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.success) {
                    hideModal();
                    fetchJobs();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert('An unexpected error occurred.');
            }
        });
        
        // --- Edit / Delete Actions ---
        tableBody.addEventListener('click', async (e) => {
            const editBtn = e.target.closest('.btn-edit');
            const deleteBtn = e.target.closest('.btn-delete');
            
            if (editBtn) {
                const id = editBtn.dataset.id;
                isEditMode = true;
                try {
                    const response = await fetch(`fetch_single_listing.php?id=${id}`);
                    const result = await response.json();
                    
                    if (result.success) {
                        const data = result.data;
                        modalTitle.textContent = `Edit: ${data.job_title}`;
                        hiddenEditId.value = data.id;
                        listingTypeSelect.value = data.listing_type;
                        
                        // Populate fields
                        document.getElementById('job_title').value = data.job_title;
                        document.getElementById('company_name').value = data.company_name;
                        document.getElementById('location').value = data.location;
                        document.getElementById('pay_details').value = data.pay_details;
                        document.getElementById('duration').value = data.duration;
                        document.getElementById('qualification').value = data.qualification;
                        document.getElementById('experience').value = data.experience;
                        document.getElementById('gender').value = data.gender;
                        document.getElementById('skills').value = data.skills;
                        document.getElementById('contact_person').value = data.contact_person;
                        document.getElementById('contact_phone').value = data.contact_phone;
                        
                        updateFormFields();
                        showModal();
                    } else { alert('Could not load listing details.'); }
                } catch (error) { console.error('Edit fetch error:', error); }
            }
            
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                if (confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
                    try {
                        const formData = new FormData();
                        formData.append('id', id);
                        const response = await fetch('delete_job.php', { method: 'POST', body: formData });
                        const result = await response.json();
                        if (result.success) { fetchJobs(); } 
                        else { alert('Failed to delete listing.'); }
                    } catch (error) { console.error('Delete error:', error); }
                }
            }
        });

        // Load data on page load
        fetchJobs();
      });
    </script>
</body>
</html>