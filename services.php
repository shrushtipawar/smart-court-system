<?php
session_start();

// Check if config exists
$config_path = __DIR__ . '/config/database.php';
if (!file_exists($config_path) || !is_readable($config_path)) {
    header('Location: setup.php');
    exit();
}

require_once 'config/database.php';

try {
    $db = new Database();
    
    // Try multiple ways to get connection
    if (method_exists($db, 'getConnection')) {
        $conn = $db->getConnection();
    } elseif (property_exists($db, 'conn')) {
        // Try to access conn property using reflection
        $reflection = new ReflectionClass($db);
        $property = $reflection->getProperty('conn');
        $property->setAccessible(true);
        $conn = $property->getValue($db);
    } else {
        // Try to get connection directly
        $conn = $db->conn ?? null;
    }
    
    if ($conn) {
        // Check if services table exists
        $tables = $conn->query("SHOW TABLES LIKE 'services'")->rowCount();
        
        if ($tables == 0) {
            // Create services table
            $conn->exec("
                CREATE TABLE IF NOT EXISTS services (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    icon VARCHAR(50) DEFAULT 'fa-cogs',
                    price DECIMAL(10,2),
                    price_unit VARCHAR(20) DEFAULT 'month',
                    features TEXT,
                    is_featured TINYINT(1) DEFAULT 0,
                    is_active TINYINT(1) DEFAULT 1,
                    order_index INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
            
            // Insert default services
            $defaultServices = [
                [
                    'title' => 'AI Legal Research',
                    'description' => 'Advanced AI algorithms analyze precedents, statutes, and legal documents in seconds.',
                    'icon' => 'fa-brain',
                    'price' => 99,
                    'features' => "Instant Case Analysis\nStatute Tracking\nDocument Review AI\n24/7 Availability",
                    'is_featured' => 1,
                    'is_active' => 1,
                    'order_index' => 1
                ],
                [
                    'title' => 'Case Management',
                    'description' => 'Automated scheduling, priority-based assignment, and real-time tracking of cases.',
                    'icon' => 'fa-folder-open',
                    'price' => 149,
                    'features' => "Automated Scheduling\nPriority-based Assignment\nReal-time Updates\nDocument Management",
                    'is_featured' => 0,
                    'is_active' => 1,
                    'order_index' => 2
                ],
                [
                    'title' => 'Document Automation',
                    'description' => 'Generate legal documents automatically with AI-powered templates and auto-fill forms.',
                    'icon' => 'fa-file-contract',
                    'price' => 79,
                    'features' => "Smart Templates\nAuto-fill Forms\nE-signature Integration\nVersion Control",
                    'is_featured' => 0,
                    'is_active' => 1,
                    'order_index' => 3
                ]
            ];
            
            foreach ($defaultServices as $service) {
                $stmt = $conn->prepare("
                    INSERT INTO services (title, description, icon, price, features, is_featured, is_active, order_index)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $service['title'],
                    $service['description'],
                    $service['icon'],
                    $service['price'],
                    $service['features'],
                    $service['is_featured'],
                    $service['is_active'],
                    $service['order_index']
                ]);
            }
        }
        
        // Get services
        $stmt = $conn->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_index, created_at DESC");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $featured_services = array_filter($services, function($service) {
            return isset($service['is_featured']) && $service['is_featured'] == 1;
        });
    } else {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    // Use default services if database fails
    $services = [];
    $featured_services = [];
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - JusticeFlow</title>
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
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 150px 0 100px;
            margin-top: 70px;
            position: relative;
            overflow: hidden;
        }
        
        .service-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--secondary-color);
        }
        
        .service-card.featured {
            border: 2px solid var(--accent-color);
            background: linear-gradient(135deg, #f8fff8 0%, #f0f7ff 100%);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 25px;
        }
        
        .featured-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .price-tag {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .price-unit {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .features-list li {
            padding: 5px 0;
            position: relative;
            padding-left: 25px;
        }
        
        .features-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--accent-color);
            font-weight: bold;
        }
        
        .service-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }
        
        .service-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 116, 218, 0.3);
        }
        
        .comparison-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .comparison-table th {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: center;
        }
        
        .comparison-table td {
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
        }
        
        .comparison-table .feature-cell {
            text-align: left;
            font-weight: 500;
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
        
        /* Demo notice */
        .demo-notice {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--accent-color);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            font-size: 0.9rem;
            max-width: 300px;
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
                    <a href="about.php" class="nav-link">
                        <i class="fas fa-info-circle"></i>
                        <span>About</span>
                    </a>
                    <a href="services.php" class="nav-link active">
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
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Comprehensive Legal Services</h1>
                    <p class="lead mb-4">From AI-powered research to dispute resolution, we offer end-to-end legal technology solutions for individuals and businesses.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#services" class="btn btn-light btn-lg">
                            <i class="fas fa-list me-2"></i>View Services
                        </a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-calendar me-2"></i>Book Consultation
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-handshake fa-4x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Database Error Notice -->
    <?php if (isset($db_error)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning">
            <i class="fas fa-database me-2"></i>
            <strong>Database Notice:</strong> Using default services. <?php echo htmlspecialchars($db_error); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Services Grid -->
    <section id="services" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Our Legal Services</h2>
                <p class="lead text-muted">Choose from our range of AI-powered legal solutions</p>
            </div>
            
            <?php if (empty($services)): ?>
                <div class="row">
                    <!-- Default Services -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card featured">
                            <span class="featured-badge">Most Popular</span>
                            <div class="service-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <h3 class="h4 mb-3">AI Legal Research</h3>
                            <p class="text-muted mb-4">Advanced AI algorithms analyze precedents, statutes, and legal documents in seconds.</p>
                            <ul class="features-list">
                                <li>Instant Case Analysis</li>
                                <li>Statute Tracking</li>
                                <li>Document Review AI</li>
                                <li>24/7 Availability</li>
                            </ul>
                            <div class="mt-4">
                                <div class="price-tag">$99<span class="price-unit">/month</span></div>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=AI Legal Research'">Get Started</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h3 class="h4 mb-3">Case Management</h3>
                            <p class="text-muted mb-4">Automated scheduling, priority-based assignment, and real-time tracking of cases.</p>
                            <ul class="features-list">
                                <li>Automated Scheduling</li>
                                <li>Priority-based Assignment</li>
                                <li>Real-time Updates</li>
                                <li>Document Management</li>
                            </ul>
                            <div class="mt-4">
                                <div class="price-tag">$149<span class="price-unit">/month</span></div>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=Case Management'">Get Started</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h3 class="h4 mb-3">Document Automation</h3>
                            <p class="text-muted mb-4">Generate legal documents automatically with AI-powered templates and auto-fill forms.</p>
                            <ul class="features-list">
                                <li>Smart Templates</li>
                                <li>Auto-fill Forms</li>
                                <li>E-signature Integration</li>
                                <li>Version Control</li>
                            </ul>
                            <div class="mt-4">
                                <div class="price-tag">$79<span class="price-unit">/month</span></div>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=Document Automation'">Get Started</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h3 class="h4 mb-3">Dispute Resolution</h3>
                            <p class="text-muted mb-4">Online mediation and arbitration services with AI-powered settlement predictions.</p>
                            <ul class="features-list">
                                <li>Online Mediation</li>
                                <li>Settlement Prediction</li>
                                <li>Virtual Hearings</li>
                                <li>Case Resolution Tracking</li>
                            </ul>
                            <div class="mt-4">
                                <div class="price-tag">$199<span class="price-unit">/month</span></div>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=Dispute Resolution'">Get Started</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3 class="h4 mb-3">Compliance Monitoring</h3>
                            <p class="text-muted mb-4">Real-time compliance tracking and regulatory change alerts for businesses.</p>
                            <ul class="features-list">
                                <li>Regulatory Alerts</li>
                                <li>Compliance Reports</li>
                                <li>Audit Trail</li>
                                <li>Risk Assessment</li>
                            </ul>
                            <div class="mt-4">
                                <div class="price-tag">$249<span class="price-unit">/month</span></div>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=Compliance Monitoring'">Get Started</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h3 class="h4 mb-3">Legal Analytics</h3>
                            <p class="text-muted mb-4">Predictive analytics for case outcomes, judge behavior, and settlement amounts.</p>
                            <ul class="features-list">
                                <li>Case Outcome Prediction</li>
                                <li>Judge Behavior Analysis</li>
                                <li>Settlement Amount Estimates</li>
                                <li>Trend Analysis</li>
                            </ul>
                            <div class="mt-4">
                                <div class="price-tag">$299<span class="price-unit">/month</span></div>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=Legal Analytics'">Get Started</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($services as $service): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card <?php echo isset($service['is_featured']) && $service['is_featured'] ? 'featured' : ''; ?>">
                            <?php if (isset($service['is_featured']) && $service['is_featured']): ?>
                                <span class="featured-badge">Featured</span>
                            <?php endif; ?>
                            
                            <div class="service-icon">
                                <i class="fas <?php echo htmlspecialchars($service['icon'] ?? 'fa-cogs'); ?>"></i>
                            </div>
                            
                            <h3 class="h4 mb-3"><?php echo htmlspecialchars($service['title']); ?></h3>
                            <p class="text-muted mb-4"><?php echo htmlspecialchars($service['description']); ?></p>
                            
                            <?php if (!empty($service['features'])): ?>
                                <ul class="features-list">
                                    <?php 
                                    $features = explode("\n", $service['features']);
                                    foreach ($features as $feature):
                                        if (trim($feature)): ?>
                                            <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                                    <?php endif; endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <?php if (!empty($service['price'])): ?>
                                    <div class="price-tag">$<?php echo $service['price']; ?><span class="price-unit">/<?php echo $service['price_unit'] ?? 'month'; ?></span></div>
                                <?php endif; ?>
                                <button class="service-btn mt-3" onclick="window.location.href='contact.php?service=<?php echo urlencode($service['title']); ?>'">
                                    Get Started
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Service Comparison -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Service Comparison</h2>
                <p class="lead text-muted">Choose the plan that fits your needs</p>
            </div>
            
            <div class="comparison-table">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th width="30%">Features</th>
                            <th width="23%">Basic</th>
                            <th width="23%">Professional</th>
                            <th width="23%">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="feature-cell">AI Legal Research</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-cell">Case Management</td>
                            <td>5 cases</td>
                            <td>Unlimited</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td class="feature-cell">Document Automation</td>
                            <td>10 documents/month</td>
                            <td>50 documents/month</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td class="feature-cell">Priority Support</td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-cell">Custom AI Training</td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-cell">Monthly Price</td>
                            <td><strong>$99</strong></td>
                            <td><strong>$299</strong></td>
                            <td><strong>Custom</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-cell"></td>
                            <td><button class="btn btn-outline-primary w-100" onclick="window.location.href='contact.php?plan=Basic'">Choose Basic</button></td>
                            <td><button class="btn btn-primary w-100" onclick="window.location.href='contact.php?plan=Professional'">Choose Pro</button></td>
                            <td><button class="btn btn-success w-100" onclick="window.location.href='contact.php?plan=Enterprise'">Contact Sales</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold text-white mb-3">Not Sure Which Service You Need?</h2>
                    <p class="lead text-white mb-0">Schedule a free consultation with our legal tech experts.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="contact.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-calendar-check me-2"></i>Book Free Consultation
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h4>JusticeFlow</h4>
                    <p>Revolutionizing legal practice through technology and innovation.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>&copy; <?php echo date('Y'); ?> JusticeFlow. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Demo Notice -->
    <div class="demo-notice">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Services Demo:</strong> Click "Get Started" to contact us about any service
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
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
    </script>
</body>
</html>