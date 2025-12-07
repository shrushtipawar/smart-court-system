<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                   href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cases.php' ? 'active' : ''; ?>" 
                   href="cases.php">
                    <i class="fas fa-folder me-2"></i>
                    Case Management
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'legal_research.php' ? 'active' : ''; ?>" 
                   href="legal_research.php">
                    <i class="fas fa-brain me-2"></i>
                    Legal Research
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dispute_resolution.php' ? 'active' : ''; ?>" 
                   href="dispute_resolution.php">
                    <i class="fas fa-handshake me-2"></i>
                    Dispute Resolution
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'hearings.php' ? 'active' : ''; ?>" 
                   href="hearings.php">
                    <i class="fas fa-gavel me-2"></i>
                    Hearings
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : ''; ?>" 
                   href="documents.php">
                    <i class="fas fa-file-contract me-2"></i>
                    Documents
                </a>
            </li>
            
            <?php if(in_array($_SESSION['role'], ['admin', 'judge'])): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>" 
                   href="analytics.php">
                    <i class="fas fa-chart-line me-2"></i>
                    Analytics
                </a>
            </li>
            <?php endif; ?>
            
            <?php if($_SESSION['role'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" 
                   href="users.php">
                    <i class="fas fa-users me-2"></i>
                    User Management
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Quick Actions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="new_case.php">
                    <i class="fas fa-plus-circle me-2"></i>
                    New Case
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="calendar.php">
                    <i class="fas fa-calendar me-2"></i>
                    Calendar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notifications.php">
                    <i class="fas fa-bell me-2"></i>
                    Notifications
                    <span class="badge bg-danger rounded-pill ms-2">3</span>
                </a>
            </li>
        </ul>
    </div>
</nav>