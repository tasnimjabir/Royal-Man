<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royal Man Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #d4af37; --secondary: #1a1a1a; --accent: #28a745; --danger: #dc3545; }
        body { font-family: 'Inter', sans-serif; background: #f4f7fc; color: var(--secondary);}
        .sidebar { background: #fff; border-right: 1px solid #e9ecef; height: 100vh; position: fixed; width: 250px; padding-top: 60px; }
        .sidebar .nav-link { color: var(--secondary); font-weight: 500; padding: 15px 20px; border-radius: 8px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #f8f9fa; color: var(--primary); }
        .main-content { margin-left: 250px; padding: 80px 30px; min-height:800px }
        .admin-header { background: #fff; border-bottom: 1px solid #e9ecef; position: fixed; width: 100%; z-index: 1000; padding: 15px 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; }
        .card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: #c19a2e; }
        .btn-accent { background: var(--accent); color: white; }
        .table th { background: #f8f9fa; }
        .badge-primary { background: var(--primary); color: #000; }
    </style>
</head>
<body>
<header class="admin-header">
    <div class="container d-flex align-items-center justify-content-between">
        <h4 class="mb-0 text-primary fw-bold">Royal Man <span class="text-secondary">Admin</span></h4>
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
</header>

<nav class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= $page == 'products' ? 'active' : '' ?>" href="products.php"><i class="bi bi-box-seam me-2"></i> Products</a></li>
        <li class="nav-item"><a class="nav-link <?= $page == 'categories' ? 'active' : '' ?>" href="categories.php"><i class="bi bi-tags me-2"></i> Categories</a></li>
        <li class="nav-item"><a class="nav-link <?= $page == 'customers' ? 'active' : '' ?>" href="customers.php"><i class="bi bi-people me-2"></i> Customers</a></li>
        <li class="nav-item"><a class="nav-link <?= $page == 'settings' ? 'active' : '' ?>" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a></li>
    </ul>
</nav>

<div class="main-content">