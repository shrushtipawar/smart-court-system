<?php
// reports.php
session_start();

// Check if user is logged in (simplified check)
if (!isset($_SESSION['user_id'])) {
    // In a real application, you would redirect to login page
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'johndoe';
}

// Determine language preference
$language = isset($_GET['lang']) && $_GET['lang'] == 'hi' ? 'hi' : 'en';

// Translations
$translations = [
    'en' => [
        'title' => 'Reports & Analytics',
        'dashboard' => 'Dashboard',
        'analytics' => 'Analytics',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'reports' => 'Reports',
        'generate_report' => 'Generate Report',
        'export' => 'Export',
        'print' => 'Print',
        'save' => 'Save',
        'filter' => 'Filter',
        'clear_filters' => 'Clear Filters',
        'date_range' => 'Date Range',
        'from' => 'From',
        'to' => 'To',
        'category' => 'Category',
        'status' => 'Status',
        'type' => 'Type',
        'all' => 'All',
        'sales' => 'Sales',
        'users' => 'Users',
        'traffic' => 'Traffic',
        'performance' => 'Performance',
        'financial' => 'Financial',
        'inventory' => 'Inventory',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly' => 'Yearly',
        'summary' => 'Summary',
        'detailed' => 'Detailed',
        'comparative' => 'Comparative',
        'trend' => 'Trend',
        'custom' => 'Custom',
        'report_type' => 'Report Type',
        'report_name' => 'Report Name',
        'created_date' => 'Created Date',
        'last_modified' => 'Last Modified',
        'actions' => 'Actions',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'download' => 'Download',
        'share' => 'Share',
        'schedule' => 'Schedule',
        'saved_reports' => 'Saved Reports',
        'quick_reports' => 'Quick Reports',
        'report_templates' => 'Report Templates',
        'generate_new' => 'Generate New',
        'sales_report' => 'Sales Report',
        'user_report' => 'User Report',
        'traffic_report' => 'Traffic Report',
        'performance_report' => 'Performance Report',
        'financial_report' => 'Financial Report',
        'inventory_report' => 'Inventory Report',
        'revenue' => 'Revenue',
        'orders' => 'Orders',
        'customers' => 'Customers',
        'growth' => 'Growth',
        'conversion_rate' => 'Conversion Rate',
        'avg_order_value' => 'Avg. Order Value',
        'top_products' => 'Top Products',
        'top_categories' => 'Top Categories',
        'top_regions' => 'Top Regions',
        'new_users' => 'New Users',
        'active_users' => 'Active Users',
        'user_engagement' => 'User Engagement',
        'page_views' => 'Page Views',
        'sessions' => 'Sessions',
        'bounce_rate' => 'Bounce Rate',
        'traffic_sources' => 'Traffic Sources',
        'server_performance' => 'Server Performance',
        'application_performance' => 'Application Performance',
        'response_time' => 'Response Time',
        'uptime' => 'Uptime',
        'income' => 'Income',
        'expenses' => 'Expenses',
        'profit' => 'Profit',
        'tax' => 'Tax',
        'cash_flow' => 'Cash Flow',
        'balance_sheet' => 'Balance Sheet',
        'stock_levels' => 'Stock Levels',
        'low_stock' => 'Low Stock',
        'out_of_stock' => 'Out of Stock',
        'best_sellers' => 'Best Sellers',
        'slow_movers' => 'Slow Movers',
        'data_source' => 'Data Source',
        'parameters' => 'Parameters',
        'format' => 'Format',
        'pdf' => 'PDF',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'html' => 'HTML',
        'email_report' => 'Email Report',
        'schedule_report' => 'Schedule Report',
        'frequency' => 'Frequency',
        'recipients' => 'Recipients',
        'next_run' => 'Next Run',
        'last_run' => 'Last Run',
        'run_now' => 'Run Now',
        'report_generator' => 'Report Generator',
        'select_data' => 'Select Data',
        'choose_columns' => 'Choose Columns',
        'apply_filters' => 'Apply Filters',
        'preview' => 'Preview',
        'generate' => 'Generate',
        'loading' => 'Loading',
        'no_data' => 'No data available',
        'total' => 'Total',
        'average' => 'Average',
        'maximum' => 'Maximum',
        'minimum' => 'Minimum',
        'median' => 'Median',
        'standard_deviation' => 'Standard Deviation',
        'percentile' => 'Percentile',
        'trend_analysis' => 'Trend Analysis',
        'year_over_year' => 'Year Over Year',
        'month_over_month' => 'Month Over Month',
        'week_over_week' => 'Week Over Week',
        'comparison' => 'Comparison',
        'vs_previous_period' => 'vs Previous Period',
        'vs_last_year' => 'vs Last Year',
        'select_period' => 'Select Period',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'last_90_days' => 'Last 90 Days',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'this_quarter' => 'This Quarter',
        'last_quarter' => 'Last Quarter',
        'this_year' => 'This Year',
        'last_year' => 'Last Year',
        'custom_range' => 'Custom Range',
        'apply' => 'Apply',
        'cancel' => 'Cancel',
        'reset' => 'Reset',
        'search' => 'Search',
        'refresh' => 'Refresh',
        'help' => 'Help',
        'documentation' => 'Documentation',
        'support' => 'Support',
        'feedback' => 'Feedback',
        'report_metrics' => 'Report Metrics',
        'visualizations' => 'Visualizations',
        'data_table' => 'Data Table',
        'charts' => 'Charts',
        'graphs' => 'Graphs',
        'tables' => 'Tables',
        'insights' => 'Insights',
        'recommendations' => 'Recommendations',
        'key_findings' => 'Key Findings',
        'executive_summary' => 'Executive Summary',
        'detailed_analysis' => 'Detailed Analysis',
        'appendix' => 'Appendix',
        'notes' => 'Notes',
        'disclaimer' => 'Disclaimer',
        'generated_on' => 'Generated on',
        'page' => 'Page',
        'of' => 'of',
        'rows_per_page' => 'Rows per page',
        'previous' => 'Previous',
        'next' => 'Next',
        'first' => 'First',
        'last' => 'Last',
        'sort_ascending' => 'Sort ascending',
        'sort_descending' => 'Sort descending',
        'filter_by' => 'Filter by',
        'select_all' => 'Select All',
        'deselect_all' => 'Deselect All',
        'selected' => 'selected',
        'no_records' => 'No records found',
        'loading_data' => 'Loading data...',
        'processing' => 'Processing...',
        'error_loading_data' => 'Error loading data',
        'try_again' => 'Try again',
        'contact_admin' => 'Contact administrator',
        'system_error' => 'System error',
        'unauthorized' => 'Unauthorized',
        'access_denied' => 'Access denied',
        'invalid_parameters' => 'Invalid parameters',
        'session_expired' => 'Session expired',
        'login_again' => 'Please login again',
    ],
    'hi' => [
        'title' => 'रिपोर्ट्स और एनालिटिक्स',
        'dashboard' => 'डैशबोर्ड',
        'analytics' => 'एनालिटिक्स',
        'profile' => 'प्रोफाइल',
        'settings' => 'सेटिंग्स',
        'logout' => 'लॉग आउट',
        'reports' => 'रिपोर्ट्स',
        'generate_report' => 'रिपोर्ट जनरेट करें',
        'export' => 'एक्सपोर्ट',
        'print' => 'प्रिंट',
        'save' => 'सेव',
        'filter' => 'फिल्टर',
        'clear_filters' => 'फिल्टर हटाएं',
        'date_range' => 'तिथि सीमा',
        'from' => 'से',
        'to' => 'तक',
        'category' => 'श्रेणी',
        'status' => 'स्थिति',
        'type' => 'प्रकार',
        'all' => 'सभी',
        'sales' => 'सेल्स',
        'users' => 'यूजर्स',
        'traffic' => 'ट्रैफिक',
        'performance' => 'परफॉर्मेंस',
        'financial' => 'वित्तीय',
        'inventory' => 'इन्वेंटरी',
        'active' => 'सक्रिय',
        'inactive' => 'निष्क्रिय',
        'pending' => 'लंबित',
        'completed' => 'पूर्ण',
        'cancelled' => 'रद्द',
        'daily' => 'दैनिक',
        'weekly' => 'साप्ताहिक',
        'monthly' => 'मासिक',
        'quarterly' => 'त्रैमासिक',
        'yearly' => 'वार्षिक',
        'summary' => 'सारांश',
        'detailed' => 'विस्तृत',
        'comparative' => 'तुलनात्मक',
        'trend' => 'ट्रेंड',
        'custom' => 'कस्टम',
        'report_type' => 'रिपोर्ट प्रकार',
        'report_name' => 'रिपोर्ट नाम',
        'created_date' => 'बनाई गई तिथि',
        'last_modified' => 'अंतिम संशोधन',
        'actions' => 'कार्य',
        'view' => 'देखें',
        'edit' => 'संपादित करें',
        'delete' => 'हटाएं',
        'download' => 'डाउनलोड',
        'share' => 'शेयर',
        'schedule' => 'शेड्यूल',
        'saved_reports' => 'सहेजी गई रिपोर्ट्स',
        'quick_reports' => 'क्विक रिपोर्ट्स',
        'report_templates' => 'रिपोर्ट टेम्प्लेट्स',
        'generate_new' => 'नई जनरेट करें',
        'sales_report' => 'सेल्स रिपोर्ट',
        'user_report' => 'यूजर रिपोर्ट',
        'traffic_report' => 'ट्रैफिक रिपोर्ट',
        'performance_report' => 'परफॉर्मेंस रिपोर्ट',
        'financial_report' => 'वित्तीय रिपोर्ट',
        'inventory_report' => 'इन्वेंटरी रिपोर्ट',
        'revenue' => 'रेवेन्यू',
        'orders' => 'ऑर्डर्स',
        'customers' => 'कस्टमर्स',
        'growth' => 'ग्रोथ',
        'conversion_rate' => 'कन्वर्जन रेट',
        'avg_order_value' => 'औसत ऑर्डर वैल्यू',
        'top_products' => 'टॉप प्रोडक्ट्स',
        'top_categories' => 'टॉप कैटेगरीज',
        'top_regions' => 'टॉप रीजन्स',
        'new_users' => 'नए यूजर्स',
        'active_users' => 'सक्रिय यूजर्स',
        'user_engagement' => 'यूजर एंगेजमेंट',
        'page_views' => 'पेज व्यूज',
        'sessions' => 'सेशन्स',
        'bounce_rate' => 'बाउंस रेट',
        'traffic_sources' => 'ट्रैफिक सोर्सेज',
        'server_performance' => 'सर्वर परफॉर्मेंस',
        'application_performance' => 'एप्लिकेशन परफॉर्मेंस',
        'response_time' => 'रिस्पॉन्स टाइम',
        'uptime' => 'अपटाइम',
        'income' => 'आय',
        'expenses' => 'खर्च',
        'profit' => 'लाभ',
        'tax' => 'टैक्स',
        'cash_flow' => 'कैश फ्लो',
        'balance_sheet' => 'बैलेंस शीट',
        'stock_levels' => 'स्टॉक लेवल',
        'low_stock' => 'लो स्टॉक',
        'out_of_stock' => 'आउट ऑफ स्टॉक',
        'best_sellers' => 'बेस्ट सेलर्स',
        'slow_movers' => 'स्लो मूवर्स',
        'data_source' => 'डेटा सोर्स',
        'parameters' => 'पैरामीटर्स',
        'format' => 'फॉर्मेट',
        'pdf' => 'पीडीएफ',
        'excel' => 'एक्सेल',
        'csv' => 'सीएसवी',
        'html' => 'एचटीएमएल',
        'email_report' => 'ईमेल रिपोर्ट',
        'schedule_report' => 'शेड्यूल रिपोर्ट',
        'frequency' => 'फ्रीक्वेंसी',
        'recipients' => 'प्राप्तकर्ता',
        'next_run' => 'अगली रन',
        'last_run' => 'अंतिम रन',
        'run_now' => 'अभी रन करें',
        'report_generator' => 'रिपोर्ट जेनरेटर',
        'select_data' => 'डेटा चुनें',
        'choose_columns' => 'कॉलम चुनें',
        'apply_filters' => 'फिल्टर लागू करें',
        'preview' => 'प्रिव्यू',
        'generate' => 'जनरेट',
        'loading' => 'लोडिंग',
        'no_data' => 'कोई डेटा उपलब्ध नहीं',
        'total' => 'कुल',
        'average' => 'औसत',
        'maximum' => 'अधिकतम',
        'minimum' => 'न्यूनतम',
        'median' => 'मध्यमान',
        'standard_deviation' => 'मानक विचलन',
        'percentile' => 'प्रतिशतक',
        'trend_analysis' => 'ट्रेंड एनालिसिस',
        'year_over_year' => 'साल दर साल',
        'month_over_month' => 'महीने दर महीने',
        'week_over_week' => 'सप्ताह दर सप्ताह',
        'comparison' => 'तुलना',
        'vs_previous_period' => 'बनाम पिछली अवधि',
        'vs_last_year' => 'बनाम पिछला साल',
        'select_period' => 'अवधि चुनें',
        'today' => 'आज',
        'yesterday' => 'कल',
        'last_7_days' => 'पिछले 7 दिन',
        'last_30_days' => 'पिछले 30 दिन',
        'last_90_days' => 'पिछले 90 दिन',
        'this_month' => 'इस महीने',
        'last_month' => 'पिछले महीने',
        'this_quarter' => 'इस तिमाही',
        'last_quarter' => 'पिछली तिमाही',
        'this_year' => 'इस साल',
        'last_year' => 'पिछले साल',
        'custom_range' => 'कस्टम रेंज',
        'apply' => 'लागू करें',
        'cancel' => 'रद्द करें',
        'reset' => 'रीसेट',
        'search' => 'खोज',
        'refresh' => 'रिफ्रेश',
        'help' => 'मदद',
        'documentation' => 'डॉक्यूमेंटेशन',
        'support' => 'सपोर्ट',
        'feedback' => 'फीडबैक',
        'report_metrics' => 'रिपोर्ट मेट्रिक्स',
        'visualizations' => 'विज़ुअलाइजेशन',
        'data_table' => 'डेटा टेबल',
        'charts' => 'चार्ट्स',
        'graphs' => 'ग्राफ़',
        'tables' => 'टेबल्स',
        'insights' => 'इनसाइट्स',
        'recommendations' => 'सिफारिशें',
        'key_findings' => 'मुख्य निष्कर्ष',
        'executive_summary' => 'कार्यकारी सारांश',
        'detailed_analysis' => 'विस्तृत विश्लेषण',
        'appendix' => 'परिशिष्ट',
        'notes' => 'नोट्स',
        'disclaimer' => 'अस्वीकरण',
        'generated_on' => 'पर जनरेट किया गया',
        'page' => 'पेज',
        'of' => 'का',
        'rows_per_page' => 'प्रति पेज पंक्तियाँ',
        'previous' => 'पिछला',
        'next' => 'अगला',
        'first' => 'पहला',
        'last' => 'अंतिम',
        'sort_ascending' => 'आरोही क्रम में क्रमबद्ध करें',
        'sort_descending' => 'अवरोही क्रम में क्रमबद्ध करें',
        'filter_by' => 'द्वारा फ़िल्टर करें',
        'select_all' => 'सभी चुनें',
        'deselect_all' => 'सभी अचयनित करें',
        'selected' => 'चयनित',
        'no_records' => 'कोई रिकॉर्ड नहीं मिला',
        'loading_data' => 'डेटा लोड हो रहा है...',
        'processing' => 'प्रोसेसिंग...',
        'error_loading_data' => 'डेटा लोड करने में त्रुटि',
        'try_again' => 'पुनः प्रयास करें',
        'contact_admin' => 'व्यवस्थापक से संपर्क करें',
        'system_error' => 'सिस्टम त्रुटि',
        'unauthorized' => 'अनधिकृत',
        'access_denied' => 'पहुंच अस्वीकृत',
        'invalid_parameters' => 'अमान्य पैरामीटर',
        'session_expired' => 'सत्र समाप्त',
        'login_again' => 'कृपया फिर से लॉगिन करें',
    ]
];

