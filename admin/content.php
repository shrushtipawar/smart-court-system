<?php
session_start();
require_once '../config/database.php';
require_once '../config/admin_config.php';

$db = new Database();
requireAdmin();

$page = $_GET['page'] ?? 'about';
$section = $_GET['section'] ?? '';
$action = $_GET['action'] ?? 'edit';

$success = '';
$error = '';

// Define page sections
$page_sections = [
    'about' => [
        'hero_title' => 'Hero Title',
        'hero_description' => 'Hero Description',
        'mission' => 'Mission Statement',
        'vision' => 'Vision Statement',
        'story' => 'Our Story',
        'team_members' => 'Team Members (JSON)',
        'milestones' => 'Milestones (JSON)',
        'statistics' => 'Statistics (JSON)',
        'values' => 'Our Values (JSON)',
        'cta' => 'Call to Action'
    ],
    'register' => [
        'hero_title' => 'Hero Title',
        'hero_description' => 'Hero Description',
        'terms' => 'Terms & Conditions'
    ],
    'home' => [
        'hero_title' => 'Hero Title',
        'hero_description' => 'Hero Description',
        'features' => 'Features (JSON)',
        'testimonials' => 'Testimonials (JSON)'
    ]
];

// Get content for editing
$content_data = null;
if ($section && isset($page_sections[$page][$section])) {
    try {
        $stmt = $db->conn->prepare("
            SELECT * FROM dynamic_content 
            WHERE page_name = ? AND section_name = ?
        ");
        $stmt->execute([$page, $section]);
        $content_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$content_data) {
            // Initialize with empty content
            $content_data = [
                'page_name' => $page,
                'section_name' => $section,
                'content' => '',
                'content_type' => 'text'
            ];
        }
    } catch (PDOException $e) {
        $error = 'Error loading content: ' . $e->getMessage();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_name = $_POST['page_name'] ?? '';
    $section_name = $_POST['section_name'] ?? '';
    $content = $_POST['content'] ?? '';
    $content_type = $_POST['content_type'] ?? 'text';
    
    if (empty($page_name) || empty($section_name)) {
        $error = 'Page and section names are required';
    } else {
        try {
            // Check if content exists
            $stmt = $db->conn->prepare("
                SELECT id FROM dynamic_content 
                WHERE page_name = ? AND section_name = ?
            ");
            $stmt->execute([$page_name, $section_name]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing content
                $stmt = $db->conn->prepare("
                    UPDATE dynamic_content 
                    SET content = ?, content_type = ?, updated_at = NOW() 
                    WHERE page_name = ? AND section_name = ?
                ");
                $stmt->execute([$content, $content_type, $page_name, $section_name]);
            } else {
                // Insert new content
                $stmt = $db->conn->prepare("
                    INSERT INTO dynamic_content (page_name, section_name, content, content_type, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$page_name, $section_name, $content, $content_type]);
            }
            
            $success = 'Content saved successfully';
            logActivity('CONTENT_UPDATE', "Updated $page_name - $section_name");
            
        } catch (PDOException $e) {
            $error = 'Error saving content: ' . $e->getMessage();
        }
    }
}

// Get all content for current page
$page_contents = [];
try {
    $stmt = $db->conn->prepare("
        SELECT * FROM dynamic_content 
        WHERE page_name = ? 
        ORDER BY display_order, section_name
    ");
    $stmt->execute([$page]);
    $page_contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ignore error if table doesn't exist yet
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - <?php echo ADMIN_TITLE; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CodeMirror for JSON/Code editing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
    
    <style>
        .page-card {
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .page-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .page-card.active {
            border-left: 5px solid #1a365d;
        }
        
        .section-list {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .section-item {
            padding: 10px 15px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .section-item:hover {
            background-color: #f8f9fa;
            border-left-color: #1a365d;
        }
        
        .section-item.active {
            background-color: rgba(26, 54, 93, 0.1);
            border-left-color: #1a365d;
        }
        
        .CodeMirror {
            height: 400px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        
        .json-preview {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
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
                        <i class="fas fa-edit me-2"></i>
                        Dynamic Content Management
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
                    <!-- Pages List -->
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Pages</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($page_sections as $page_key => $page_info): ?>
                                        <a href="content.php?page=<?php echo $page_key; ?>" 
                                           class="list-group-item list-group-item-action <?php echo $page_key == $page ? 'active' : ''; ?>">
                                            <i class="fas fa-file-alt me-2"></i>
                                            <?php echo ucfirst($page_key); ?> Page
                                            <span class="badge bg-primary float-end">
                                                <?php 
                                                $count = 0;
                                                foreach ($page_contents as $content) {
                                                    if ($content['page_name'] == $page_key) $count++;
                                                }
                                                echo $count;
                                                ?>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Page Sections -->
                        <?php if (isset($page_sections[$page])): ?>
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo ucfirst($page); ?> Sections</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="section-list">
                                        <?php foreach ($page_sections[$page] as $section_key => $section_title): ?>
                                            <a href="content.php?page=<?php echo $page; ?>&section=<?php echo $section_key; ?>" 
                                               class="d-block section-item <?php echo $section_key == $section ? 'active' : ''; ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?php echo $section_title; ?></strong>
                                                        <div class="text-muted small"><?php echo $section_key; ?></div>
                                                    </div>
                                                    <?php 
                                                    $has_content = false;
                                                    foreach ($page_contents as $content) {
                                                        if ($content['section_name'] == $section_key) {
                                                            $has_content = true;
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($has_content): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content Editor -->
                    <div class="col-md-9">
                        <?php if ($section && isset($page_sections[$page][$section])): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        Edit: <?php echo $page_sections[$page][$section]; ?>
                                        <small class="text-muted">(<?php echo $section; ?>)</small>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="contentForm">
                                        <input type="hidden" name="page_name" value="<?php echo $page; ?>">
                                        <input type="hidden" name="section_name" value="<?php echo $section; ?>">
                                        
                                        <div class="mb-3">
                                            <label for="content_type" class="form-label">Content Type</label>
                                            <select class="form-select" id="content_type" name="content_type" onchange="updateEditor()">
                                                <option value="text" <?php echo ($content_data['content_type'] ?? 'text') == 'text' ? 'selected' : ''; ?>>Plain Text</option>
                                                <option value="html" <?php echo ($content_data['content_type'] ?? 'text') == 'html' ? 'selected' : ''; ?>>HTML</option>
                                                <option value="json" <?php echo ($content_data['content_type'] ?? 'text') == 'json' ? 'selected' : ''; ?>>JSON</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Content</label>
                                            
                                            <?php if (($content_data['content_type'] ?? 'text') == 'json'): ?>
                                                <textarea class="form-control d-none" id="content" name="content" rows="15"><?php echo htmlspecialchars($content_data['content'] ?? ''); ?></textarea>
                                                <div id="jsonEditor"></div>
                                            <?php else: ?>
                                                <textarea class="form-control" id="content" name="content" rows="15"><?php echo htmlspecialchars($content_data['content'] ?? ''); ?></textarea>
                                            <?php endif; ?>
                                            
                                            <div class="form-text">
                                                <?php if (strpos($section, 'JSON') !== false): ?>
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    This section requires valid JSON format. Use JSONLint to validate.
                                                <?php elseif (($content_data['content_type'] ?? 'text') == 'html'): ?>
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    HTML content is allowed. Use proper HTML tags.
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if (($content_data['content_type'] ?? 'text') == 'json' && !empty($content_data['content'])): ?>
                                            <div class="mb-3">
                                                <label class="form-label">JSON Preview</label>
                                                <div class="json-preview">
                                                    <pre><?php 
                                                    $json = json_decode($content_data['content'], true);
                                                    if ($json) {
                                                        echo htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT));
                                                    } else {
                                                        echo 'Invalid JSON';
                                                    }
                                                    ?></pre>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Save Content
                                            </button>
                                            <a href="content.php?page=<?php echo $page; ?>" class="btn btn-outline-secondary">
                                                Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-edit fa-4x text-muted mb-4"></i>
                                    <h4>Select a section to edit</h4>
                                    <p class="text-muted">
                                        Choose a page from the left, then select a section to edit its content.
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CodeMirror -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
    
    <script>
        let jsonEditor = null;
        
        function initJSONEditor() {
            const textarea = document.getElementById('content');
            if (!textarea) return;
            
            jsonEditor = CodeMirror(document.getElementById('jsonEditor'), {
                value: textarea.value,
                mode: "application/json",
                theme: "dracula",
                lineNumbers: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                indentUnit: 4,
                tabSize: 4
            });
            
            // Update textarea when editor changes
            jsonEditor.on('change', function(cm) {
                textarea.value = cm.getValue();
            });
        }
        
        function updateEditor() {
            const type = document.getElementById('content_type').value;
            const textarea = document.getElementById('content');
            
            if (type === 'json') {
                // Switch to CodeMirror
                textarea.classList.add('d-none');
                
                if (!document.getElementById('jsonEditor').hasChildNodes()) {
                    initJSONEditor();
                } else {
                    jsonEditor.setOption('mode', 'application/json');
                }
                
                document.getElementById('jsonEditor').classList.remove('d-none');
            } else {
                // Switch to textarea
                document.getElementById('jsonEditor').classList.add('d-none');
                textarea.classList.remove('d-none');
                
                if (type === 'html') {
                    // Could add HTML editor here
                }
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (($content_data['content_type'] ?? 'text') == 'json'): ?>
                initJSONEditor();
            <?php endif; ?>
            
            // Form validation for JSON
            document.getElementById('contentForm').addEventListener('submit', function(e) {
                const type = document.getElementById('content_type').value;
                const content = type === 'json' ? jsonEditor.getValue() : document.getElementById('content').value;
                
                if (type === 'json' && content.trim() !== '') {
                    try {
                        JSON.parse(content);
                    } catch (err) {
                        e.preventDefault();
                        alert('Invalid JSON format: ' + err.message);
                        return false;
                    }
                }
                return true;
            });
        });
    </script>
</body>
</html>