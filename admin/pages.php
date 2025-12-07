<?php
session_start();
require_once '../config/database.php';
require_once '../config/admin_config.php';

$db = new Database();
requireAdmin();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

$success = '';
$error = '';

// Process actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_action = $_POST['action'] ?? '';
    
    switch ($form_action) {
        case 'add_page':
        case 'edit_page':
            $page_id = $_POST['page_id'] ?? 0;
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $content = $_POST['content'] ?? '';
            $meta_title = trim($_POST['meta_title'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $meta_keywords = trim($_POST['meta_keywords'] ?? '');
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            $template = $_POST['template'] ?? 'default';
            $parent_id = $_POST['parent_id'] ?? null;
            $menu_order = $_POST['menu_order'] ?? 0;
            $show_in_menu = isset($_POST['show_in_menu']) ? 1 : 0;
            
            // Generate slug from title if empty
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            }
            
            try {
                if ($form_action === 'add_page') {
                    $stmt = $db->conn->prepare("
                        INSERT INTO site_pages 
                        (title, slug, content, meta_title, meta_description, meta_keywords, 
                         is_published, template, parent_id, menu_order, show_in_menu, created_by, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    
                    $stmt->execute([
                        $title, $slug, $content, $meta_title, $meta_description, $meta_keywords,
                        $is_published, $template, $parent_id, $menu_order, $show_in_menu,
                        $_SESSION['user_id']
                    ]);
                    
                    $success = 'Page created successfully';
                    logActivity('PAGE_CREATE', "Created page: $title");
                    
                } else {
                    $stmt = $db->conn->prepare("
                        UPDATE site_pages 
                        SET title = ?, slug = ?, content = ?, meta_title = ?, meta_description = ?, 
                            meta_keywords = ?, is_published = ?, template = ?, parent_id = ?, 
                            menu_order = ?, show_in_menu = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $title, $slug, $content, $meta_title, $meta_description, $meta_keywords,
                        $is_published, $template, $parent_id, $menu_order, $show_in_menu,
                        $page_id
                    ]);
                    
                    $success = 'Page updated successfully';
                    logActivity('PAGE_UPDATE', "Updated page: $title");
                }
                
                header("Location: pages.php?action=edit&id=" . ($form_action === 'add_page' ? $db->conn->lastInsertId() : $page_id) . "&success=" . urlencode($success));
                exit;
                
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) { // Duplicate slug
                    $error = 'Page slug already exists';
                } else {
                    $error = 'Error saving page: ' . $e->getMessage();
                }
            }
            break;
            
        case 'delete_page':
            $page_id = $_POST['page_id'] ?? 0;
            
            try {
                // Check if page has children
                $stmt = $db->conn->prepare("SELECT COUNT(*) FROM site_pages WHERE parent_id = ?");
                $stmt->execute([$page_id]);
                $has_children = $stmt->fetchColumn() > 0;
                
                if ($has_children) {
                    $error = 'Cannot delete page that has sub-pages';
                } else {
                    $stmt = $db->conn->prepare("DELETE FROM site_pages WHERE id = ?");
                    $stmt->execute([$page_id]);
                    
                    $success = 'Page deleted successfully';
                    logActivity('PAGE_DELETE', "Deleted page ID: $page_id");
                    
                    header('Location: pages.php?success=' . urlencode($success));
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error deleting page: ' . $e->getMessage();
            }
            break;
    }
}

// Get page data for edit
$page = null;
$parent_pages = [];
if ($action === 'edit' && $id) {
    try {
        $stmt = $db->conn->prepare("SELECT * FROM site_pages WHERE id = ?");
        $stmt->execute([$id]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$page) {
            $error = 'Page not found';
            $action = 'list';
        }
    } catch (PDOException $e) {
        $error = 'Error fetching page: ' . $e->getMessage();
        $action = 'list';
    }
}

// Get parent pages for dropdown
try {
    $stmt = $db->conn->query("
        SELECT id, title, slug 
        FROM site_pages 
        WHERE parent_id IS NULL 
        ORDER BY menu_order, title
    ");
    $parent_pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist yet
}

// Get all pages for list view
if ($action === 'list') {
    try {
        $stmt = $db->conn->query("
            SELECT p.*, 
                   (SELECT COUNT(*) FROM site_pages WHERE parent_id = p.id) as child_count,
                   u.username as author
            FROM site_pages p
            LEFT JOIN users u ON p.created_by = u.id
            ORDER BY menu_order, title
        ");
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize pages hierarchically
        function buildPageTree($pages, $parent_id = null) {
            $tree = [];
            foreach ($pages as $page) {
                if ($page['parent_id'] == $parent_id) {
                    $page['children'] = buildPageTree($pages, $page['id']);
                    $tree[] = $page;
                }
            }
            return $tree;
        }
        
        $page_tree = buildPageTree($pages);
        
    } catch (PDOException $e) {
        $error = 'Error fetching pages: ' . $e->getMessage();
        $pages = [];
        $page_tree = [];
    }
}

// Show success/error messages from URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Management - <?php echo ADMIN_TITLE; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    
    <style>
        .page-item {
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 10px;
            background: white;
        }
        
        .page-item:hover {
            background-color: #f8f9fa;
        }
        
        .page-item .page-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .page-item:hover .page-actions {
            opacity: 1;
        }
        
        .page-children {
            margin-left: 30px;
            border-left: 2px dashed #dee2e6;
            padding-left: 15px;
        }
        
        .badge-published {
            background-color: #0d9d6b;
        }
        
        .badge-draft {
            background-color: #f59e0b;
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
                        <i class="fas fa-file-alt me-2"></i>
                        <?php echo $action === 'add' ? 'Add New Page' : 
                               ($action === 'edit' ? 'Edit Page' : 'Page Management'); ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?php if ($action === 'list'): ?>
                            <a href="pages.php?action=add" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Add Page
                            </a>
                        <?php else: ?>
                            <a href="pages.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        <?php endif; ?>
                    </div>
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
                
                <!-- Content based on action -->
                <?php if ($action === 'add' || $action === 'edit'): ?>
                    <!-- Add/Edit Page Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="" id="pageForm">
                                <input type="hidden" name="action" value="<?php echo $action === 'add' ? 'add_page' : 'edit_page'; ?>">
                                <?php if ($action === 'edit' && $page): ?>
                                    <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Page Title *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="title" 
                                                   name="title" 
                                                   value="<?php echo htmlspecialchars($page['title'] ?? ''); ?>" 
                                                   required
                                                   onkeyup="updateSlug()">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="slug" class="form-label">URL Slug *</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="slug" 
                                                   name="slug" 
                                                   value="<?php echo htmlspecialchars($page['slug'] ?? ''); ?>" 
                                                   required>
                                            <div class="form-text">Page URL: /page/<span id="slugPreview"><?php echo htmlspecialchars($page['slug'] ?? ''); ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="parent_id" class="form-label">Parent Page</label>
                                            <select class="form-select" id="parent_id" name="parent_id">
                                                <option value="">No Parent (Top Level)</option>
                                                <?php foreach ($parent_pages as $parent): ?>
                                                    <?php if ($action === 'edit' && $page && $parent['id'] == $page['id']) continue; ?>
                                                    <option value="<?php echo $parent['id']; ?>"
                                                        <?php echo ($page['parent_id'] ?? '') == $parent['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($parent['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="template" class="form-label">Template</label>
                                            <select class="form-select" id="template" name="template">
                                                <option value="default" <?php echo ($page['template'] ?? 'default') == 'default' ? 'selected' : ''; ?>>Default</option>
                                                <option value="full-width" <?php echo ($page['template'] ?? 'default') == 'full-width' ? 'selected' : ''; ?>>Full Width</option>
                                                <option value="sidebar-left" <?php echo ($page['template'] ?? 'default') == 'sidebar-left' ? 'selected' : ''; ?>>Sidebar Left</option>
                                                <option value="sidebar-right" <?php echo ($page['template'] ?? 'default') == 'sidebar-right' ? 'selected' : ''; ?>>Sidebar Right</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="menu_order" class="form-label">Menu Order</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="menu_order" 
                                                   name="menu_order" 
                                                   value="<?php echo htmlspecialchars($page['menu_order'] ?? 0); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Page Content</label>
                                    <textarea class="form-control" 
                                              id="content" 
                                              name="content" 
                                              rows="15"><?php echo htmlspecialchars($page['content'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="meta_title" 
                                                   name="meta_title" 
                                                   value="<?php echo htmlspecialchars($page['meta_title'] ?? ''); ?>">
                                            <div class="form-text">Title for search engines (50-60 characters)</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control" 
                                                      id="meta_description" 
                                                      name="meta_description" 
                                                      rows="3"><?php echo htmlspecialchars($page['meta_description'] ?? ''); ?></textarea>
                                            <div class="form-text">Description for search engines (150-160 characters)</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="meta_keywords" 
                                           name="meta_keywords" 
                                           value="<?php echo htmlspecialchars($page['meta_keywords'] ?? ''); ?>">
                                    <div class="form-text">Comma-separated keywords</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_published" 
                                               name="is_published"
                                               <?php echo ($page['is_published'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_published">
                                            Publish this page
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="show_in_menu" 
                                               name="show_in_menu"
                                               <?php echo ($page['show_in_menu'] ?? 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="show_in_menu">
                                            Show in navigation menu
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?php echo $action === 'add' ? 'Create Page' : 'Update Page'; ?>
                                    </button>
                                </div>
                            </form>
                            
                            <?php if ($action === 'edit' && $page): ?>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Page Information</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Created</span>
                                                <span><?php echo date('M j, Y', strtotime($page['created_at'])); ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Last Updated</span>
                                                <span><?php echo date('M j, Y', strtotime($page['updated_at'])); ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Author</span>
                                                <span><?php echo htmlspecialchars($page['author'] ?? 'Unknown'); ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5>Page Actions</h5>
                                        <div class="d-grid gap-2">
                                            <a href="../<?php echo $page['slug']; ?>" 
                                               class="btn btn-outline-primary" 
                                               target="_blank">
                                                <i class="fas fa-external-link-alt me-2"></i>View Page
                                            </a>
                                            
                                            <form method="POST" action="" onsubmit="return confirm('Delete this page? This cannot be undone.');">
                                                <input type="hidden" name="action" value="delete_page">
                                                <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="fas fa-trash me-2"></i>Delete Page
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Page List -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($pages)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-file-alt fa-4x text-muted mb-4"></i>
                                    <h4>No Pages Found</h4>
                                    <p class="text-muted">Create your first page to get started.</p>
                                    <a href="pages.php?action=add" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Create First Page
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Render page tree -->
                                <?php function renderPageTree($pages, $level = 0) { ?>
                                    <?php foreach ($pages as $page): ?>
                                        <div class="page-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <?php if ($level > 0): ?>
                                                        <span class="me-2" style="width: <?php echo $level * 20; ?>px"></span>
                                                    <?php endif; ?>
                                                    <i class="fas fa-file-alt text-muted me-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <?php echo htmlspecialchars($page['title']); ?>
                                                            <span class="badge <?php echo $page['is_published'] ? 'badge-published' : 'badge-draft'; ?> ms-2">
                                                                <?php echo $page['is_published'] ? 'Published' : 'Draft'; ?>
                                                            </span>
                                                            <?php if ($page['child_count'] > 0): ?>
                                                                <span class="badge bg-info ms-1">
                                                                    <?php echo $page['child_count']; ?> children
                                                                </span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <small class="text-muted">
                                                            /<?php echo htmlspecialchars($page['slug']); ?> • 
                                                            <?php echo date('M j, Y', strtotime($page['created_at'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="page-actions">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="pages.php?action=edit&id=<?php echo $page['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="../<?php echo $page['slug']; ?>" 
                                                           class="btn btn-outline-success" 
                                                           target="_blank" 
                                                           title="View">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger" 
                                                                title="Delete"
                                                                onclick="deletePage(<?php echo $page['id']; ?>, '<?php echo addslashes($page['title']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (!empty($page['children'])): ?>
                                            <div class="page-children">
                                                <?php renderPageTree($page['children'], $level + 1); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php } ?>
                                
                                <?php renderPageTree($page_tree); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- TinyMCE Initialization -->
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 400,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px }'
        });
        
        // Auto-generate slug from title
        function updateSlug() {
            const title = document.getElementById('title').value;
            const slugInput = document.getElementById('slug');
            const slugPreview = document.getElementById('slugPreview');
            
            if (slugInput.value === '' || slugInput.value === slugPreview.textContent) {
                let slug = title.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/--+/g, '-')
                    .trim();
                
                slugInput.value = slug;
                slugPreview.textContent = slug;
            }
        }
        
        // Delete page confirmation
        function deletePage(pageId, title) {
            if (confirm('Are you sure you want to delete page "' + title + '"? This cannot be undone.')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                var actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_page';
                form.appendChild(actionInput);
                
                var pageIdInput = document.createElement('input');
                pageIdInput.type = 'hidden';
                pageIdInput.name = 'page_id';
                pageIdInput.value = pageId;
                form.appendChild(pageIdInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>