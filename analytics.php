<?php
// analytics.php
// Start session for user authentication (optional)
session_start();

// Dummy data for demonstration
// In a real application, this would come from a database
$analytics_data = [
    'total_visitors' => 12543,
    'total_pageviews' => 54876,
    'bounce_rate' => 34.5,
    'avg_session_duration' => '4m 32s',
    'new_visitors' => 68,
    'returning_visitors' => 32,
    'top_pages' => [
        ['page' => '/home', 'views' => 12453, 'change' => '+12%'],
        ['page' => '/products', 'views' => 9876, 'change' => '+5%'],
        ['page' => '/blog', 'views' => 7654, 'change' => '-2%'],
        ['page' => '/about', 'views' => 5432, 'change' => '+8%'],
        ['page' => '/contact', 'views' => 3210, 'change' => '+15%'],
    ],
    'traffic_sources' => [
        'Direct' => 40,
        'Organic Search' => 30,
        'Social Media' => 15,
        'Referral' => 10,
        'Email' => 5,
    ],
    'visitors_by_country' => [
        'India' => 45,
        'United States' => 20,
        'United Kingdom' => 10,
        'Canada' => 8,
        'Australia' => 7,
        'Other' => 10,
    ],
    'daily_visitors' => [
        '2023-10-01' => 450,
        '2023-10-02' => 520,
        '2023-10-03' => 480,
        '2023-10-04' => 610,
        '2023-10-05' => 590,
        '2023-10-06' => 530,
        '2023-10-07' => 410,
    ],
];

// Determine language preference (Hindi/English)
$language = isset($_GET['lang']) && $_GET['lang'] == 'hi' ? 'hi' : 'en';

// Translations
$translations = [
    'en' => [
        'title' => 'Analytics Dashboard',
        'overview' => 'Overview',
        'visitors' => 'Visitors',
        'pageviews' => 'Pageviews',
        'bounce_rate' => 'Bounce Rate',
        'avg_session' => 'Avg. Session',
        'new_visitors' => 'New Visitors',
        'returning_visitors' => 'Returning Visitors',
        'top_pages' => 'Top Pages',
        'traffic_sources' => 'Traffic Sources',
        'visitors_by_country' => 'Visitors by Country',
        'daily_visitors' => 'Daily Visitors',
        'page' => 'Page',
        'views' => 'Views',
        'change' => 'Change',
        'country' => 'Country',
        'percentage' => 'Percentage',
        'date_range' => 'Date Range',
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'last_90_days' => 'Last 90 Days',
        'download_report' => 'Download Report',
        'refresh_data' => 'Refresh Data',
        'real_time' => 'Real-time',
        'today' => 'Today',
        'this_week' => 'This Week',
        'this_month' => 'This Month',
        'total' => 'Total',
        'visitors_today' => 'Visitors Today',
        'pageviews_today' => 'Pageviews Today',
    ],
    'hi' => [
        'title' => 'एनालिटिक्स डैशबोर्ड',
        'overview' => 'अवलोकन',
        'visitors' => 'आगंतुक',
        'pageviews' => 'पेज दृश्य',
        'bounce_rate' => 'बाउंस दर',
        'avg_session' => 'औसत सत्र',
        'new_visitors' => 'नए आगंतुक',
        'returning_visitors' => 'पुराने आगंतुक',
        'top_pages' => 'शीर्ष पृष्ठ',
        'traffic_sources' => 'ट्रैफ़िक स्रोत',
        'visitors_by_country' => 'देश के अनुसार आगंतुक',
        'daily_visitors' => 'दैनिक आगंतुक',
        'page' => 'पृष्ठ',
        'views' => 'दृश्य',
        'change' => 'परिवर्तन',
        'country' => 'देश',
        'percentage' => 'प्रतिशत',
        'date_range' => 'तिथि सीमा',
        'last_7_days' => 'पिछले 7 दिन',
        'last_30_days' => 'पिछले 30 दिन',
        'last_90_days' => 'पिछले 90 दिन',
        'download_report' => 'रिपोर्ट डाउनलोड करें',
        'refresh_data' => 'डेटा रीफ्रेश करें',
        'real_time' => 'रियल-टाइम',
        'today' => 'आज',
        'this_week' => 'इस सप्ताह',
        'this_month' => 'इस महीने',
        'total' => 'कुल',
        'visitors_today' => 'आज के आगंतुक',
        'pageviews_today' => 'आज के पेज दृश्य',
    ]
];

