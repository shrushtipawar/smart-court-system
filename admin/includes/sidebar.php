<?php
// Prevent direct access
if (!defined('ADMIN_SIDEBAR')) {
    die('Direct access not permitted');
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" 
                   href="index.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" 
                   href="users.php">
                    <i class="fas fa-users me-2"></i>
                    Users
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'pages.php' ? 'active' : ''; ?>" 
                   href="pages.php">
                    <i class="fas fa-file-alt me-2"></i>
                    Pages
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'content.php' ? 'active' : ''; ?>" 
                   href="content.php">
                    <i class="fas fa-edit me-2"></i>
                    Dynamic Content
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'media.php' ? 'active' : ''; ?>" 
                   href="media.php">
                    <i class="fas fa-images me-2"></i>
                    Media Library
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Settings</span>
        </h6>
        
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" 
                   href="settings.php">
                    <i class="fas fa-cogs me-2"></i>
                    Site Settings
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="logs.php">
                    <i class="fas fa-history me-2"></i>
                    Activity Logs
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="backup.php">
                    <i class="fas fa-database me-2"></i>
                    Backup & Restore
                </a>
            </li>
        </ul>
        
        <div class="px-3 mt-4">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center py-3">
                    <i class="fas fa-shield-alt fa-2x mb-2"></i>
                    <h6 class="card-title mb-1">Admin Panel</h6>
                    <small class="opacity-75">v<?php echo ADMIN_VERSION; ?></small>
                </div>
            </div>
        </div>
    </div>
</nav>