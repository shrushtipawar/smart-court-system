<?php
require_once 'config/database.php';
$db = new Database();
$conn = $db->conn;

$sql = "ALTER TABLE users 
        ADD COLUMN user_type ENUM('client', 'lawyer', 'business') DEFAULT 'client',
        ADD COLUMN verification_token VARCHAR(64),
        ADD COLUMN status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
        ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

try {
    $conn->exec($sql);
    echo "SUCCESS! Columns added. <a href='register.php'>Go to registration</a>";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
?>