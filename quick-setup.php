<?php
// save this as quick_setup.php in the justiceflow folder
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'justiceflow';

// Create config directory
if (!is_dir('config')) {
    mkdir('config', 0777, true);
}

// Create database.php content
$db_content = '<?php
$db_host = "' . $host . '";
$db_user = "' . $username . '";
$db_pass = "' . $password . '";
$db_name = "' . $database . '";

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8mb4");
    
    // Create users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM(\'admin\',\'lawyer\',\'client\',\'business\') DEFAULT \'client\',
        status ENUM(\'active\',\'pending\',\'suspended\',\'deleted\') DEFAULT \'pending\',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create admin user
    $hashed_password = password_hash(\'admin123\', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([\'admin\', \'admin@justiceflow.com\', $hashed_password, \'Administrator\', \'admin\', \'active\']);
    
    echo "Database setup completed successfully!<br>";
    echo "Admin login: admin / admin123";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>';

// Write to file
file_put_contents('config/database.php', $db_content);
echo "Configuration file created successfully!<br>";
echo "<a href='admin/login.php'>Go to Admin Login</a>";
?>