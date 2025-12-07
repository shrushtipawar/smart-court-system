<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$db = new Database();
$auth = new Auth($db);
$auth->requireLogin();

$conn = $db->getConnection();

// Search functionality
$search_results = [];
$search_query = '';

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $keywords = explode(' ', $search_query);
    
    $sql = "SELECT * FROM legal_documents WHERE is_public = 1";
    $params = [];
    
    if(!empty($search_query)) {
        $sql .= " AND (title LIKE ? OR content LIKE ? OR keywords LIKE ?";
        foreach($keywords as $keyword) {
            $sql .= " OR title LIKE ? OR content LIKE ? OR keywords LIKE ?";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        $sql .= ")";
        
        // Add initial parameters
        array_unshift($params, "%$search_query%", "%$search_query%", "%$search_query%");
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all documents
$stmt = $conn->prepare("SELECT * FROM legal_documents WHERE is_public = 1 ORDER BY created_at DESC");
$stmt->execute();
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JusticeFlow - Legal Research</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">AI Legal Research</h1>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                        <i class="fas fa-upload me-2"></i>Upload Document
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Search Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-lg" 
                                           name="search" placeholder="Search precedents, statutes, regulations..." 
                                           value="<?php echo htmlspecialchars($search_query); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    Search
                                </button>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="precedents" checked>
                                    <label class="form-check-label" for="precedents">Precedents</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="statutes" checked>
                                    <label class="form-check-label" for="statutes">Statutes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="regulations" checked>
                                    <label class="form-check-label" for="regulations">Regulations</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="forms">
                                    <label class="form-check-label" for="forms">Forms</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if(!empty($search_query)): ?>
                <div class="alert alert-info">
                    Found <?php echo count($search_results); ?> results for "<?php echo htmlspecialchars($search_query); ?>"
                </div>
                <?php endif; ?>
                
                <!-- AI Analysis Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">AI Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-4 border rounded">
                                    <i class="fas fa-brain fa-3x text-primary mb-3"></i>
                                    <h5>Similar Case Matching</h5>
                                    <p class="text-muted">Find similar cases based on facts and legal issues</p>
                                    <button class="btn btn-outline-primary">Analyze</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4 border rounded">
                                    <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                                    <h5>Statute Analysis</h5>
                                    <p class="text-muted">Track changes and interpretations of statutes</p>
                                    <button class="btn btn-outline-success">Analyze</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4 border rounded">
                                    <i class="fas fa-file-contract fa-3x text-warning mb-3"></i>
                                    <h5>Document Review</h5>
                                    <p class="text-muted">AI-powered review of legal documents</p>
                                    <button class="btn btn-outline-warning">Analyze</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Documents Grid -->
                <div class="row">
                    <?php foreach($documents as $doc): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($doc['document_type']); ?>
                                    </span>
                                    <small class="text-muted">
                                        <i class="far fa-eye me-1"></i><?php echo $doc['view_count']; ?>
                                    </small>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($doc['title']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo substr($doc['content'], 0, 150) . '...'; ?>
                                </p>
                                <?php if($doc['category']): ?>
                                <p class="mb-2">
                                    <small class="text-muted">Category: <?php echo htmlspecialchars($doc['category']); ?></small>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        <?php echo date('M d, Y', strtotime($doc['created_at'])); ?>
                                    </small>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Upload Document Modal -->
    <?php if($_SESSION['role'] == 'admin'): ?>
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Legal Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="upload_document.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Document Type *</label>
                            <select class="form-select" name="document_type" required>
                                <option value="precedent">Precedent</option>
                                <option value="statute">Statute</option>
                                <option value="regulation">Regulation</option>
                                <option value="guideline">Guideline</option>
                                <option value="form">Form</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" name="category">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keywords (comma separated)</label>
                            <input type="text" class="form-control" name="keywords">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload File (optional)</label>
                            <input type="file" class="form-control" name="document_file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>