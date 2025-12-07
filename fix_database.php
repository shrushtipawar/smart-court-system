<?php
// fix_database.php
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->conn;

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Fix Database Structure</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { padding: 20px; background: #f8f9fa; }
        .container { max-width: 900px; }
        .success { color: #198754; }
        .error { color: #dc3545; }
        .info { color: #0dcaf0; }
        .card { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='card'>
            <div class='card-header bg-primary text-white'>
                <h2><i class='fas fa-database'></i> Fix Database Structure</h2>
            </div>
            <div class='card-body'>";

try {
    if (!$conn) {
        throw new Exception("Database connection failed!");
    }
    
    echo "<h4>Current Users Table Structure:</h4>";
    
    // Check current table structure
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table class='table table-bordered'>
            <thead class='table-light'>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                    <th>Default</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($columns as $col) {
        echo "<tr>
                <td>{$col['Field']}</td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
                <td>{$col['Default']}</td>
              </tr>";
    }
    
    echo "</tbody></table>";
    
    // Define the missing columns and their SQL
    $missingColumns = [
        'user_type' => [
            'sql' => "ALTER TABLE users ADD COLUMN user_type ENUM('client', 'lawyer', 'business') DEFAULT 'client'",
            'description' => 'User type column for registration form'
        ],
        'verification_token' => [
            'sql' => "ALTER TABLE users ADD COLUMN verification_token VARCHAR(64)",
            'description' => 'Email verification token'
        ],
        'status' => [
            'sql' => "ALTER TABLE users ADD COLUMN status ENUM('pending', 'active', 'suspended') DEFAULT 'pending'",
            'description' => 'User account status'
        ],
        'created_at' => [
            'sql' => "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            'description' => 'Account creation timestamp'
        ]
    ];
    
    echo "<h4>Adding Missing Columns:</h4>";
    
    foreach ($missingColumns as $columnName => $columnInfo) {
        // Check if column exists
        $checkStmt = $conn->prepare("SHOW COLUMNS FROM users LIKE ?");
        $checkStmt->execute([$columnName]);
        
        if ($checkStmt->rowCount() > 0) {
            echo "<div class='alert alert-info'>
                    <i class='fas fa-check-circle'></i> 
                    Column <strong>'$columnName'</strong> already exists
                  </div>";
        } else {
            try {
                $conn->exec($columnInfo['sql']);
                echo "<div class='alert alert-success'>
                        <i class='fas fa-check-circle'></i> 
                        Successfully added column <strong>'$columnName'</strong><br>
                        <small>{$columnInfo['description']}</small>
                      </div>";
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>
                        <i class='fas fa-exclamation-circle'></i> 
                        Error adding column <strong>'$columnName'</strong>: " . htmlspecialchars($e->getMessage()) . "
                      </div>";
            }
        }
    }
    
    // Optional: Add updated_at column
    echo "<h4>Optional Columns:</h4>";
    
    $optionalColumns = [
        'updated_at' => [
            'sql' => "ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            'description' => 'Last update timestamp (auto-updates)'
        ]
    ];
    
    foreach ($optionalColumns as $columnName => $columnInfo) {
        $checkStmt = $conn->prepare("SHOW COLUMNS FROM users LIKE ?");
        $checkStmt->execute([$columnName]);
        
        if ($checkStmt->rowCount() > 0) {
            echo "<div class='alert alert-info'>
                    <i class='fas fa-check-circle'></i> 
                    Column <strong>'$columnName'</strong> already exists
                  </div>";
        } else {
            try {
                $conn->exec($columnInfo['sql']);
                echo "<div class='alert alert-success'>
                        <i class='fas fa-check-circle'></i> 
                        Successfully added column <strong>'$columnName'</strong><br>
                        <small>{$columnInfo['description']}</small>
                      </div>";
            } catch (PDOException $e) {
                echo "<div class='alert alert-warning'>
                        <i class='fas fa-exclamation-triangle'></i> 
                        Optional column <strong>'$columnName'</strong> not added: " . htmlspecialchars($e->getMessage()) . "
                      </div>";
            }
        }
    }
    
    echo "<hr>";
    
    // Verify the fix worked
    echo "<h4>Verifying Fix...</h4>";
    
    $requiredColumns = ['user_type', 'verification_token', 'status', 'created_at'];
    $allGood = true;
    
    foreach ($requiredColumns as $col) {
        $checkStmt = $conn->prepare("SHOW COLUMNS FROM users LIKE ?");
        $checkStmt->execute([$col]);
        
        if ($checkStmt->rowCount() > 0) {
            echo "<div class='alert alert-success'>
                    <i class='fas fa-check-circle'></i> 
                    Column <strong>'$col'</strong> exists ✓
                  </div>";
        } else {
            echo "<div class='alert alert-danger'>
                    <i class='fas fa-times-circle'></i> 
                    Column <strong>'$col'</strong> is still missing ✗
                  </div>";
            $allGood = false;
        }
    }
    
    if ($allGood) {
        echo "<div class='alert alert-success text-center'>
                <h4><i class='fas fa-thumbs-up'></i> Database Fixed Successfully!</h4>
                <p>Your registration form should now work properly.</p>
                <a href='register.php' class='btn btn-success'>
                    <i class='fas fa-user-plus'></i> Test Registration
                </a>
              </div>";
    } else {
        echo "<div class='alert alert-warning text-center'>
                <h4><i class='fas fa-exclamation-triangle'></i> Some Issues Remain</h4>
                <p>Check the errors above and try running the SQL manually.</p>
              </div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>
            <h4><i class='fas fa-exclamation-circle'></i> Error</h4>
            " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "</div>
        <div class='card-footer text-center'>
            <a href='register.php' class='btn btn-primary'>Back to Registration</a>
            <a href='index.php' class='btn btn-secondary'>Back to Home</a>
        </div>
    </div>
    
    <div class='card mt-4'>
        <div class='card-header bg-info text-white'>
            <h5>SQL Commands to Run Manually</h5>
        </div>
        <div class='card-body'>
            <p>If the automated fix didn't work, run these SQL commands in phpMyAdmin or MySQL:</p>
            <pre class='bg-dark text-white p-3 rounded'>
-- Add user_type column
ALTER TABLE users ADD COLUMN user_type ENUM('client', 'lawyer', 'business') DEFAULT 'client';

-- Add verification_token column
ALTER TABLE users ADD COLUMN verification_token VARCHAR(64);

-- Add status column
ALTER TABLE users ADD COLUMN status ENUM('pending', 'active', 'suspended') DEFAULT 'pending';

-- Add created_at column
ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Optional: Add updated_at column
ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;</pre>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
</body>
</html>";
?>