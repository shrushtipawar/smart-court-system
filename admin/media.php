<?php
session_start();
require_once '../config/database.php';
require_once '../config/admin_config.php';

$db = new Database();
requireAdmin();

$action = $_GET['action'] ?? 'list';
$success = '';
$error = '';

// Create uploads directory if it doesn't exist
$upload_dir = ADMIN_UPLOAD_PATH . 'images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $alt_text = $_POST['alt_text'] ?? '';
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload error: ' . $file['error'];
    } elseif ($file['size'] > ADMIN_MAX_FILE_SIZE) {
        $error = 'File too large. Maximum size: ' . (ADMIN_MAX_FILE_SIZE / 1024 / 1024) . 'MB';
    } else {
        // Get file extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, ADMIN_ALLOWED_IMAGE_TYPES)) {
            $error = 'Invalid file type. Allowed: ' . implode(', ', ADMIN_ALLOWED_IMAGE_TYPES);
        } else {
            // Generate unique filename
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $file['name']);
            $filepath = 'images/' . $filename;
            $full_path = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                // Get file info
                $file_size = $file['size'];
                $mime_type = mime_content_type($full_path);
                
                // Insert into database
                try {
                    $stmt = $db->conn->prepare("
                        INSERT INTO media (file_name, file_path, file_type, file_size, mime_type, alt_text, uploaded_by, uploaded_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    
                    $stmt->execute([
                        $file['name'],
                        $filepath,
                        $file_ext,
                        $file_size,
                        $mime_type,
                        $alt_text,
                        $_SESSION['user_id']
                    ]);
                    
                    $success = 'File uploaded successfully';
                    logActivity('MEDIA_UPLOAD', "Uploaded: {$file['name']}");
                    
                } catch (PDOException $e) {
                    $error = 'Error saving file info: ' . $e->getMessage();
                    // Delete uploaded file if database insert failed
                    unlink($full_path);
                }
            } else {
                $error = 'Failed to move uploaded file';
            }
        }
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $file_id = $_GET['delete'];
    
    try {
        // Get file info
        $stmt = $db->conn->prepare("SELECT file_path FROM media WHERE id = ?");
        $stmt->execute([$file_id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file) {
            // Delete from filesystem
            $file_path = ADMIN_UPLOAD_PATH . $file['file_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete from database
            $stmt = $db->conn->prepare("DELETE FROM media WHERE id = ?");
            $stmt->execute([$file_id]);
            
            $success = 'File deleted successfully';
            logActivity('MEDIA_DELETE', "Deleted file ID: $file_id");
        }
        
    } catch (PDOException $e) {
        $error = 'Error deleting file: ' . $e->getMessage();
    }
}

// Get media files
try {
    $search = $_GET['search'] ?? '';
    $type_filter = $_GET['type'] ?? '';
    
    $query = "SELECT m.*, u.username as uploader 
              FROM media m 
              LEFT JOIN users u ON m.uploaded_by = u.id 
              WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (m.file_name LIKE ? OR m.alt_text LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    if (!empty($type_filter)) {
        $query .= " AND m.file_type = ?";
        $params[] = $type_filter;
    }
    
    $query .= " ORDER BY m.uploaded_at DESC";
    
    $stmt = $db->conn->prepare($query);
    $stmt->execute($params);
    $media_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get file type statistics
    $stmt = $db->conn->query("
        SELECT file_type, COUNT(*) as count 
        FROM media 
        GROUP BY file_type 
        ORDER BY count DESC
    ");
    $type_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = 'Error loading media: ' . $e->getMessage();
    $media_files = [];
    $type_stats = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library - <?php echo ADMIN_TITLE; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    
    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .media-item {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            background: white;
        }
        
        .media-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .media-thumbnail {
            height: 150px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .media-thumbnail img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .media-info {
            padding: 15px;
        }
        
        .file-icon {
            font-size: 3rem;
            color: #6c757d;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            border-color: #1a365d;
            background-color: rgba(26, 54, 93, 0.05);
        }
        
        .upload-area.dragover {
            border-color: #0d9d6b;
            background-color: rgba(13, 157, 107, 0.1);
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
                        <i class="fas fa-images me-2"></i>
                        Media Library
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-upload me-2"></i>Upload Files
                        </button>
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
                
                <!-- Statistics -->
                <div class="row mb-4">
                    <?php foreach ($type_stats as $stat): ?>
                        <div class="col-md-3 col-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="text-uppercase text-muted small"><?php echo strtoupper($stat['file_type']); ?></div>
                                    <div class="h3 mb-0"><?php echo $stat['count']; ?></div>
                                    <div class="small">Files</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="col-md-3 col-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="text-uppercase text-muted small">Total</div>
                                <div class="h3 mb-0"><?php echo count($media_files); ?></div>
                                <div class="small">Files</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <input type="hidden" name="action" value="list">
                            
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           placeholder="Search files..." 
                                           value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <select class="form-select" name="type">
                                    <option value="">All File Types</option>
                                    <?php foreach ($type_stats as $stat): ?>
                                        <option value="<?php echo $stat['file_type']; ?>"
                                            <?php echo $type_filter === $stat['file_type'] ? 'selected' : ''; ?>>
                                            <?php echo strtoupper($stat['file_type']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Media Grid -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($media_files)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-images fa-4x text-muted mb-4"></i>
                                <h4>No Media Files</h4>
                                <p class="text-muted">Upload your first file to get started.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-upload me-2"></i>Upload Files
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="media-grid">
                                <?php foreach ($media_files as $file): ?>
                                    <div class="media-item">
                                        <div class="media-thumbnail">
                                            <?php if (in_array($file['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <a href="<?php echo '../uploads/' . $file['file_path']; ?>" 
                                                   data-lightbox="media-gallery" 
                                                   data-title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                                    <img src="<?php echo '../uploads/' . $file['file_path']; ?>" 
                                                         alt="<?php echo htmlspecialchars($file['alt_text']); ?>"
                                                         loading="lazy">
                                                </a>
                                            <?php else: ?>
                                                <div class="file-icon">
                                                    <i class="fas fa-file"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="media-info">
                                            <h6 class="mb-2" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                                <?php echo strlen($file['file_name']) > 20 ? 
                                                    substr($file['file_name'], 0, 20) . '...' : 
                                                    $file['file_name']; ?>
                                            </h6>
                                            <div class="small text-muted mb-2">
                                                <?php echo strtoupper($file['file_type']); ?> • 
                                                <?php echo round($file['file_size'] / 1024, 1); ?> KB
                                            </div>
                                            <?php if ($file['alt_text']): ?>
                                                <div class="small mb-2" title="<?php echo htmlspecialchars($file['alt_text']); ?>">
                                                    <?php echo strlen($file['alt_text']) > 30 ? 
                                                        substr($file['alt_text'], 0, 30) . '...' : 
                                                        $file['alt_text']; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($file['uploader'] ?? 'Unknown'); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('M j, Y', strtotime($file['uploaded_at'])); ?>
                                            </div>
                                            <div class="mt-3 d-flex justify-content-between">
                                                <a href="<?php echo '../uploads/' . $file['file_path']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   target="_blank" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success copy-url"
                                                        data-url="<?php echo '../uploads/' . $file['file_path']; ?>"
                                                        title="Copy URL">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteFile(<?php echo $file['id']; ?>, '<?php echo addslashes($file['file_name']); ?>')"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                    <div class="modal-body">
                        <div class="upload-area" id="dropArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5>Drag & Drop Files Here</h5>
                            <p class="text-muted">or click to browse</p>
                            <input type="file" name="file" id="fileInput" class="d-none" accept="image/*">
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-folder-open me-2"></i>Choose Files
                                </button>
                            </div>
                            <div class="mt-3 small text-muted">
                                Max file size: <?php echo (ADMIN_MAX_FILE_SIZE / 1024 / 1024); ?>MB • 
                                Allowed: <?php echo implode(', ', ADMIN_ALLOWED_IMAGE_TYPES); ?>
                            </div>
                        </div>
                        
                        <div id="filePreview" class="mt-3 d-none">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="fileName"></span> (<span id="fileSize"></span>)
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label for="alt_text" class="form-label">Alt Text (Optional)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="alt_text" 
                                   name="alt_text" 
                                   placeholder="Description for accessibility">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Lightbox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    
    <script>
        // Initialize lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Image %1 of %2'
        });
        
        // File upload handling
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadBtn = document.getElementById('uploadBtn');
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropArea.classList.add('dragover');
        }
        
        function unhighlight() {
            dropArea.classList.remove('dragover');
        }
        
        // Handle dropped files
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                handleFiles(files);
            }
        }
        
        // Handle file input change
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
        
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                
                // Check file size
                if (file.size > <?php echo ADMIN_MAX_FILE_SIZE; ?>) {
                    alert('File too large. Maximum size: <?php echo (ADMIN_MAX_FILE_SIZE / 1024 / 1024); ?>MB');
                    return;
                }
                
                // Check file type
                const allowedTypes = <?php echo json_encode(ADMIN_ALLOWED_IMAGE_TYPES); ?>;
                const fileExt = file.name.split('.').pop().toLowerCase();
                
                if (!allowedTypes.includes(fileExt)) {
                    alert('Invalid file type. Allowed: ' + allowedTypes.join(', '));
                    return;
                }
                
                // Show preview
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.classList.remove('d-none');
                uploadBtn.disabled = false;
            }
        }
        
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' bytes';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            else return (bytes / 1048576).toFixed(1) + ' MB';
        }
        
        // Copy URL to clipboard
        document.querySelectorAll('.copy-url').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                    
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-success');
                    }, 2000);
                });
            });
        });
        
        // Delete file confirmation
        function deleteFile(fileId, fileName) {
            if (confirm('Are you sure you want to delete "' + fileName + '"? This cannot be undone.')) {
                window.location.href = 'media.php?delete=' + fileId;
            }
        }
        
        // Reset form when modal is closed
        const uploadModal = document.getElementById('uploadModal');
        uploadModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('uploadForm').reset();
            filePreview.classList.add('d-none');
            uploadBtn.disabled = true;
        });
    </script>
</body>
</html>