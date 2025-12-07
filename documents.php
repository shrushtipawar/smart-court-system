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
    
    // Get documents from database if available
    try {
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM documents WHERE is_public = 1 ORDER BY created_at DESC");
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group by category
        $categories = [];
        foreach ($documents as $doc) {
            $category = $doc['category'] ?? 'general';
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = $doc;
        }
    } catch (Exception $e) {
        $categories = [];
    }
   
} catch (Exception $e) {
    $categories = [];
    
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
    <title>Legal Documents - JusticeFlow</title>
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
        
        /* Simplified Navbar (Matching index.php) */
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
        
        /* Document Cards */
        .document-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            transition: all 0.3s;
            background: white;
        }
        
        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .document-icon {
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
        
        .file-size {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .download-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 116, 218, 0.3);
        }
        
        .category-badge {
            background: var(--accent-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: inline-block;
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
    </style>
</head>
<body>
    <!-- Simplified Navigation Bar (Matching index.php) -->
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
                        <a class="nav-link active" href="documents.php">
                            <i class="fas fa-file-alt me-1"></i>Documents
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
                    <h1 class="display-4 fw-bold mb-4">Legal Documents & Templates</h1>
                    <p class="lead mb-4">Access comprehensive legal documents, templates, and resources for your legal needs. All documents are professionally drafted and regularly updated.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#documents" class="btn btn-light btn-lg">
                            <i class="fas fa-search me-2"></i>Browse Documents
                        </a>
                        <a href="#categories" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-filter me-2"></i>View Categories
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-file-contract fa-4x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Documents Section -->
    <section id="documents" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Available Documents</h2>
                <p class="lead text-muted">Browse our collection of legal documents and templates</p>
            </div>
            
            <?php if (empty($categories)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-muted mb-4"></i>
                    <h3>No Documents Available</h3>
                    <p class="text-muted">Documents will be added soon. Check back later or contact us for specific documents.</p>
                    <a href="contact.php" class="btn btn-primary mt-3">
                        <i class="fas fa-envelope me-2"></i>Request Documents
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category => $docs): ?>
                <div class="mb-5" id="category-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $category))); ?>">
                    <h3 class="h4 mb-4">
                        <span class="category-badge"><?php echo htmlspecialchars(ucfirst($category)); ?></span>
                        <?php echo htmlspecialchars(ucfirst($category)); ?> Documents
                    </h3>
                    <div class="row g-4">
                        <?php foreach ($docs as $doc): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="document-card">
                                <div class="document-icon">
                                    <?php 
                                    $ext = strtolower(pathinfo($doc['file_name'] ?? '', PATHINFO_EXTENSION));
                                    if ($ext === 'pdf') {
                                        echo '<i class="fas fa-file-pdf"></i>';
                                    } elseif (in_array($ext, ['doc', 'docx'])) {
                                        echo '<i class="fas fa-file-word"></i>';
                                    } else {
                                        echo '<i class="fas fa-file-alt"></i>';
                                    }
                                    ?>
                                </div>
                                <h4 class="h5 mb-3"><?php echo htmlspecialchars($doc['title']); ?></h4>
                                <p class="text-muted mb-3"><?php echo htmlspecialchars($doc['description'] ?? 'No description available'); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="file-size">
                                        <i class="fas fa-hdd me-1"></i>
                                        <?php echo isset($doc['file_size']) ? round($doc['file_size'] / 1024, 2) . ' KB' : 'Unknown'; ?>
                                    </span>
                                    <span class="text-muted small">
                                        <i class="fas fa-download me-1"></i>
                                        <?php echo $doc['download_count'] ?? 0; ?> downloads
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <a href="download.php?id=<?php echo $doc['id']; ?>" class="btn download-btn w-100">
                                        <i class="fas fa-download me-2"></i>Download Document
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Categories Section -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Document Categories</h2>
                <p class="lead text-muted">Find documents by category</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-gavel fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Legal Forms</h4>
                        <p class="text-muted small">Official legal forms and applications</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-file-contract fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Contracts</h4>
                        <p class="text-muted small">Business and personal contracts</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-balance-scale fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Court Documents</h4>
                        <p class="text-muted small">Court filings and legal proceedings</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center p-4">
                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-business-time fa-2x text-white"></i>
                        </div>
                        <h4 class="h5 mb-2">Business</h4>
                        <p class="text-muted small">Corporate and business documents</p>
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
                    <h2 class="display-5 fw-bold text-white mb-3">Need Custom Documents?</h2>
                    <p class="lead text-white mb-0">Our legal experts can draft custom documents tailored to your specific needs.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="contact.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-file-alt me-2"></i>Request Custom Draft
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
                        <li class="mb-2"><a href="ask-ai.php">Ask AI</a></li>
                        <li><a href="#">Pricing</a></li>
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