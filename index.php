<?php
session_start();

// Initialize analytics with default values
$analytics = [
    'resolution_rate' => 68.5,
    'avg_processing_days' => 45,
    'total_cases' => 150,
    'active_cases' => 80,
    'resolved_cases' => 70
];

// Initialize database connection
try {
    // Check if config directory exists, if not create it
    if (!is_dir('config')) {
        mkdir('config', 0777, true);
    }
    
    // Check if database.php exists
    if (!file_exists('config/database.php')) {
        // Redirect to setup
        header('Location: setup.php');
        exit;
    }
    
    require_once 'config/database.php';
    $db = new Database();
    
    // Get analytics data from database if available
    // Example: 
    // $analytics = $db->getAnalytics();
   
} catch (Exception $e) {
    // Check if we should redirect to setup
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        // Database doesn't exist, show setup message
        echo '<div style="padding: 20px; text-align: center;">
                <h2>Database Setup Required</h2>
                <p>The justiceflow database doesn\'t exist.</p>
                <a href="setup.php" style="display: inline-block; padding: 10px 20px; background: #1a365d; color: white; text-decoration: none; border-radius: 5px;">
                    Click here to run setup
                </a>
              </div>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JusticeFlow - LegalTech & Governance Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
        }
        
        /* Simplified Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 15px 0;
        }
        
        .navbar-custom .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            margin: 0 10px;
            padding: 8px 20px !important;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .navbar-custom .nav-link:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white !important;
            transform: translateY(-2px);
        }
        
        .navbar-custom .nav-link.active {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white !important;
        }
        
        .brand-gradient {
            background: linear-gradient(135deg, #2d74da 0%, #0d9d6b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 150px 0 100px;
            margin-top: 70px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="%23ffffff" opacity="0.05" d="M50,0 L100,50 L50,100 L0,50 Z"/></svg>');
            background-size: 200px;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0) translateX(0); }
            100% { transform: translateY(-100px) translateX(-100px); }
        }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            transition: transform 0.3s;
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.25);
        }
        
        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s;
            background: white;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 116, 218, 0.3);
        }
        
        /* Ask AI Section */
        .ai-chat-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        
        .ai-message {
            background: #f0f7ff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--secondary-color);
        }
        
        .user-message {
            background: #f0fff4;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent-color);
        }
        
        /* Footer */
        .footer {
            background: var(--primary-color);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-links a {
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 0 60px;
            }
            
            .navbar-custom .nav-link {
                margin: 5px 0;
                text-align: center;
            }
        }
        
        /* How It Works Steps */
        .step-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            height: 100%;
            transition: all 0.3s;
            border: 2px solid #e2e8f0;
        }
        
        .step-card:hover {
            transform: translateY(-10px);
            border-color: var(--secondary-color);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <!-- Simplified Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.php">
                <i class="fas fa-balance-scale text-primary me-2"></i>
                <span class="brand-gradient">JusticeFlow</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle me-1"></i>About Us
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="features.php">
                            <i class="fas fa-star me-1"></i>Features
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="works.php">
                            <i class="fas fa-cogs me-1"></i>Works
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="ask-ai.php">
                            <i class="fas fa-robot me-1"></i>Ask AI
                        </a>
                    </li>
                </ul>
                
                <!-- User Actions -->
                <div class="d-flex align-items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                               id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg me-2"></i>
                                <span><?php echo $_SESSION['full_name'] ?? 'User'; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="dashboard.php">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="d-flex gap-2">
                            <a href="login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                            <a href="register.php" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">
                        Revolutionizing Legal Tech with 
                        <span class="text-gradient">AI-Powered Solutions</span>
                    </h1>
                    <p class="lead mb-5">
                        Transform your legal practice with intelligent case management, 
                        AI-driven research, and automated dispute resolution systems.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register.php" class="btn btn-light btn-lg px-5">
                            <i class="fas fa-rocket me-2"></i>Get Started Free
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg px-5">
                            <i class="fas fa-play-circle me-2"></i>Watch Demo
                        </a>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="row mt-5 g-3">
                        <div class="col-4">
                            <div class="text-center">
                                <h4 class="fw-bold"><?php echo $analytics['resolution_rate']; ?>%</h4>
                                <small>Resolution Rate</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h4 class="fw-bold">45%</h4>
                                <small>Faster Processing</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h4 class="fw-bold"><?php echo $analytics['active_cases']; ?>+</h4>
                                <small>Active Cases</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="stat-card text-center">
                                <i class="fas fa-brain fa-3x mb-3"></i>
                                <h3 class="display-4 fw-bold">AI</h3>
                                <p class="mb-0">Powered Research</p>
                                <small class="text-light">Instant Legal Analysis</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card text-center">
                                <i class="fas fa-bolt fa-3x mb-3"></i>
                                <h3 class="display-4 fw-bold">24/7</h3>
                                <p class="mb-0">Legal Assistance</p>
                                <small class="text-light">Always Available</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card text-center">
                                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                <h3 class="display-4 fw-bold">100%</h3>
                                <p class="mb-0">Secure & Compliant</p>
                                <small class="text-light">Data Protection</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card text-center">
                                <i class="fas fa-handshake fa-3x mb-3"></i>
                                <h3 class="display-4 fw-bold">95%</h3>
                                <p class="mb-0">Client Satisfaction</p>
                                <small class="text-light">Happy Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Ask AI Section -->
    <section id="ask-ai" class="py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="ai-chat-container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-robot text-primary me-2"></i>
                        Ask Our Legal AI Assistant
                    </h2>
                    <p class="lead text-muted">Get instant answers to your legal questions powered by AI</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Chat Messages -->
                        <div class="mb-4" style="max-height: 400px; overflow-y: auto;">
                            <div class="ai-message">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-robot text-primary me-2"></i>
                                    <strong>Legal AI Assistant</strong>
                                    <span class="badge bg-primary ms-2">AI</span>
                                </div>
                                <p class="mb-0">Hello! I'm your legal AI assistant. How can I help you today? You can ask me about legal procedures, document reviews, case analysis, or any legal queries.</p>
                            </div>
                            
                            <div class="user-message">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user text-success me-2"></i>
                                    <strong>User</strong>
                                </div>
                                <p class="mb-0">What documents are needed for filing a divorce case?</p>
                            </div>
                            
                            <div class="ai-message">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-robot text-primary me-2"></i>
                                    <strong>Legal AI Assistant</strong>
                                    <span class="badge bg-primary ms-2">AI</span>
                                </div>
                                <p class="mb-0">For filing a divorce case, you typically need:
                                1. Marriage certificate
                                2. Proof of address
                                3. ID proof of both parties
                                4. Income proof
                                5. Photographs
                                6. Details of children (if any)
                                7. Evidence for grounds of divorce</p>
                            </div>
                        </div>
                        
                        <!-- Chat Input -->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" 
                                   placeholder="Type your legal question here...">
                            <button class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        
                        <!-- Quick Questions -->
                        <div class="mt-4">
                            <p class="text-muted mb-2">Try asking:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-outline-primary btn-sm">How to draft a will?</button>
                                <button class="btn btn-outline-primary btn-sm">Property transfer procedure</button>
                                <button class="btn btn-outline-primary btn-sm">Legal rights of tenants</button>
                                <button class="btn btn-outline-primary btn-sm">Consumer complaint process</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4">
                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                    AI Capabilities
                                </h5>
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Legal Document Analysis
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Case Law Research
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Contract Review
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Legal Advice Generation
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Document Drafting
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        24/7 Availability
                                    </li>
                                </ul>
                                
                                <hr class="my-4">
                                
                                <h6 class="mb-3">
                                    <i class="fas fa-chart-line text-info me-2"></i>
                                    AI Accuracy
                                </h6>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                                <small class="text-muted">92% accuracy in legal analysis</small>
                                
                                <div class="mt-4">
                                    <a href="#" class="btn btn-primary w-100">
                                        <i class="fas fa-bolt me-2"></i>Try Premium AI Features
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about-us" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        About JusticeFlow
                    </h2>
                    <p class="lead text-muted mb-4">
                        JusticeFlow is a cutting-edge LegalTech platform that combines artificial intelligence 
                        with legal expertise to revolutionize how legal services are delivered.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bullseye fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Our Mission</h5>
                                    <p class="text-muted">Democratize access to legal services through technology.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-eye fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Our Vision</h5>
                                    <p class="text-muted">Create a world where justice is accessible to everyone.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-handshake fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Our Values</h5>
                                    <p class="text-muted">Integrity, Innovation, Accessibility, Excellence.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users fa-2x text-danger"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Our Team</h5>
                                    <p class="text-muted">Experts in law, technology, and AI.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fas fa-book me-2"></i>Read Our Story
                    </a>
                </div>
                
                <div class="col-lg-6">
                    <div class="position-relative">
                        <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                            <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 class="img-fluid" alt="About JusticeFlow">
                            <div class="card-img-overlay d-flex align-items-end" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                <div class="text-white p-4">
                                    <h4 class="mb-2">Founded in 2023</h4>
                                    <p class="mb-0">Serving 1000+ clients worldwide</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats Overlay -->
                        <div class="position-absolute top-0 end-0 mt-3 me-3">
                            <div class="bg-white rounded-circle shadow-sm p-3">
                                <h3 class="fw-bold text-primary mb-0">50+</h3>
                                <small>Team Members</small>
                            </div>
                        </div>
                        
                        <div class="position-absolute bottom-0 start-0 mb-3 ms-3">
                            <div class="bg-white rounded-circle shadow-sm p-3">
                                <h3 class="fw-bold text-success mb-0">25+</h3>
                                <small>Countries</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">Our Features</h2>
                <p class="lead text-muted">Powerful tools designed for modern legal practice</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-brain fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">AI Legal Research</h3>
                        <p class="text-muted mb-4">Advanced AI algorithms analyze precedents, statutes, and legal documents in seconds.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Instant Case Analysis</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Statute Tracking</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Similar Case Matching</li>
                            <li><i class="fas fa-check text-success me-2"></i> Document Review AI</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary mt-3">Learn More</a>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-folder-open fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Case Management</h3>
                        <p class="text-muted mb-4">Automated scheduling, priority-based assignment, and real-time tracking of cases.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Automated Scheduling</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Priority-based Assignment</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Real-time Updates</li>
                            <li><i class="fas fa-check text-success me-2"></i> Document Management</li>
                        </ul>
                        <a href="#" class="btn btn-outline-success mt-3">Learn More</a>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-handshake fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Dispute Resolution</h3>
                        <p class="text-muted mb-4">Virtual mediation rooms with secure communication tools for efficient resolution.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Virtual Mediation</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Arbitration Scheduling</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Settlement Tools</li>
                            <li><i class="fas fa-check text-success me-2"></i> Secure Sharing</li>
                        </ul>
                        <a href="#" class="btn btn-outline-warning mt-3">Learn More</a>
                    </div>
                </div>
                
                <!-- Additional Features -->
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-file-contract fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Document Automation</h3>
                        <p class="text-muted mb-4">Generate legal documents automatically with AI-powered templates.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Smart Templates</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Auto-fill Forms</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> E-signature Integration</li>
                            <li><i class="fas fa-check text-success me-2"></i> Version Control</li>
                        </ul>
                        <a href="#" class="btn btn-outline-info mt-3">Learn More</a>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Analytics & Insights</h3>
                        <p class="text-muted mb-4">Data-driven insights for better decision making and case predictions.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Case Prediction</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Performance Metrics</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Trend Analysis</li>
                            <li><i class="fas fa-check text-success me-2"></i> Custom Reports</li>
                        </ul>
                        <a href="#" class="btn btn-outline-danger mt-3">Learn More</a>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <div class="bg-purple rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #6f42c1;">
                                <i class="fas fa-shield-alt fa-2x text-white"></i>
                            </div>
                        </div>
                        <h3 class="h4 mb-3">Security & Compliance</h3>
                        <p class="text-muted mb-4">Enterprise-grade security with full compliance to legal standards.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> End-to-End Encryption</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Data Privacy</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Audit Trail</li>
                            <li><i class="fas fa-check text-success me-2"></i> Regulatory Compliance</li>
                        </ul>
                        <a href="#" class="btn btn-outline-purple mt-3" style="border-color: #6f42c1; color: #6f42c1;">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">How It Works</h2>
                <p class="lead text-muted">Simple steps to transform your legal practice</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4 class="h5 mb-3">Sign Up</h4>
                        <p class="text-muted">Create your free account in minutes. No credit card required.</p>
                        <div class="mt-3">
                            <i class="fas fa-user-plus fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4 class="h5 mb-3">Set Up</h4>
                        <p class="text-muted">Configure your preferences and integrate with existing systems.</p>
                        <div class="mt-3">
                            <i class="fas fa-cogs fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4 class="h5 mb-3">Automate</h4>
                        <p class="text-muted">Let AI handle research, document generation, and case management.</p>
                        <div class="mt-3">
                            <i class="fas fa-robot fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h4 class="h5 mb-3">Grow</h4>
                        <p class="text-muted">Scale your practice with data-driven insights and automation.</p>
                        <div class="mt-3">
                            <i class="fas fa-chart-line fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Process Flow -->
            <div class="mt-5 text-center">
                <div class="d-flex justify-content-center align-items-center mb-4">
                    <div class="text-center mx-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-upload"></i>
                        </div>
                        <p class="mt-2 mb-0">Upload Case</p>
                    </div>
                    
                    <div class="flex-grow-1 border-top border-2 border-primary"></div>
                    
                    <div class="text-center mx-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-brain"></i>
                        </div>
                        <p class="mt-2 mb-0">AI Analysis</p>
                    </div>
                    
                    <div class="flex-grow-1 border-top border-2 border-success"></div>
                    
                    <div class="text-center mx-4">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <p class="mt-2 mb-0">Generate Report</p>
                    </div>
                    
                    <div class="flex-grow-1 border-top border-2 border-warning"></div>
                    
                    <div class="text-center mx-4">
                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="mt-2 mb-0">Case Resolution</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">What Our Clients Say</h2>
                <p class="lead text-muted">Trusted by legal professionals worldwide</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=John+Doe&background=2d74da&color=fff" 
                                     class="rounded-circle me-3" width="50" alt="John Doe">
                                <div>
                                    <h5 class="mb-0">John Doe</h5>
                                    <small class="text-muted">Senior Partner, LawFirm Inc.</small>
                                </div>
                            </div>
                            <p class="card-text">"JusticeFlow has transformed how we handle cases. The AI research saves us hours of work every day."</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=0d9d6b&color=fff" 
                                     class="rounded-circle me-3" width="50" alt="Jane Smith">
                                <div>
                                    <h5 class="mb-0">Jane Smith</h5>
                                    <small class="text-muted">Legal Director, TechCorp</small>
                                </div>
                            </div>
                            <p class="card-text">"The document automation feature alone has increased our efficiency by 200%. Highly recommended!"</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://ui-avatars.com/api/?name=Robert+Johnson&background=ffc107&color=000" 
                                     class="rounded-circle me-3" width="50" alt="Robert Johnson">
                                <div>
                                    <h5 class="mb-0">Robert Johnson</h5>
                                    <small class="text-muted">Solo Practitioner</small>
                                </div>
                            </div>
                            <p class="card-text">"As a solo practitioner, JusticeFlow gives me the capabilities of a large firm at a fraction of the cost."</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold text-white mb-3">Ready to Transform Your Legal Practice?</h2>
                    <p class="lead text-white mb-0">Join thousands of legal professionals already using JusticeFlow</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="register.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-user-plus me-2"></i>Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h3 class="h4 mb-4">
                        <i class="fas fa-balance-scale me-2"></i>
                        <span class="text-gradient">JusticeFlow</span>
                    </h3>
                    <p class="text-light mb-4">Revolutionizing legal practice through AI-powered technology and innovative solutions.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-5"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light fs-5"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light fs-5"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-light fs-5"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="h6 mb-4 text-white">Product</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="#features">Features</a></li>
                        <li class="mb-2"><a href="#how-it-works">How It Works</a></li>
                        <li class="mb-2"><a href="#ask-ai">Ask AI</a></li>
                        <li class="mb-2"><a href="#">Pricing</a></li>
                        <li><a href="#">API</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="h6 mb-4 text-white">Company</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="#about-us">About Us</a></li>
                        <li class="mb-2"><a href="#">Careers</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Press</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="h6 mb-4 text-white">Legal</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="#">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#">Terms of Service</a></li>
                        <li class="mb-2"><a href="#">Cookie Policy</a></li>
                        <li class="mb-2"><a href="#">GDPR</a></li>
                        <li><a href="#">Compliance</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: #444;">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-light">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        123 Legal Street, San Francisco, CA 94107
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light">
                        &copy; <?php echo date('Y'); ?> JusticeFlow. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple counter animation
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.display-4');
            counters.forEach(counter => {
                const text = counter.textContent;
                const target = parseFloat(text.replace('%', '').replace('+', ''));
                if (!isNaN(target)) {
                    let count = 0;
                    const increment = target / 100;
                    const updateCounter = () => {
                        if (count < target) {
                            count += increment;
                            counter.textContent = Math.round(count) + (text.includes('%') ? '%' : text.includes('+') ? '+' : '');
                            setTimeout(updateCounter, 20);
                        } else {
                            counter.textContent = text;
                        }
                    };
                    updateCounter();
                }
            });
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    if(this.getAttribute('href') !== '#') {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });
            
            // AI Chat Functionality
            const chatInput = document.querySelector('.input-group input');
            const chatSendBtn = document.querySelector('.input-group button');
            const chatContainer = document.querySelector('.ai-chat-container .mb-4');
            
            if(chatSendBtn) {
                chatSendBtn.addEventListener('click', sendMessage);
                chatInput.addEventListener('keypress', function(e) {
                    if(e.key === 'Enter') {
                        sendMessage();
                    }
                });
            }
            
            function sendMessage() {
                const message = chatInput.value.trim();
                if(message) {
                    // Add user message
                    const userMessage = document.createElement('div');
                    userMessage.className = 'user-message';
                    userMessage.innerHTML = `
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user text-success me-2"></i>
                            <strong>You</strong>
                        </div>
                        <p class="mb-0">${message}</p>
                    `;
                    chatContainer.appendChild(userMessage);
                    
                    // Clear input
                    chatInput.value = '';
                    
                    // Scroll to bottom
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                    
                    // Simulate AI response after delay
                    setTimeout(() => {
                        const aiResponse = getAIResponse(message);
                        const aiMessage = document.createElement('div');
                        aiMessage.className = 'ai-message';
                        aiMessage.innerHTML = `
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-robot text-primary me-2"></i>
                                <strong>Legal AI Assistant</strong>
                                <span class="badge bg-primary ms-2">AI</span>
                            </div>
                            <p class="mb-0">${aiResponse}</p>
                        `;
                        chatContainer.appendChild(aiMessage);
                        
                        // Scroll to bottom
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }, 1000);
                }
            }
            
            function getAIResponse(question) {
                const responses = [
                    "I understand your question about legal matters. Based on my analysis, I recommend consulting with a legal professional for specific advice tailored to your situation.",
                    "That's an excellent question! Legal procedures can vary based on jurisdiction and specific circumstances. I suggest reviewing relevant statutes and case laws.",
                    "For accurate legal advice, please provide more details about your specific situation. This will help me give you a more precise response.",
                    "Based on general legal principles, I can provide guidance. However, remember that this is not legal advice and you should consult with a qualified attorney.",
                    "I've analyzed similar cases and legal precedents. The common approach involves following established legal procedures and documentation requirements."
                ];
                
                return responses[Math.floor(Math.random() * responses.length)];
            }
            
            // Quick question buttons
            document.querySelectorAll('.btn-outline-primary.btn-sm').forEach(btn => {
                btn.addEventListener('click', function() {
                    chatInput.value = this.textContent;
                    sendMessage();
                });
            });
        });
    </script>
</body>
</html>