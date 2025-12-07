in this file create chatbot add the api key "hYeKqE0RylTnImr86U4dGqr8sqwwG2KC"<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['full_name'] ?? '';
$user_role = $_SESSION['user_role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JusticeFlow - Legal Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2d74da;
            --secondary-color: #0d9d6b;
            --accent-color: #6c63ff;
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --bg-light: #f8f9fa;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.1);
            --gradient-primary: linear-gradient(135deg, #2d74da 0%, #0d9d6b 100%);
            --gradient-accent: linear-gradient(135deg, #6c63ff 0%, #2d74da 100%);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background-color: #ffffff;
            padding-top: 80px; /* For fixed navbar */
        }
        
        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: var(--shadow-md);
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition-smooth);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1050;
        }
        
        .navbar.scrolled {
            padding: 10px 0;
            box-shadow: var(--shadow-lg);
            background: rgba(255, 255, 255, 0.99);
        }
        
        /* Brand Styling */
        .navbar-brand {
            padding: 0;
            margin-right: 2rem;
        }
        
        .brand-wrapper {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: var(--transition-smooth);
        }
        
        .brand-wrapper:hover {
            transform: translateY(-1px);
        }
        
        .brand-icon-container {
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 6px 20px rgba(45, 116, 218, 0.25);
            margin-right: 12px;
            position: relative;
            overflow: hidden;
        }
        
        .brand-icon-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }
        
        .brand-wrapper:hover .brand-icon-container::after {
            transform: translateX(100%);
        }
        
        .brand-text {
            display: flex;
            flex-direction: column;
        }
        
        .brand-main {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        
        .brand-tagline {
            font-size: 0.75rem;
            color: var(--text-secondary);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
            font-weight: 600;
        }
        
        /* Navigation Links */
        .navbar-nav {
            gap: 6px;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px !important;
            color: var(--text-secondary) !important;
            font-weight: 500;
            border-radius: 12px;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 12px;
        }
        
        .nav-icon {
            font-size: 1.1rem;
            margin-right: 10px;
            position: relative;
            z-index: 1;
            transition: var(--transition-smooth);
            width: 24px;
            text-align: center;
        }
        
        .nav-text {
            position: relative;
            z-index: 1;
            font-size: 0.95rem;
        }
        
        .nav-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 600;
            z-index: 2;
        }
        
        /* Hover and Active States */
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 116, 218, 0.2);
        }
        
        .nav-link:hover::before {
            opacity: 1;
        }
        
        .nav-link:hover .nav-icon {
            transform: scale(1.1);
        }
        
        .nav-link.active {
            color: white !important;
            background: var(--gradient-primary);
            box-shadow: 0 4px 15px rgba(45, 116, 218, 0.3);
        }
        
        .nav-link.active::before {
            opacity: 1;
        }
        
        /* User Dropdown */
        .user-dropdown .dropdown-toggle {
            background: rgba(45, 116, 218, 0.05);
            border: 1px solid rgba(45, 116, 218, 0.1);
            border-radius: 14px;
            padding: 10px 16px;
            color: var(--primary-color);
            transition: var(--transition-smooth);
            text-decoration: none;
        }
        
        .user-dropdown .dropdown-toggle:hover {
            background: rgba(45, 116, 218, 0.1);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(45, 116, 218, 0.15);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            margin-right: 12px;
            position: relative;
            overflow: hidden;
        }
        
        .user-avatar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .user-info {
            text-align: left;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            line-height: 1.2;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--secondary-color);
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .user-status {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            padding: 10px;
            min-width: 240px;
            margin-top: 12px !important;
            border: 1px solid var(--border-color);
            animation: dropdownFadeIn 0.3s ease;
        }
        
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item {
            padding: 12px 16px;
            border-radius: 10px;
            margin: 2px 0;
            color: var(--text-primary);
            font-weight: 500;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--gradient-primary);
            transform: scaleY(0);
            transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }
        
        .dropdown-item:hover {
            background: rgba(45, 116, 218, 0.05);
            color: var(--primary-color);
            padding-left: 20px;
        }
        
        .dropdown-item:hover::before {
            transform: scaleY(1);
        }
        
        .dropdown-item i {
            width: 24px;
            margin-right: 12px;
            color: var(--text-secondary);
        }
        
        .dropdown-item:hover i {
            color: var(--primary-color);
        }
        
        .dropdown-divider {
            margin: 8px 0;
            opacity: 0.5;
        }
        
        .logout-btn {
            color: #ef4444 !important;
        }
        
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.08) !important;
        }
        
        /* Auth Buttons */
        .auth-buttons {
            display: flex;
            gap: 16px;
        }
        
        .btn-login {
            border-radius: 12px;
            padding: 12px 24px;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-login:hover {
            background: rgba(45, 116, 218, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(45, 116, 218, 0.15);
        }
        
        .btn-register {
            border-radius: 12px;
            padding: 12px 28px;
            background: var(--gradient-primary);
            border: none;
            font-weight: 600;
            transition: var(--transition-smooth);
            box-shadow: 0 6px 20px rgba(45, 116, 218, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .btn-register::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(45, 116, 218, 0.4);
        }
        
        .btn-register:hover::after {
            animation: shine 1.5s;
        }
        
        @keyframes shine {
            100% {
                transform: translateX(100%);
            }
        }
        
        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            body {
                padding-top: 70px;
            }
            
            .navbar {
                padding: 10px 0;
            }
            
            .navbar-collapse {
                background: white;
                padding: 20px;
                border-radius: 20px;
                margin-top: 15px;
                box-shadow: var(--shadow-lg);
                max-height: calc(100vh - 100px);
                overflow-y: auto;
            }
            
            .navbar-nav {
                gap: 8px;
                margin-bottom: 20px;
            }
            
            .nav-link {
                justify-content: flex-start;
                padding: 14px 20px !important;
            }
            
            .auth-buttons {
                flex-direction: column;
                gap: 12px !important;
            }
            
            .auth-buttons .btn {
                width: 100%;
                justify-content: center;
            }
            
            .brand-main {
                font-size: 1.5rem;
            }
            
            .brand-icon-container {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
        
        /* Desktop Optimization */
        @media (min-width: 992px) {
            .navbar .container {
                max-width: 1200px;
            }
        }
        
        /* Notification Badge Animation */
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }
        
        .has-notification .nav-badge {
            animation: pulse 2s infinite;
        }
        
        /* Accessibility */
        .nav-link:focus,
        .dropdown-toggle:focus,
        .btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .navbar {
                background: rgba(15, 23, 42, 0.95);
                border-bottom-color: rgba(255, 255, 255, 0.1);
            }
            
            .navbar.scrolled {
                background: rgba(15, 23, 42, 0.98);
            }
            
            .nav-link {
                color: #cbd5e1 !important;
            }
            
            .nav-link:hover,
            .nav-link.active {
                color: white !important;
            }
            
            .navbar-collapse {
                background: #0f172a;
            }
            
            .brand-tagline {
                color: #94a3b8;
            }
        }
        
        /* Utility Classes */
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-gradient {
            background: var(--gradient-primary);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="index.php">
                <div class="brand-wrapper">
                    <div class="brand-icon-container">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-main">JusticeFlow</span>
                        <span class="brand-tagline">Legal Excellence</span>
                    </div>
                </div>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                           href="index.php">
                            <div class="nav-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <span class="nav-text">Home</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" 
                           href="about.php">
                            <div class="nav-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <span class="nav-text">About Us</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'features.php' ? 'active' : ''; ?>" 
                           href="features.php">
                            <div class="nav-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="nav-text">Features</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'works.php' ? 'active' : ''; ?>" 
                           href="works.php">
                            <div class="nav-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <span class="nav-text">How It Works</span>
                        </a>
                    </li>
                    
                    <li class="nav-item has-notification">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ask-ai.php' ? 'active' : ''; ?>" 
                           href="ask-ai.php">
                            <div class="nav-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <span class="nav-text">Ask AI</span>
                            <span class="nav-badge">NEW</span>
                        </a>
                    </li>
                    
                    <?php if($logged_in && $user_role === 'lawyer'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cases.php' ? 'active' : ''; ?>" 
                           href="cases.php">
                            <div class="nav-icon">
                                <i class="fas fa-gavel"></i>
                            </div>
                            <span class="nav-text">My Cases</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- User Actions -->
                <div class="user-actions">
                    <?php if($logged_in): ?>
                        <!-- User Dropdown -->
                        <div class="dropdown user-dropdown">
                            <a class="dropdown-toggle d-flex align-items-center" href="#" 
                               id="userDropdown" role="button" data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                    <div class="user-status"></div>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                                    <div class="user-role"><?php echo htmlspecialchars($user_role); ?></div>
                                </div>
                                <i class="fas fa-chevron-down ms-2" style="font-size: 0.9rem;"></i>
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="dashboard.php">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-user-cog"></i>
                                        <span>Profile Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="documents.php">
                                        <i class="fas fa-file-contract"></i>
                                        <span>My Documents</span>
                                    </a>
                                </li>
                                <?php if($user_role === 'lawyer' || $user_role === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="clients.php">
                                        <i class="fas fa-users"></i>
                                        <span>Client Management</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="notifications.php">
                                        <i class="fas fa-bell"></i>
                                        <span>Notifications</span>
                                        <span class="badge bg-danger ms-auto">3</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item logout-btn" href="logout.php">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Authentication Buttons -->
                        <div class="auth-buttons">
                            <a href="login.php" class="btn btn-outline-primary btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login
                            </a>
                            <a href="register.php" class="btn btn-primary btn-register">
                                <i class="fas fa-user-plus me-2"></i>
                                Get Started Free
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Chatbot Button -->
    <button id="chatbotToggle" class="btn btn-primary rounded-circle shadow-lg" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 1000; border: none; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-robot" style="font-size: 1.5rem;"></i>
    </button>

    <!-- Chatbot Container -->
    <div id="chatbotContainer" class="shadow-lg" style="display: none; position: fixed; bottom: 100px; right: 30px; width: 350px; height: 500px; background: white; border-radius: 15px; z-index: 1000; overflow: hidden; flex-direction: column;">
        <!-- Chat Header -->
        <div class="d-flex justify-content-between align-items-center p-3" style="background: linear-gradient(135deg, #2d74da 0%, #0d9d6b 100%); color: white;">
            <h5 class="mb-0"><i class="fas fa-robot me-2"></i> Legal Assistant</h5>
            <button id="minimizeChat" class="btn btn-sm btn-light"><i class="fas fa-minus"></i></button>
        </div>
        
        <!-- Chat Messages -->
        <div id="chatMessages" style="flex: 1; overflow-y: auto; padding: 15px;">
            <div class="chat-message bot-message mb-3">
                <div class="message-bubble bg-light p-3 rounded">
                    Hello! I'm your legal assistant. How can I help you today?
                </div>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="p-3 border-top">
            <div class="input-group">
                <input type="text" id="userInput" class="form-control" placeholder="Type your message..." aria-label="Message input">
                <button class="btn btn-primary" id="sendMessage">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 fw-bold mb-4 text-gradient">JusticeFlow</h1>
                <p class="lead mb-4">Modern Legal Case Management System</p>
                <div class="card border-0 shadow-lg rounded-3 overflow-hidden">
                    <div class="card-body p-5">
                        <h2 class="card-title h3 mb-4">Welcome to Your Enhanced Navbar</h2>
                        <p class="card-text">
                            This is a demonstration of the improved JusticeFlow navbar. The navigation includes:
                        </p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item border-0 px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Enhanced visual design with modern gradients
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Smooth animations and transitions
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Responsive design for all devices
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Role-based navigation (Lawyer/Admin features)
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Dark mode support
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Accessibility features
                            </li>
                        </ul>
                        <div class="alert alert-info border-0 bg-light">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> This is a standalone HTML file with all styles included. 
                            For production, you would separate the CSS into external files.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Chatbot functionality
        document.addEventListener('DOMContentLoaded', function() {
            const chatbotToggle = document.getElementById('chatbotToggle');
            const chatbotContainer = document.getElementById('chatbotContainer');
            const minimizeChat = document.getElementById('minimizeChat');
            const chatMessages = document.getElementById('chatMessages');
            const userInput = document.getElementById('userInput');
            const sendMessageBtn = document.getElementById('sendMessage');
            
            let isChatOpen = false;
            let isMinimized = false;
            // Using our PHP endpoint instead of direct API call
            const API_ENDPOINT = 'chat-api.php';
            
            // Toggle chat window
            chatbotToggle.addEventListener('click', () => {
                if (isMinimized) {
                    chatbotContainer.style.display = 'flex';
                    isMinimized = false;
                } else {
                    chatbotContainer.style.display = isChatOpen ? 'none' : 'flex';
                    isChatOpen = !isChatOpen;
                }
                if (isChatOpen && !isMinimized) {
                    userInput.focus();
                }
            });
            
            // Minimize chat
            minimizeChat.addEventListener('click', (e) => {
                e.stopPropagation();
                chatbotContainer.style.display = 'none';
                isMinimized = true;
            });
            
            // Send message on button click or Enter key
            function sendMessage() {
                const message = userInput.value.trim();
                if (message === '') return;
                
                // Add user message to chat
                addMessage(message, 'user');
                userInput.value = '';
                
                // Show typing indicator
                const typingIndicator = addTypingIndicator();
                
                // Call our PHP endpoint
                fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Remove typing indicator
                    typingIndicator.remove();
                    
                    // Add AI response to chat
                    if (data.choices && data.choices.length > 0 && data.choices[0].message) {
                        addMessage(data.choices[0].message.content, 'bot');
                    } else {
                        console.error('Unexpected API response format:', data);
                        addMessage('I received an unexpected response format. Please try again.', 'bot');
                    }
                })
                .catch(error => {
                    console.error('API Error Details:', {
                        error: error.toString(),
                        message: error.message,
                        stack: error.stack
                    });
                    typingIndicator.remove();
                    addMessage('I apologize, but I encountered an error: ' + error.message, 'bot');
                });
            }
            
            // Add message to chat
            function addMessage(text, sender) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${sender}-message mb-3`;
                messageDiv.innerHTML = `
                    <div class="message-bubble ${sender === 'user' ? 'bg-primary text-white' : 'bg-light'} p-3 rounded">
                        ${text}
                    </div>
                `;
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Add typing indicator
            function addTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'typing-indicator mb-3';
                typingDiv.innerHTML = `
                    <div class="typing-bubble bg-light p-3 rounded">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                `;
                chatMessages.appendChild(typingDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                return typingDiv;
            }
            
            // Event listeners
            sendMessageBtn.addEventListener('click', sendMessage);
            userInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
        
        // Add some styles for the chat
        const style = document.createElement('style');
        style.textContent = `
            .user-message {
                display: flex;
                justify-content: flex-end;
                margin-left: 20%;
            }
            .bot-message {
                display: flex;
                justify-content: flex-start;
                margin-right: 20%;
            }
            .message-bubble {
                max-width: 80%;
                word-wrap: break-word;
                box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }
            .typing-bubble {
                display: inline-block;
                padding: 10px 15px;
                border-radius: 18px;
            }
            .dot {
                display: inline-block;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #6c757d;
                margin: 0 2px;
                animation: bounce 1.4s infinite ease-in-out both;
            }
            .dot:nth-child(1) { animation-delay: -0.32s; }
            .dot:nth-child(2) { animation-delay: -0.16s; }
            @keyframes bounce {
                0%, 80%, 100% { transform: scale(0); }
                40% { transform: scale(1); }
            }
            #chatbotToggle {
                transition: all 0.3s ease;
                background: linear-gradient(135deg, #2d74da 0%, #0d9d6b 100%);
            }
            #chatbotToggle:hover {
                transform: scale(1.1);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
        `;
        document.head.appendChild(style);
    </script>
    
    <script>
        // Navbar scroll effect
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            
            // Add scroll effect
            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Add click animation to nav items
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Add active class to clicked item
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Close mobile menu if open
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        const toggleBtn = document.querySelector('.navbar-toggler');
                        toggleBtn.click();
                    }
                });
            });
            
            // Add ripple effect to buttons
            const buttons = document.querySelectorAll('.btn, .nav-link, .dropdown-item');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple element
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    
                    // Calculate ripple size
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    // Style ripple
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.6);
                        transform: scale(0);
                        animation: ripple-animation 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        top: ${y}px;
                        left: ${x}px;
                        pointer-events: none;
                        z-index: 0;
                    `;
                    
                    // Add ripple to button
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    // Remove ripple after animation
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
            
            // Add ripple animation to CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                
                /* Additional animations */
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .fade-in-up {
                    animation: fadeInUp 0.6s ease;
                }
            `;
            document.head.appendChild(style);
            
            // Demo notification badge
            const aiNavItem = document.querySelector('.has-notification');
            let pulseCount = 0;
            
            setInterval(() => {
                if (pulseCount < 3) {
                    aiNavItem.querySelector('.nav-badge').style.animation = 'none';
                    setTimeout(() => {
                        aiNavItem.querySelector('.nav-badge').style.animation = 'pulse 2s infinite';
                    }, 10);
                    pulseCount++;
                }
            }, 10000);
            
            // Simulate page load
            document.querySelector('main').classList.add('fade-in-up');
            
            // Console greeting
            console.log('%c⚖️ JusticeFlow Navbar Loaded Successfully', 
                'color: #2d74da; font-size: 14px; font-weight: bold;');
            console.log('%cDesigned for Legal Professionals', 
                'color: #0d9d6b; font-size: 12px;');
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (window.innerWidth > 991.98 && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
            }
        });
    </script>
</body>
</html>