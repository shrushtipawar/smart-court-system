<?php
// Admin panel configuration
define('ADMIN_TITLE', 'JusticeFlow Admin Panel');
define('ADMIN_VERSION', '1.0.0');
define('ADMIN_SESSION_KEY', 'justiceflow_admin');
define('ADMIN_UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('ADMIN_ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ADMIN_MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Admin roles and permissions
$admin_roles = [
    'admin' => [
        'dashboard' => true,
        'users' => true,
        'pages' => true,
        'content' => true,
        'media' => true,
        'settings' => true,
        'logs' => true
    ],
    'editor' => [
        'dashboard' => true,
        'pages' => true,
        'content' => true,
        'media' => true
    ],
    'moderator' => [
        'dashboard' => true,
        'users' => true
    ]
];

// Check if user is admin
function isAdmin() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $user_id = $_SESSION['user_id'];
    global $db;
    
    try {
        $stmt = $db->conn->prepare("SELECT role FROM users WHERE id = ? AND status = 'active'");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ($user && $user['role'] === 'admin');
    } catch (PDOException $e) {
        return false;
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = 'Access denied. Admin privileges required.';
        header('Location: ../index.php');
        exit;
    }
}

// Get admin setting
function getAdminSetting($key, $default = '') {
    global $db;
    
    try {
        $stmt = $db->conn->prepare("SELECT setting_value FROM admin_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

// Update admin setting
function updateAdminSetting($key, $value) {
    global $db;
    
    try {
        $stmt = $db->conn->prepare("
            INSERT INTO admin_settings (setting_key, setting_value) 
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
        ");
        return $stmt->execute([$key, $value, $value]);
    } catch (PDOException $e) {
        return false;
    }
}

// Log activity
function logActivity($action, $description = '') {
    global $db;
    
    try {
        $stmt = $db->conn->prepare("
            INSERT INTO activity_log (user_id, action, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $user_id = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        return $stmt->execute([$user_id, $action, $description, $ip, $agent]);
    } catch (PDOException $e) {
        return false;
    }
}

// Get user by ID
function getUserById($id) {
    global $db;
    
    try {
        $stmt = $db->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

// Get all users
function getAllUsers($limit = null) {
    global $db;
    
    try {
        $sql = "SELECT * FROM users WHERE status != 'deleted' ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $db->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Check if table exists
function tableExists($table_name) {
    global $db;
    
    try {
        $stmt = $db->conn->query("SHOW TABLES LIKE '$table_name'");
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Initialize database tables if they don't exist
function initializeDatabaseTables() {
    global $db;
    
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
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
        )",
        
        'admin_settings' => "CREATE TABLE IF NOT EXISTS admin_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type VARCHAR(50) DEFAULT 'text',
            category VARCHAR(50) DEFAULT 'general',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        'dynamic_content' => "CREATE TABLE IF NOT EXISTS dynamic_content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_name VARCHAR(100) NOT NULL,
            section_name VARCHAR(100) NOT NULL,
            content_type ENUM('text','html','json','array') DEFAULT 'text',
            content TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_section (page_name, section_name)
        )",
        
        'media' => "CREATE TABLE IF NOT EXISTS media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_type VARCHAR(100),
            file_size INT,
            mime_type VARCHAR(100),
            alt_text VARCHAR(255),
            uploaded_by INT,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
        )",
        
        'site_pages' => "CREATE TABLE IF NOT EXISTS site_pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            content LONGTEXT,
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords TEXT,
            is_published BOOLEAN DEFAULT TRUE,
            is_homepage BOOLEAN DEFAULT FALSE,
            template VARCHAR(100) DEFAULT 'default',
            parent_id INT DEFAULT NULL,
            menu_order INT DEFAULT 0,
            show_in_menu BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by INT,
            FOREIGN KEY (parent_id) REFERENCES site_pages(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
        )",
        
        'activity_log' => "CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )"
    ];
    
    foreach ($tables as $table_name => $sql) {
        if (!tableExists($table_name)) {
            try {
                $db->conn->exec($sql);
            } catch (PDOException $e) {
                // Table creation failed
                error_log("Failed to create table $table_name: " . $e->getMessage());
            }
        }
    }
    
    // Insert default settings if they don't exist
    $default_settings = [
        ['site_title', 'JusticeFlow', 'text', 'general'],
        ['site_description', 'Legal Tech Platform', 'text', 'general'],
        ['site_logo', '', 'image', 'general'],
        ['contact_email', 'admin@justiceflow.com', 'email', 'contact'],
        ['contact_phone', '+1 (555) 123-4567', 'text', 'contact'],
        ['contact_address', '123 Legal Street, Law City', 'textarea', 'contact'],
        ['facebook_url', 'https://facebook.com/justiceflow', 'url', 'social'],
        ['twitter_url', 'https://twitter.com/justiceflow', 'url', 'social'],
        ['linkedin_url', 'https://linkedin.com/company/justiceflow', 'url', 'social'],
        ['instagram_url', 'https://instagram.com/justiceflow', 'url', 'social'],
        ['primary_color', '#1a365d', 'color', 'design'],
        ['secondary_color', '#2d74da', 'color', 'design'],
        ['accent_color', '#0d9d6b', 'color', 'design'],
        ['maintenance_mode', '0', 'boolean', 'system']
    ];
    
    foreach ($default_settings as $setting) {
        $stmt = $db->conn->prepare("
            INSERT IGNORE INTO admin_settings (setting_key, setting_value, setting_type, category) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute($setting);
    }
}

// Auto-initialize database on first run
function autoInitializeDatabase() {
    global $db;
    
    // Check if users table exists
    if (!tableExists('users')) {
        initializeDatabaseTables();
        
        // Create default admin user if doesn't exist
        $stmt = $db->conn->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        $admin_exists = $stmt->fetchColumn();
        
        if (!$admin_exists) {
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $db->conn->prepare("
                INSERT INTO users (username, email, password, full_name, role, status, is_verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute(['admin', 'admin@justiceflow.com', $hashed_password, 'Administrator', 'admin', 'active', 1]);
        }
    }
}
?>