$t = $translations[$language];

// Function to format numbers with commas
function formatNumber($num) {
    return number_format($num);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $language == 'hi' ? 'hi' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background-color: var(--primary-color);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card {
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
            padding: 0.75rem 1.25rem;
        }
        
        .stat-card {
            border-left: 0.25rem solid;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .visitors-card {
            border-left-color: var(--primary-color);
        }
        
        .pageviews-card {
            border-left-color: var(--success-color);
        }
        
        .bounce-card {
            border-left-color: var(--danger-color);
        }
        
        .session-card {
            border-left-color: var(--info-color);
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        
        .percentage-change {
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .positive {
            color: var(--success-color);
        }
        
        .negative {
            color: var(--danger-color);
        }
        
        .language-switcher {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: rgba(255,255,255,0.2);
        }
        
        .language-switcher:hover {
            background-color: rgba(255,255,255,0.3);
        }
        
        .date-range-btn.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        footer {
            border-top: 1px solid #e3e6f0;
            color: #858796;
        }
        
        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-line me-2"></i>
                <?php echo $t['title']; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dateRangeDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?php echo $t['date_range']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item date-range-btn active" href="#" data-range="7"><?php echo $t['last_7_days']; ?></a></li>
                            <li><a class="dropdown-item date-range-btn" href="#" data-range="30"><?php echo $t['last_30_days']; ?></a></li>
                            <li><a class="dropdown-item date-range-btn" href="#" data-range="90"><?php echo $t['last_90_days']; ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="refreshData">
                            <i class="fas fa-sync-alt me-1"></i>
                            <?php echo $t['refresh_data']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="downloadReport">
                            <i class="fas fa-download me-1"></i>
                            <?php echo $t['download_report']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item ms-2">
                        <div class="language-switcher nav-link" onclick="toggleLanguage()">
                            <i class="fas fa-language me-1"></i>
                            <?php echo $language == 'hi' ? 'English' : 'हिंदी'; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card visitors-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <?php echo $t['visitors']; ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo formatNumber($analytics_data['total_visitors']); ?>
                                </div>
                                <div class="mt-2 mb-0">
                                    <span class="percentage-change positive">
                                        <i class="fas fa-arrow-up me-1"></i>12%
                                    </span>
                                    <span class="text-muted ml-2"><?php echo $t['last_7_days']; ?></span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users stat-icon text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card pageviews-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    <?php echo $t['pageviews']; ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo formatNumber($analytics_data['total_pageviews']); ?>
                                </div>
                                <div class="mt-2 mb-0">
                                    <span class="percentage-change positive">
                                        <i class="fas fa-arrow-up me-1"></i>8%
                                    </span>
                                    <span class="text-muted ml-2"><?php echo $t['last_7_days']; ?></span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-eye stat-icon text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card bounce-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    <?php echo $t['bounce_rate']; ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $analytics_data['bounce_rate']; ?>%
                                </div>
                                <div class="mt-2 mb-0">
                                    <span class="percentage-change negative">
                                        <i class="fas fa-arrow-down me-1"></i>3%
                                    </span>
                                    <span class="text-muted ml-2"><?php echo $t['last_7_days']; ?></span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-sign-out-alt stat-icon text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card session-card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    <?php echo $t['avg_session']; ?>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $analytics_data['avg_session_duration']; ?>
                                </div>
                                <div class="mt-2 mb-0">
                                    <span class="percentage-change positive">
                                        <i class="fas fa-arrow-up me-1"></i>5%
                                    </span>
                                    <span class="text-muted ml-2"><?php echo $t['last_7_days']; ?></span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock stat-icon text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts and Tables Row -->
        <div class="row">
            <!-- Daily Visitors Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-area me-1"></i>
                            <?php echo $t['daily_visitors']; ?>
                        </h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                <div class="dropdown-header"><?php echo $t['view_by']; ?></div>
                                <a class="dropdown-item" href="#"><?php echo $t['today']; ?></a>
                                <a class="dropdown-item" href="#"><?php echo $t['this_week']; ?></a>
                                <a class="dropdown-item" href="#"><?php echo $t['this_month']; ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="dailyVisitorsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Visitor Types -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-friends me-1"></i>
                            <?php echo $t['visitors']; ?> <?php echo $t['total']; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="visitorTypeChart"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <div class="row">
                                <div class="col-6">
                                    <div class="h5 font-weight-bold text-primary">
                                        <?php echo $analytics_data['new_visitors']; ?>%
                                    </div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase">
                                        <?php echo $t['new_visitors']; ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="h5 font-weight-bold text-success">
                                        <?php echo $analytics_data['returning_visitors']; ?>%
                                    </div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase">
                                        <?php echo $t['returning_visitors']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second Row: Tables -->
        <div class="row">
            <!-- Top Pages -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-alt me-1"></i>
                            <?php echo $t['top_pages']; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th><?php echo $t['page']; ?></th>
                                        <th><?php echo $t['views']; ?></th>
                                        <th><?php echo $t['change']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($analytics_data['top_pages'] as $page): ?>
                                    <tr>
                                        <td><?php echo $page['page']; ?></td>
                                        <td><?php echo formatNumber($page['views']); ?></td>
                                        <td>
                                            <?php 
                                            $change_class = strpos($page['change'], '+') !== false ? 'positive' : 'negative';
                                            echo "<span class='$change_class'><i class='fas " . (strpos($page['change'], '+') !== false ? 'fa-arrow-up' : 'fa-arrow-down') . " me-1'></i>" . $page['change'] . "</span>";
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Traffic Sources -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-traffic-light me-1"></i>
                            <?php echo $t['traffic_sources']; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trafficSourcesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Third Row: Country Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-globe-asia me-1"></i>
                            <?php echo $t['visitors_by_country']; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th><?php echo $t['country']; ?></th>
                                        <th><?php echo $t['percentage']; ?></th>
                                        <th><?php echo $t['visitors']; ?></th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($analytics_data['visitors_by_country'] as $country => $percentage): ?>
                                    <tr>
                                        <td>
                                            <img src="https://flagcdn.com/16x12/<?php echo strtolower(getCountryCode($country)); ?>.png" 
                                                 alt="<?php echo $country; ?>" class="me-2">
                                            <?php echo $country; ?>
                                        </td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo $percentage; ?>%" 
                                                     aria-valuenow="<?php echo $percentage; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $percentage; ?>%</td>
                                        <td>
                                            <?php 
                                            $trend = rand(0, 1) ? 'up' : 'down';
                                            $trend_class = $trend == 'up' ? 'positive' : 'negative';
                                            $trend_icon = $trend == 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
                                            $trend_value = $trend == 'up' ? '+'.rand(1, 10).'%' : '-'.rand(1, 5).'%';
                                            echo "<span class='$trend_class'><i class='fas $trend_icon me-1'></i>$trend_value</span>";
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-4">
                <span>Copyright &copy; <?php echo date('Y'); ?> Analytics Dashboard. <?php echo $t['real_time']; ?>: <span id="liveVisitorCount"><?php echo rand(5, 15); ?></span> <?php echo strtolower($t['visitors']); ?> <?php echo $t['today']; ?></span>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to toggle language
        function toggleLanguage() {
            const currentLang = "<?php echo $language; ?>";
            const newLang = currentLang === 'hi' ? 'en' : 'hi';
            window.location.href = `analytics.php?lang=${newLang}`;
        }
        
        // Daily Visitors Chart
        const dailyVisitorsCtx = document.getElementById('dailyVisitorsChart').getContext('2d');
        const dailyVisitorsChart = new Chart(dailyVisitorsCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    $dates = array_keys($analytics_data['daily_visitors']);
                    foreach($dates as $date) {
                        echo "'" . date('M j', strtotime($date)) . "',";
                    }
                ?>],
                datasets: [{
                    label: '<?php echo $t['visitors']; ?>',
                    data: [<?php echo implode(',', $analytics_data['daily_visitors']); ?>],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });
        
        // Visitor Type Chart (Doughnut)
        const visitorTypeCtx = document.getElementById('visitorTypeChart').getContext('2d');
        const visitorTypeChart = new Chart(visitorTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['<?php echo $t['new_visitors']; ?>', '<?php echo $t['returning_visitors']; ?>'],
                datasets: [{
                    data: [<?php echo $analytics_data['new_visitors']; ?>, <?php echo $analytics_data['returning_visitors']; ?>],
                    backgroundColor: ['#4e73df', '#1cc88a'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Traffic Sources Chart
        const trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
        const trafficSourcesChart = new Chart(trafficSourcesCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    $sources = array_keys($analytics_data['traffic_sources']);
                    foreach($sources as $source) {
                        echo "'$source',";
                    }
                ?>],
                datasets: [{
                    label: '<?php echo $t['percentage']; ?> (%)',
                    data: [<?php echo implode(',', $analytics_data['traffic_sources']); ?>],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Date range selector
        document.querySelectorAll('.date-range-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all buttons
                document.querySelectorAll('.date-range-btn').forEach(b => {
                    b.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get the selected range
                const range = this.getAttribute('data-range');
                
                // Show loading indicator
                const cardBody = document.querySelector('#dailyVisitorsChart').closest('.card-body');
                const originalContent = cardBody.innerHTML;
                cardBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading data...</p></div>';
                
                // Simulate API call
                setTimeout(() => {
                    // Update chart with new data based on range
                    let newData = [];
                    let newLabels = [];
                    
                    if (range === '7') {
                        newData = [450, 520, 480, 610, 590, 530, 410];
                        newLabels = ['Oct 1', 'Oct 2', 'Oct 3', 'Oct 4', 'Oct 5', 'Oct 6', 'Oct 7'];
                    } else if (range === '30') {
                        newData = [];
                        newLabels = [];
                        for (let i = 1; i <= 30; i++) {
                            newData.push(Math.floor(Math.random() * 200) + 400);
                            newLabels.push('Oct ' + i);
                        }
                    } else if (range === '90') {
                        newData = [];
                        newLabels = [];
                        for (let i = 1; i <= 90; i += 3) {
                            newData.push(Math.floor(Math.random() * 300) + 300);
                            newLabels.push('Day ' + i);
                        }
                    }
                    
                    // Update chart
                    dailyVisitorsChart.data.labels = newLabels;
                    dailyVisitorsChart.data.datasets[0].data = newData;
                    dailyVisitorsChart.update();
                    
                    // Restore original content
                    cardBody.innerHTML = originalContent;
                    
                    // Re-attach the chart to the canvas
                    document.getElementById('dailyVisitorsChart').getContext('2d');
                    
                    // Show success message
                    showNotification('Data updated for ' + this.textContent.trim(), 'success');
                }, 1500);
            });
        });
        
        // Refresh data button
        document.getElementById('refreshData').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading
            showNotification('Refreshing data...', 'info');
            
            // Simulate refresh
            setTimeout(() => {
                // Update live visitor count
                const newCount = Math.floor(Math.random() * 10) + 5;
                document.getElementById('liveVisitorCount').textContent = newCount;
                
                // Show success message
                showNotification('Data refreshed successfully!', 'success');
            }, 2000);
        });
        
        // Download report button
        document.getElementById('downloadReport').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading
            showNotification('Preparing report for download...', 'info');
            
            // Simulate download
            setTimeout(() => {
                showNotification('Report downloaded successfully!', 'success');
                
                // Create a temporary link to trigger download
                const link = document.createElement('a');
                link.href = '#';
                link.download = 'analytics-report-<?php echo date("Y-m-d"); ?>.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 3000);
        });
        
        // Function to show notifications
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
        
        // Simulate real-time visitor updates
        setInterval(() => {
            const currentCount = parseInt(document.getElementById('liveVisitorCount').textContent);
            const change = Math.random() > 0.5 ? 1 : -1;
            const newCount = Math.max(1, currentCount + change);
            document.getElementById('liveVisitorCount').textContent = newCount;
        }, 10000);
    </script>
    
    <?php
    // Helper function to get country code (simplified)
    function getCountryCode($country) {
        $countryCodes = [
            'India' => 'in',
            'United States' => 'us',
            'United Kingdom' => 'gb',
            'Canada' => 'ca',
            'Australia' => 'au',
            'Other' => 'un'
        ];
        
        return isset($countryCodes[$country]) ? $countryCodes[$country] : 'un';
    }
    ?>
</body>
</html>