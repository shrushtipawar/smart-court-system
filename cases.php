<?php
session_start();

// Check if config exists
if (!file_exists('config/database.php') || !is_readable('config/database.php')) {
    header('Location: setup.php');
    exit();
}

require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Handle case creation
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_case'])) {
    $case_number = 'CASE-' . date('Ymd') . '-' . rand(1000, 9999);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'civil';
    $priority = $_POST['priority'] ?? 'medium';
    $filing_date = $_POST['filing_date'] ?? date('Y-m-d');
    $court_type = $_POST['court_type'] ?? '';
    $plaintiff_name = trim($_POST['plaintiff_name'] ?? '');
    $defendant_name = trim($_POST['defendant_name'] ?? '');
    $user_type = $_SESSION['user_type'] ?? 'client';
    
    // Validate required fields
    if (empty($title) || empty($plaintiff_name) || empty($defendant_name)) {
        $error = "Please fill in all required fields";
    } else {
        try {
            // Check if cases table exists, create it if not
            $stmt = $conn->query("SHOW TABLES LIKE 'cases'");
            if ($stmt->rowCount() == 0) {
                // Create cases table
                $conn->exec("
                    CREATE TABLE IF NOT EXISTS cases (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        case_number VARCHAR(50) UNIQUE NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        description TEXT,
                        category VARCHAR(100),
                        priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                        status ENUM('filed', 'under_review', 'hearing', 'judgement', 'appealed', 'closed') DEFAULT 'filed',
                        filing_date DATE,
                        court_type VARCHAR(100),
                        plaintiff_name VARCHAR(255),
                        defendant_name VARCHAR(255),
                        judge_name VARCHAR(255),
                        lawyer_name VARCHAR(255),
                        created_by INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_case_number (case_number),
                        INDEX idx_status (status),
                        INDEX idx_priority (priority),
                        INDEX idx_category (category)
                    )
                ");
            }
            
            $stmt = $conn->prepare("INSERT INTO cases 
                (case_number, title, description, category, priority, status, filing_date, 
                 court_type, plaintiff_name, defendant_name, created_by) 
                VALUES (?, ?, ?, ?, ?, 'filed', ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $case_number, $title, $description, $category, $priority, 
                $filing_date, $court_type, $plaintiff_name, $defendant_name, 
                $_SESSION['user_id']
            ]);
            
            $success = "Case created successfully! Case Number: $case_number";
            
            // Clear form fields
            $_POST = [];
            
        } catch(PDOException $e) {
            $error = "Failed to create case: " . $e->getMessage();
        }
    }
}

