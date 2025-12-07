<?php
session_start();

// Process steps
$process_steps = [
    [
        'step' => 1,
        'title' => 'Sign Up & Setup',
        'description' => 'Create your account and configure your preferences',
        'details' => [
            'Create your JusticeFlow account in minutes',
            'Set up your profile and preferences',
            'Integrate with existing tools and systems',
            'Invite team members (if needed)'
        ],
        'icon' => 'fas fa-user-plus',
        'time' => '5 minutes',
        'color' => 'primary'
    ],
    [
        'step' => 2,
        'title' => 'Upload Your Case',
        'description' => 'Add case details and upload relevant documents',
        'details' => [
            'Enter case information manually or use templates',
            'Upload documents, evidence, and notes',
            'Set priorities and deadlines',
            'Assign to team members'
        ],
        'icon' => 'fas fa-upload',
        'time' => '10-15 minutes',
        'color' => 'success'
    ],
    [
        'step' => 3,
        'title' => 'AI Analysis & Research',
        'description' => 'Let our AI analyze and research your case',
        'details' => [
            'AI reviews documents and identifies key points',
            'Finds relevant case laws and precedents',
            'Analyzes legal arguments and strategies',
            'Generates research reports'
        ],
        'icon' => 'fas fa-brain',
        'time' => '2-5 minutes',
        'color' => 'warning'
    ],
    [
        'step' => 4,
        'title' => 'Generate Documents',
        'description' => 'Create legal documents automatically',
        'details' => [
            'AI drafts legal documents based on analysis',
            'Review and edit generated documents',
            'Add custom clauses and modifications',
            'Prepare for signatures'
        ],
        'icon' => 'fas fa-file-contract',
        'time' => '3-8 minutes',
        'color' => 'info'
    ],
    [
        'step' => 5,
        'title' => 'Case Management',
        'description' => 'Manage and track your case progress',
        'details' => [
            'Track deadlines and hearings',
            'Communicate with stakeholders',
            'Update case status and notes',
            'Generate progress reports'
        ],
        'icon' => 'fas fa-tasks',
        'time' => 'Ongoing',
        'color' => 'danger'
    ],
    [
        'step' => 6,
        'title' => 'Resolution & Analysis',
        'description' => 'Close case and analyze performance',
        'details' => [
            'Document case outcome',
            'Generate final reports',
            'Analyze performance metrics',
            'Learn for future cases'
        ],
        'icon' => 'fas fa-chart-line',
        'time' => 'Variable',
        'color' => 'purple'
    ]
];