$t = $translations[$language];

// Get report type from URL or default to sales
$report_type = isset($_GET['type']) ? $_GET['type'] : 'sales';
$period = isset($_GET['period']) ? $_GET['period'] : 'last_30_days';
$format = isset($_GET['format']) ? $_GET['format'] : 'summary';

// Dummy data for reports
$reports_data = [
    'sales' => [
        'title' => $t['sales_report'],
        'description' => 'Detailed sales performance analysis',
        'metrics' => [
            'revenue' => 1254300,
            'orders' => 2345,
            'customers' => 1876,
            'growth' => 12.5,
            'conversion_rate' => 3.2,
            'avg_order_value' => 534.50,
        ],
        'top_products' => [
            ['name' => 'Product A', 'sales' => 234500, 'units' => 456, 'growth' => 15],
            ['name' => 'Product B', 'sales' => 198700, 'units' => 389, 'growth' => 8],
            ['name' => 'Product C', 'sales' => 176500, 'units' => 345, 'growth' => 22],
            ['name' => 'Product D', 'sales' => 154300, 'units' => 298, 'growth' => -3],
            ['name' => 'Product E', 'sales' => 128900, 'units' => 256, 'growth' => 5],
        ],
        'daily_data' => [
            '2023-10-01' => 34500,
            '2023-10-02' => 38900,
            '2023-10-03' => 41200,
            '2023-10-04' => 39800,
            '2023-10-05' => 42300,
            '2023-10-06' => 38700,
            '2023-10-07' => 35600,
        ],
    ],
    'users' => [
        'title' => $t['user_report'],
        'description' => 'User acquisition and engagement analysis',
        'metrics' => [
            'total_users' => 12543,
            'new_users' => 345,
            'active_users' => 8765,
            'growth' => 8.2,
            'engagement_rate' => 42.5,
            'avg_session' => '4m 32s',
        ],
        'user_growth' => [
            'Jan' => 856, 'Feb' => 923, 'Mar' => 1045, 'Apr' => 1123,
            'May' => 1245, 'Jun' => 1345, 'Jul' => 1456, 'Aug' => 1567,
            'Sep' => 1678, 'Oct' => 1789, 'Nov' => 1890, 'Dec' => 2012,
        ],
    ],
    'traffic' => [
        'title' => $t['traffic_report'],
        'description' => 'Website traffic and engagement analysis',
        'metrics' => [
            'page_views' => 54876,
            'sessions' => 23456,
            'bounce_rate' => 34.5,
            'avg_session_duration' => '3m 45s',
            'new_visitors' => 68,
            'returning_visitors' => 32,
        ],
        'sources' => [
            'Direct' => 40,
            'Organic Search' => 30,
            'Social Media' => 15,
            'Referral' => 10,
            'Email' => 5,
        ],
    ],
    'performance' => [
        'title' => $t['performance_report'],
        'description' => 'System and application performance metrics',
        'metrics' => [
            'response_time' => '245ms',
            'uptime' => 99.8,
            'server_load' => 42,
            'error_rate' => 0.2,
            'throughput' => '1250 req/s',
            'memory_usage' => 65,
        ],
        'response_times' => [
            'Mon' => 234, 'Tue' => 245, 'Wed' => 238, 'Thu' => 256,
            'Fri' => 242, 'Sat' => 231, 'Sun' => 228,
        ],
    ],
    'financial' => [
        'title' => $t['financial_report'],
        'description' => 'Financial performance and analysis',
        'metrics' => [
            'income' => 1567800,
            'expenses' => 987600,
            'profit' => 580200,
            'tax' => 145050,
            'profit_margin' => 37,
            'cash_flow' => 435150,
        ],
        'expense_categories' => [
            'Salary' => 45,
            'Marketing' => 20,
            'Operations' => 15,
            'Technology' => 12,
            'Administration' => 8,
        ],
    ],
    'inventory' => [
        'title' => $t['inventory_report'],
        'description' => 'Inventory levels and stock analysis',
        'metrics' => [
            'total_items' => 2456,
            'low_stock' => 45,
            'out_of_stock' => 12,
            'total_value' => 4567800,
            'turnover_rate' => 4.2,
            'carrying_cost' => 123400,
        ],
        'stock_levels' => [
            ['product' => 'Product A', 'current' => 456, 'min' => 100, 'status' => 'good'],
            ['product' => 'Product B', 'current' => 89, 'min' => 100, 'status' => 'low'],
            ['product' => 'Product C', 'current' => 234, 'min' => 150, 'status' => 'good'],
            ['product' => 'Product D', 'current' => 45, 'min' => 50, 'status' => 'low'],
            ['product' => 'Product E', 'current' => 0, 'min' => 75, 'status' => 'out'],
        ],
    ],
];

