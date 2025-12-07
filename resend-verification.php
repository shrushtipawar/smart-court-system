<?php
session_start();

if (!file_exists('config/database.php')) {
    header('Location: setup.php');
    exit();
}

require_once 'config/database.php';

$message = '';
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Check if user exists and is not verified
            $stmt = $conn->prepare("SELECT id, username, verification_token, status, verified FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                if ($user['status'] == 'active' || $user['verified'] == 1) {
                    $message = "Your account is already verified. You can login.";
                } else {
                    // Generate new verification token
                    $new_token = bin2hex(random_bytes(32));
                    
                    $stmt = $conn->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
                    $stmt->execute([$new_token, $user['id']]);
                    
                    // Create verification link
                    $verification_link = "http://{$_SERVER['HTTP_HOST']}/justiceflow/verify.php?token=$new_token";
                    
                    // In production: Send email here
                    // mail($email, "Resend: Verify Your JusticeFlow Account", "Click here: $verification_link");
                    
                    $message = "A new verification link has been generated. Check your email.";
                    
                    // Store for demo
                    $_SESSION['new_verification_link'] = $verification_link;
                }
            } else {
                $error = "No account found with that email address";
            }
            
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .resend-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        
        .verification-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a365d 0%, #2d74da 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }
    </style>
</head>
<body>
    <div class="resend-card">
        <div class="verification-icon">
            <i class="fas fa-envelope"></i>
        </div>
        
        <h2 class="text-center mb-4">Resend Verification Email</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
                <?php if (isset($_SESSION['new_verification_link'])): ?>
                    <hr>
                    <p class="mb-2"><strong>Demo Link:</strong></p>
                    <div class="bg-light p-3 rounded small">
                        <?php echo htmlspecialchars($_SESSION['new_verification_link']); ?>
                    </div>
                    <?php unset($_SESSION['new_verification_link']); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($email); ?>" required>
                <div class="form-text">Enter the email address you used for registration</div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Resend Verification Email
                </button>
                <a href="login.php" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Back to Login
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        </form>
    </div>
</body>
</html>