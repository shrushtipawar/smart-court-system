<?php
includes/admin-auth.php

/**
 * Check if user is logged in as admin
 */
function isAdminLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['logged_in']) && 
           $_SESSION['logged_in'] === true && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'admin';
}

/**
 * Require admin login - redirect if not admin
 */
function requireAdminLogin($redirect = 'admin-login.php') {
    if (!isAdminLoggedIn()) {
        // Store current URL for redirect back after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to admin login
        header("Location: $redirect");
        exit();
    }
}

/**
 * Get admin user data
 */
function getAdminUser() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}

/**
 * Log admin activity
 */
function logAdminActivity($action, $details = '') {
    if (!isAdminLoggedIn()) {
        return false;
    }
    
    try {
        require_once 'config/database.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        // Create admin_logs table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS admin_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            action VARCHAR(255) NOT NULL,
            details TEXT,
            ip_address VARCHAR(50),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_admin (admin_id),
            INDEX idx_action (action)
        )";
        $conn->exec($sql);
        
        // Log the activity
        $stmt = $conn->prepare("
            INSERT INTO admin_logs (admin_id, action, details, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $_SESSION['user_id'],
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
    } catch (Exception $e) {
        error_log("Log admin activity error: " . $e->getMessage());
        return false;
    }
}