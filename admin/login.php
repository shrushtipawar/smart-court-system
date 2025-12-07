<?php
session_start();

// Check if already logged in as admin
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: admin/index.php');
    exit();
}

// Check if config exists
// Check if config exists
$config_path = __DIR__ . 'config/database.php';
if (!file_exists($config_path) || !is_readable($config_path)) {    die("<div style='padding: 20px; text-align: center;'>
            <h3>Database Configuration Missing</h3>
            <p>Please run setup first.</p>
            <a href='setup.php' class='btn btn-primary'>Run Setup</a>
         </div>");
}

require_once 'config/database.php';

$error = '';
$success = '';

try {
    $db = new Database();
    $conn = $db->getConnection();
} catch (Exception $e) {
    $error = 'Database connection failed. Please check your configuration.';
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            // Check if user exists and is admin
            $stmt = $conn->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = 'admin'");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Check if user is active
                    if ($user['status'] != 'active') {
                        $error = 'Your account is not active. Please contact administrator.';
                    } else {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['logged_in'] = true;
                        
                        // Update last login
                        $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                        $updateStmt->execute([$user['id']]);
                        
                        // Set admin session flag
                        $_SESSION['admin_logged_in'] = true;
                        
                        // Redirect to admin dashboard
                        header("Location: admin/index.php");
                        exit();
                    }
                } else {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Access denied. Admin privileges required.';
            }
            
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - JusticeFlow</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --admin-primary: #1a365d;
            --admin-secondary: #2d74da;
            --admin-accent: #0d9d6b;
            --admin-dark: #0f172a;
            --admin-light: #f8fafc;
        }
        
        body {
            background: linear-gradient(135deg, var(--admin-dark) 0%, var(--admin-primary) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        .admin-login-container {
            max-width: 420px;
            width: 100%;
        }
        
        .admin-login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .admin-login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--admin-primary), var(--admin-accent));
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .admin-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin-right: 15px;
            box-shadow: 0 10px 20px rgba(26, 54, 93, 0.3);
        }
        
        .admin-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }
        
        .admin-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(26, 54, 93, 0.1);
            border-radius: 12px;
            padding: 10px 20px;
            margin-bottom: 30px;
        }
        
        .security-badge i {
            color: var(--admin-accent);
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .security-badge span {
            color: var(--admin-primary);
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--admin-primary);
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .input-group .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 20px 14px 50px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
            height: 52px;
        }
        
        .input-group .form-control:focus {
            border-color: var(--admin-secondary);
            box-shadow: 0 0 0 3px rgba(45, 116, 218, 0.15);
        }
        
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
            z-index: 5;
        }
        
        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.2rem;
            padding: 5px;
            z-index: 5;
        }
        
        .password-toggle:hover {
            color: var(--admin-secondary);
        }
        
        .btn-admin-login {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            border: none;
            color: white;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(26, 54, 93, 0.2);
        }
        
        .btn-admin-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(26, 54, 93, 0.3);
        }
        
        .btn-admin-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee 0%, #fdd 100%);
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
            color: #0d9d6b;
            border-left: 4px solid #0d9d6b;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #cbd5e1;
            border-radius: 4px;
            margin-right: 8px;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: var(--admin-secondary);
            border-color: var(--admin-secondary);
        }
        
        .form-check-label {
            color: #64748b;
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .forgot-password {
            color: var(--admin-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .forgot-password:hover {
            color: var(--admin-primary);
            text-decoration: underline;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .login-footer p {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        
        .back-to-site {
            color: var(--admin-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-to-site:hover {
            color: var(--admin-primary);
            text-decoration: underline;
        }
        
        .session-info {
            background: rgba(26, 54, 93, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #64748b;
            text-align: center;
        }
        
        .session-info i {
            color: var(--admin-accent);
            margin-right: 5px;
        }
        
        @media (max-width: 576px) {
            .admin-login-card {
                padding: 30px 20px;
            }
            
            .admin-title {
                font-size: 1.7rem;
            }
            
            .admin-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }
            
            .input-group .form-control {
                padding: 12px 20px 12px 45px;
                height: 48px;
            }
        }
        
        /* Loading animation */
        .btn-admin-login.loading {
            position: relative;
            color: transparent;
        }
        
        .btn-admin-login.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-header">
                <div class="admin-logo">
                    <div class="admin-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="admin-title">JusticeFlow</div>
                        <div class="admin-subtitle">ADMINISTRATOR PANEL</div>
                    </div>
                </div>
                
                <div class="security-badge">
                    <i class="fas fa-lock"></i>
                    <span>Secure Admin Access</span>
                </div>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fs-5"></i>
                        <div><?php echo htmlspecialchars($success); ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!$conn): ?>
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-database me-3 fs-5"></i>
                        <div>Database connection failed. Please check your configuration.</div>
                    </div>
                </div>
            <?php else: ?>
            <form method="POST" action="" id="adminLoginForm">
                <div class="input-group">
                    <i class="input-icon fas fa-user-shield"></i>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Admin username or email" 
                           required
                           autofocus>
                </div>
                
                <div class="input-group">
                    <i class="input-icon fas fa-key"></i>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Admin password" 
                           required>
                    <button type="button" class="password-toggle" id="passwordToggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                
                <div class="remember-me">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember this device
                        </label>
                    </div>
                    <a href="forgot-password.php?admin=1" class="forgot-password">
                        Forgot password?
                    </a>
                </div>
                
                <button type="submit" class="btn btn-admin-login" id="loginButton">
                    <i class="fas fa-sign-in-alt me-2"></i>Access Admin Panel
                </button>
                
                <div class="session-info">
                    <i class="fas fa-info-circle"></i>
                    Session will expire after 30 minutes of inactivity
                </div>
            </form>
            <?php endif; ?>
            
            <div class="login-footer">
                <p>Need help? Contact system administrator</p>
                <a href="index.php" class="back-to-site">
                    <i class="fas fa-arrow-left"></i> Back to main site
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Password toggle
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation and loading animation
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const loginButton = document.getElementById('loginButton');
            
            if (!username || !password) {
                e.preventDefault();
                showAlert('Please fill in all required fields', 'danger');
                return;
            }
            
            // Show loading state
            loginButton.classList.add('loading');
            loginButton.disabled = true;
            
            // Simulate network delay for UX
            setTimeout(() => {
                loginButton.classList.remove('loading');
                loginButton.disabled = false;
            }, 1500);
        });
        
        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }
            
            // Check for stored username
            const savedUsername = localStorage.getItem('admin_username');
            if (savedUsername) {
                document.getElementById('username').value = savedUsername;
                document.getElementById('remember').checked = true;
            }
            
            // Save username if remember is checked
            document.getElementById('remember').addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('admin_username', document.getElementById('username').value);
                } else {
                    localStorage.removeItem('admin_username');
                }
            });
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+Enter to submit form
            if (e.ctrlKey && e.key === 'Enter') {
                document.getElementById('adminLoginForm').submit();
            }
            
            // Escape to clear form
            if (e.key === 'Escape') {
                document.getElementById('username').value = '';
                document.getElementById('password').value = '';
                document.getElementById('username').focus();
            }
        });
        
        // Show alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas ${type === 'danger' ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-3 fs-5"></i>
                    <div>${message}</div>
                </div>
            `;
            
            const existingAlert = document.querySelector('.alert');
            if (existingAlert) {
                existingAlert.parentNode.insertBefore(alertDiv, existingAlert.nextSibling);
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.style.opacity = '0';
                        alertDiv.style.transition = 'opacity 0.3s';
                        setTimeout(() => {
                            if (alertDiv.parentNode) {
                                alertDiv.parentNode.removeChild(alertDiv);
                            }
                        }, 300);
                    }
                }, 5000);
            }
        }
        
        // Auto-hide error messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            });
        }, 5000);
        
        // Detect if cookies are enabled
        if (!navigator.cookieEnabled) {
            showAlert('Cookies must be enabled to use the admin panel', 'danger');
        }
        
        // Detect if JavaScript is enabled (it is, obviously)
        document.documentElement.classList.add('js-enabled');
    </script>
</body>
</html>