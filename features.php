<?php
session_start();

// Features data
$features = [
    [
        'category' => 'AI Legal Research',
        'icon' => 'fas fa-brain',
        'color' => 'primary',
        'features' => [
            ['name' => 'Instant Case Analysis', 'desc' => 'AI analyzes legal documents in seconds'],
            ['name' => 'Precedent Matching', 'desc' => 'Find relevant case laws instantly'],
            ['name' => 'Statute Tracking', 'desc' => 'Stay updated with legal amendments'],
            ['name' => 'Document Review AI', 'desc' => 'Automated contract and document review']
        ],
        'stats' => ['92% Accuracy', '5x Faster', '24/7 Available']
    ],
    [
        'category' => 'Case Management',
        'icon' => 'fas fa-folder-open',
        'color' => 'success',
        'features' => [
            ['name' => 'Automated Scheduling', 'desc' => 'Smart calendar for hearings and deadlines'],
            ['name' => 'Priority-based Assignment', 'desc' => 'Intelligent case prioritization'],
            ['name' => 'Real-time Updates', 'desc' => 'Instant notifications and status tracking'],
            ['name' => 'Document Management', 'desc' => 'Secure document storage and sharing']
        ],
        'stats' => ['60% Time Saved', '100% Organized', 'Real-time Sync']
    ],
    [
        'category' => 'Document Automation',
        'icon' => 'fas fa-file-contract',
        'color' => 'warning',
        'features' => [
            ['name' => 'Smart Templates', 'desc' => '1000+ ready-to-use legal templates'],
            ['name' => 'Auto-fill Forms', 'desc' => 'Automatically populate legal forms'],
            ['name' => 'E-signature Integration', 'desc' => 'Secure digital signing'],
            ['name' => 'Version Control', 'desc' => 'Track document changes and history']
        ],
        'stats' => ['200+ Templates', '80% Faster', 'Secure']
    ],
    [
        'category' => 'Dispute Resolution',
        'icon' => 'fas fa-handshake',
        'color' => 'info',
        'features' => [
            ['name' => 'Virtual Mediation', 'desc' => 'Online dispute resolution platform'],
            ['name' => 'Arbitration Scheduling', 'desc' => 'Automated arbitration management'],
            ['name' => 'Settlement Tools', 'desc' => 'Digital settlement agreements'],
            ['name' => 'Secure Sharing', 'desc' => 'Encrypted document exchange']
        ],
        'stats' => ['70% Success Rate', '50% Cost Saved', 'Online']
    ],
    [
        'category' => 'Analytics & Insights',
        'icon' => 'fas fa-chart-line',
        'color' => 'danger',
        'features' => [
            ['name' => 'Case Prediction', 'desc' => 'AI-powered case outcome prediction'],
            ['name' => 'Performance Metrics', 'desc' => 'Track lawyer and case performance'],
            ['name' => 'Trend Analysis', 'desc' => 'Identify legal trends and patterns'],
            ['name' => 'Custom Reports', 'desc' => 'Generate detailed analytics reports']
        ],
        'stats' => ['95% Accuracy', 'Data-driven', 'Real-time']
    ],
    [
        'category' => 'Security & Compliance',
        'icon' => 'fas fa-shield-alt',
        'color' => 'purple',
        'features' => [
            ['name' => 'End-to-End Encryption', 'desc' => 'Military-grade data protection'],
            ['name' => 'Data Privacy', 'desc' => 'GDPR and HIPAA compliant'],
            ['name' => 'Audit Trail', 'desc' => 'Complete activity logging'],
            ['name' => 'Regulatory Compliance', 'desc' => 'Automatic compliance checking']
        ],
        'stats' => ['100% Secure', 'Compliant', 'Encrypted']
    ]
];

