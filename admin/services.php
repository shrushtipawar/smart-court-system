<?php
session_start();
require_once '../config/database.php';
require_once '../config/admin_config.php';

$db = new Database();
requireAdmin();

$category = $_GET['category'] ?? 'general';
$success = '';
$error = '';

// Get all settings
try {
    $stmt = $db->conn->query("SELECT * FROM admin_settings ORDER BY category, setting_key");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize by category
    $settings_by_category = [];
    foreach ($settings as $setting) {
        $settings_by_category[$setting['category']][] = $setting;
    }
} catch (PDOException $e) {
    $error = 'Error loading settings: ' . $e->getMessage();
    $settings_by_category = [];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings_data = $_POST['settings'] ?? [];
    
    try {
        $db->conn->beginTransaction();
        
        foreach ($settings_data as $key => $value) {
            updateAdminSetting($key, $value);
        }
        
        $db->conn->commit();
        $success = 'Settings updated successfully';
        logActivity('SETTINGS_UPDATE', "Updated $category settings");
        
        // Refresh settings
        $stmt = $db->conn->query("SELECT * FROM admin_settings ORDER BY category, setting_key");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings_by_category = [];
        foreach ($settings as $setting) {
            $settings_by_category[$setting['category']][] = $setting;
        }
        
    } catch (PDOException $e) {
        $db->conn->rollBack();
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - <?php echo ADMIN_TITLE; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Color picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.min.css">
    
    <style>
        .settings-nav .nav-link {
            border-radius: 0;
            border-left: 3px solid transparent;
        }
        
        .settings-nav .nav-link.active {
            border-left-color: #1a365d;
            background-color: rgba(26, 54, 93, 0.1);
        }
        
        .setting-group {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background: white;
        }
        
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php define('ADMIN_HEADER', true); include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php define('ADMIN_SIDEBAR', true); include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-cogs me-2"></i>
                        Site Settings
                    </h1>
                </div>
                
                <!-- Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Settings Navigation -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body p-0">
                                <nav class="nav flex-column settings-nav">
                                    <a class="nav-link <?php echo $category == 'general' ? 'active' : ''; ?>" 
                                       href="settings.php?category=general">
                                        <i class="fas fa-globe me-2"></i>General
                                    </a>
                                    <a class="nav-link <?php echo $category == 'design' ? 'active' : ''; ?>" 
                                       href="settings.php?category=design">
                                        <i class="fas fa-palette me-2"></i>Design
                                    </a>
                                    <a class="nav-link <?php echo $category == 'contact' ? 'active' : ''; ?>" 
                                       href="settings.php?category=contact">
                                        <i class="fas fa-address-book me-2"></i>Contact
                                    </a>
                                    <a class="nav-link <?php echo $category == 'social' ? 'active' : ''; ?>" 
                                       href="settings.php?category=social">
                                        <i class="fas fa-share-alt me-2"></i>Social Media
                                    </a>
                                    <a class="nav-link <?php echo $category == 'system' ? 'active' : ''; ?>" 
                                       href="settings.php?category=system">
                                        <i class="fas fa-server me-2"></i>System
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Settings Form -->
                    <div class="col-md-9">
                        <form method="POST" action="" id="settingsForm">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <?php echo ucfirst($category); ?> Settings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($settings_by_category[$category])): ?>
                                        <?php foreach ($settings_by_category[$category] as $setting): ?>
                                            <div class="mb-3">
                                                <label for="setting_<?php echo $setting['setting_key']; ?>" 
                                                       class="form-label">
                                                    <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                                                </label>
                                                
                                                <?php if ($setting['setting_type'] == 'textarea'): ?>
                                                    <textarea class="form-control" 
                                                              id="setting_<?php echo $setting['setting_key']; ?>" 
                                                              name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                              rows="4"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                                
                                                <?php elseif ($setting['setting_type'] == 'boolean'): ?>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               role="switch"
                                                               id="setting_<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]"
                                                               value="1"
                                                               <?php echo $setting['setting_value'] ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="setting_<?php echo $setting['setting_key']; ?>">
                                                            <?php echo $setting['setting_value'] ? 'Enabled' : 'Disabled'; ?>
                                                        </label>
                                                    </div>
                                                
                                                <?php elseif ($setting['setting_type'] == 'color'): ?>
                                                    <div class="input-group colorpicker-component">
                                                        <span class="input-group-text">
                                                            <div class="color-preview" 
                                                                 style="background-color: <?php echo $setting['setting_value']; ?>"></div>
                                                        </span>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="setting_<?php echo $setting['setting_key']; ?>" 
                                                               name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-eye-dropper"></i>
                                                        </span>
                                                    </div>
                                                
                                                <?php else: ?>
                                                    <input type="<?php echo $setting['setting_type'] == 'email' ? 'email' : 'text'; ?>" 
                                                           class="form-control" 
                                                           id="setting_<?php echo $setting['setting_key']; ?>" 
                                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                                <?php endif; ?>
                                                
                                                <?php if ($setting['setting_type'] == 'url'): ?>
                                                    <div class="form-text">
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        <?php echo $setting['setting_value'] ? 
                                                            '<a href="' . htmlspecialchars($setting['setting_value']) . '" target="_blank">Visit URL</a>' : 
                                                            'Enter full URL including https://'; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Save Changes
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="fas fa-undo me-2"></i>Reset
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-cogs fa-4x text-muted mb-4"></i>
                                            <h4>No Settings Found</h4>
                                            <p class="text-muted">No settings available for this category.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Color Picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
    
    <script>
        // Initialize color pickers
        $(document).ready(function() {
            $('.colorpicker-component').colorpicker();
            
            // Update color preview
            $('.colorpicker-component').on('colorpickerChange', function(event) {
                $(this).find('.color-preview').css('background-color', event.color.toString());
            });
        });
        
        // Confirm before leaving page with unsaved changes
        let formChanged = false;
        document.getElementById('settingsForm').addEventListener('change', function() {
            formChanged = true;
        });
        
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
        
        // Reset confirmation
        document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
            if (!confirm('Reset all changes in this form?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>