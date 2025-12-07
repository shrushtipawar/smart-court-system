<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if config exists
if (!file_exists('config/database.php') || !is_readable('config/database.php')) {
    header('Location: setup.php');
    exit();
}

require_once 'config/database.php';

// Try to create database connection
try {
    $db = new Database();
    // Try multiple ways to get connection
    if (method_exists($db, 'getConnection')) {
        $conn = $db->getConnection();
    } elseif (method_exists($db, 'connect')) {
        $conn = $db->connect();
    } elseif (isset($db->conn)) {
        // Try to access conn property
        try {
            $reflection = new ReflectionClass($db);
            $property = $reflection->getProperty('conn');
            $property->setAccessible(true);
            $conn = $property->getValue($db);
        } catch (ReflectionException $e) {
            $conn = null;
        }
    } else {
        $conn = null;
    }
} catch (Exception $e) {
    $conn = null;
}

// Now include the dynamic content functions
if (file_exists('includes/dynamic-content.php')) {
    require_once 'includes/dynamic-content.php';
} else {
    // Fallback functions if dynamic-content.php doesn't exist
    function getSiteSetting($key, $default = '') {
        return $default;
    }
    
    function getAboutContent() {
        return [];
    }
    
    function getDefaultAboutContent() {
        return [];
    }
}

// Get about content
$about_content = getAboutContent();

// Ensure $about_content is an array
if (!is_array($about_content)) {
    $about_content = getDefaultAboutContent();
}

