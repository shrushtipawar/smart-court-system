<?php
// Start session
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if config exists
$config_path = __DIR__ . '/../config/database.php';
if (!file_exists($config_path) || !is_readable($config_path)) {
    die("<div style='padding: 20px; text-align: center;'>
            <h3>Database Configuration Missing</h3>
            <p>Please run setup first.</p>
            <a href='../setup.php' class='btn btn-primary'>Run Setup</a>
         </div>");
}

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Initialize variables
$error = '';
$total_users = 0;
$total_cases = 0;
$total_services = 0;
$total_contacts = 0;
$conn = null;

try {
    // Create database connection
    $db = new Database();
    $conn = $db->conn;
    
    // Get user statistics
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $total_users = $stmt->fetchColumn();
    
    // Get cases statistics
    $stmt = $conn->query("SELECT COUNT(*) as count FROM cases");
    $total_cases = $stmt->fetchColumn();
    
    // Get services statistics (if table exists)
    try {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM services");
        $total_services = $stmt->fetchColumn();
    } catch (Exception $e) {
        $total_services = 0; // Table might not exist yet
    }
    
    // Get contacts statistics (if table exists)
    try {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM contacts");
        $total_contacts = $stmt->fetchColumn();
    } catch (Exception $e) {
        $total_contacts = 0; // Table might not exist yet
    }
    
} catch (Exception $e) {
    $error = 'Database Error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
        }
        
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, var(--primary-color) 0%, #2a4365 100%);
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand h3 {
            color: white;
            margin: 0;
            font-size: 1.3rem;
        }
        
        .sidebar-brand p {
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
            overflow-y: auto;
            height: calc(100vh - 80px);
        }
        
        .nav-item {
            margin: 0.2rem 1rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .nav-link .badge {
            margin-left: auto;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                margin-left: -250px;
            }
            
            .admin-sidebar.active {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        .admin-header {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
            transition: all 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stats-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .recent-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .badge-status {
            padding: 0.4em 0.8em;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            margin: 1rem;
            color: white;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-shield-alt me-2"></i>Admin Panel</h3>
            <p>JusticeFlow Management</p>
        </div>
        
        <!-- User Profile -->
        <div class="user-profile">
            <div class="user-avatar">
                <?php echo isset($_SESSION['full_name']) ? strtoupper(substr($_SESSION['full_name'], 0, 1)) : 'A'; ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin'); ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['user_role'] ?? 'Admin'); ?></div>
            </div>
        </div>
        
        <!-- Menu -->
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="pages.php">
                        <i class="fas fa-file-alt"></i> Pages
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="about-content.php">
                        <i class="fas fa-info-circle"></i> About Content
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="services.php">
                        <i class="fas fa-cogs"></i> Services
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="team.php">
                        <i class="fas fa-users"></i> Team Members
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="testimonials.php">
                        <i class="fas fa-comments"></i> Testimonials
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="documents.php">
                        <i class="fas fa-file-pdf"></i> Documents
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="contacts.php">
                        <i class="fas fa-envelope"></i> Contact Messages
                        <?php if ($total_contacts > 0): ?>
                        <span class="badge bg-danger"><?php echo $total_contacts; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="users.php">
                        <i class="fas fa-user-friends"></i> Users
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="admin-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-0">Dashboard Overview</h1>
                    <p class="text-muted mb-0">Welcome to JusticeFlow Admin Panel</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="text-muted me-3"><?php echo date('F d, Y'); ?></span>
                    <a href="../index.php" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> View Site
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_users; ?></div>
                    <div class="stats-label">Total Users</div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i> Active
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, var(--accent-color) 0%, #10b981 100%);">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_cases; ?></div>
                    <div class="stats-label">Total Cases</div>
                    <div class="mt-2">
                        <small class="text-warning">
                            <i class="fas fa-clock me-1"></i> Manage
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_services; ?></div>
                    <div class="stats-label">Services</div>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-star me-1"></i> Add New
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #9d4edd 0%, #c77dff 100%);">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_contacts; ?></div>
                    <div class="stats-label">Messages</div>
                    <div class="mt-2">
                        <small class="text-danger">
                            <i class="fas fa-circle me-1"></i> Manage
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="recent-table">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Recent Users</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($conn):
                                    try {
                                        $stmt = $conn->query("SELECT username, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 5");
                                        $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (count($recentUsers) > 0):
                                            foreach ($recentUsers as $user):
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'lawyer' ? 'warning' : 'primary'); ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status bg-<?php echo $user['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                                <?php 
                                            endforeach;
                                        else:
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-users me-2"></i>
                                        No users found
                                    </td>
                                </tr>
                                <?php 
                                        endif;
                                    } catch (Exception $e) { 
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        Unable to load recent users
                                    </td>
                                </tr>
                                <?php 
                                    }
                                else:
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        Database connection failed
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stats-card">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="list-group list-group-flush">
                        <a href="about-content.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-edit me-2 text-primary"></i>
                            Edit About Page
                        </a>
                        <a href="services.php?action=add" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-plus me-2 text-success"></i>
                            Add New Service
                        </a>
                        <a href="team.php?action=add" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-user-plus me-2 text-info"></i>
                            Add Team Member
                        </a>
                        <a href="settings.php" class="list-group-item list-group-item-action border-0 px-0 py-2">
                            <i class="fas fa-cog me-2 text-warning"></i>
                            Update Settings
                        </a>
                    </div>
                </div>
                
                <div class="stats-card mt-3">
                    <h6 class="mb-3">System Status</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Database</span>
                        <span class="badge bg-<?php echo $conn ? 'success' : 'danger'; ?>">
                            <?php echo $conn ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>PHP Version</span>
                        <span class="badge bg-info"><?php echo phpversion(); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Server Time</span>
                        <span class="badge bg-secondary"><?php echo date('H:i:s'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Memory Usage</span>
                        <span class="badge bg-warning">
                            <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>