<?php
session_start();

// Only allow access from localhost or during setup
$allowed_ips = ['127.0.0.1', '::1'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && !isset($_GET['setup'])) {
    die('Access denied');
}

require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$message = '';
$success = false;

// Create admin user if not exists
try {
    // Check if admin exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin' OR email = 'admin@justiceflow.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Create admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, full_name, role, status, created_at) 
                VALUES ('admin', 'admin@justiceflow.com', ?, 'System Administrator', 'admin', 'active', NOW())";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$hashed_password])) {
            $message = "Admin user created successfully!<br>Username: admin<br>Password: admin123<br><strong>Please change the password immediately!</strong>";
            $success = true;
        } else {
            $message = "Failed to create admin user.";
        }
    } else {
        $message = "Admin user already exists.";
        $success = true;
    }
    
    // Check and update existing admin users
    $stmt = $conn->query("SELECT id, username FROM users WHERE username = 'admin'");
    $admin_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin_user) {
        $update_sql = "UPDATE users SET role = 'admin' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->execute([$admin_user['id']]);
    }
    
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>Admin User Setup
                        </h4>
                    </div>
                    <div class="card-body text-center p-5">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                <h4 class="alert-heading">Success!</h4>
                                <p><?php echo $message; ?></p>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="admin-login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Go to Admin Login
                                    </a>
                                    <a href="index.php" class="btn btn-outline-primary">
                                        <i class="fas fa-home me-2"></i>Back to Home
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                                <h4 class="alert-heading">Setup Required</h4>
                                <p><?php echo $message; ?></p>
                                <hr>
                                <a href="setup.php" class="btn btn-warning">
                                    <i class="fas fa-cog me-2"></i>Run Full Setup
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>