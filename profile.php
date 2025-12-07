<?php
// profile.php
session_start();

// Check if user is logged in, otherwise redirect to login
// For this example, we'll simulate a logged-in user
if (!isset($_SESSION['user_id'])) {
    // In a real application, you would redirect to login page
    // header('Location: login.php');
    // exit();
    
    // For demo, we'll create a dummy session
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'johndoe';
    $_SESSION['email'] = 'john.doe@example.com';
}

// User data - in real app, this would come from database
$user_data = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'email' => $_SESSION['email'],
    'full_name' => 'John Doe',
    'phone' => '+1 (555) 123-4567',
    'location' => 'New York, USA',
    'bio' => 'Web developer with 5+ years of experience. Passionate about creating beautiful and functional web applications.',
    'profile_pic' => 'https://randomuser.me/api/portraits/men/32.jpg',
    'cover_pic' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
    'joined_date' => '2022-03-15',
    'last_login' => date('Y-m-d H:i:s'),
    'status' => 'active',
    'role' => 'Administrator',
    'website' => 'https://johndoe.dev',
    'github' => 'https://github.com/johndoe',
    'twitter' => 'https://twitter.com/johndoe',
    'linkedin' => 'https://linkedin.com/in/johndoe',
];

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile information
        $user_data['full_name'] = htmlspecialchars($_POST['full_name']);
        $user_data['phone'] = htmlspecialchars($_POST['phone']);
        $user_data['location'] = htmlspecialchars($_POST['location']);
        $user_data['bio'] = htmlspecialchars($_POST['bio']);
        $user_data['website'] = htmlspecialchars($_POST['website']);
        $user_data['github'] = htmlspecialchars($_POST['github']);
        $user_data['twitter'] = htmlspecialchars($_POST['twitter']);
        $user_data['linkedin'] = htmlspecialchars($_POST['linkedin']);
        
        $message = 'Profile updated successfully!';
        $message_type = 'success';
    } elseif (isset($_POST['change_password'])) {
        // Change password logic
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            // In real app, verify current password and update in database
            $message = 'Password changed successfully!';
            $message_type = 'success';
        } else {
            $message = 'New passwords do not match!';
            $message_type = 'error';
        }
    } elseif (isset($_POST['upload_avatar'])) {
        // Handle avatar upload
        // In real app, process file upload here
        $message = 'Profile picture updated!';
        $message_type = 'success';
    }
}

// Determine language preference
$language = isset($_GET['lang']) && $_GET['lang'] == 'hi' ? 'hi' : 'en';

