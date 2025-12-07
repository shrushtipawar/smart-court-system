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

// Check if case ID is provided
if (!isset($_GET['id'])) {
    header('Location: cases.php');
    exit();
}

$case_id = intval($_GET['id']);
$db = new Database();
$conn = $db->getConnection();

// Get case details
try {
    $stmt = $conn->prepare("SELECT c.*, u.full_name as created_by_name 
                           FROM cases c 
                           LEFT JOIN users u ON c.created_by = u.id 
                           WHERE c.id = ?");
    $stmt->execute([$case_id]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$case) {
        header('Location: cases.php');
        exit();
    }
    
    // Check if user has permission to view this case
    $user_type = $_SESSION['user_type'] ?? 'client';
    $user_id = $_SESSION['user_id'];
    
    $has_permission = false;
    if ($user_type == 'admin') {
        $has_permission = true;
    } elseif ($user_type == 'lawyer') {
        $lawyer_name = $_SESSION['full_name'] ?? $_SESSION['username'];
        if ($case['lawyer_name'] == $lawyer_name || $case['created_by'] == $user_id) {
            $has_permission = true;
        }
    } elseif ($case['created_by'] == $user_id) {
        $has_permission = true;
    }
    
    if (!$has_permission) {
        header('Location: cases.php');
        exit();
    }
    
} catch(PDOException $e) {
    header('Location: cases.php');
    exit();
}

// Handle case updates
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_case'])) {
    $title = trim($_POST['title'] ?? '');
    $status = $_POST['status'] ?? 'filed';
    $priority = $_POST['priority'] ?? 'medium';
    $description = trim($_POST['description'] ?? '');
    $judge_name = trim($_POST['judge_name'] ?? '');
    $lawyer_name = trim($_POST['lawyer_name'] ?? '');
    $next_hearing = $_POST['next_hearing'] ?? null;
    $notes = trim($_POST['notes'] ?? '');
    
    try {
        $stmt = $conn->prepare("UPDATE cases SET 
                               title = ?, status = ?, priority = ?, description = ?,
                               judge_name = ?, lawyer_name = ?, next_hearing = ?, notes = ?,
                               updated_at = CURRENT_TIMESTAMP
                               WHERE id = ?");
        
        $stmt->execute([
            $title, $status, $priority, $description,
            $judge_name, $lawyer_name, $next_hearing, $notes,
            $case_id
        ]);
        
        $success = "Case updated successfully!";
        
        // Refresh case data
        $stmt = $conn->prepare("SELECT c.*, u.full_name as created_by_name 
                               FROM cases c 
                               LEFT JOIN users u ON c.created_by = u.id 
                               WHERE c.id = ?");
        $stmt->execute([$case_id]);
        $case = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        $error = "Failed to update case: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Details - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1a365d 0%, #2d74da 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-balance-scale me-2"></i>JusticeFlow
            </a>
            <a href="cases.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Back to Cases
            </a>
        </div>
    </nav>

    <main class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
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
                
                <!-- Case Header -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="h3 mb-2"><?php echo htmlspecialchars($case['title']); ?></h1>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($case['case_number']); ?></span>
                                    <?php 
                                    $status_class = 'bg-' . ($case['status'] == 'closed' ? 'success' : 
                                                           ($case['status'] == 'hearing' ? 'warning' : 
                                                           ($case['status'] == 'appealed' ? 'danger' : 'info')));
                                    ?>
                                    <span class="badge <?php echo $status_class; ?> me-2">
                                        <?php echo ucfirst(str_replace('_', ' ', $case['status'])); ?>
                                    </span>
                                    <?php 
                                    $priority_class = 'bg-' . ($case['priority'] == 'critical' ? 'danger' : 
                                                            ($case['priority'] == 'high' ? 'warning' : 
                                                            ($case['priority'] == 'medium' ? 'info' : 'success')));
                                    ?>
                                    <span class="badge <?php echo $priority_class; ?>">
                                        <?php echo ucfirst($case['priority']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Created by</small>
                                <strong><?php echo htmlspecialchars($case['created_by_name'] ?? 'Unknown'); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo date('F j, Y, g:i a', strtotime($case['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Case Details -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Case Details</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Case Title</label>
                                            <input type="text" class="form-control" name="title" 
                                                   value="<?php echo htmlspecialchars($case['title']); ?>" 
                                                   <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'readonly' : ''; ?>>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Category</label>
                                            <input type="text" class="form-control" 
                                                   value="<?php echo htmlspecialchars($case['category']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" 
                                                    <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'disabled' : ''; ?>>
                                                <option value="filed" <?php echo $case['status'] == 'filed' ? 'selected' : ''; ?>>Filed</option>
                                                <option value="under_review" <?php echo $case['status'] == 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                                <option value="hearing" <?php echo $case['status'] == 'hearing' ? 'selected' : ''; ?>>Hearing</option>
                                                <option value="judgement" <?php echo $case['status'] == 'judgement' ? 'selected' : ''; ?>>Judgement</option>
                                                <option value="appealed" <?php echo $case['status'] == 'appealed' ? 'selected' : ''; ?>>Appealed</option>
                                                <option value="closed" <?php echo $case['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Priority</label>
                                            <select class="form-select" name="priority" 
                                                    <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'disabled' : ''; ?>>
                                                <option value="low" <?php echo $case['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                                                <option value="medium" <?php echo $case['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                                <option value="high" <?php echo $case['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                                                <option value="critical" <?php echo $case['priority'] == 'critical' ? 'selected' : ''; ?>>Critical</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Plaintiff</label>
                                            <input type="text" class="form-control" 
                                                   value="<?php echo htmlspecialchars($case['plaintiff_name']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Defendant</label>
                                            <input type="text" class="form-control" 
                                                   value="<?php echo htmlspecialchars($case['defendant_name']); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Judge Name</label>
                                            <input type="text" class="form-control" name="judge_name" 
                                                   value="<?php echo htmlspecialchars($case['judge_name'] ?? ''); ?>"
                                                   <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'readonly' : ''; ?>>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Lawyer Name</label>
                                            <input type="text" class="form-control" name="lawyer_name" 
                                                   value="<?php echo htmlspecialchars($case['lawyer_name'] ?? ''); ?>"
                                                   <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'readonly' : ''; ?>>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Filing Date</label>
                                            <input type="date" class="form-control" 
                                                   value="<?php echo $case['filing_date'] ?? $case['created_at']; ?>" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Next Hearing Date</label>
                                            <input type="date" class="form-control" name="next_hearing" 
                                                   value="<?php echo $case['next_hearing'] ?? ''; ?>"
                                                   <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'readonly' : ''; ?>>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="4"
                                                      <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'readonly' : ''; ?>><?php echo htmlspecialchars($case['description'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Case Notes</label>
                                            <textarea class="form-control" name="notes" rows="4"
                                                      <?php echo ($_SESSION['user_type'] ?? 'client') == 'client' ? 'readonly' : ''; ?>><?php echo htmlspecialchars($case['notes'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <?php if (in_array($_SESSION['user_type'] ?? 'client', ['admin', 'lawyer'])): ?>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" name="update_case" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Case
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Case Timeline -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Case Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Case Filed</h6>
                                            <p class="text-muted mb-0">
                                                <?php echo date('F j, Y', strtotime($case['filing_date'] ?? $case['created_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if ($case['status'] == 'under_review'): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Under Review</h6>
                                            <p class="text-muted mb-0">Currently being reviewed by the court</p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($case['status'] == 'hearing' && $case['next_hearing']): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-warning"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Next Hearing</h6>
                                            <p class="text-muted mb-0">
                                                <?php echo date('F j, Y', strtotime($case['next_hearing'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($case['status'] == 'closed'): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Case Closed</h6>
                                            <p class="text-muted mb-0">Resolution completed</p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <style>
                                    .timeline {
                                        position: relative;
                                        padding-left: 30px;
                                    }
                                    .timeline-item {
                                        position: relative;
                                        padding-bottom: 20px;
                                    }
                                    .timeline-marker {
                                        position: absolute;
                                        left: -30px;
                                        top: 0;
                                        width: 12px;
                                        height: 12px;
                                        border-radius: 50%;
                                    }
                                    .timeline-content {
                                        padding-left: 10px;
                                    }
                                </style>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="documents.php?case=<?php echo $case['case_number']; ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-file-alt me-2"></i>View Documents
                                    </a>
                                    <a href="#" class="btn btn-outline-info">
                                        <i class="fas fa-calendar me-2"></i>Schedule Hearing
                                    </a>
                                    <a href="#" class="btn btn-outline-warning">
                                        <i class="fas fa-envelope me-2"></i>Send Update
                                    </a>
                                    <?php if ($_SESSION['user_type'] == 'admin' || $_SESSION['user_id'] == $case['created_by']): ?>
                                    <a href="?delete=<?php echo $case['id']; ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this case?');">
                                        <i class="fas fa-trash me-2"></i>Delete Case
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>