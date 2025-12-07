<?php
// Prevent direct access
if (!defined('ADMIN_HEADER')) {
    die('Direct access not permitted');
}

// Get admin title from config if not defined
if (!defined('ADMIN_TITLE')) {
    define('ADMIN_TITLE', 'JusticeFlow Admin Panel');
}
?>
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php">
        <i class="fas fa-balance-scale me-2"></i>
        <?php echo ADMIN_TITLE; ?>
    </a>
    
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" 
            data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="w-100"></div>
    
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <div class="dropdown">
                <a href="#" class="nav-link px-3 dropdown-toggle" id="userDropdown" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin'); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item" href="../profile.php">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="../index.php">
                            <i class="fas fa-external-link-alt me-2"></i>View Site
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>