// Saved reports
$saved_reports = [
    [
        'id' => 1,
        'name' => 'Monthly Sales Summary',
        'type' => 'sales',
        'created' => '2023-10-01',
        'modified' => '2023-10-15',
        'schedule' => 'monthly',
        'format' => 'pdf',
    ],
    [
        'id' => 2,
        'name' => 'User Growth Report',
        'type' => 'users',
        'created' => '2023-09-15',
        'modified' => '2023-10-10',
        'schedule' => 'weekly',
        'format' => 'excel',
    ],
    [
        'id' => 3,
        'name' => 'Website Traffic Analysis',
        'type' => 'traffic',
        'created' => '2023-10-05',
        'modified' => '2023-10-05',
        'schedule' => 'daily',
        'format' => 'html',
    ],
    [
        'id' => 4,
        'name' => 'Q3 Financial Report',
        'type' => 'financial',
        'created' => '2023-10-01',
        'modified' => '2023-10-01',
        'schedule' => 'quarterly',
        'format' => 'pdf',
    ],
    [
        'id' => 5,
        'name' => 'Inventory Status',
        'type' => 'inventory',
        'created' => '2023-10-10',
        'modified' => '2023-10-10',
        'schedule' => 'weekly',
        'format' => 'excel',
    ],
];

// Format number with commas
function formatNumber($num) {
    return number_format($num);
}

