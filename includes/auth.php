<?php
class Auth {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function login($username, $password) {
        try {
            // Get user from database
            $user = $this->db->getUserByUsername($username);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            // Check if user is active
            if (isset($user['status']) && $user['status'] != 'active') {
                return ['success' => false, 'message' => 'Account is disabled'];
            }
            
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            $_SESSION['logged_in'] = true;
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            return ['success' => true, 'message' => 'Login successful'];
            
        } catch(Exception $e) {
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
    
    private function updateLastLogin($userId) {
        try {
            $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$userId]);
        } catch(Exception $e) {
            // Silently fail
        }
    }
    
    public function register($username, $email, $password, $full_name = '') {
        try {
            // Check if user already exists
            $existingUser = $this->db->getUserByUsername($username);
            if ($existingUser) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            // Create user
            $result = $this->db->createUser($username, $email, $password, $full_name);
            
            if ($result) {
                return ['success' => true, 'message' => 'Registration successful. Please login.'];
            } else {
                return ['success' => false, 'message' => 'Registration failed. Please try again.'];
            }
            
        } catch(Exception $e) {
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
    
    public function logout() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        return true;
    }
    
    public function isLoggedIn() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
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
    
    public function requireLogin($redirect = 'login.php') {
        if (!$this->isLoggedIn()) {
            header("Location: $redirect");
            exit();
        }
    }
    
    public function requireRole($role, $redirect = 'index.php') {
        $this->requireLogin();
        
        $userRole = $_SESSION['user_role'] ?? 'user';
        if ($userRole !== $role) {
            header("Location: $redirect");
            exit();
        }
    }
}
?>