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
    
    // Get dispute resolution methods if available
    try {
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM dispute_methods WHERE is_active = 1 ORDER BY display_order ASC");
        $dispute_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get success stories
        $stmt = $conn->query("SELECT * FROM success_stories WHERE category = 'dispute' AND is_featured = 1 ORDER BY created_at DESC LIMIT 3");
        $success_stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get dispute statistics
        $stmt = $conn->query("SELECT * FROM dispute_statistics ORDER BY id DESC LIMIT 1");
        $dispute_stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $dispute_methods = [];
        $success_stories = [];
        $dispute_stats = [];
    }
   
} catch (Exception $e) {
    $dispute_methods = [];
    $success_stories = [];
    $dispute_stats = [];
    
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

// Default dispute methods if database empty
if (empty($dispute_methods)) {
    $dispute_methods = [
        [
            'id' => 1,
            'title' => 'Online Mediation',
            'description' => 'Facilitated negotiation through our secure online platform with professional mediators.',
            'icon' => 'fa-handshake',
            'duration' => '7-14 days',
            'cost' => 'Affordable',
            'success_rate' => '85%'
        ],
        [
            'id' => 2,
            'title' => 'Arbitration',
            'description' => 'Binding resolution by neutral arbitrators with enforceable awards.',
            'icon' => 'fa-scale-balanced',
            'duration' => '30-60 days',
            'cost' => 'Moderate',
            'success_rate' => '92%'
        ],
        [
            'id' => 3,
            'title' => 'Conciliation',
            'description' => 'Informal process where a conciliator helps parties reach a settlement.',
            'icon' => 'fa-users',
            'duration' => '10-20 days',
            'cost' => 'Low',
            'success_rate' => '78%'
        ],
        [
            'id' => 4,
            'title' => 'Negotiation',
            'description' => 'Direct negotiation between parties with our communication tools.',
            'icon' => 'fa-comments',
            'duration' => '5-10 days',
            'cost' => 'Free',
            'success_rate' => '70%'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispute Resolution - JusticeFlow</title>
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
        
        /* Dispute Cards */
        .dispute-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .dispute-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .dispute-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 15px 15px 0 0;
        }
        
        .dispute-icon {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        /* Process Steps */
        .process-step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            flex-shrink: 0;
            margin-right: 20px;
        }
        
        .step-content {
            flex: 1;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        
        /* Success Stories */
        .story-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            transition: all 0.3s;
            background: white;
        }
        
        .story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: var(--accent-color);
        }
        
        .client-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        /* Cost Badge */
        .cost-badge {
            background: #4ecdc4;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 5px;
        }
        
        .duration-badge {
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 5px;
        }
        
        .success-badge {
            background: var(--accent-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 5px;
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
            
            .process-step {
                flex-direction: column;
                text-align: center;
            }
            
            .step-number {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
        
        /* Dispute Form */
        .dispute-form-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        
        .form-step {
            display: none;
            animation: fadeIn 0.5s;
        }
        
        .form-step.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Progress Bar */
        .progress-container {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 4px;
            transition: width 0.3s;
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
                        <a class="nav-link" href="index.php">
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
                        <a class="nav-link" href="documents.php">
                            <i class="fas fa-file-alt me-1"></i>Documents
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="research.php">
                            <i class="fas fa-search me-1"></i>Research
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link active" href="disputes.php">
                            <i class="fas fa-handshake me-1"></i>Disputes
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
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Online Dispute Resolution</h1>
                    <p class="lead mb-4">Resolve disputes quickly, affordably, and effectively through our online platform. Avoid costly litigation with professional mediation and arbitration services.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#methods" class="btn btn-light btn-lg">
                            <i class="fas fa-handshake me-2"></i>Explore Methods
                        </a>
                        <a href="#start-dispute" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play-circle me-2"></i>Start Resolution
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-peace fa-4x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Dispute Statistics -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $dispute_stats['success_rate'] ?? '85'; ?>%</div>
                        <p class="text-muted mb-0">Success Rate</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $dispute_stats['avg_days'] ?? '24'; ?> days</div>
                        <p class="text-muted mb-0">Average Resolution Time</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $dispute_stats['savings'] ?? '70'; ?>%</div>
                        <p class="text-muted mb-0">Cost Savings vs Litigation</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $dispute_stats['cases_resolved'] ?? '1500'; ?>+</div>
                        <p class="text-muted mb-0">Cases Resolved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Resolution Methods -->
    <section id="methods" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Dispute Resolution Methods</h2>
                <p class="lead text-muted">Choose the best method for your specific dispute</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($dispute_methods as $method): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="dispute-card">
                        <div class="dispute-icon">
                            <i class="fas <?php echo htmlspecialchars($method['icon'] ?? 'fa-handshake'); ?>"></i>
                        </div>
                        <h3 class="h4 mb-3"><?php echo htmlspecialchars($method['title']); ?></h3>
                        <p class="text-muted mb-4"><?php echo htmlspecialchars($method['description']); ?></p>
                        
                        <div class="mt-4">
                            <span class="cost-badge">
                                <i class="fas fa-dollar-sign me-1"></i>
                                <?php echo htmlspecialchars($method['cost']); ?>
                            </span>
                            <span class="duration-badge">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo htmlspecialchars($method['duration']); ?>
                            </span>
                            <span class="success-badge">
                                <i class="fas fa-chart-line me-1"></i>
                                <?php echo htmlspecialchars($method['success_rate']); ?>% Success
                            </span>
                        </div>
                        
                        <div class="mt-4">
                            <a href="dispute-method.php?id=<?php echo $method['id']; ?>" class="btn btn-primary w-100">
                                <i class="fas fa-info-circle me-2"></i>Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- How It Works -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">How It Works</h2>
                <p class="lead text-muted">Simple 5-step process to resolve your dispute</p>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4 class="h5 mb-2">Submit Your Dispute</h4>
                            <p class="text-muted mb-0">Fill out our simple online form with details of your dispute. Upload relevant documents and evidence.</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4 class="h5 mb-2">Choose Resolution Method</h4>
                            <p class="text-muted mb-0">Select from mediation, arbitration, or other methods based on your needs and the nature of the dispute.</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4 class="h5 mb-2">Select Neutral Professional</h4>
                            <p class="text-muted mb-0">Choose from our panel of certified mediators, arbitrators, or legal experts.</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h4 class="h5 mb-2">Online Resolution Process</h4>
                            <p class="text-muted mb-0">Participate in virtual sessions, submit evidence, and communicate through our secure platform.</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h4 class="h5 mb-2">Get Resolution & Agreement</h4>
                            <p class="text-muted mb-0">Receive a legally binding agreement or award that resolves your dispute.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Start Dispute Form -->
    <section id="start-dispute" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="dispute-form-container">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-3">Start Resolution Process</h2>
                            <p class="text-muted">Begin your dispute resolution journey in minutes</p>
                        </div>
                        
                        <div class="progress-container">
                            <div class="progress-bar" id="form-progress" style="width: 25%"></div>
                        </div>
                        
                        <!-- Step 1: Basic Information -->
                        <div class="form-step active" id="step-1">
                            <h4 class="h5 mb-4">Basic Information</h4>
                            <div class="mb-3">
                                <label class="form-label">Dispute Type</label>
                                <select class="form-select">
                                    <option selected>Select dispute type</option>
                                    <option value="contract">Contract Dispute</option>
                                    <option value="property">Property Dispute</option>
                                    <option value="business">Business Dispute</option>
                                    <option value="consumer">Consumer Dispute</option>
                                    <option value="employment">Employment Dispute</option>
                                    <option value="family">Family Dispute</option>
                                    <option value="neighbor">Neighbor Dispute</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Brief Description</label>
                                <textarea class="form-control" rows="3" placeholder="Describe your dispute briefly"></textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-primary" disabled>Previous</button>
                                <button class="btn btn-primary" onclick="nextStep(2)">Next: Choose Method</button>
                            </div>
                        </div>
                        
                        <!-- Step 2: Choose Method -->
                        <div class="form-step" id="step-2">
                            <h4 class="h5 mb-4">Choose Resolution Method</h4>
                            <div class="mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="method" id="mediation">
                                    <label class="form-check-label" for="mediation">
                                        <strong>Mediation</strong>
                                        <small class="text-muted d-block">Facilitated negotiation with a neutral mediator</small>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="method" id="arbitration">
                                    <label class="form-check-label" for="arbitration">
                                        <strong>Arbitration</strong>
                                        <small class="text-muted d-block">Binding decision by an arbitrator</small>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="method" id="conciliation">
                                    <label class="form-check-label" for="conciliation">
                                        <strong>Conciliation</strong>
                                        <small class="text-muted d-block">Informal settlement assistance</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="method" id="negotiation">
                                    <label class="form-check-label" for="negotiation">
                                        <strong>Direct Negotiation</strong>
                                        <small class="text-muted d-block">Private negotiation with our tools</small>
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-primary" onclick="prevStep(1)">Previous</button>
                                <button class="btn btn-primary" onclick="nextStep(3)">Next: Upload Documents</button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Upload Documents -->
                        <div class="form-step" id="step-3">
                            <h4 class="h5 mb-4">Upload Relevant Documents</h4>
                            <div class="mb-4">
                                <p class="text-muted mb-3">Upload any documents related to your dispute (contracts, emails, evidence, etc.)</p>
                                <div class="border rounded p-4 text-center">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <p class="mb-3">Drag & drop files here or click to browse</p>
                                    <input type="file" class="form-control" multiple>
                                    <small class="text-muted d-block mt-2">Max file size: 10MB each. Supported: PDF, DOC, JPG, PNG</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-primary" onclick="prevStep(2)">Previous</button>
                                <button class="btn btn-primary" onclick="nextStep(4)">Next: Final Details</button>
                            </div>
                        </div>
                        
                        <!-- Step 4: Final Details -->
                        <div class="form-step" id="step-4">
                            <h4 class="h5 mb-4">Final Details & Submission</h4>
                            <div class="mb-4">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Your dispute resolution request is ready to submit!
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="terms">
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="consent">
                                    <label class="form-check-label" for="consent">
                                        I consent to electronic communications and online dispute resolution
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-primary" onclick="prevStep(3)">Previous</button>
                                <button class="btn btn-primary" onclick="submitForm()">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Success Stories -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Success Stories</h2>
                <p class="lead text-muted">See how others have resolved their disputes</p>
            </div>
            
            <div class="row g-4">
                <?php if (empty($success_stories)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                        <h3>No Success Stories Yet</h3>
                        <p class="text-muted">Be the first to share your success story!</p>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach ($success_stories as $story): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="story-card">
                        <div class="d-flex align-items-center mb-4">
                            <div class="client-avatar">
                                <?php echo substr($story['client_name'] ?? 'C', 0, 1); ?>
                            </div>
                            <div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($story['client_name']); ?></h5>
                                <p class="text-muted small mb-0"><?php echo htmlspecialchars($story['dispute_type']); ?> Case</p>
                            </div>
                        </div>
                        <p class="text-muted mb-4">"<?php echo htmlspecialchars(substr($story['testimonial'], 0, 150)) . '...'; ?>"</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo isset($story['resolution_date']) ? date('M Y', strtotime($story['resolution_date'])) : 'Recent'; ?>
                            </span>
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Resolved
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold text-white mb-3">Ready to Resolve Your Dispute?</h2>
                    <p class="lead text-white mb-0">Start the process today and save time, money, and stress.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="#start-dispute" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-play-circle me-2"></i>Start Now
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
                        <li class="mb-2"><a href="features.php">Features</a></li>
                        <li class="mb-2"><a href="works.php">How It Works</a></li>
                        <li class="mb-2"><a href="documents.php">Documents</a></li>
                        <li class="mb-2"><a href="research.php">Research</a></li>
                        <li><a href="disputes.php">Dispute Resolution</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="h6 mb-4 text-white">Company</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="about.php">About Us</a></li>
                        <li class="mb-2"><a href="#">Careers</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Press</a></li>
                        <li><a href="contact.php">Contact</a></li>
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
        // Form step navigation
        let currentStep = 1;
        const totalSteps = 4;
        
        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(el => {
                el.classList.remove('active');
            });
            
            // Show current step
            document.getElementById(`step-${step}`).classList.add('active');
            
            // Update progress bar
            const progress = (step / totalSteps) * 100;
            document.getElementById('form-progress').style.width = `${progress}%`;
            
            currentStep = step;
        }
        
        function nextStep(step) {
            // Validate current step before proceeding
            if (validateStep(currentStep)) {
                showStep(step);
            }
        }
        
        function prevStep(step) {
            showStep(step);
        }
        
        function validateStep(step) {
            // Simple validation for each step
            switch(step) {
                case 1:
                    const disputeType = document.querySelector('select').value;
                    const description = document.querySelector('textarea').value;
                    if (disputeType === 'Select dispute type' || description.trim() === '') {
                        alert('Please select a dispute type and provide a description.');
                        return false;
                    }
                    break;
                    
                case 2:
                    const methodSelected = document.querySelector('input[name="method"]:checked');
                    if (!methodSelected) {
                        alert('Please select a resolution method.');
                        return false;
                    }
                    break;
                    
                case 4:
                    const terms = document.getElementById('terms');
                    const consent = document.getElementById('consent');
                    if (!terms.checked || !consent.checked) {
                        alert('Please agree to the terms and provide consent.');
                        return false;
                    }
                    break;
            }
            return true;
        }
        
        function submitForm() {
            if (validateStep(4)) {
                // Here you would typically submit the form via AJAX
                alert('Your dispute resolution request has been submitted! Our team will contact you within 24 hours.');
                
                // Reset form (optional)
                setTimeout(() => {
                    showStep(1);
                    document.querySelector('form').reset();
                }, 2000);
            }
        }
        
        // Smooth scrolling for anchor links
        document.addEventListener('DOMContentLoaded', function() {
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
            
            // Make current page active in navbar
            const currentPage = window.location.pathname.split('/').pop();
            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                if(link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>