// Safely decode JSON strings
function safelyDecodeJson($data, $key, $default = []) {
    if (!isset($data[$key])) {
        return $default;
    }
    
    if (is_string($data[$key])) {
        $decoded = json_decode($data[$key], true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }
    
    return is_array($data[$key]) ? $data[$key] : $default;
}

// Convert JSON strings to arrays for easier access
$about_content['team_members'] = safelyDecodeJson($about_content, 'team_members', []);
$about_content['milestones'] = safelyDecodeJson($about_content, 'milestones', []);
$about_content['statistics'] = safelyDecodeJson($about_content, 'statistics', []);
$about_content['values'] = safelyDecodeJson($about_content, 'values', []);
$about_content['hero'] = safelyDecodeJson($about_content, 'hero', []);
$about_content['story'] = safelyDecodeJson($about_content, 'story', []);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            color: var(--dark-color);
        }
        
        h1, h2, h3, h4, h5 {
            color: var(--primary-color);
        }
        
        .hero-about {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 150px 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .mission-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .values-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s;
        }
        
        .values-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .team-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .team-image {
            height: 250px;
            object-fit: cover;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .milestone-timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .milestone-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--secondary-color);
        }
        
        .milestone-item {
            position: relative;
            margin-bottom: 30px;
        }
        
        .milestone-item::before {
            content: '';
            position: absolute;
            left: -40px;
            top: 20px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary-color);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 116, 218, 0.3);
        }
        
        /* Modern Navbar */
        .modern-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
        }
        
        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
        }
        
        .nav-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 12px;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(45, 116, 218, 0.2);
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .nav-link {
            color: var(--dark-color);
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(45, 116, 218, 0.3);
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
        }
        
        .story-card {
            background: linear-gradient(135deg, #f8fff8 0%, #f0f7ff 100%);
            border-radius: 20px;
            padding: 40px;
            border: 2px solid var(--accent-color);
        }
        
        .story-image {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .hero-about {
                padding: 120px 0 60px;
            }
            
            .milestone-timeline {
                padding-left: 20px;
            }
            
            .milestone-item::before {
                left: -30px;
            }
            
            .nav-menu {
                flex-direction: column;
                width: 100%;
                gap: 5px;
            }
            
            .nav-link {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="modern-navbar">
        <div class="container">
            <div class="nav-content">
                <!-- Logo -->
                <a href="index.php" class="nav-logo">
                    <div class="logo-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <span class="logo-text">JusticeFlow</span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="nav-menu">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="about.php" class="nav-link active">
                        <i class="fas fa-info-circle"></i>
                        <span>About</span>
                    </a>
                    <a href="services.php" class="nav-link">
                        <i class="fas fa-handshake"></i>
                        <span>Services</span>
                    </a>
                    <a href="documents.php" class="nav-link">
                        <i class="fas fa-file-pdf"></i>
                        <span>Documents</span>
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        <?php 
                        echo !empty($about_content['hero']['title']) 
                            ? htmlspecialchars($about_content['hero']['title']) 
                            : 'About JusticeFlow'; 
                        ?>
                    </h1>
                    <p class="lead mb-4">
                        <?php 
                        echo !empty($about_content['hero']['description']) 
                            ? htmlspecialchars($about_content['hero']['description']) 
                            : 'Revolutionizing legal practice through cutting-edge technology and AI-powered solutions.'; 
                        ?>
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#our-story" class="btn btn-light btn-lg">
                            <i class="fas fa-book-open me-2"></i>Our Story
                        </a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-handshake me-2"></i>Get in Touch
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-balance-scale fa-4x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <?php 
                $defaultStats = [
                    ['value' => '1M+', 'label' => 'Active Users', 'icon' => 'fa-users'],
                    ['value' => '50+', 'label' => 'Countries', 'icon' => 'fa-globe'],
                    ['value' => '25K+', 'label' => 'Legal Cases', 'icon' => 'fa-gavel'],
                    ['value' => '95%', 'label' => 'Client Satisfaction', 'icon' => 'fa-smile']
                ];
                
                $stats = !empty($about_content['statistics']) ? $about_content['statistics'] : $defaultStats;
                
                foreach ($stats as $stat):
                    if (is_array($stat) && !empty($stat['value'])):
                ?>
                        <div class="col-md-3 col-6 mb-4">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas <?php echo htmlspecialchars($stat['icon'] ?? 'fa-chart-line'); ?>"></i>
                                </div>
                                <h3 class="display-6 fw-bold mb-2"><?php echo htmlspecialchars($stat['value']); ?></h3>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($stat['label'] ?? ''); ?></p>
                            </div>
                        </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section id="our-story" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="story-image">
                        <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                             alt="JusticeFlow Team" class="img-fluid rounded">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="story-card">
                        <h2 class="fw-bold mb-4">Our Story</h2>
                        <p class="lead mb-4">
                            <?php 
                            echo !empty($about_content['story']['description']) 
                                ? htmlspecialchars($about_content['story']['description']) 
                                : 'Founded in 2023, JusticeFlow began with a simple mission: to bridge the gap between traditional legal practices and modern technology. Our team of legal experts and tech innovators came together to create a platform that makes justice more accessible, efficient, and transparent.'; 
                            ?>
                        </p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-rocket text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Founded</h5>
                                        <p class="text-muted mb-0">2023</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Team Size</h5>
                                        <p class="text-muted mb-0">50+ Experts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-5">
        <div class="container">
            <div class="mission-card">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="text-center p-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-bullseye fa-2x text-white"></i>
                            </div>
                            <h3 class="h4 mb-3">Our Mission</h3>
                            <p class="text-muted">
                                <?php 
                                echo !empty($about_content['mission']) 
                                    ? htmlspecialchars($about_content['mission']) 
                                    : 'To revolutionize the legal industry through technology, making justice accessible, efficient, and transparent for everyone.'; 
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="text-center p-4">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-eye fa-2x text-white"></i>
                            </div>
                            <h3 class="h4 mb-3">Our Vision</h3>
                            <p class="text-muted">
                                <?php 
                                echo !empty($about_content['vision']) 
                                    ? htmlspecialchars($about_content['vision']) 
                                    : 'A world where legal services are as accessible as any other essential service, powered by technology and innovation.'; 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Our Core Values</h2>
                <p class="lead text-muted">The principles that guide everything we do</p>
            </div>
            <div class="row g-4">
                <?php 
                $defaultValues = [
                    [
                        'title' => 'Integrity',
                        'description' => 'We uphold the highest ethical standards in everything we do.',
                        'icon' => 'fa-shield-alt',
                        'color' => 'primary'
                    ],
                    [
                        'title' => 'Innovation',
                        'description' => 'Continuously pushing boundaries to create better legal solutions.',
                        'icon' => 'fa-lightbulb',
                        'color' => 'warning'
                    ],
                    [
                        'title' => 'Accessibility',
                        'description' => 'Making legal services available to everyone, everywhere.',
                        'icon' => 'fa-universal-access',
                        'color' => 'success'
                    ],
                    [
                        'title' => 'Excellence',
                        'description' => 'Striving for perfection in every aspect of our service.',
                        'icon' => 'fa-award',
                        'color' => 'danger'
                    ],
                    [
                        'title' => 'Collaboration',
                        'description' => 'Working together with legal professionals and clients.',
                        'icon' => 'fa-handshake',
                        'color' => 'info'
                    ],
                    [
                        'title' => 'Transparency',
                        'description' => 'Clear, honest communication in all our dealings.',
                        'icon' => 'fa-eye',
                        'color' => 'primary'
                    ]
                ];
                
                $values = !empty($about_content['values']) ? $about_content['values'] : $defaultValues;
                
                foreach ($values as $value):
                    if (is_array($value) && !empty($value['title'])):
                ?>
                        <div class="col-md-4 mb-4">
                            <div class="values-card">
                                <div class="text-center mb-4">
                                    <div class="bg-<?php echo htmlspecialchars($value['color'] ?? 'primary'); ?> rounded-circle d-inline-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas <?php echo htmlspecialchars($value['icon'] ?? 'fa-star'); ?> fa-2x text-white"></i>
                                    </div>
                                </div>
                                <h4 class="h5 text-center mb-3"><?php echo htmlspecialchars($value['title']); ?></h4>
                                <p class="text-muted text-center">
                                    <?php echo htmlspecialchars($value['description'] ?? ''); ?>
                                </p>
                            </div>
                        </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold text-white mb-3">Ready to Transform Your Legal Practice?</h2>
                    <p class="lead text-white mb-0">Join thousands of legal professionals who trust JusticeFlow.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="register.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-user-plus me-2"></i>Get Started Free
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-balance-scale me-2"></i>JusticeFlow
                    </h5>
                    <p>Revolutionizing legal practice through technology and innovation.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Company</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="about.php" class="text-white-50 text-decoration-none">About</a></li>
                        <li class="mb-2"><a href="services.php" class="text-white-50 text-decoration-none">Services</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="fw-bold mb-3">Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="services.php" class="text-white-50 text-decoration-none">AI Legal Research</a></li>
                        <li class="mb-2"><a href="services.php" class="text-white-50 text-decoration-none">Case Management</a></li>
                        <li class="mb-2"><a href="services.php" class="text-white-50 text-decoration-none">Document Automation</a></li>
                        <li class="mb-2"><a href="services.php" class="text-white-50 text-decoration-none">Dispute Resolution</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5 class="fw-bold mb-3">Contact Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            123 Legal Street, San Francisco, CA
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            +1 (555) 123-4567
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            contact@justiceflow.com
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> JusticeFlow. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="privacy.php" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="terms.php" class="text-white-50 text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Mobile menu collapse after click
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });
    </script>
</body>
</html>