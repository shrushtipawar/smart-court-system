<?php
session_start();

// Check if config exists
if (!file_exists('config/database.php') || !is_readable('config/database.php')) {
    die("<div style='padding: 20px; text-align: center;'>
            <h3>Database Configuration Missing</h3>
            <p>Please run setup first.</p>
            <a href='setup.php' class='btn btn-primary'>Run Setup</a>
         </div>");
}

require_once 'config/database.php';

$message = '';
$success = false;
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = "Invalid verification link. No token provided.";
} else {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if verification_token column exists
        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'verification_token'");
        if ($stmt->rowCount() == 0) {
            // Add verification_token column if it doesn't exist
            $conn->exec("ALTER TABLE users ADD COLUMN verification_token VARCHAR(64)");
        }
        
        // Check if status column exists
        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
        if ($stmt->rowCount() == 0) {
            // Add status column if it doesn't exist
            $conn->exec("ALTER TABLE users ADD COLUMN status ENUM('pending', 'active', 'inactive', 'suspended') DEFAULT 'pending'");
        }
        
        // Check if verified column exists (for backward compatibility)
        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'verified'");
        if ($stmt->rowCount() == 0) {
            // Add verified column if it doesn't exist
            $conn->exec("ALTER TABLE users ADD COLUMN verified TINYINT(1) DEFAULT 0");
        }
        
        // Find user by verification token
        $stmt = $conn->prepare("SELECT id, username, email, status, verified FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Check if already verified
            if ($user['status'] == 'active' || $user['verified'] == 1) {
                $message = "Your email is already verified. You can login to your account.";
                $success = true;
                
                // Set session if user is logged in
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']) {
                    $_SESSION['verified'] = true;
                }
            } else {
                // Update user to active
                $stmt = $conn->prepare("UPDATE users SET status = 'active', verified = 1, verification_token = NULL WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                $message = "Email verification successful! Your account is now active.";
                $success = true;
                
                // Set session if user is logged in
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']) {
                    $_SESSION['verified'] = true;
                    $_SESSION['status'] = 'active';
                }
                
                // Auto-login user if not already logged in
                if (!isset($_SESSION['user_id'])) {
                    // Get user details
                    $stmt = $conn->prepare("SELECT username, email, full_name, user_type FROM users WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($userDetails) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $userDetails['username'];
                        $_SESSION['email'] = $userDetails['email'];
                        $_SESSION['full_name'] = $userDetails['full_name'] ?? $userDetails['username'];
                        $_SESSION['user_type'] = $userDetails['user_type'] ?? 'client';
                        $_SESSION['verified'] = true;
                        $_SESSION['status'] = 'active';
                    }
                }
            }
        } else {
            $message = "Invalid verification token. The link may have expired or already been used.";
        }
        
    } catch (Exception $e) {
        $message = "Error during verification: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - JusticeFlow</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        
        .verification-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: <?php echo $success ? 'linear-gradient(135deg, #0d9d6b 0%, #2d74da 100%)' : 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)'; ?>;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 40px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 116, 218, 0.3);
        }
        
        .token-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 20px 0;
        }
        
        @media (max-width: 576px) {
            .verification-card {
                padding: 30px 20px;
            }
            
            .verification-icon {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <div class="verification-icon">
            <?php if ($success): ?>
                <i class="fas fa-check"></i>
            <?php else: ?>
                <i class="fas fa-exclamation"></i>
            <?php endif; ?>
        </div>
        
        <h2 class="mb-3">
            <?php echo $success ? 'Verification Successful!' : 'Verification Failed'; ?>
        </h2>
        
        <p class="text-muted mb-4">
            <?php echo $message; ?>
        </p>
        
        <?php if (!empty($token)): ?>
        <div class="token-display">
            <small>Token: <?php echo htmlspecialchars($token); ?></small>
        </div>
        <?php endif; ?>
        
        <div class="d-grid gap-3">
            <?php if ($success): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Your Account
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <a href="index.php" class="btn btn-outline-primary">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
            
            <?php if (!$success): ?>
                <a href="register.php" class="btn btn-outline-secondary">
                    <i class="fas fa-user-plus me-2"></i>Register Again
                </a>
            <?php endif; ?>
        </div>
        
        <div class="mt-4 pt-3 border-top">
            <p class="text-muted small mb-2">
                <i class="fas fa-info-circle me-1"></i>
                Need help? <a href="contact.php" class="text-decoration-none">Contact Support</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-redirect after 10 seconds if successful
        <?php if ($success): ?>
        setTimeout(function() {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'dashboard.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }, 10000);
        <?php endif; ?>
        
        // Countdown timer
        <?php if ($success): ?>
        let countdown = 10;
        const countdownElement = document.createElement('p');
        countdownElement.className = 'text-muted small mt-3';
        countdownElement.id = 'countdown';
        document.querySelector('.d-grid').appendChild(countdownElement);
        
        function updateCountdown() {
            countdown--;
            document.getElementById('countdown').textContent = 
                'Redirecting in ' + countdown + ' seconds...';
            
            if (countdown > 0) {
                setTimeout(updateCountdown, 1000);
            }
        }
        
        updateCountdown();
        <?php endif; ?>
    </script>
</body>
</html>