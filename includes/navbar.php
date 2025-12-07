<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'JusticeFlow'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2d74da;
            --accent-color: #0d9d6b;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.3s;
            margin: 0 5px;
            border-radius: 5px;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .badge-notification {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 0.6rem;
            padding: 2px 5px;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .dropdown-item {
            padding: 8px 15px;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
        <div class="container">
            <!-- Brand/Logo -->
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-balance-scale me-2"></i>
                JusticeFlow
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navbar Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">
                            <i class="fas fa-info-circle me-1"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" href="services.php">
                            <i class="fas fa-gavel me-1"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cases.php' ? 'active' : ''; ?>" href="cases.php">
                            <i class="fas fa-folder-open me-1"></i> Cases
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>" href="documents.php">
                            <i class="fas fa-file-alt me-1"></i> Documents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                            <i class="fas fa-envelope me-1"></i> Contact
                        </a>
                    </li>
                </ul>
                
                <!-- Right Side: User/Auth -->
                <div class="navbar-nav align-items-center">
                    <!-- Search -->
                    <form class="d-flex me-3" action="search.php" method="GET" style="min-width: 200px;">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" placeholder="Search..." name="query">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Notifications -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="badge bg-danger badge-notification">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex w-100 justify-content-between">
                                            <small class="text-primary"><i class="fas fa-calendar-check me-1"></i> Court Hearing</small>
                                            <small class="text-muted">5 min ago</small>
                                        </div>
                                        <p class="mb-0 small">Hearing scheduled for Case #1234</p>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex w-100 justify-content-between">
                                            <small class="text-success"><i class="fas fa-file-upload me-1"></i> Document</small>
                                            <small class="text-muted">1 hour ago</small>
                                        </div>
                                        <p class="mb-0 small">New document uploaded by lawyer</p>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="notifications.php">View All</a></li>
                            </ul>
                        </li>
                        
                        <!-- Messages -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-envelope"></i>
                                <span class="badge bg-warning badge-notification">2</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown">
                                <li><h6 class="dropdown-header">Messages</h6></li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex">
                                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=2d74da&color=fff&size=40" 
                                                 class="rounded-circle me-2" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">John Doe</h6>
                                                <small class="text-muted">Regarding case #5678...</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="messages.php">View All Messages</a></li>
                            </ul>
                        </li>
                        
                        <!-- User Profile -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=2d74da&color=fff&size=40'; ?>" 
                                     class="user-avatar me-2" alt="User">
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-user me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="dashboard.php">
                                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="my-cases.php">
                                        <i class="fas fa-folder me-2"></i> My Cases
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="settings.php">
                                        <i class="fas fa-cog me-2"></i> Settings
                                    </a>
                                </li>
                                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-warning" href="admin/">
                                            <i class="fas fa-shield-alt me-2"></i> Admin Panel
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login & Register buttons -->
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm ms-2" href="register.php">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Highlight active nav item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = location.pathname.split('/').pop();
            document.querySelectorAll('.nav-link').forEach(link => {
                if(link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });
        
        // Auto-hide dropdowns on mobile after click
        document.querySelectorAll('.dropdown-menu a').forEach(link => {
            link.addEventListener('click', function() {
                if(window.innerWidth < 992) {
                    const navbarCollapse = document.getElementById('navbarNav');
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            });
        });
    </script>
</body>
</html>