// Get cases based on user type
$cases = [];
try {
    $user_type = $_SESSION['user_type'] ?? 'client';
    $user_id = $_SESSION['user_id'];
    
    if ($user_type == 'admin') {
        // Admin can see all cases
        $stmt = $conn->prepare("SELECT c.*, u.full_name as created_by_name 
                               FROM cases c 
                               LEFT JOIN users u ON c.created_by = u.id 
                               ORDER BY c.created_at DESC");
        $stmt->execute();
    } elseif ($user_type == 'lawyer') {
        // Lawyer can see cases assigned to them or they created
        $stmt = $conn->prepare("SELECT c.*, u.full_name as created_by_name 
                               FROM cases c 
                               LEFT JOIN users u ON c.created_by = u.id 
                               WHERE c.lawyer_name = ? OR c.created_by = ? 
                               ORDER BY c.created_at DESC");
        $lawyer_name = $_SESSION['full_name'] ?? $_SESSION['username'];
        $stmt->execute([$lawyer_name, $user_id]);
    } else {
        // Client can only see cases they created
        $stmt = $conn->prepare("SELECT c.*, u.full_name as created_by_name 
                               FROM cases c 
                               LEFT JOIN users u ON c.created_by = u.id 
                               WHERE c.created_by = ? 
                               ORDER BY c.created_at DESC");
        $stmt->execute([$user_id]);
    }
    
    $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If table doesn't exist or error occurs, use empty array
    $cases = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JusticeFlow - Case Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            padding: 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            background: linear-gradient(180deg, var(--primary-color) 0%, #2a4365 100%);
            width: 250px;
        }
        
        main {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            main {
                margin-left: 0;
            }
        }
        
        .case-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        
        .case-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .priority-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .priority-low { background-color: #d4edda; color: #155724; }
        .priority-medium { background-color: #fff3cd; color: #856404; }
        .priority-high { background-color: #f8d7da; color: #721c24; }
        .priority-critical { background-color: #dc3545; color: white; }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-filed { background-color: #cce5ff; color: #004085; }
        .status-under_review { background-color: #d1ecf1; color: #0c5460; }
        .status-hearing { background-color: #fff3cd; color: #856404; }
        .status-judgement { background-color: #d4edda; color: #155724; }
        .status-appealed { background-color: #f8d7da; color: #721c24; }
        .status-closed { background-color: #e2e3e5; color: #383d41; }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-balance-scale me-2"></i>JusticeFlow
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cases.php"><i class="fas fa-folder-open me-1"></i>Cases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="documents.php"><i class="fas fa-file-alt me-1"></i>Documents</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text">
                                <small class="text-muted">Role:</small><br>
                                <strong><?php echo ucfirst($_SESSION['user_type'] ?? 'User'); ?></strong>
                            </span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-2">Case Management</h1>
                        <p class="text-muted mb-0">Manage and track your legal cases</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                            <i class="fas fa-plus me-2"></i>New Case
                        </button>
                    </div>
                </div>
                
                <!-- Alerts -->
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Case Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card case-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo count($cases); ?></h5>
                                        <small class="text-muted">Total Cases</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card case-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo count(array_filter($cases, fn($case) => $case['status'] == 'closed')); ?></h5>
                                        <small class="text-muted">Closed Cases</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card case-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo count(array_filter($cases, fn($case) => $case['status'] == 'hearing')); ?></h5>
                                        <small class="text-muted">Active Hearings</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card case-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                                        <i class="fas fa-gavel"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo count(array_filter($cases, fn($case) => $case['status'] == 'judgement')); ?></h5>
                                        <small class="text-muted">Awaiting Judgement</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cases Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Cases</h5>
                        <div class="d-flex">
                            <input type="text" class="form-control form-control-sm me-2" placeholder="Search cases..." id="searchInput">
                            <button class="btn btn-sm btn-outline-primary" onclick="exportCases()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($cases)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                            <h4>No Cases Found</h4>
                            <p class="text-muted">Create your first case to get started</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                                <i class="fas fa-plus me-2"></i>Create Case
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="casesTable">
                                <thead>
                                    <tr>
                                        <th>Case #</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Plaintiff</th>
                                        <th>Defendant</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($cases as $case): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($case['case_number']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                Filed: <?php echo date('M d, Y', strtotime($case['filing_date'] ?? $case['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td><?php echo htmlspecialchars($case['title']); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($case['category'] ?? 'Not set'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $priority_class = 'priority-' . ($case['priority'] ?? 'medium');
                                            $priority_text = ucfirst($case['priority'] ?? 'medium');
                                            ?>
                                            <span class="priority-badge <?php echo $priority_class; ?>">
                                                <?php echo $priority_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_class = 'status-' . ($case['status'] ?? 'filed');
                                            $status_text = ucfirst(str_replace('_', ' ', $case['status'] ?? 'filed'));
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($case['plaintiff_name']); ?></td>
                                        <td><?php echo htmlspecialchars($case['defendant_name']); ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($case['created_at'])); ?><br>
                                                by <?php echo htmlspecialchars($case['created_by_name'] ?? 'Unknown'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="case_details.php?id=<?php echo $case['id']; ?>" 
                                                   class="action-btn btn btn-sm btn-outline-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (in_array($_SESSION['user_type'] ?? 'client', ['admin', 'lawyer'])): ?>
                                                <a href="edit_case.php?id=<?php echo $case['id']; ?>" 
                                                   class="action-btn btn btn-sm btn-outline-warning ms-1" 
                                                   title="Edit Case">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user_type'] == 'admin' || $_SESSION['user_id'] == $case['created_by']): ?>
                                                <a href="?delete=<?php echo $case['id']; ?>" 
                                                   class="action-btn btn btn-sm btn-outline-danger ms-1" 
                                                   title="Delete Case"
                                                   onclick="return confirm('Are you sure you want to delete this case?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Case Modal -->
    <div class="modal fade" id="createCaseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Case</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Title *</label>
                                <input type="text" class="form-control" name="title" 
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="civil" <?php echo ($_POST['category'] ?? '') == 'civil' ? 'selected' : ''; ?>>Civil</option>
                                    <option value="criminal" <?php echo ($_POST['category'] ?? '') == 'criminal' ? 'selected' : ''; ?>>Criminal</option>
                                    <option value="family" <?php echo ($_POST['category'] ?? '') == 'family' ? 'selected' : ''; ?>>Family</option>
                                    <option value="commercial" <?php echo ($_POST['category'] ?? '') == 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                                    <option value="constitutional" <?php echo ($_POST['category'] ?? '') == 'constitutional' ? 'selected' : ''; ?>>Constitutional</option>
                                    <option value="labor" <?php echo ($_POST['category'] ?? '') == 'labor' ? 'selected' : ''; ?>>Labor</option>
                                    <option value="property" <?php echo ($_POST['category'] ?? '') == 'property' ? 'selected' : ''; ?>>Property</option>
                                    <option value="consumer" <?php echo ($_POST['category'] ?? '') == 'consumer' ? 'selected' : ''; ?>>Consumer</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority *</label>
                                <select class="form-select" name="priority" required>
                                    <option value="medium" <?php echo ($_POST['priority'] ?? '') == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="low" <?php echo ($_POST['priority'] ?? '') == 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="high" <?php echo ($_POST['priority'] ?? '') == 'high' ? 'selected' : ''; ?>>High</option>
                                    <option value="critical" <?php echo ($_POST['priority'] ?? '') == 'critical' ? 'selected' : ''; ?>>Critical</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Filing Date *</label>
                                <input type="date" class="form-control" name="filing_date" 
                                       value="<?php echo $_POST['filing_date'] ?? date('Y-m-d'); ?>" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Plaintiff Name *</label>
                                <input type="text" class="form-control" name="plaintiff_name" 
                                       value="<?php echo htmlspecialchars($_POST['plaintiff_name'] ?? ''); ?>" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Defendant Name *</label>
                                <input type="text" class="form-control" name="defendant_name" 
                                       value="<?php echo htmlspecialchars($_POST['defendant_name'] ?? ''); ?>" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Court Type</label>
                                <select class="form-select" name="court_type">
                                    <option value="">Select Court Type</option>
                                    <option value="District Court" <?php echo ($_POST['court_type'] ?? '') == 'District Court' ? 'selected' : ''; ?>>District Court</option>
                                    <option value="High Court" <?php echo ($_POST['court_type'] ?? '') == 'High Court' ? 'selected' : ''; ?>>High Court</option>
                                    <option value="Supreme Court" <?php echo ($_POST['court_type'] ?? '') == 'Supreme Court' ? 'selected' : ''; ?>>Supreme Court</option>
                                    <option value="Family Court" <?php echo ($_POST['court_type'] ?? '') == 'Family Court' ? 'selected' : ''; ?>>Family Court</option>
                                    <option value="Consumer Court" <?php echo ($_POST['court_type'] ?? '') == 'Consumer Court' ? 'selected' : ''; ?>>Consumer Court</option>
                                    <option value="Labour Court" <?php echo ($_POST['court_type'] ?? '') == 'Labour Court' ? 'selected' : ''; ?>>Labour Court</option>
                                    <option value="Tribunal" <?php echo ($_POST['court_type'] ?? '') == 'Tribunal' ? 'selected' : ''; ?>>Tribunal</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" 
                                          placeholder="Brief description of the case..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_case" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Case
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#casesTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [[0, 'desc']] // Sort by case number descending
            });
            
            // Search functionality
            $('#searchInput').on('keyup', function() {
                $('#casesTable').DataTable().search(this.value).draw();
            });
        });
        
        // Export cases function
        function exportCases() {
            alert('Export functionality would be implemented here. In a real application, this would generate a CSV or PDF report.');
        }
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                // Scroll to first invalid field
                const firstInvalid = this.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalid.focus();
                }
            }
        });
    </script>
</body>
</html>