<?php
session_start();

// Check if database is already set up
$is_setup = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process setup form
    $host = $_POST['host'] ?? 'localhost';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $database = $_POST['database'] ?? 'justiceflow';
    
    // First, test database connection
    try {
        $temp_conn = new PDO("mysql:host=$host", $username, $password);
        $temp_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $temp_conn->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the database
        $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES utf8mb4");
        
        // Create tables
        $conn->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            role ENUM('admin','lawyer','client','business') DEFAULT 'client',
            status ENUM('active','pending','suspended','deleted') DEFAULT 'pending',
            avatar VARCHAR(255),
            bio TEXT,
            experience_years INT DEFAULT 0,
            specialization VARCHAR(100),
            last_login DATETIME,
            last_seen DATETIME,
            is_verified BOOLEAN DEFAULT FALSE,
            verification_token VARCHAR(100),
            reset_token VARCHAR(100),
            reset_expiry DATETIME,
            remember_token VARCHAR(100),
            remember_expiry DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $conn->exec("CREATE TABLE IF NOT EXISTS cases (
            id INT AUTO_INCREMENT PRIMARY KEY,
            case_number VARCHAR(50) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            client_id INT,
            lawyer_id INT,
            status ENUM('active','pending','resolved','closed') DEFAULT 'pending',
            category VARCHAR(100),
            priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
            filing_date DATE,
            resolution_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $conn->exec("CREATE TABLE IF NOT EXISTS admin_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type VARCHAR(50) DEFAULT 'text',
            category VARCHAR(50) DEFAULT 'general',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Create admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, status, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@justiceflow.com', $hashed_password, 'Administrator', 'admin', 'active', 1]);
        
        // Insert default settings
        $settings = [
            ['site_title', 'JusticeFlow', 'text', 'general'],
            ['site_description', 'Legal Tech Platform', 'text', 'general'],
            ['contact_email', 'admin@justiceflow.com', 'email', 'contact']
        ];
        
        foreach ($settings as $setting) {
            $stmt = $conn->prepare("INSERT INTO admin_settings (setting_key, setting_value, setting_type, category) VALUES (?, ?, ?, ?)");
            $stmt->execute($setting);
        }
        
        // Create config directory if it doesn't exist
        $config_dir = __DIR__ . '/config';
        if (!is_dir($config_dir)) {
            mkdir($config_dir, 0777, true);
        }
        
        // Create database.php config file
        $config_content = "<?php\nclass Database {\n    public \$conn;\n\n    public function __construct() {\n        try {\n            \$this->conn = new PDO(\"mysql:host=$host;dbname=$database\", \"$username\", \"$password\");\n            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n            \$this->conn->exec(\"SET NAMES utf8mb4\");\n        } catch(PDOException \$e) {\n            die(\"Connection failed: \" . \$e->getMessage());\n        }\n    }\n}\n?>";
        
        if (file_put_contents($config_dir . '/database.php', $config_content)) {
            $is_setup = true;
        } else {
            $error = 'Failed to write config file. Check folder permissions.';
        }
        
    } catch(PDOException $e) {
        $error = 'Database Error: ' . $e->getMessage();
    }
}

// Check if already setup
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    try {
        $db = new Database();
        $is_setup = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JusticeFlow - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a365d 0%, #2d74da 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .setup-container { max-width: 600px; width: 100%; }
        .setup-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; }
        .setup-header { background: linear-gradient(135deg, #1a365d 0%, #2d74da 100%); color: white; padding: 30px; text-align: center; }
        .setup-body { padding: 30px; }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <h1><i class="fas fa-balance-scale"></i> JusticeFlow Setup</h1>
                <p>Welcome to JusticeFlow installation</p>
            </div>
            
            <div class="setup-body">
                <?php if ($is_setup): ?>
                    <div class="alert alert-success">
                        <h4>✓ Setup Complete!</h4>
                        <p>JusticeFlow has been successfully installed.</p>
                        <p><strong>Default Admin Credentials:</strong></p>
                        <ul>
                            <li><strong>Username:</strong> admin</li>
                            <li><strong>Password:</strong> admin123</li>
                            <li><strong>Email:</strong> admin@justiceflow.com</li>
                        </ul>
                        <div class="mt-3">
                            <a href="index.php" class="btn btn-primary me-2">Go to Website</a>
                            <a href="admin/login.php" class="btn btn-success">Admin Login</a>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <strong>Important:</strong> Change the default admin password immediately!
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Database Host</label>
                            <input type="text" class="form-control" name="host" value="localhost" required>
                            <small class="text-muted">Usually 'localhost' for XAMPP</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Database Username</label>
                            <input type="text" class="form-control" name="username" value="root" required>
                            <small class="text-muted">Default is 'root' for XAMPP</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" name="password">
                            <small class="text-muted">Leave empty for default XAMPP installation</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" name="database" value="justiceflow" required>
                            <small class="text-muted">Database will be created if it doesn't exist</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Note:</strong> Make sure MySQL is running in XAMPP Control Panel.
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">Install JusticeFlow</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>