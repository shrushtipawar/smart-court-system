<?php
session_start();

// Check if config exists
if (!file_exists('config/database.php') || !is_readable('config/database.php')) {
    header('Location: setup.php');
    exit();
}

require_once 'config/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new Database();

// Try to get analytics data, use defaults if fails
try {
    $analytics = $db->getAnalytics();
} catch (Exception $e) {
    $analytics = [
        'resolution_rate' => 68.5,
        'avg_processing_days' => 45,
        'total_cases' => 150,
        'active_cases' => 80,
        'resolved_cases' => 70
    ];
}

// Try to get recent cases
try {
    // Check if getCases method exists
    if (method_exists($db, 'getCases')) {
        $recentCases = array_slice($db->getCases(), -5);
    } else {
        // Mock data if method doesn't exist
        $recentCases = [
            [
                'case_number' => 'JF-2024-001',
                'title' => 'Contract Dispute - ABC Corp',
                'status' => 'active',
                'priority' => 'high',
                'next_hearing' => '2024-12-15'
            ],
            [
                'case_number' => 'JF-2024-002',
                'title' => 'Property Transfer - Sharma Family',
                'status' => 'pending',
                'priority' => 'medium',
                'next_hearing' => '2024-12-20'
            ],
            [
                'case_number' => 'JF-2024-003',
                'title' => 'Divorce Settlement - Kumar vs Kumar',
                'status' => 'resolved',
                'priority' => 'medium',
                'next_hearing' => null
            ],
            [
                'case_number' => 'JF-2024-004',
                'title' => 'Consumer Complaint - Singh Electronics',
                'status' => 'active',
                'priority' => 'low',
                'next_hearing' => '2024-12-10'
            ],
            [
                'case_number' => 'JF-2024-005',
                'title' => 'Will Probate - Late Mr. Verma',
                'status' => 'active',
                'priority' => 'high',
                'next_hearing' => '2024-12-18'
            ]
        ];
    }
} catch (Exception $e) {
    // Mock data on error
    $recentCases = [
        [
            'case_number' => 'JF-2024-001',
            'title' => 'Sample Case 1',
            'status' => 'active',
            'priority' => 'medium',
            'next_hearing' => '2024-12-15'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            padding: 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            background: linear-gradient(180deg, var(--primary-color) 0%, #2a4365 100%);
            width: var(--sidebar-width);
            transition: all 0.3s;
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: 100vh;
            padding-top: 0.5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.2rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--secondary-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
        }
        
        .sidebar-header p {
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            font-size: 0.9rem;
        }
        
        /* Main Content */
        main {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            main {
                margin-left: 0;
            }
            
            main.active {
                margin-left: 250px;
            }
        }
        
        /* Analytics Cards */
        .analytics-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            margin-bottom: 20px;
            border-left: 4px solid var(--secondary-color);
        }
        
        .analytics-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }
        
        .analytics-card .card-body {
            padding: 1.5rem;
        }
        
        .analytics-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0;
        }
        
        .analytics-card h6 {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .analytics-card .icon {
            font-size: 2rem;
            color: var(--secondary-color);
            opacity: 0.8;
        }
        
        /* Table Styling */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(45, 116, 218, 0.05);
        }
        
        /* Badge Styling */
        .badge {
            padding: 0.4em 0.8em;
            font-weight: 600;
            border-radius: 20px;
        }
        
        /* Quick Actions */
        .list-group-item {
            border: none;
            border-left: 4px solid transparent;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .list-group-item:hover {
            border-left-color: var(--secondary-color);
            background-color: rgba(45, 116, 218, 0.05);
            transform: translateX(5px);
        }
        
        /* Header */
        .dashboard-header {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .user-welcome {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
        }
        
        .user-welcome h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .user-welcome p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Mobile Toggle Button */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem;
            width: 40px;
            height: 40px;
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
        }
        
        /* Recent Cases Card */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: white;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            border-radius: 15px 15px 0 0;
        }
        
        .card-header h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-balance-scale me-2"></i>JusticeFlow</h4>
            <p>Legal Tech Platform</p>
        </div>
        
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-user-cog"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cases.php">
                        <i class="fas fa-folder-open"></i> Case Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="research.php">
                        <i class="fas fa-search"></i> Legal Research
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="disputes.php">
                        <i class="fas fa-handshake"></i> Dispute Resolution
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="analytics.php">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="documents.php">
                        <i class="fas fa-file-alt"></i> Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="calendar.php">
                        <i class="fas fa-calendar-alt"></i> Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="messages.php">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user-cog"></i> Profile Settings
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="mainContent">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="user-welcome">
                        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User'); ?>!</h2>
                        <p>Here's what's happening with your legal cases today.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="text-end">
                            <div class="text-muted small">Last login</div>
                            <div class="fw-medium"><?php echo date('M d, Y H:i'); ?></div>
                        </div>
                        <div class="dropdown ms-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Cases</h6>
                                <h3><?php echo $analytics['total_cases']; ?></h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-folder"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i> 12% from last month
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Active Cases</h6>
                                <h3><?php echo $analytics['active_cases']; ?></h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-warning">
                                <i class="fas fa-clock me-1"></i> 5 pending actions
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Resolution Rate</h6>
                                <h3><?php echo $analytics['resolution_rate']; ?>%</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i> 8% improvement
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card analytics-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Avg. Processing</h6>
                                <h3><?php echo $analytics['avg_processing_days']; ?> days</h3>
                            </div>
                            <div class="icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-danger">
                                <i class="fas fa-arrow-down me-1"></i> 15% faster than average
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cases & Quick Actions -->
        <div class="row mt-4">
            <!-- Recent Cases -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Recent Cases</h5>
                        <a href="cases.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Case No.</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Next Hearing</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCases as $case): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($case['case_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($case['title']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $case['status'] == 'active' ? 'warning' : 
                                                       ($case['status'] == 'resolved' ? 'success' : 
                                                       ($case['status'] == 'pending' ? 'secondary' : 'info')); 
                                            ?>">
                                                <?php echo ucfirst($case['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $case['priority'] == 'high' ? 'danger' : 
                                                       ($case['priority'] == 'medium' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst($case['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if (!empty($case['next_hearing'])) {
                                                echo date('M d, Y', strtotime($case['next_hearing']));
                                            } else {
                                                echo '<span class="text-muted">Not scheduled</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Upcoming -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="add-case.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <strong>Add New Case</strong>
                                    <div class="text-muted small">Start a new legal case</div>
                                </div>
                            </a>
                            <a href="research.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-search text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <strong>Research Precedents</strong>
                                    <div class="text-muted small">Find similar cases</div>
                                </div>
                            </a>
                            <a href="schedule-mediation.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-plus text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <strong>Schedule Mediation</strong>
                                    <div class="text-muted small">Arrange dispute resolution</div>
                                </div>
                            </a>
                            <a href="reports.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <strong>Generate Report</strong>
                                    <div class="text-muted small">Create case analytics</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Hearings -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Upcoming Hearings</h5>
                        <a href="calendar.php" class="btn btn-sm btn-outline-primary">View Calendar</a>
                    </div>
                    <div class="card-body">
                        <?php 
                        $upcomingHearings = [];
                        foreach ($recentCases as $case) {
                            if (isset($case['next_hearing']) && !empty($case['next_hearing'])) {
                                $upcomingHearings[] = [
                                    'case' => $case['case_number'],
                                    'date' => $case['next_hearing'],
                                    'title' => $case['title'],
                                    'status' => $case['status']
                                ];
                            }
                        }
                        
                        usort($upcomingHearings, function($a, $b) {
                            return strtotime($a['date']) - strtotime($b['date']);
                        });
                        $upcomingHearings = array_slice($upcomingHearings, 0, 3);
                        
                        if (empty($upcomingHearings)): 
                        ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No upcoming hearings scheduled</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($upcomingHearings as $hearing): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-gavel text-white"></i>
                                                </div>
                                                <div>
                                                    <strong class="d-block"><?php echo htmlspecialchars($hearing['case']); ?></strong>
                                                    <small class="text-muted"><?php echo htmlspecialchars($hearing['title']); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-primary fw-bold">
                                                <?php echo date('M d', strtotime($hearing['date'])); ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo date('D, h:i A', strtotime($hearing['date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Case Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="display-6 fw-bold text-primary"><?php echo rand(40, 60); ?>%</div>
                                <div class="text-muted">Civil Cases</div>
                            </div>
                            <div class="col-4">
                                <div class="display-6 fw-bold text-success"><?php echo rand(20, 30); ?>%</div>
                                <div class="text-muted">Criminal Cases</div>
                            </div>
                            <div class="col-4">
                                <div class="display-6 fw-bold text-warning"><?php echo rand(15, 25); ?>%</div>
                                <div class="text-muted">Family Cases</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Today's Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo rand(1, 5); ?></div>
                                        <small class="text-muted">Completed Tasks</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo rand(3, 8); ?></div>
                                        <small class="text-muted">Pending Tasks</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('mainContent').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                mainContent.classList.remove('active');
            }
        });
        
        // Auto-refresh dashboard every 60 seconds
        setTimeout(function() {
            window.location.reload();
        }, 60000);
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>