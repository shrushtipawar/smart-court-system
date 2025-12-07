<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root {
        --primary-color: #1a365d;
        --secondary-color: #2d74da;
        --accent-color: #0d9d6b;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fb;
        padding-top: 0;
    }
    
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background: linear-gradient(180deg, var(--primary-color) 0%, #2a4365 100%);
        box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: all 0.3s;
    }
    
    .main-content {
        margin-left: 250px;
        padding: 20px;
        min-height: 100vh;
        transition: all 0.3s;
    }
    
    @media (max-width: 768px) {
        .admin-sidebar {
            margin-left: -250px;
        }
        
        .admin-sidebar.active {
            margin-left: 0;
        }
        
        .main-content {
            margin-left: 0;
        }
    }
    
    .admin-header {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    
    .card-header {
        background: white;
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
        border-radius: 15px 15px 0 0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border: none;
    }
</style>