// Translations
$translations = [
    'en' => [
        'title' => 'User Profile',
        'my_profile' => 'My Profile',
        'edit_profile' => 'Edit Profile',
        'change_password' => 'Change Password',
        'logout' => 'Logout',
        'full_name' => 'Full Name',
        'username' => 'Username',
        'email' => 'Email Address',
        'phone' => 'Phone Number',
        'location' => 'Location',
        'bio' => 'Bio',
        'website' => 'Website',
        'joined' => 'Joined',
        'last_login' => 'Last Login',
        'status' => 'Status',
        'role' => 'Role',
        'social_links' => 'Social Links',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'update_password' => 'Update Password',
        'upload_photo' => 'Upload Photo',
        'change_photo' => 'Change Photo',
        'remove_photo' => 'Remove Photo',
        'profile_info' => 'Profile Information',
        'security' => 'Security',
        'preferences' => 'Preferences',
        'activity_log' => 'Activity Log',
        'notifications' => 'Notifications',
        'dashboard' => 'Dashboard',
        'settings' => 'Settings',
        'account_settings' => 'Account Settings',
        'view_profile' => 'View Profile',
        'edit' => 'Edit',
        'follow' => 'Follow',
        'message' => 'Message',
        'posts' => 'Posts',
        'followers' => 'Followers',
        'following' => 'Following',
        'about_me' => 'About Me',
        'contact_info' => 'Contact Information',
        'social_media' => 'Social Media',
        'account_details' => 'Account Details',
        'recent_activity' => 'Recent Activity',
    ],
    'hi' => [
        'title' => 'यूज़र प्रोफाइल',
        'my_profile' => 'मेरी प्रोफाइल',
        'edit_profile' => 'प्रोफाइल संपादित करें',
        'change_password' => 'पासवर्ड बदलें',
        'logout' => 'लॉग आउट',
        'full_name' => 'पूरा नाम',
        'username' => 'यूज़रनेम',
        'email' => 'ईमेल पता',
        'phone' => 'फ़ोन नंबर',
        'location' => 'स्थान',
        'bio' => 'जीवन परिचय',
        'website' => 'वेबसाइट',
        'joined' => 'शामिल हुए',
        'last_login' => 'अंतिम लॉगिन',
        'status' => 'स्थिति',
        'role' => 'भूमिका',
        'social_links' => 'सोशल लिंक्स',
        'save_changes' => 'परिवर्तन सहेजें',
        'cancel' => 'रद्द करें',
        'current_password' => 'वर्तमान पासवर्ड',
        'new_password' => 'नया पासवर्ड',
        'confirm_password' => 'पासवर्ड की पुष्टि करें',
        'update_password' => 'पासवर्ड अपडेट करें',
        'upload_photo' => 'फोटो अपलोड करें',
        'change_photo' => 'फोटो बदलें',
        'remove_photo' => 'फोटो हटाएं',
        'profile_info' => 'प्रोफाइल जानकारी',
        'security' => 'सुरक्षा',
        'preferences' => 'प्राथमिकताएं',
        'activity_log' => 'गतिविधि लॉग',
        'notifications' => 'सूचनाएं',
        'dashboard' => 'डैशबोर्ड',
        'settings' => 'सेटिंग्स',
        'account_settings' => 'खाता सेटिंग्स',
        'view_profile' => 'प्रोफाइल देखें',
        'edit' => 'संपादित करें',
        'follow' => 'फॉलो करें',
        'message' => 'संदेश',
        'posts' => 'पोस्ट',
        'followers' => 'फॉलोवर्स',
        'following' => 'फॉलोइंग',
        'about_me' => 'मेरे बारे में',
        'contact_info' => 'संपर्क जानकारी',
        'social_media' => 'सोशल मीडिया',
        'account_details' => 'खाता विवरण',
        'recent_activity' => 'हाल की गतिविधि',
    ]
];

$t = $translations[$language];

// Recent activity data
$recent_activity = [
    ['icon' => 'fas fa-sign-in-alt', 'activity' => 'Logged in', 'time' => '2 hours ago', 'color' => 'success'],
    ['icon' => 'fas fa-user-edit', 'activity' => 'Updated profile information', 'time' => '1 day ago', 'color' => 'primary'],
    ['icon' => 'fas fa-lock', 'activity' => 'Changed password', 'time' => '3 days ago', 'color' => 'warning'],
    ['icon' => 'fas fa-image', 'activity' => 'Uploaded new profile picture', 'time' => '1 week ago', 'color' => 'info'],
    ['icon' => 'fas fa-cog', 'activity' => 'Updated account settings', 'time' => '2 weeks ago', 'color' => 'secondary'],
];

// Dummy stats for profile
$profile_stats = [
    'posts' => 47,
    'followers' => 1284,
    'following' => 362,
];
?>

