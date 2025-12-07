<?php
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection(); // Connection लें

// Initialize variables
$errors = [];
$success = false;
$username = $email = $full_name = $phone = $user_type = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $user_type = $_POST['user_type'] ?? 'client';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = $_POST['terms'] ?? '';

    // Validation
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors['username'] = 'Username must be 3-20 characters (letters, numbers, underscore)';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    if (empty($full_name)) {
        $errors['full_name'] = 'Full name is required';
    } elseif (strlen($full_name) < 2) {
        $errors['full_name'] = 'Full name must be at least 2 characters';
    }

    if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors['phone'] = 'Please enter a valid 10-digit phone number';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password must contain uppercase, lowercase, and numbers';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($terms)) {
        $errors['terms'] = 'You must agree to the terms and conditions';
    }

    // Check if username or email already exists
    if (empty($errors) && $conn) {
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors['general'] = 'Username or email already exists';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Database error: ' . $e->getMessage();
        }
    } elseif (empty($errors) && !$conn) {
        $errors['general'] = 'Database connection failed';
    }

    // If no errors, insert into database
    if (empty($errors) && $conn) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_token = bin2hex(random_bytes(32));
            $status = 'pending'; // Email verification required
            
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, full_name, phone, user_type, verification_token, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $username, 
                $email, 
                $hashed_password, 
                $full_name, 
                $phone, 
                $user_type, 
                $verification_token,
                $status
            ]);
            
            $user_id = $conn->lastInsertId();
            
            // Send verification email
            $verification_link = "http://{$_SERVER['HTTP_HOST']}/verify.php?token=$verification_token";
            // In production, you would send an actual email here
            // mail($email, "Verify Your JusticeFlow Account", "Click here: $verification_link");
            
            // Store verification link in session for demo purposes
            $_SESSION['verification_link'] = $verification_link;
            
            $success = true;
            
            // Auto-login after registration
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['user_type'] = $user_type;
            $_SESSION['verified'] = false;
            
        } catch (PDOException $e) {
            $errors['general'] = 'Registration failed: ' . $e->getMessage();
        }
    } elseif (empty($errors) && !$conn) {
        $errors['general'] = 'Database connection failed';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - JusticeFlow</title>
    
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
            padding-top: 70px;
        }
        
        .register-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .user-type-card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
        }
        
        .user-type-card:hover {
            border-color: var(--secondary-color);
            transform: translateY(-5px);
        }
        
        .user-type-card.selected {
            border-color: var(--secondary-color);
            background-color: rgba(45, 116, 218, 0.05);
        }
        
        .user-type-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(45, 116, 218, 0.25);
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 116, 218, 0.3);
        }
        
        .login-link {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            height: 5px;
            background: #dee2e6;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .requirements-list {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .requirement-met {
            color: var(--accent-color);
        }
        
        .requirement-not-met {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Success Modal -->
    <?php if ($success): ?>
    <div class="modal fade show" id="successModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-success">Registration Successful!</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>
                    <h4 class="mb-3">Welcome to JusticeFlow, <?php echo htmlspecialchars($full_name); ?>!</h4>
                    <p class="text-muted mb-4">
                        Your account has been created successfully. Please verify your email address to complete the registration.
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Demo Note:</strong> In production, you would receive a verification email.<br>
                        <small>Verification link: <?php echo htmlspecialchars($verification_link ?? ''); ?></small>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="register-container py-5">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <h1 class="display-5 fw-bold">Join JusticeFlow</h1>
                <p class="lead mb-0">Create your account to access legal tech solutions</p>
            </div>
            
            <!-- Body -->
            <div class="register-body">
                <?php if (!isset($_SESSION['user_id']) && $conn === null): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-database me-2"></i>
                        Database connection failed. Please check your database configuration.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="registerForm">
                    <!-- User Type Selection -->
                    <div class="mb-5">
                        <h5 class="mb-4">I am a:</h5>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="user-type-card <?php echo ($user_type == 'client') ? 'selected' : ''; ?>" 
                                     onclick="selectUserType('client')">
                                    <div class="user-type-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h6 class="text-center mb-2">Individual Client</h6>
                                    <p class="text-muted text-center small mb-0">
                                        Seeking legal assistance for personal matters
                                    </p>
                                    <input type="radio" name="user_type" value="client" 
                                           <?php echo ($user_type == 'client') ? 'checked' : ''; ?> 
                                           style="display: none;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-type-card <?php echo ($user_type == 'lawyer') ? 'selected' : ''; ?>" 
                                     onclick="selectUserType('lawyer')">
                                    <div class="user-type-icon">
                                        <i class="fas fa-gavel"></i>
                                    </div>
                                    <h6 class="text-center mb-2">Legal Professional</h6>
                                    <p class="text-muted text-center small mb-0">
                                        Lawyer, paralegal, or legal researcher
                                    </p>
                                    <input type="radio" name="user_type" value="lawyer" 
                                           <?php echo ($user_type == 'lawyer') ? 'checked' : ''; ?> 
                                           style="display: none;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-type-card <?php echo ($user_type == 'business') ? 'selected' : ''; ?>" 
                                     onclick="selectUserType('business')">
                                    <div class="user-type-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h6 class="text-center mb-2">Business/Organization</h6>
                                    <p class="text-muted text-center small mb-0">
                                        Company, NGO, or legal department
                                    </p>
                                    <input type="radio" name="user_type" value="business" 
                                           <?php echo ($user_type == 'business') ? 'checked' : ''; ?> 
                                           style="display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Full Name *
                                </label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="<?php echo htmlspecialchars($full_name); ?>" 
                                       required>
                                <?php if (isset($errors['full_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['full_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-at me-1"></i>Username *
                                </label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" 
                                       name="username" 
                                       value="<?php echo htmlspecialchars($username); ?>" 
                                       required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['username']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">3-20 characters (letters, numbers, underscore)</div>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Address *
                                </label>
                                <input type="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email); ?>" 
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['email']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Phone Number
                                </label>
                                <input type="tel" 
                                       class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo htmlspecialchars($phone); ?>">
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['phone']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Optional - 10 digits without spaces</div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password *
                                </label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password" 
                                       required
                                       onkeyup="checkPasswordStrength()">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['password']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Password Strength Meter -->
                                <div class="password-strength mt-2">
                                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                                </div>
                                
                                <!-- Password Requirements -->
                                <div class="requirements-list mt-2">
                                    <div id="reqLength" class="requirement-not-met">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        At least 8 characters
                                    </div>
                                    <div id="reqUppercase" class="requirement-not-met">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        One uppercase letter
                                    </div>
                                    <div id="reqLowercase" class="requirement-not-met">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        One lowercase letter
                                    </div>
                                    <div id="reqNumber" class="requirement-not-met">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        One number
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirm Password *
                                </label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required
                                       onkeyup="checkPasswordMatch()">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo htmlspecialchars($errors['confirm_password']); ?>
                                    </div>
                                <?php endif; ?>
                                <div id="passwordMatch" class="form-text"></div>
                            </div>
                            
                            <!-- Terms & Conditions -->
                            <div class="mb-4">
                                <div class="form-check <?php echo isset($errors['terms']) ? 'is-invalid' : ''; ?>">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="terms" 
                                           name="terms"
                                           <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terms'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="terms">
                                        I agree to the 
                                        <a href="terms.php" class="login-link">Terms of Service</a> 
                                        and 
                                        <a href="privacy.php" class="login-link">Privacy Policy</a> *
                                    </label>
                                </div>
                                <?php if (isset($errors['terms'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo htmlspecialchars($errors['terms']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-register btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>
                            
                            <!-- Login Link -->
                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Already have an account? 
                                    <a href="login.php" class="login-link">
                                        <i class="fas fa-sign-in-alt me-1"></i>Sign In
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // User Type Selection
        function selectUserType(type) {
            // Remove selected class from all cards
            document.querySelectorAll('.user-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Set the radio button
            document.querySelector(`input[name="user_type"][value="${type}"]`).checked = true;
        }
        
        // Password Strength Checker
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            
            let strength = 0;
            let color = '#dc3545'; // Red
            
            // Check length
            const hasLength = password.length >= 8;
            document.getElementById('reqLength').className = hasLength ? 'requirement-met' : 'requirement-not-met';
            if (hasLength) strength += 25;
            
            // Check uppercase
            const hasUppercase = /[A-Z]/.test(password);
            document.getElementById('reqUppercase').className = hasUppercase ? 'requirement-met' : 'requirement-not-met';
            if (hasUppercase) strength += 25;
            
            // Check lowercase
            const hasLowercase = /[a-z]/.test(password);
            document.getElementById('reqLowercase').className = hasLowercase ? 'requirement-met' : 'requirement-not-met';
            if (hasLowercase) strength += 25;
            
            // Check number
            const hasNumber = /[0-9]/.test(password);
            document.getElementById('reqNumber').className = hasNumber ? 'requirement-met' : 'requirement-not-met';
            if (hasNumber) strength += 25;
            
            // Update strength bar
            strengthBar.style.width = strength + '%';
            
            // Change color based on strength
            if (strength >= 75) {
                color = '#0d9d6b'; // Green
            } else if (strength >= 50) {
                color = '#ffc107'; // Yellow
            } else if (strength >= 25) {
                color = '#fd7e14'; // Orange
            }
            
            strengthBar.style.backgroundColor = color;
        }
        
        // Password Match Checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchDiv.textContent = '';
                matchDiv.className = 'form-text';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.textContent = '✓ Passwords match';
                matchDiv.className = 'form-text text-success';
            } else {
                matchDiv.textContent = '✗ Passwords do not match';
                matchDiv.className = 'form-text text-danger';
            }
        }
        
        // Form Validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let valid = true;
            
            // Check terms
            if (!document.getElementById('terms').checked) {
                document.querySelector('.form-check').classList.add('is-invalid');
                valid = false;
            } else {
                document.querySelector('.form-check').classList.remove('is-invalid');
            }
            
            // Check password match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('is-invalid');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                // Scroll to first error
                document.querySelector('.is-invalid').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
        
        // Close success modal
        function closeModal() {
            document.querySelector('.modal').style.display = 'none';
            document.querySelector('.modal-backdrop').style.display = 'none';
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-select user type if not selected
            if (!document.querySelector('input[name="user_type"]:checked')) {
                selectUserType('client');
            }
            
            // Check password strength on load if there's a value
            if (document.getElementById('password').value) {
                checkPasswordStrength();
                checkPasswordMatch();
            }
        });
    </script>
</body>
</html>