// Success stories
$success_stories = [
    [
        'name' => 'LawFirm Inc.',
        'result' => 'Increased efficiency by 60%',
        'details' => 'Reduced research time from 8 hours to 30 minutes per case',
        'logo' => 'LF'
    ],
    [
        'name' => 'Corporate Legal Dept.',
        'result' => 'Saved ₹50L annually',
        'details' => 'Automated contract review saved thousands of billable hours',
        'logo' => 'CLD'
    ],
    [
        'name' => 'Solo Practitioner',
        'result' => 'Doubled case capacity',
        'details' => 'Managed twice as many cases with same resources',
        'logo' => 'SP'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - JusticeFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
        }
        
        .hero-works {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 120px 0 80px;
            margin-top: 70px;
        }
        
        .process-step-card {
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .process-step-card:hover {
            transform: translateY(-5px);
            border-color: var(--secondary-color);
        }
        
        .step-number {
            position: absolute;
            top: -15px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: white;
            border: 3px solid var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .step-timeline {
            position: relative;
            padding-left: 40px;
        }
        
        .step-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--secondary-color);
        }
        
        .timeline-dot {
            position: absolute;
            left: 8px;
            top: 0;
            width: 16px;
            height: 16px;
            background: var(--secondary-color);
            border-radius: 50%;
            border: 3px solid white;
        }
        
        .demo-video {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
        }
        
        .time-badge {
            background: var(--accent-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .success-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            border-left: 4px solid var(--accent-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-works">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4">How JusticeFlow Works</h1>
                    <p class="lead mb-4">
                        Discover our simple, powerful process that transforms complex legal work 
                        into efficient, automated workflows.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#process" class="btn btn-light btn-lg">
                            <i class="fas fa-play-circle me-2"></i>See the Process
                        </a>
                        <a href="#demo" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-video me-2"></i>Watch Demo
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-cogs fa-10x opacity-25"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Overview -->
    <section id="process" class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Our 6-Step Process</h2>
            <div class="row">
                <div class="col-lg-8">
                    <div class="step-timeline">
                        <?php foreach($process_steps as $step): ?>
                        <div class="mb-5 position-relative">
                            <div class="timeline-dot"></div>
                            <div class="process-step-card shadow-sm ms-4">
                                <div class="step-number"><?php echo $step['step']; ?></div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="h4 mb-2"><?php echo $step['title']; ?></h3>
                                        <p class="text-muted mb-3"><?php echo $step['description']; ?></p>
                                    </div>
                                    <span class="time-badge"><?php echo $step['time']; ?></span>
                                </div>
                                
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <ul class="list-unstyled">
                                            <?php foreach($step['details'] as $detail): ?>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <?php echo $detail; ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="<?php echo $step['icon']; ?> fa-2x text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 100px;">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-4">
                                <h4 class="card-title mb-4">Process Benefits</h4>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3">Time Saved per Case:</h6>
                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-success" style="width: 75%"></div>
                                    </div>
                                    <small class="text-muted">Average 75% time reduction</small>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3">Cost Reduction:</h6>
                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-warning" style="width: 60%"></div>
                                    </div>
                                    <small class="text-muted">Average 60% cost savings</small>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="mb-3">Accuracy Improvement:</h6>
                                    <div class="progress mb-2" style="height: 10px;">
                                        <div class="progress-bar bg-info" style="width: 92%"></div>
                                    </div>
                                    <small class="text-muted">92% accuracy in legal analysis</small>
                                </div>
                                
                                <hr class="my-4">
                                
                                <h6 class="mb-3">Key Metrics:</h6>
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="display-6 fw-bold">10x</div>
                                        <small class="text-muted">Faster Research</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="display-6 fw-bold">24/7</div>
                                        <small class="text-muted">Availability</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="display-6 fw-bold">99.9%</div>
                                        <small class="text-muted">Uptime</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="display-6 fw-bold">95%</div>
                                        <small class="text-muted">Satisfaction</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Demo -->
    <section id="demo" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Interactive Demo</h2>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="demo-video">
                        <h3 class="h4 mb-4">
                            <i class="fas fa-play-circle text-primary me-2"></i>
                            Watch How It Works
                        </h3>
                        <div class="ratio ratio-16x9 mb-4">
                            <div class="bg-dark rounded d-flex align-items-center justify-content-center" 
                                 style="min-height: 300px;">
                                <div class="text-center text-white">
                                    <i class="fas fa-play-circle fa-4x mb-3"></i>
                                    <p>Demo Video</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Download PDF Guide
                            </a>
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-calendar me-2"></i>Schedule Live Demo
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="demo-video">
                        <h3 class="h4 mb-4">
                            <i class="fas fa-hand-pointer text-success me-2"></i>
                            Try It Yourself
                        </h3>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Demo Scenario:</label>
                            <select class="form-select mb-3" id="demoScenario">
                                <option value="contract">Contract Review</option>
                                <option value="research">Legal Research</option>
                                <option value="document">Document Generation</option>
                                <option value="case">Case Management</option>
                            </select>
                            
                            <div id="demoContent">
                                <div class="mb-3">
                                    <h6>Contract Review Demo:</h6>
                                    <p class="text-muted">Upload a contract to see AI analysis in action</p>
                                    <input type="file" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <button class="btn btn-success w-100 mb-3" onclick="runDemo()">
                            <i class="fas fa-play me-2"></i>Run Demo
                        </button>
                        
                        <div id="demoResult" class="bg-white p-3 rounded" style="display: none;">
                            <h6>Demo Results:</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     style="width: 100%">Processing...</div>
                            </div>
                            <div id="resultContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Success Stories</h2>
            <div class="row">
                <?php foreach($success_stories as $story): ?>
                <div class="col-lg-4 mb-4">
                    <div class="success-card shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <strong><?php echo $story['logo']; ?></strong>
                            </div>
                            <div>
                                <h5 class="mb-1"><?php echo $story['name']; ?></h5>
                                <p class="text-success mb-0 fw-bold"><?php echo $story['result']; ?></p>
                            </div>
                        </div>
                        <p class="text-muted mb-4"><?php echo $story['details']; ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>3 months implementation
                            </small>
                            <a href="#" class="btn btn-sm btn-outline-primary">Read Case Study</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Implementation Guide -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Implementation Guide</h2>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                            <h4 class="h5 mb-3">Week 1-2: Planning</h4>
                            <p class="text-muted">Assess needs, set goals, and create implementation plan</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Needs Assessment</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Goal Setting</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Team Training Plan</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-cogs fa-2x text-white"></i>
                            </div>
                            <h4 class="h5 mb-3">Week 3-4: Setup</h4>
                            <p class="text-muted">Configure system, migrate data, and integrate tools</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>System Configuration</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Data Migration</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Tool Integration</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-rocket fa-2x text-white"></i>
                            </div>
                            <h4 class="h5 mb-3">Week 5-6: Go Live</h4>
                            <p class="text-muted">Launch, train team, and optimize processes</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>System Launch</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Team Training</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Process Optimization</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <div class="row align-items-center text-white">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
                    <p class="mb-0">Our team will guide you through every step of implementation</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="register.php" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-play-circle me-2"></i>Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function runDemo() {
            const scenario = document.getElementById('demoScenario').value;
            const demoResult = document.getElementById('demoResult');
            const resultContent = document.getElementById('resultContent');
            
            demoResult.style.display = 'block';
            
            // Simulate processing
            setTimeout(() => {
                let content = '';
                
                switch(scenario) {
                    case 'contract':
                        content = `
                            <div class="alert alert-success">
                                <strong>Contract Analysis Complete!</strong>
                                <ul class="mt-2 mb-0">
                                    <li>3 potential issues identified</li>
                                    <li>2 missing clauses found</li>
                                    <li>Risk level: Medium</li>
                                    <li>Review time saved: 3 hours</li>
                                </ul>
                            </div>
                        `;
                        break;
                    case 'research':
                        content = `
                            <div class="alert alert-info">
                                <strong>Legal Research Complete!</strong>
                                <ul class="mt-2 mb-0">
                                    <li>15 relevant cases found</li>
                                    <li>5 key statutes identified</li>
                                    <li>Research time: 45 seconds</li>
                                    <li>Traditional time: 4-6 hours</li>
                                </ul>
                            </div>
                        `;
                        break;
                    case 'document':
                        content = `
                            <div class="alert alert-warning">
                                <strong>Document Generated!</strong>
                                <ul class="mt-2 mb-0">
                                    <li>Document created in 30 seconds</li>
                                    <li>10 sections automatically filled</li>
                                    <li>Ready for review and signing</li>
                                    <li>Time saved: 2 hours</li>
                                </ul>
                            </div>
                        `;
                        break;
                    case 'case':
                        content = `
                            <div class="alert alert-primary">
                                <strong>Case Setup Complete!</strong>
                                <ul class="mt-2 mb-0">
                                    <li>Case timeline created</li>
                                    <li>3 deadlines scheduled</li>
                                    <li>Team assigned and notified</li>
                                    <li>All documents organized</li>
                                </ul>
                            </div>
                        `;
                        break;
                }
                
                resultContent.innerHTML = content;
            }, 2000);
        }
        
        // Update demo content based on selection
        document.getElementById('demoScenario').addEventListener('change', function() {
            const scenario = this.value;
            const demoContent = document.getElementById('demoContent');
            let content = '';
            
            switch(scenario) {
                case 'contract':
                    content = `
                        <div class="mb-3">
                            <h6>Contract Review Demo:</h6>
                            <p class="text-muted">Upload a contract to see AI analysis in action</p>
                            <input type="file" class="form-control">
                        </div>
                    `;
                    break;
                case 'research':
                    content = `
                        <div class="mb-3">
                            <h6>Legal Research Demo:</h6>
                            <p class="text-muted">Enter a legal question for AI research</p>
                            <textarea class="form-control" rows="3" 
                                      placeholder="Enter your legal research question..."></textarea>
                        </div>
                    `;
                    break;
                case 'document':
                    content = `
                        <div class="mb-3">
                            <h6>Document Generation Demo:</h6>
                            <p class="text-muted">Select document type to generate</p>
                            <select class="form-select">
                                <option>Non-Disclosure Agreement</option>
                                <option>Employment Contract</option>
                                <option>Rental Agreement</option>
                                <option>Will & Testament</option>
                            </select>
                        </div>
                    `;
                    break;
                case 'case':
                    content = `
                        <div class="mb-3">
                            <h6>Case Management Demo:</h6>
                            <p class="text-muted">Create a new case with timeline</p>
                            <input type="text" class="form-control mb-2" placeholder="Case Title">
                            <input type="date" class="form-control mb-2" placeholder="Filing Date">
                            <select class="form-select">
                                <option>Select Case Type</option>
                                <option>Civil</option>
                                <option>Criminal</option>
                                <option>Family</option>
                                <option>Corporate</option>
                            </select>
                        </div>
                    `;
                    break;
            }
            
            demoContent.innerHTML = content;
        });
    </script>
</body>
</html>