<!DOCTYPE html>
<html lang="<?php echo $language == 'hi' ? 'hi' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?> - <?php echo $user_data['full_name']; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
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
        
        .profile-cover {
            height: 300px;
            background-image: url('<?php echo $user_data['cover_pic']; ?>');
            background-size: cover;
            background-position: center;
            border-radius: 0 0 15px 15px;
            position: relative;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            position: absolute;
            bottom: -75px;
            left: 50px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.2);
            object-fit: cover;
        }
        
        .profile-header {
            margin-top: 90px;
            padding-bottom: 20px;
        }
        
        .profile-stats {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        
        .profile-action-btn {
            margin-left: 10px;
        }
        
        .sidebar-nav {
            position: sticky;
            top: 20px;
        }
        
        .nav-pills .nav-link {
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 500;
        }
        
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
            background-color: var(--primary-color);
            color: white;
        }
        
        .nav-pills .nav-link:hover:not(.active) {
            background-color: #f8f9fc;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px;
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
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
        }
        
        .github { background-color: #333; }
        .twitter { background-color: #1da1f2; }
        .linkedin { background-color: #0077b5; }
        .website { background-color: var(--primary-color); }
        
        .activity-item {
            border-left: 3px solid;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
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
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-active {
            background-color: rgba(28, 200, 138, 0.2);
            color: var(--success-color);
        }
        
        .badge-role {
            background-color: rgba(78, 115, 223, 0.2);
            color: var(--primary-color);
        }
        
        .avatar-upload {
            position: relative;
            display: inline-block;
        }
        
        .avatar-upload-label {
            cursor: pointer;
        }
        
        .avatar-upload-input {
            display: none;
        }
        
        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .avatar-upload:hover .avatar-overlay {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .profile-cover {
                height: 200px;
            }
            
            .profile-avatar {
                width: 120px;
                height: 120px;
                bottom: -60px;
                left: 50%;
                transform: translateX(-50%);
            }
            
            .profile-header {
                margin-top: 70px;
                text-align: center;
            }
            
            .profile-action-btn {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .sidebar-nav {
                position: static;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-circle me-2 text-primary"></i>
                <?php echo $t['my_profile']; ?>
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
                        <a class="nav-link active" href="profile.php">
                            <i class="fas fa-user me-1"></i>
                            <?php echo $t['my_profile']; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog me-1"></i>
                            <?php echo $t['settings']; ?>
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
    
    <!-- Profile Cover -->
    <div class="profile-cover">
        <!-- Cover image -->
    </div>
    
    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Message Alert -->
        <?php if ($message): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-<?php echo $message_type == 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar-nav">
                    <div class="card">
                        <div class="card-body text-center">
                            <!-- Avatar Upload -->
                            <div class="avatar-upload mb-3">
                                <label for="avatar-input" class="avatar-upload-label">
                                    <img id="profile-avatar-img" src="<?php echo $user_data['profile_pic']; ?>" 
                                         alt="Profile Picture" class="profile-avatar">
                                    <div class="avatar-overlay">
                                        <i class="fas fa-camera fa-2x"></i>
                                    </div>
                                </label>
                                <input type="file" id="avatar-input" class="avatar-upload-input" accept="image/*">
                            </div>
                            
                            <h4 class="mb-1"><?php echo $user_data['full_name']; ?></h4>
                            <p class="text-muted mb-3">@<?php echo $user_data['username']; ?></p>
                            
                            <div class="mb-3">
                                <span class="badge-status badge-active me-2">
                                    <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                    <?php echo ucfirst($user_data['status']); ?>
                                </span>
                                <span class="badge-status badge-role">
                                    <?php echo $user_data['role']; ?>
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2 mb-4">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="fas fa-edit me-1"></i>
                                    <?php echo $t['edit_profile']; ?>
                                </button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-lock me-1"></i>
                                    <?php echo $t['change_password']; ?>
                                </button>
                            </div>
                            
                            <!-- Profile Stats -->
                            <div class="profile-stats">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo $profile_stats['posts']; ?></div>
                                        <div class="stat-label"><?php echo $t['posts']; ?></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo $profile_stats['followers']; ?></div>
                                        <div class="stat-label"><?php echo $t['followers']; ?></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo $profile_stats['following']; ?></div>
                                        <div class="stat-label"><?php echo $t['following']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <div class="card">
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#profile-info" data-bs-toggle="tab">
                                        <i class="fas fa-user-circle me-2"></i>
                                        <?php echo $t['profile_info']; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#account-details" data-bs-toggle="tab">
                                        <i class="fas fa-id-card me-2"></i>
                                        <?php echo $t['account_details']; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#social-media" data-bs-toggle="tab">
                                        <i class="fas fa-share-alt me-2"></i>
                                        <?php echo $t['social_media']; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#recent-activity" data-bs-toggle="tab">
                                        <i class="fas fa-history me-2"></i>
                                        <?php echo $t['recent_activity']; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#preferences" data-bs-toggle="tab">
                                        <i class="fas fa-sliders-h me-2"></i>
                                        <?php echo $t['preferences']; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Profile Information Tab -->
                    <div class="tab-pane fade show active" id="profile-info">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <?php echo $t['about_me']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['full_name']; ?></h6>
                                        <p class="fs-5"><?php echo $user_data['full_name']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['username']; ?></h6>
                                        <p class="fs-5">@<?php echo $user_data['username']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['email']; ?></h6>
                                        <p class="fs-5"><?php echo $user_data['email']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['phone']; ?></h6>
                                        <p class="fs-5"><?php echo $user_data['phone']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['location']; ?></h6>
                                        <p class="fs-5"><?php echo $user_data['location']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['website']; ?></h6>
                                        <p class="fs-5">
                                            <a href="<?php echo $user_data['website']; ?>" target="_blank">
                                                <?php echo $user_data['website']; ?>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted"><?php echo $t['bio']; ?></h6>
                                        <p class="fs-5"><?php echo $user_data['bio']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Details Tab -->
                    <div class="tab-pane fade" id="account-details">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-id-card me-2"></i>
                                    <?php echo $t['account_details']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['joined']; ?></h6>
                                        <p class="fs-5"><?php echo date('F j, Y', strtotime($user_data['joined_date'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['last_login']; ?></h6>
                                        <p class="fs-5"><?php echo date('F j, Y, g:i a', strtotime($user_data['last_login'])); ?></p>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['status']; ?></h6>
                                        <p class="fs-5">
                                            <span class="badge-status badge-active">
                                                <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                                <?php echo ucfirst($user_data['status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted"><?php echo $t['role']; ?></h6>
                                        <p class="fs-5">
                                            <span class="badge-status badge-role">
                                                <?php echo $user_data['role']; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted">Account ID</h6>
                                        <p class="fs-5">USR-<?php echo str_pad($user_data['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Tab -->
                    <div class="tab-pane fade" id="social-media">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-share-alt me-2"></i>
                                    <?php echo $t['social_links']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted mb-3">GitHub</h6>
                                        <?php if ($user_data['github']): ?>
                                        <a href="<?php echo $user_data['github']; ?>" target="_blank" class="social-icon github">
                                            <i class="fab fa-github fa-lg"></i>
                                        </a>
                                        <a href="<?php echo $user_data['github']; ?>" target="_blank" class="text-decoration-none">
                                            <?php echo $user_data['github']; ?>
                                        </a>
                                        <?php else: ?>
                                        <p class="text-muted">Not connected</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted mb-3">Twitter</h6>
                                        <?php if ($user_data['twitter']): ?>
                                        <a href="<?php echo $user_data['twitter']; ?>" target="_blank" class="social-icon twitter">
                                            <i class="fab fa-twitter fa-lg"></i>
                                        </a>
                                        <a href="<?php echo $user_data['twitter']; ?>" target="_blank" class="text-decoration-none">
                                            <?php echo $user_data['twitter']; ?>
                                        </a>
                                        <?php else: ?>
                                        <p class="text-muted">Not connected</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted mb-3">LinkedIn</h6>
                                        <?php if ($user_data['linkedin']): ?>
                                        <a href="<?php echo $user_data['linkedin']; ?>" target="_blank" class="social-icon linkedin">
                                            <i class="fab fa-linkedin fa-lg"></i>
                                        </a>
                                        <a href="<?php echo $user_data['linkedin']; ?>" target="_blank" class="text-decoration-none">
                                            <?php echo $user_data['linkedin']; ?>
                                        </a>
                                        <?php else: ?>
                                        <p class="text-muted">Not connected</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-muted mb-3"><?php echo $t['website']; ?></h6>
                                        <?php if ($user_data['website']): ?>
                                        <a href="<?php echo $user_data['website']; ?>" target="_blank" class="social-icon website">
                                            <i class="fas fa-globe fa-lg"></i>
                                        </a>
                                        <a href="<?php echo $user_data['website']; ?>" target="_blank" class="text-decoration-none">
                                            <?php echo $user_data['website']; ?>
                                        </a>
                                        <?php else: ?>
                                        <p class="text-muted">Not provided</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity Tab -->
                    <div class="tab-pane fade" id="recent-activity">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    <?php echo $t['recent_activity']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recent_activity as $activity): ?>
                                <div class="activity-item border-<?php echo $activity['color']; ?>">
                                    <div class="d-flex">
                                        <div class="activity-icon bg-<?php echo $activity['color']; ?>">
                                            <i class="<?php echo $activity['icon']; ?>"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?php echo $activity['activity']; ?></h6>
                                            <p class="text-muted mb-0"><?php echo $activity['time']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preferences Tab -->
                    <div class="tab-pane fade" id="preferences">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-sliders-h me-2"></i>
                                    <?php echo $t['preferences']; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="preferencesForm">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                                <label class="form-check-label" for="emailNotifications">
                                                    <?php echo $t['notifications']; ?> Email
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="pushNotifications" checked>
                                                <label class="form-check-label" for="pushNotifications">
                                                    Push Notifications
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="newsletterSubscription">
                                                <label class="form-check-label" for="newsletterSubscription">
                                                    Newsletter Subscription
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="languagePreference" class="form-label">Preferred Language</label>
                                                <select class="form-select" id="languagePreference">
                                                    <option value="en" selected>English</option>
                                                    <option value="hi">Hindi</option>
                                                    <option value="es">Spanish</option>
                                                    <option value="fr">French</option>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="timezone" class="form-label">Timezone</label>
                                                <select class="form-select" id="timezone">
                                                    <option value="EST" selected>Eastern Time (EST)</option>
                                                    <option value="CST">Central Time (CST)</option>
                                                    <option value="MST">Mountain Time (MST)</option>
                                                    <option value="PST">Pacific Time (PST)</option>
                                                    <option value="IST">India Standard Time (IST)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Save Preferences
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">
                            <i class="fas fa-user-edit me-2"></i>
                            <?php echo $t['edit_profile']; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label"><?php echo $t['full_name']; ?></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo $user_data['full_name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label"><?php echo $t['email']; ?></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo $user_data['email']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label"><?php echo $t['phone']; ?></label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo $user_data['phone']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label"><?php echo $t['location']; ?></label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo $user_data['location']; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label"><?php echo $t['bio']; ?></label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo $user_data['bio']; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label"><?php echo $t['website']; ?></label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?php echo $user_data['website']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="github" class="form-label">GitHub</label>
                                <input type="url" class="form-control" id="github" name="github" 
                                       value="<?php echo $user_data['github']; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="twitter" class="form-label">Twitter</label>
                                <input type="url" class="form-control" id="twitter" name="twitter" 
                                       value="<?php echo $user_data['twitter']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                       value="<?php echo $user_data['linkedin']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <?php echo $t['cancel']; ?>
                        </button>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            <?php echo $t['save_changes']; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">
                            <i class="fas fa-lock me-2"></i>
                            <?php echo $t['change_password']; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label"><?php echo $t['current_password']; ?></label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label"><?php echo $t['new_password']; ?></label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><?php echo $t['confirm_password']; ?></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <?php echo $t['cancel']; ?>
                        </button>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key me-1"></i>
                            <?php echo $t['update_password']; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to toggle language
        function toggleLanguage() {
            const currentLang = "<?php echo $language; ?>";
            const newLang = currentLang === 'hi' ? 'en' : 'hi';
            window.location.href = `profile.php?lang=${newLang}`;
        }
        
        // Handle avatar upload
        document.getElementById('avatar-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-avatar-img').src = e.target.result;
                    
                    // Show success message
                    showNotification('Profile picture updated successfully!', 'success');
                    
                    // In a real app, you would upload the file to the server here
                    // Example: uploadAvatar(file);
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Handle preferences form submission
        document.getElementById('preferencesForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Show loading
            showNotification('Saving preferences...', 'info');
            
            // Simulate API call
            setTimeout(() => {
                showNotification('Preferences saved successfully!', 'success');
            }, 1500);
        });
        
        // Function to show notifications
        function showNotification(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
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
        
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Activate tab based on URL hash
            const hash = window.location.hash;
            if (hash) {
                const tabTrigger = document.querySelector(`a[href="${hash}"]`);
                if (tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }
            
            // Update URL when tab changes
            const tabTriggers = document.querySelectorAll('a[data-bs-toggle="tab"]');
            tabTriggers.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    window.location.hash = event.target.getAttribute('href');
                });
            });
        });
        
        // Password strength checker
        document.getElementById('new_password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.getElementById('password-strength');
            
            if (!strengthIndicator) {
                const strengthDiv = document.createElement('div');
                strengthDiv.id = 'password-strength';
                strengthDiv.className = 'mt-2';
                this.parentNode.appendChild(strengthDiv);
            }
            
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    feedback = '<span class="text-danger">Weak password</span>';
                    break;
                case 2:
                    feedback = '<span class="text-warning">Fair password</span>';
                    break;
                case 3:
                    feedback = '<span class="text-info">Good password</span>';
                    break;
                case 4:
                    feedback = '<span class="text-success">Strong password</span>';
                    break;
            }
            
            document.getElementById('password-strength').innerHTML = feedback;
        });
    </script>
</body>
</html>