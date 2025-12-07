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
    
    // Get recent case laws if available
    try {
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM case_laws WHERE is_published = 1 ORDER BY decision_date DESC LIMIT 6");
        $case_laws = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get popular statutes
        $stmt = $conn->query("SELECT * FROM statutes WHERE is_active = 1 ORDER BY importance DESC LIMIT 4");
        $statutes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get research topics
        $stmt = $conn->query("SELECT * FROM research_topics WHERE is_active = 1 ORDER BY views DESC LIMIT 6");
        $research_topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $case_laws = [];
        $statutes = [];
        $research_topics = [];
    }
   
} catch (Exception $e) {
    $case_laws = [];
    $statutes = [];
    $research_topics = [];
    
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
    <title>Legal Research - JusticeFlow</title>
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
        
        /* Research Cards */
        .research-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            transition: all 0.3s;
            background: white;
        }
        
        .research-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .case-badge {
            background: var(--accent-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .statute-badge {
            background: var(--secondary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .research-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        /* Search Box */
        .search-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 20px 30px;
            border-radius: 15px;
            border: 2px solid #e2e8f0;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(45, 116, 218, 0.1);
        }
        
        .search-box button {
            position: absolute;
            right: 10px;
            top: 10px;
            height: calc(100% - 20px);
            padding: 0 30px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            border: none;
        }
        
        /* Quick Filters */
        .filter-btn {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            padding: 8px 20px;
            border-radius: 10px;
            color: var(--dark-color);
            transition: all 0.3s;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            border-color: var(--secondary-color);
        }
        
        /* Topic Tags */
        .topic-tag {
            background: #f0f7ff;
            color: var(--secondary-color);
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin: 5px;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .topic-tag:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
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
            
            .search-container {
                padding: 20px;
                margin-top: -30px;
            }
        }
        
        /* Court Badges */
        .court-badge {
            background: #ff6b6b;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-left: 10px;
        }
        
        /* Year Badge */
        .year-badge {
            background: #4ecdc4;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-left: 10px;
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
                        <a class="nav-link active" href="research.php">
                            <i class="fas fa-search me-1"></i>Research
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
                    <h1 class="display-4 fw-bold mb-4">Legal Research Center</h1>
                    <p class="lead mb-4">Access comprehensive legal research tools, case laws, statutes, and legal analysis powered by AI. Stay updated with the latest legal developments.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#search" class="btn btn-light btn-lg">
                            <i class="fas fa-search me-2"></i>Start Research
                        </a>
                        <a href="#case-laws" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-gavel me-2"></i>Browse Case Laws
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-search-plus fa-4x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Search Section -->
    <section id="search" class="py-5">
        <div class="container">
            <div class="search-container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-3">
                        <i class="fas fa-search text-primary me-2"></i>
                        Advanced Legal Research
                    </h2>
                    <p class="lead text-muted">Search through millions of case laws, statutes, and legal documents</p>
                </div>
                
                <div class="search-box mb-4">
                    <input type="text" class="form-control" placeholder="Search case laws, statutes, legal principles...">
                    <button class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
                
                <div class="text-center mb-4">
                    <p class="text-muted mb-3">Quick filters:</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button class="filter-btn active">All</button>
                        <button class="filter-btn">Case Laws</button>
                        <button class="filter-btn">Statutes</button>
                        <button class="filter-btn">Regulations</button>
                        <button class="filter-btn">Legal Articles</button>
                        <button class="filter-btn">International Laws</button>
                    </div>
                </div>
                
                <div class="text-center">
                    <p class="text-muted">Popular topics:</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="#" class="topic-tag">Contract Law</a>
                        <a href="#" class="topic-tag">Criminal Law</a>
                        <a href="#" class="topic-tag">Family Law</a>
                        <a href="#" class="topic-tag">Constitutional Law</a>
                        <a href="#" class="topic-tag">Property Law</a>
                        <a href="#" class="topic-tag">Intellectual Property</a>
                        <a href="#" class="topic-tag">Corporate Law</a>
                        <a href="#" class="topic-tag">Human Rights</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Case Laws Section -->
    <section id="case-laws" class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-3">Recent Case Laws</h2>
                    <p class="text-muted">Latest court decisions and judgments</p>
                </div>
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-2"></i>View All
                </a>
            </div>
            
            <?php if (empty($case_laws)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-gavel fa-4x text-muted mb-4"></i>
                    <h3>No Case Laws Available</h3>
                    <p class="text-muted">Case laws will be added soon.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($case_laws as $case): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="research-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="case-badge">Case Law</span>
                                    <?php if (isset($case['court'])): ?>
                                        <span class="court-badge"><?php echo htmlspecialchars($case['court']); ?></span>
                                    <?php endif; ?>
                                    <?php if (isset($case['decision_date'])): ?>
                                        <span class="year-badge"><?php echo date('Y', strtotime($case['decision_date'])); ?></span>
                                    <?php endif; ?>
                                </div>
                                <i class="fas fa-bookmark text-primary"></i>
                            </div>
                            <h4 class="h5 mb-3"><?php echo htmlspecialchars($case['title']); ?></h4>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars(substr($case['summary'] ?? 'No summary available', 0, 150)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo isset($case['decision_date']) ? date('M d, Y', strtotime($case['decision_date'])) : 'Date not available'; ?>
                                </span>
                                <span class="text-muted small">
                                    <i class="fas fa-eye me-1"></i>
                                    <?php echo $case['views'] ?? 0; ?> views
                                </span>
                            </div>
                            <div class="mt-3">
                                <a href="case-details.php?id=<?php echo $case['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-file-alt me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Statutes Section -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold mb-3">Important Statutes</h2>
                    <p class="text-muted">Key legislation and statutory provisions</p>
                </div>
                <a href="#" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i>Browse All
                </a>
            </div>
            
            <?php if (empty($statutes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-scale-balanced fa-4x text-muted mb-4"></i>
                    <h3>No Statutes Available</h3>
                    <p class="text-muted">Statutes will be added soon.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($statutes as $statute): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="research-card text-center">
                            <div class="research-icon mx-auto">
                                <i class="fas fa-scale-balanced"></i>
                            </div>
                            <span class="statute-badge">Statute</span>
                            <h4 class="h5 mb-3"><?php echo htmlspecialchars($statute['title']); ?></h4>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($statute['description'] ?? '', 0, 100)) . '...'; ?></p>
                            <div class="mt-3">
                                <a href="statute-details.php?id=<?php echo $statute['id']; ?>" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-2"></i>Read More
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Research Topics Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Research Topics</h2>
                <p class="lead text-muted">Explore in-depth analysis of legal topics</p>
            </div>
            
            <?php if (empty($research_topics)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-4"></i>
                    <h3>No Research Topics Available</h3>
                    <p class="text-muted">Research topics will be added soon.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($research_topics as $topic): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="research-card">
                            <div class="d-flex align-items-start mb-3">
                                <div class="research-icon me-3">
                                    <?php 
                                    $icon = 'fa-file-alt';
                                    switch($topic['category'] ?? 'general') {
                                        case 'criminal': $icon = 'fa-gavel'; break;
                                        case 'civil': $icon = 'fa-scale-balanced'; break;
                                        case 'corporate': $icon = 'fa-building'; break;
                                        case 'constitutional': $icon = 'fa-landmark'; break;
                                        case 'international': $icon = 'fa-globe'; break;
                                        default: $icon = 'fa-file-alt';
                                    }
                                    ?>
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <h4 class="h5 mb-2"><?php echo htmlspecialchars($topic['title']); ?></h4>
                                    <p class="text-muted small mb-0">By <?php echo htmlspecialchars($topic['author'] ?? 'JusticeFlow Research'); ?></p>
                                </div>
                            </div>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars(substr($topic['summary'] ?? '', 0, 120)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo isset($topic['read_time']) ? $topic['read_time'] . ' min read' : '5 min read'; ?>
                                </span>
                                <span class="text-muted small">
                                    <i class="fas fa-eye me-1"></i>
                                    <?php echo $topic['views'] ?? 0; ?>
                                </span>
                            </div>
                            <div class="mt-3">
                                <a href="research-topic.php?id=<?php echo $topic['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-book-open me-2"></i>Read Research
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Research Tools Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Research Tools</h2>
                <p class="lead text-muted">Powerful tools to enhance your legal research</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-book fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Case Finder</h4>
                        <p class="text-muted small">Find relevant case laws based on your legal issues</p>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-2">Use Tool</a>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-chart-line fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Legal Trends</h4>
                        <p class="text-muted small">Analyze legal trends and patterns over time</p>
                        <a href="#" class="btn btn-outline-success btn-sm mt-2">Explore</a>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-balance-scale fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Statute Compare</h4>
                        <p class="text-muted small">Compare statutes across different jurisdictions</p>
                        <a href="#" class="btn btn-outline-warning btn-sm mt-2">Compare</a>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-quote-right fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Citation Generator</h4>
                        <p class="text-muted small">Generate proper legal citations automatically</p>
                        <a href="#" class="btn btn-outline-info btn-sm mt-2">Generate</a>
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
                    <h2 class="display-5 fw-bold text-white mb-3">Need Professional Research?</h2>
                    <p class="lead text-white mb-0">Our legal experts can conduct comprehensive research for your specific case.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="contact.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-user-tie me-2"></i>Hire Researcher
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
                        <li><a href="ask-ai.php">AI Assistant</a></li>
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
            
            // Filter buttons functionality
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Here you would typically filter the search results
                    const filter = this.textContent;
                    console.log('Filtering by:', filter);
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-box input');
            const searchBtn = document.querySelector('.search-box button');
            
            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') {
                    performSearch();
                }
            });
            
            function performSearch() {
                const query = searchInput.value.trim();
                if(query) {
                    // Here you would typically make an AJAX call to search
                    console.log('Searching for:', query);
                    alert('Search functionality would be implemented here. Query: ' + query);
                }
            }
            
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