// Format currency
function formatCurrency($num) {
    return '$' . number_format($num);
}

// Get period dates
function getPeriodDates($period) {
    $today = date('Y-m-d');
    $dates = [];
    
    switch($period) {
        case 'today':
            $dates['from'] = $today;
            $dates['to'] = $today;
            break;
        case 'yesterday':
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $dates['from'] = $yesterday;
            $dates['to'] = $yesterday;
            break;
        case 'last_7_days':
            $dates['from'] = date('Y-m-d', strtotime('-7 days'));
            $dates['to'] = $today;
            break;
        case 'last_30_days':
            $dates['from'] = date('Y-m-d', strtotime('-30 days'));
            $dates['to'] = $today;
            break;
        case 'last_90_days':
            $dates['from'] = date('Y-m-d', strtotime('-90 days'));
            $dates['to'] = $today;
            break;
        case 'this_month':
            $dates['from'] = date('Y-m-01');
            $dates['to'] = $today;
            break;
        case 'last_month':
            $dates['from'] = date('Y-m-01', strtotime('-1 month'));
            $dates['to'] = date('Y-m-t', strtotime('-1 month'));
            break;
        case 'this_quarter':
            $month = date('n');
            $quarter = ceil($month / 3);
            $start_month = (($quarter - 1) * 3) + 1;
            $dates['from'] = date('Y') . '-' . str_pad($start_month, 2, '0', STR_PAD_LEFT) . '-01';
            $dates['to'] = $today;
            break;
        case 'this_year':
            $dates['from'] = date('Y-01-01');
            $dates['to'] = $today;
            break;
        default:
            $dates['from'] = date('Y-m-d', strtotime('-30 days'));
            $dates['to'] = $today;
    }
    
    return $dates;
}

