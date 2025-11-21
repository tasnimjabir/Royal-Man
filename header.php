<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royal Man - Premium Men's Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --dark: #1a1a1a;
            --light: #f8f9fa;
        }
        body { font-family: 'Inter', sans-serif; background: #fff; color: var(--dark); }
        .navbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 2px 20px rgba(0,0,0,0.05); }
        .nav-link { color: #333 !important; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: var(--gold) !important; }
        .btn-gold { background: var(--gold); color: #000; font-weight: 600; border: none; }
        .btn-gold:hover { background: #c19a2e; transform: translateY(-2px); }
        .card { transition: all 0.3s; border: none; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        .text-gold { color: var(--gold); }
        .hero-img { height: 280px; object-fit: cover; border-radius: 12px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top py-3">
    <div class="container">
        <a class="navbar-brand fs-2 fw-bold text-gold" href="/">Royal Man</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-4">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#shop">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="#new">New</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item">
                    <a href="cart.php" class="btn btn-outline-dark position-relative">
                        <i class="bi bi-bag"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>