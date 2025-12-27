<?php
require 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royal Man - Premium Men's Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .navbar { transition: all 0.3s; }
        .navbar.scrolled { background-color: rgba(255,255,255,0.98) !important; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        #searchOverlay { transition: opacity 0.4s ease; opacity: 0; visibility: hidden; }
        #searchOverlay.active { opacity: 1; visibility: visible; }
        #searchInput:focus { outline: none; box-shadow: none; }
        
        /* Desktop Navbar Layout */
        .navbar-flex-desktop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        
        .navbar-brand-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        
        .navbar-menu {
            flex: 1;
            display: flex;
            justify-content: center;
            margin: 0 auto;
        }
        
        .navbar-actions {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
        }
        
        /* Mobile Navbar Layout */
        @media (max-width: 991.98px) {
            .navbar-flex-desktop {
                flex-wrap: wrap;
            }
            
            .navbar-toggler-wrapper {
                order: 1;
            }
            
            .navbar-brand-wrapper {
                order: 2;
                flex: 0 0 auto;
            }
            
            .navbar-actions-mobile {
                order: 3;
                flex: 0 0 auto;
                display: flex !important;
                gap: 0.5rem;
            }
            
            .navbar-menu {
                display: none !important;
            }
        }
        
        /* Dropdown Hover/Click */
        .dropdown:hover > .dropdown-menu {
            display: block;
        }
        
        .dropdown.show > .dropdown-menu {
            display: block;
        }
        
        .dropdown-menu {
            animation: slideDown 0.2s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Mobile dropdown toggle */
        @media (max-width: 991.98px) {
            .dropdown-toggle::after {
                transition: transform 0.3s ease;
            }
            
            .dropdown-toggle[aria-expanded="true"]::after {
                transform: rotate(180deg);
            }
        }
        
        /* Search and Cart Buttons */
        .search-cart-btn {
            border: none;
            background: none;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-cart-btn:hover {
            color: #666;
        }
        @media (min-width: 992px) {
        .navbar-expand-lg .navbar-collapse {
                 display: none !important;
                flex-basis: auto;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top py-3 bg-white">
    <div class="container-fluid">
        <div class="navbar-flex-desktop w-100">
            
            <!-- Mobile Toggler Left -->
            <div class="navbar-toggler-wrapper d-lg-none">
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Logo Center -->
            <div class="navbar-brand-wrapper">
                <a class="navbar-brand fs-2 fw-bold" href="/">Royal Man</a>
            </div>

            <!-- Desktop Menu Center -->
            <div class="navbar-menu d-none d-lg-flex">
                <ul class="navbar-nav align-items-center gap-4">
                    <li class="nav-item">
                        <a class="nav-link fw-500" href="/">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-500" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <?php
                            $cats = $pdo->query("SELECT name, slug FROM categories ORDER BY name")->fetchAll();
                            foreach ($cats as $c):
                            ?>
                                <li><a class="dropdown-item py-2" href="/category.php?slug=<?= htmlspecialchars($c['slug']) ?>">
                                    <?= htmlspecialchars($c['name']) ?>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-500" href="products">Shop</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-500" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Search and Cart Right -->
            <div class="navbar-actions-mobile d-lg-none">
                <button id="searchBtnMobile" class="search-cart-btn text-dark">
                    <i class="bi bi-search fs-5"></i>
                </button>
                <a href="cart.php" class="btn btn-link text-dark p-0 m-1 position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        <?= array_sum(array_column($_SESSION['cart'] ?? [], 'quantity')) ?>
                    </span>
                </a>
            </div>

            <!-- Desktop Actions Right -->
            <div class="navbar-actions d-none d-lg-flex">
                <button id="searchBtnDesktop" class="search-cart-btn text-dark">
                    <i class="bi bi-search fs-5"></i>
                </button>
                <a href="cart.php" class="btn btn-link text-dark p-0 position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        <?= array_sum(array_column($_SESSION['cart'] ?? [], 'quantity')) ?>
                    </span>
                </a>
            </div>
        </div>

        <!-- Mobile Collapsed Menu -->
        <div class="collapse navbar-collapse d-lg-none" id="navbarNav">
            <ul class="navbar-nav w-100 mt-3">
                <li class="nav-item">
                    <a class="nav-link fw-500 py-2" href="/">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-500 py-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu border-0">
                        <?php
                        $cats = $pdo->query("SELECT name, slug FROM categories ORDER BY name")->fetchAll();
                        foreach ($cats as $c):
                        ?>
                            <li><a class="dropdown-item py-2" href="category.php?slug=<?= htmlspecialchars($c['slug']) ?>">
                                <?= htmlspecialchars($c['name']) ?>
                            </a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-500 py-2" href="products">Shop</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-500" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
            </ul>
        </div>

        <!-- Search Overlay -->
        <div id="searchOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex align-items-center justify-content-center" style="z-index: 1050;">
            <div class="w-100 px-4">
                <div class="input_text d-flex align-items-center px-2 justify-content-center gap-3">
                    <i class="bi bi-search text-dark fs-3"></i>
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="form-control form-control-lg border-0 shadow-none fs-3" 
                        placeholder="Search T-shirts, Hoodies, Jackets..." 
                        autofocus
                        style="max-width: 600px;">
                    <button id="closeSearch" class="btn btn-link text-dark ms-3 p-0">
                        <i class="bi bi-x-lg fs-3"></i>
                    </button>
                </div>
                <div class="mt-4 text-center text-muted" id="searchHint">
                    Start typing to search products...
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
// Search Overlay Toggle
function openSearch() {
    document.getElementById('searchOverlay').classList.add('active');
    document.getElementById('searchInput').focus();
    document.body.style.overflow = 'hidden';
}

function closeSearch() {
    document.getElementById('searchOverlay').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Event Listeners
document.getElementById('searchBtnDesktop')?.addEventListener('click', openSearch);
document.getElementById('searchBtnMobile')?.addEventListener('click', openSearch);
document.getElementById('closeSearch').addEventListener('click', closeSearch);

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSearch();
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Update cart badge
document.addEventListener('DOMContentLoaded', function() {
    updateCartBadge();
    
    // Search functionality - Enter key
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    closeSearch();
                    window.location.href = '/search.php?q=' + encodeURIComponent(query);
                }
            }
        });
    }
    
    // Hover effect for desktop dropdowns
    if (window.innerWidth > 991) {
        const dropdowns = document.querySelectorAll('.navbar-menu .dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('mouseenter', function() {
                const toggle = this.querySelector('[data-bs-toggle="dropdown"]');
                const menu = this.querySelector('.dropdown-menu');
                if (toggle && menu) {
                    toggle.click();
                }
            });
            dropdown.addEventListener('mouseleave', function() {
                const toggle = this.querySelector('[data-bs-toggle="dropdown"]');
                const menu = this.querySelector('.dropdown-menu');
                if (toggle && menu && menu.classList.contains('show')) {
                    toggle.click();
                }
            });
        });
    }
});

function updateCartBadge() {
    const badge = document.querySelector('.badge.bg-danger');
    if (badge) {
        badge.textContent = '<?= array_sum(array_column($_SESSION['cart'] ?? [], 'quantity')) ?>';
    }
}

// Handle window resize to reinitialize dropdowns
window.addEventListener('resize', function() {
    if (window.innerWidth > 991) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu && menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        });
    }
});
</script>

</body>
</html>