// Feature comparison
$feature_comparison = [
    ['feature' => 'AI Legal Research', 'basic' => '✓', 'pro' => '✓', 'enterprise' => '✓'],
    ['feature' => 'Document Automation', 'basic' => '5/month', 'pro' => 'Unlimited', 'enterprise' => 'Unlimited'],
    ['feature' => 'Case Management', 'basic' => '✓', 'pro' => '✓', 'enterprise' => '✓'],
    ['feature' => 'Team Collaboration', 'basic' => '3 users', 'pro' => '10 users', 'enterprise' => 'Unlimited'],
    ['feature' => 'Storage Space', 'basic' => '5GB', 'pro' => '50GB', 'enterprise' => 'Unlimited'],
    ['feature' => 'Priority Support', 'basic' => '✗', 'pro' => '✓', 'enterprise' => '24/7 Support'],
    ['feature' => 'API Access', 'basic' => '✗', 'pro' => 'Limited', 'enterprise' => 'Full Access'],
    ['feature' => 'Custom Development', 'basic' => '✗', 'pro' => '✗', 'enterprise' => '✓']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
        }
        
        .hero-features {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 120px 0 80px;
            margin-top: 70px;
        }
        
        .feature-category-card {
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
            height: 100%;
            border: 2px solid transparent;
        }
        
        .feature-category-card:hover {
            transform: translateY(-10px);
            border-color: var(--secondary-color);
        }
        
        .feature-list {
            list-style: none;
            padding-left: 0;
        }
        
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .stats-badge {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 8px 15px;
            margin: 5px;
            display: inline-block;
        }
        
        .pricing-card {
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .pricing-card.popular {
            border: 2px solid var(--secondary-color);
            position: relative;
        }
        
        .popular-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            background: var(--secondary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .feature-demo {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
        }
        
        .comparison-table th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-features">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4">Powerful Features for Modern Legal Practice</h1>
                    <p class="lead mb-4">
                        Discover how JusticeFlow's comprehensive suite of tools can transform 
                        your legal practice and boost productivity.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#features-list" class="btn btn-light btn-lg">
                            <i class="fas fa-list me-2"></i>View All Features
                        </a>
                        <a href="#pricing" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-tag me-2"></i>View Pricing
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-cogs fa-10x opacity-25"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Overview -->
    <section id="features-list" class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Explore Our Features</h2>
            <div class="row g-4">
                <?php foreach($features as $feature): 
                    $colors = [
                        'primary' => '#2d74da',
                        'success' => '#0d9d6b',
                        'warning' => '#ffc107',
                        'info' => '#17a2b8',
                        'danger' => '#dc3545',
                        'purple' => '#6f42c1'
                    ];
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-category-card shadow-sm">
                        <div class="p-4" style="border-bottom: 4px solid <?php echo $colors[$feature['color']]; ?>;">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 60px; height: 60px; background: <?php echo $colors[$feature['color']]; ?>;">
                                    <i class="<?php echo $feature['icon']; ?> fa-2x text-white"></i>
                                </div>
                                <h3 class="h4 mb-0"><?php echo $feature['category']; ?></h3>
                            </div>
                            
                            <ul class="feature-list mb-4">
                                <?php foreach($feature['features'] as $item): ?>
                                <li>
                                    <strong><?php echo $item['name']; ?></strong>
                                    <p class="text-muted mb-0 small"><?php echo $item['desc']; ?></p>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="mb-3">
                                <?php foreach($feature['stats'] as $stat): ?>
                                <span class="stats-badge"><?php echo $stat; ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="fas fa-play-circle me-2"></i>Watch Demo
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Feature Demo -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">See It In Action</h2>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="feature-demo h-100">
                        <h3 class="h4 mb-4">
                            <i class="fas fa-brain text-primary me-2"></i>
                            AI Legal Research Demo
                        </h3>
                        <div class="mb-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" 
                                       placeholder="Enter legal question or case details...">
                                <button class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            
                            <div class="bg-white p-3 rounded mb-3">
                                <h5 class="mb-2">AI Analysis Results:</h5>
                                <div class="progress mb-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: 92%"></div>
                                </div>
                                <small class="text-muted">92% relevance match</small>
                                
                                <div class="mt-3">
                                    <h6>Related Case Laws:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <small>Smith vs. Jones (2023) - 85% match</small>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <small>State vs. Miller (2022) - 78% match</small>
                                        </li>
                                        <li>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <small>Brown vs. Corporation (2021) - 72% match</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="feature-demo h-100">
                        <h3 class="h4 mb-4">
                            <i class="fas fa-file-contract text-success me-2"></i>
                            Document Automation Demo
                        </h3>
                        <div class="mb-4">
                            <div class="mb-3">
                                <label class="form-label">Select Document Type:</label>
                                <select class="form-select">
                                    <option>Non-Disclosure Agreement</option>
                                    <option>Employment Contract</option>
                                    <option>Rental Agreement</option>
                                    <option>Will & Testament</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Fill Details:</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Party A Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Party B Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Duration">
                                    </div>
                                </div>
                            </div>
                            
                            <button class="btn btn-success w-100">
                                <i class="fas fa-magic me-2"></i>Generate Document
                            </button>
                            
                            <div class="mt-3 bg-white p-3 rounded">
                                <h6>Estimated Time Saved:</h6>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" style="width: 80%"></div>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <strong>80%</strong>
                                    </div>
                                </div>
                                <small class="text-muted">Average 4 hours saved per document</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Feature Comparison</h2>
            <div class="table-responsive">
                <table class="table table-bordered comparison-table">
                    <thead class="table-light">
                        <tr>
                            <th>Feature</th>
                            <th class="text-center">Basic</th>
                            <th class="text-center">Professional</th>
                            <th class="text-center">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($feature_comparison as $row): ?>
                        <tr>
                            <td><strong><?php echo $row['feature']; ?></strong></td>
                            <td class="text-center"><?php echo $row['basic']; ?></td>
                            <td class="text-center"><?php echo $row['pro']; ?></td>
                            <td class="text-center"><?php echo $row['enterprise']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Choose Your Plan</h2>
            <div class="row justify-content-center">
                <div class="col-lg-4 mb-4">
                    <div class="pricing-card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <h4 class="card-title mb-3">Basic</h4>
                            <div class="display-4 fw-bold mb-3">₹999<span class="fs-6 text-muted">/month</span></div>
                            <p class="text-muted mb-4">Perfect for individual practitioners</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> AI Legal Research</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Basic Case Management</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 5 Documents/month</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Email Support</li>
                                <li class="mb-2"><i class="fas fa-times text-danger me-2"></i> Team Collaboration</li>
                                <li class="mb-2"><i class="fas fa-times text-danger me-2"></i> API Access</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="pricing-card popular shadow-lg h-100">
                        <div class="popular-badge">Most Popular</div>
                        <div class="card-body text-center p-4">
                            <h4 class="card-title mb-3">Professional</h4>
                            <div class="display-4 fw-bold mb-3">₹2,999<span class="fs-6 text-muted">/month</span></div>
                            <p class="text-muted mb-4">Ideal for small to medium law firms</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Everything in Basic</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Unlimited Documents</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Team Collaboration (10 users)</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Priority Support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Advanced Analytics</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Limited API Access</li>
                            </ul>
                            <a href="#" class="btn btn-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="pricing-card shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <h4 class="card-title mb-3">Enterprise</h4>
                            <div class="display-4 fw-bold mb-3">Custom</div>
                            <p class="text-muted mb-4">For large organizations</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Everything in Professional</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Unlimited Users</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Custom Development</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 24/7 Support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Full API Access</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Dedicated Account Manager</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary w-100">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Frequently Asked Questions</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How accurate is the AI legal research?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Our AI achieves 92% accuracy in legal research, validated by legal experts. 
                                    It continuously learns from new cases and legal developments to improve accuracy.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can I try before purchasing?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! We offer a 14-day free trial for all plans with full access to all features. 
                                    No credit card required to start.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Is my data secure?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutely. We use end-to-end encryption, comply with GDPR and other regulations, 
                                    and undergo regular security audits. Your data is never shared with third parties.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Can I cancel anytime?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, you can cancel your subscription anytime. There are no long-term contracts 
                                    and no cancellation fees.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 text-white">
                    <h2 class="fw-bold mb-3">Ready to Transform Your Practice?</h2>
                    <p class="mb-0">Join thousands of legal professionals using JusticeFlow</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="register.php" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-rocket me-2"></i>Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>