$period_dates = getPeriodDates($period);
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
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .navbar-custom {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: 250px;
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            z-index: 100;
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.show {
                width: 250px;
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        .sidebar-heading {
            padding: 1rem 1.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--secondary-color);
            font-weight: 700;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .sidebar-item {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .sidebar-item:hover {
            background-color: #f8f9fc;
        }
        
        .sidebar-item.active {
            background-color: rgba(78, 115, 223, 0.1);
            border-left: 4px solid var(--primary-color);
        }
        
        .sidebar-item i {
            width: 20px;
            margin-right: 10px;
            color: var(--secondary-color);
        }
        
        .sidebar-item.active i {
            color: var(--primary-color);
        }
        
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
            padding: 1rem 1.25rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .metric-card {
            border-left: 4px solid;
            transition: transform 0.3s;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
        }
        
        .metric-primary { border-left-color: var(--primary-color); }
        .metric-success { border-left-color: var(--success-color); }
        .metric-info { border-left-color: var(--info-color); }
        .metric-warning { border-left-color: var(--warning-color); }
        .metric-danger { border-left-color: var(--danger-color); }
        .metric-secondary { border-left-color: var(--secondary-color); }
        
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .metric-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-change {
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .positive { color: var(--success-color); }
        .negative { color: var(--danger-color); }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .report-type-btn.active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .period-btn.active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .language-switcher {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
        }
        
        .language-switcher:hover {
            background-color: rgba(78, 115, 223, 0.2);
        }
        
        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-good { background-color: rgba(28, 200, 138, 0.2); color: var(--success-color); }
        .badge-low { background-color: rgba(246, 194, 62, 0.2); color: var(--warning-color); }
        .badge-out { background-color: rgba(231, 74, 59, 0.2); color: var(--danger-color); }
        
        .sidebar-toggle {
            display: none;
        }
        
        @media (max-width: 992px) {
            .sidebar-toggle {
                display: block;
            }
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-view { background-color: var(--primary-color); }
        .btn-edit { background-color: var(--info-color); }
        .btn-delete { background-color: var(--danger-color); }
        .btn-download { background-color: var(--success-color); }
        .btn-share { background-color: var(--warning-color); }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #e3e6f0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #3a5bd9;
            border-color: #3a5bd9;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <button class="btn sidebar-toggle me-2" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <a class="navbar-brand" href="#">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                <?php echo $t['title']; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            <?php echo $t['dashboard']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-line me-1"></i>
                            <?php echo $t['analytics']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-1"></i>
                            <?php echo $t['profile']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item ms-2">
                        <div class="language-switcher nav-link" onclick="toggleLanguage()">
                            <i class="fas fa-language me-1"></i>
                            <?php echo $language == 'hi' ? 'English' : 'हिंदी'; ?>
                        </div>
                    </li>
                    
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-primary" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            <?php echo $t['logout']; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-heading">
            <?php echo $t['report_type']; ?>
        </div>
        
        <div class="sidebar-item <?php echo $report_type == 'sales' ? 'active' : ''; ?>" onclick="changeReportType('sales')">
            <i class="fas fa-shopping-cart"></i>
            <?php echo $t['sales']; ?>
        </div>
        
        <div class="sidebar-item <?php echo $report_type == 'users' ? 'active' : ''; ?>" onclick="changeReportType('users')">
            <i class="fas fa-users"></i>
            <?php echo $t['users']; ?>
        </div>
        
        <div class="sidebar-item <?php echo $report_type == 'traffic' ? 'active' : ''; ?>" onclick="changeReportType('traffic')">
            <i class="fas fa-traffic-light"></i>
            <?php echo $t['traffic']; ?>
        </div>
        
        <div class="sidebar-item <?php echo $report_type == 'performance' ? 'active' : ''; ?>" onclick="changeReportType('performance')">
            <i class="fas fa-tachometer-alt"></i>
            <?php echo $t['performance']; ?>
        </div>
        
        <div class="sidebar-item <?php echo $report_type == 'financial' ? 'active' : ''; ?>" onclick="changeReportType('financial')">
            <i class="fas fa-money-bill-wave"></i>
            <?php echo $t['financial']; ?>
        </div>
        
        <div class="sidebar-item <?php echo $report_type == 'inventory' ? 'active' : ''; ?>" onclick="changeReportType('inventory')">
            <i class="fas fa-boxes"></i>
            <?php echo $t['inventory']; ?>
        </div>
        
        <div class="sidebar-heading mt-4">
            <?php echo $t['saved_reports']; ?>
        </div>
        
        <?php foreach ($saved_reports as $report): ?>
        <div class="sidebar-item">
            <i class="fas fa-file-<?php echo $report['format'] == 'pdf' ? 'pdf' : ($report['format'] == 'excel' ? 'excel' : 'alt'); ?>"></i>
            <?php echo $report['name']; ?>
        </div>
        <?php endforeach; ?>
        
        <div class="sidebar-heading mt-4">
            <?php echo $t['quick_reports']; ?>
        </div>
        
        <div class="sidebar-item">
            <i class="fas fa-bolt"></i>
            <?php echo $t['daily']; ?> <?php echo $t['summary']; ?>
        </div>
        
        <div class="sidebar-item">
            <i class="fas fa-calendar-week"></i>
            <?php echo $t['weekly']; ?> <?php echo $t['trend']; ?>
        </div>
        
        <div class="sidebar-item">
            <i class="fas fa-chart-line"></i>
            <?php echo $t['month_over_month']; ?>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header with Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0"><?php echo $reports_data[$report_type]['title']; ?></h2>
                <p class="text-muted mb-0"><?php echo $reports_data[$report_type]['description']; ?></p>
            </div>
            
            <div class="d-flex">
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                    <i class="fas fa-plus me-1"></i>
                    <?php echo $t['generate_new']; ?>
                </button>
                
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>
                        <?php echo $t['export']; ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')"><i class="fas fa-file-pdf me-2"></i><?php echo $t['pdf']; ?></a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportReport('excel')"><i class="fas fa-file-excel me-2"></i><?php echo $t['excel']; ?></a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportReport('csv')"><i class="fas fa-file-csv me-2"></i><?php echo $t['csv']; ?></a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportReport('html')"><i class="fas fa-file-code me-2"></i><?php echo $t['html']; ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $t['date_range']; ?></label>
                        <select class="form-select" id="periodSelect" onchange="changePeriod(this.value)">
                            <option value="today" <?php echo $period == 'today' ? 'selected' : ''; ?>><?php echo $t['today']; ?></option>
                            <option value="yesterday" <?php echo $period == 'yesterday' ? 'selected' : ''; ?>><?php echo $t['yesterday']; ?></option>
                            <option value="last_7_days" <?php echo $period == 'last_7_days' ? 'selected' : ''; ?>><?php echo $t['last_7_days']; ?></option>
                            <option value="last_30_days" <?php echo $period == 'last_30_days' ? 'selected' : ''; ?>><?php echo $t['last_30_days']; ?></option>
                            <option value="last_90_days" <?php echo $period == 'last_90_days' ? 'selected' : ''; ?>><?php echo $t['last_90_days']; ?></option>
                            <option value="this_month" <?php echo $period == 'this_month' ? 'selected' : ''; ?>><?php echo $t['this_month']; ?></option>
                            <option value="last_month" <?php echo $period == 'last_month' ? 'selected' : ''; ?>><?php echo $t['last_month']; ?></option>
                            <option value="this_quarter" <?php echo $period == 'this_quarter' ? 'selected' : ''; ?>><?php echo $t['this_quarter']; ?></option>
                            <option value="this_year" <?php echo $period == 'this_year' ? 'selected' : ''; ?>><?php echo $t['this_year']; ?></option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $t['from']; ?></label>
                        <input type="date" class="form-control" value="<?php echo $period_dates['from']; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $t['to']; ?></label>
                        <input type="date" class="form-control" value="<?php echo $period_dates['to']; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label"><?php echo $t['format']; ?></label>
                        <select class="form-select" onchange="changeFormat(this.value)">
                            <option value="summary" <?php echo $format == 'summary' ? 'selected' : ''; ?>><?php echo $t['summary']; ?></option>
                            <option value="detailed" <?php echo $format == 'detailed' ? 'selected' : ''; ?>><?php echo $t['detailed']; ?></option>
                            <option value="comparative" <?php echo $format == 'comparative' ? 'selected' : ''; ?>><?php echo $t['comparative']; ?></option>
                            <option value="trend" <?php echo $format == 'trend' ? 'selected' : ''; ?>><?php echo $t['trend']; ?></option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-outline-secondary me-2" onclick="clearFilters()">
                                <i class="fas fa-times me-1"></i>
                                <?php echo $t['clear_filters']; ?>
                            </button>
                            <button class="btn btn-primary" onclick="applyFilters()">
                                <i class="fas fa-filter me-1"></i>
                                <?php echo $t['apply_filters']; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Metrics Cards -->
        <div class="row mb-4">
            <?php 
            $metric_colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
            $i = 0;
            
            foreach ($reports_data[$report_type]['metrics'] as $key => $value):
            ?>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                <div class="card metric-card metric-<?php echo $metric_colors[$i % 6]; ?> h-100">
                    <div class="card-body">
                        <div class="metric-value">
                            <?php 
                            if (strpos($key, 'rate') !== false || strpos($key, 'margin') !== false) {
                                echo $value . '%';
                            } else if (strpos($key, 'revenue') !== false || strpos($key, 'income') !== false || 
                                      strpos($key, 'expenses') !== false || strpos($key, 'profit') !== false ||
                                      strpos($key, 'tax') !== false || strpos($key, 'value') !== false ||
                                      strpos($key, 'cost') !== false || strpos($key, 'sales') !== false) {
                                echo formatCurrency($value);
                            } else if (is_numeric($value) && $value > 1000) {
                                echo formatNumber($value);
                            } else {
                                echo $value;
                            }
                            ?>
                        </div>
                        <div class="metric-label"><?php echo $t[$key]; ?></div>
                        <div class="metric-change positive mt-2">
                            <i class="fas fa-arrow-up me-1"></i>
                            <?php echo rand(1, 20); ?>%
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            $i++;
            endforeach; 
            ?>
        </div>
        
        <!-- Charts and Data -->
        <div class="row mb-4">
            <!-- Main Chart -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?php echo $t['trend_analysis']; ?></h6>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary period-btn active" onclick="changeChartPeriod('daily')"><?php echo $t['daily']; ?></button>
                            <button class="btn btn-sm btn-outline-secondary period-btn" onclick="changeChartPeriod('weekly')"><?php echo $t['weekly']; ?></button>
                            <button class="btn btn-sm btn-outline-secondary period-btn" onclick="changeChartPeriod('monthly')"><?php echo $t['monthly']; ?></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Secondary Chart/Data -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <?php 
                            if ($report_type == 'sales') echo $t['top_products'];
                            elseif ($report_type == 'users') echo $t['user_growth'];
                            elseif ($report_type == 'traffic') echo $t['traffic_sources'];
                            elseif ($report_type == 'performance') echo $t['response_time'];
                            elseif ($report_type == 'financial') echo $t['expense_categories'];
                            elseif ($report_type == 'inventory') echo $t['stock_levels'];
                            ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="secondaryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><?php echo $t['detailed_data']; ?></h6>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-1"></i>
                        <?php echo $t['refresh']; ?>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <?php if ($report_type == 'sales'): ?>
                                <th><?php echo $t['product']; ?></th>
                                <th><?php echo $t['sales']; ?></th>
                                <th><?php echo $t['units']; ?></th>
                                <th><?php echo $t['growth']; ?></th>
                                <th><?php echo $t['actions']; ?></th>
                                <?php elseif ($report_type == 'inventory'): ?>
                                <th><?php echo $t['product']; ?></th>
                                <th><?php echo $t['current']; ?></th>
                                <th><?php echo $t['minimum']; ?></th>
                                <th><?php echo $t['status']; ?></th>
                                <th><?php echo $t['actions']; ?></th>
                                <?php else: ?>
                                <th><?php echo $t['name']; ?></th>
                                <th><?php echo $t['value']; ?></th>
                                <th><?php echo $t['percentage']; ?></th>
                                <th><?php echo $t['trend']; ?></th>
                                <th><?php echo $t['actions']; ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($report_type == 'sales'): ?>
                                <?php foreach ($reports_data[$report_type]['top_products'] as $product): ?>
                                <tr>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo formatCurrency($product['sales']); ?></td>
                                    <td><?php echo formatNumber($product['units']); ?></td>
                                    <td>
                                        <span class="<?php echo $product['growth'] >= 0 ? 'positive' : 'negative'; ?>">
                                            <i class="fas fa-arrow-<?php echo $product['growth'] >= 0 ? 'up' : 'down'; ?> me-1"></i>
                                            <?php echo $product['growth']; ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="action-btn btn-view" title="<?php echo $t['view']; ?>"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="action-btn btn-download" title="<?php echo $t['download']; ?>"><i class="fas fa-download"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php elseif ($report_type == 'inventory'): ?>
                                <?php foreach ($reports_data[$report_type]['stock_levels'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['product']; ?></td>
                                    <td><?php echo formatNumber($item['current']); ?></td>
                                    <td><?php echo formatNumber($item['min']); ?></td>
                                    <td>
                                        <span class="badge-status badge-<?php echo $item['status']; ?>">
                                            <?php echo $t[$item['status']]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="action-btn btn-edit" title="<?php echo $t['edit']; ?>"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="action-btn btn-view" title="<?php echo $t['view']; ?>"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php 
                                $data_array = [];
                                if ($report_type == 'traffic') $data_array = $reports_data[$report_type]['sources'];
                                elseif ($report_type == 'financial') $data_array = $reports_data[$report_type]['expense_categories'];
                                
                                foreach ($data_array as $key => $value):
                                ?>
                                <tr>
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo formatNumber($value); ?></td>
                                    <td><?php echo $value; ?>%</td>
                                    <td>
                                        <span class="positive">
                                            <i class="fas fa-arrow-up me-1"></i>
                                            <?php echo rand(1, 15); ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="action-btn btn-view" title="<?php echo $t['view']; ?>"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="action-btn btn-download" title="<?php echo $t['download']; ?>"><i class="fas fa-download"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Generate Report Modal -->
    <div class="modal fade" id="generateReportModal" tabindex="-1" aria-labelledby="generateReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateReportModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>
                        <?php echo $t['generate_new']; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="reportName" class="form-label"><?php echo $t['report_name']; ?></label>
                                <input type="text" class="form-control" id="reportName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="reportCategory" class="form-label"><?php echo $t['category']; ?></label>
                                <select class="form-select" id="reportCategory" required>
                                    <option value="sales"><?php echo $t['sales']; ?></option>
                                    <option value="users"><?php echo $t['users']; ?></option>
                                    <option value="traffic"><?php echo $t['traffic']; ?></option>
                                    <option value="performance"><?php echo $t['performance']; ?></option>
                                    <option value="financial"><?php echo $t['financial']; ?></option>
                                    <option value="inventory"><?php echo $t['inventory']; ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="reportType" class="form-label"><?php echo $t['report_type']; ?></label>
                                <select class="form-select" id="reportType" required>
                                    <option value="summary"><?php echo $t['summary']; ?></option>
                                    <option value="detailed"><?php echo $t['detailed']; ?></option>
                                    <option value="comparative"><?php echo $t['comparative']; ?></option>
                                    <option value="trend"><?php echo $t['trend']; ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="reportFormat" class="form-label"><?php echo $t['format']; ?></label>
                                <select class="form-select" id="reportFormat" required>
                                    <option value="pdf"><?php echo $t['pdf']; ?></option>
                                    <option value="excel"><?php echo $t['excel']; ?></option>
                                    <option value="csv"><?php echo $t['csv']; ?></option>
                                    <option value="html"><?php echo $t['html']; ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dateFrom" class="form-label"><?php echo $t['from']; ?></label>
                                <input type="date" class="form-control" id="dateFrom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dateTo" class="form-label"><?php echo $t['to']; ?></label>
                                <input type="date" class="form-control" id="dateTo" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reportParameters" class="form-label"><?php echo $t['parameters']; ?></label>
                            <textarea class="form-control" id="reportParameters" rows="3" placeholder="Add any additional parameters or notes"></textarea>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="scheduleReport">
                            <label class="form-check-label" for="scheduleReport">
                                <?php echo $t['schedule_report']; ?>
                            </label>
                        </div>
                        
                        <div id="scheduleOptions" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="frequency" class="form-label"><?php echo $t['frequency']; ?></label>
                                    <select class="form-select" id="frequency">
                                        <option value="daily"><?php echo $t['daily']; ?></option>
                                        <option value="weekly"><?php echo $t['weekly']; ?></option>
                                        <option value="monthly"><?php echo $t['monthly']; ?></option>
                                        <option value="quarterly"><?php echo $t['quarterly']; ?></option>
                                        <option value="yearly"><?php echo $t['yearly']; ?></option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="recipients" class="form-label"><?php echo $t['recipients']; ?></label>
                                    <input type="text" class="form-control" id="recipients" placeholder="Enter email addresses">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                    <button type="button" class="btn btn-primary" onclick="generateReport()">
                        <i class="fas fa-play me-1"></i>
                        <?php echo $t['generate']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Function to toggle language
        function toggleLanguage() {
            const currentLang = "<?php echo $language; ?>";
            const newLang = currentLang === 'hi' ? 'en' : 'hi';
            window.location.href = `reports.php?type=<?php echo $report_type; ?>&period=<?php echo $period; ?>&lang=${newLang}`;
        }
        
        // Function to toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // Function to change report type
        function changeReportType(type) {
            window.location.href = `reports.php?type=${type}&period=<?php echo $period; ?>&lang=<?php echo $language; ?>`;
        }
        
        // Function to change period
        function changePeriod(period) {
            window.location.href = `reports.php?type=<?php echo $report_type; ?>&period=${period}&lang=<?php echo $language; ?>`;
        }
        
        // Function to change format
        function changeFormat(format) {
            window.location.href = `reports.php?type=<?php echo $report_type; ?>&period=<?php echo $period; ?>&format=${format}&lang=<?php echo $language; ?>`;
        }
        
        // Function to clear filters
        function clearFilters() {
            document.getElementById('periodSelect').value = 'last_30_days';
            // Add more filter clearing logic as needed
            showNotification('Filters cleared', 'info');
        }
        
        // Function to apply filters
        function applyFilters() {
            // In a real app, this would apply filters and reload data
            showNotification('Filters applied successfully', 'success');
        }
        
        // Function to export report
        function exportReport(format) {
            showNotification(`Exporting report as ${format.toUpperCase()}...`, 'info');
            
            // Simulate export process
            setTimeout(() => {
                showNotification(`Report exported successfully as ${format.toUpperCase()}`, 'success');
                
                // Create a temporary link to trigger download
                const link = document.createElement('a');
                link.href = '#';
                link.download = `report_<?php echo $report_type; ?>_<?php echo date('Y-m-d'); ?>.${format}`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 2000);
        }
        
        // Function to generate report
        function generateReport() {
            const reportName = document.getElementById('reportName').value;
            const reportCategory = document.getElementById('reportCategory').value;
            
            if (!reportName) {
                showNotification('Please enter a report name', 'error');
                return;
            }
            
            showNotification(`Generating ${reportName}...`, 'info');
            
            // Simulate report generation
            setTimeout(() => {
                showNotification(`Report "${reportName}" generated successfully!`, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('generateReportModal'));
                modal.hide();
                
                // Reset form
                document.getElementById('reportForm').reset();
            }, 3000);
        }
        
        // Function to refresh data
        function refreshData() {
            showNotification('Refreshing data...', 'info');
            
            // Simulate data refresh
            setTimeout(() => {
                showNotification('Data refreshed successfully!', 'success');
                
                // In a real app, you would reload the data table here
            }, 1500);
        }
        
        // Function to change chart period
        function changeChartPeriod(period) {
            // Update active button
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update chart data based on period
            updateChartData(period);
        }
        
        // Function to show notifications
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
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
        
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Schedule report checkbox
            document.getElementById('scheduleReport').addEventListener('change', function() {
                document.getElementById('scheduleOptions').style.display = this.checked ? 'block' : 'none';
            });
            
            // Set default dates in modal
            const today = new Date().toISOString().split('T')[0];
            const lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);
            const lastMonthStr = lastMonth.toISOString().split('T')[0];
            
            document.getElementById('dateFrom').value = lastMonthStr;
            document.getElementById('dateTo').value = today;
            
            // Initialize DataTable
            if (document.getElementById('dataTable')) {
                // In a real app, you would use DataTables library
                // For now, we'll just add basic styling
            }
            
            // Create trend chart
            createTrendChart();
            
            // Create secondary chart
            createSecondaryChart();
        });
        
        // Create trend chart
        function createTrendChart() {
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            // Sample data based on report type
            let labels = [];
            let data = [];
            let label = '';
            
            <?php if ($report_type == 'sales'): ?>
            labels = [<?php 
                $dates = array_keys($reports_data[$report_type]['daily_data']);
                foreach($dates as $date) {
                    echo "'" . date('M j', strtotime($date)) . "',";
                }
            ?>];
            data = [<?php echo implode(',', $reports_data[$report_type]['daily_data']); ?>];
            label = '<?php echo $t["revenue"]; ?>';
            <?php elseif ($report_type == 'users'): ?>
            labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            data = [856, 923, 1045, 1123, 1245, 1345, 1456, 1567, 1678, 1789, 1890, 2012];
            label = '<?php echo $t["new_users"]; ?>';
            <?php elseif ($report_type == 'traffic'): ?>
            labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            data = [4500, 5200, 4800, 6100, 5900, 5300, 4100];
            label = '<?php echo $t["sessions"]; ?>';
            <?php elseif ($report_type == 'performance'): ?>
            labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            data = [234, 245, 238, 256, 242, 231, 228];
            label = '<?php echo $t["response_time"]; ?> (ms)';
            <?php elseif ($report_type == 'financial'): ?>
            labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            data = [125000, 134000, 142000, 156000, 145000, 167000, 178000, 189000, 176000, 195000, 203000, 218000];
            label = '<?php echo $t["income"]; ?>';
            <?php else: ?>
            labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            data = [456, 389, 512, 478];
            label = '<?php echo $t["units_sold"]; ?>';
            <?php endif; ?>
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
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
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    <?php if ($report_type == 'sales' || $report_type == 'financial'): ?>
                                    return '$' + value.toLocaleString();
                                    <?php else: ?>
                                    return value.toLocaleString();
                                    <?php endif; ?>
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Create secondary chart
        function createSecondaryChart() {
            const ctx = document.getElementById('secondaryChart').getContext('2d');
            
            let chartType = 'doughnut';
            let labels = [];
            let data = [];
            let backgroundColor = [];
            
            <?php if ($report_type == 'sales'): ?>
            chartType = 'bar';
            labels = [<?php 
                foreach($reports_data[$report_type]['top_products'] as $product) {
                    echo "'" . $product['name'] . "',";
                }
            ?>];
            data = [<?php 
                foreach($reports_data[$report_type]['top_products'] as $product) {
                    echo $product['sales'] . ",";
                }
            ?>];
            backgroundColor = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
            <?php elseif ($report_type == 'traffic'): ?>
            labels = [<?php 
                $sources = array_keys($reports_data[$report_type]['sources']);
                foreach($sources as $source) {
                    echo "'$source',";
                }
            ?>];
            data = [<?php echo implode(',', $reports_data[$report_type]['sources']); ?>];
            backgroundColor = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
            <?php elseif ($report_type == 'financial'): ?>
            labels = [<?php 
                $categories = array_keys($reports_data[$report_type]['expense_categories']);
                foreach($categories as $category) {
                    echo "'$category',";
                }
            ?>];
            data = [<?php echo implode(',', $reports_data[$report_type]['expense_categories']); ?>];
            backgroundColor = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
            <?php else: ?>
            chartType = 'doughnut';
            labels = ['Good', 'Low', 'Out of Stock'];
            data = [2, 2, 1];
            backgroundColor = ['#1cc88a', '#f6c23e', '#e74a3b'];
            <?php endif; ?>
            
            new Chart(ctx, {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: chartType === 'doughnut',
                            position: 'bottom'
                        }
                    },
                    scales: chartType === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    <?php if ($report_type == 'sales'): ?>
                                    return '$' + value.toLocaleString();
                                    <?php else: ?>
                                    return value + '%';
                                    <?php endif; ?>
                                }
                            }
                        }
                    } : {}
                }
            });
        }
        
        // Update chart data based on period
        function updateChartData(period) {
            showNotification(`Updating chart for ${period} period...`, 'info');
            
            // In a real app, you would fetch new data and update the chart
            setTimeout(() => {
                showNotification('Chart updated successfully!', 'success');
            }, 1000);
        }
    </script>
</body>
</html>