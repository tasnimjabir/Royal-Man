<?php 
session_start();
include 'header.php'; 
$featured = $pdo->query("SELECT name, slug, main_image FROM products ORDER BY created_at DESC LIMIT 4")->fetchAll();

// Fetch all products for grid
$products = $pdo->query("
    SELECT p.id, p.name, p.slug, p.price, p.main_image, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
")->fetchAll();
?>
<?php
// Put this at the very top of your index.php (before any HTML output)

// 1. Get visitor's real IP (works behind Cloudflare, proxies, etc.)
function getVisitorIP() {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip_list = explode(',', $_SERVER[$key]);
            $ip = trim($ip_list[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

$ip = getVisitorIP();

// 2. Get location from IP using free API (ip-api.com - no key needed, 45 req/min limit)
function getLocationFromIP($ip) {
    // Skip private & local IPs
    if (in_array($ip, ['127.0.0.1', '::1']) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_PRIVATE)) {
        return "Local/Private IP";
    }

    $url = "http://ip-api.com/json/{$ip}?fields=country,city,regionName,isp,timezone";
    $data = @file_get_contents($url);

    if ($data === false) return "Location lookup failed";

    $json = json_decode($data, true);
    if ($json && $json['status'] === 'success') {
        return trim("{$json['country']} • {$json['city']} • {$json['regionName']} • {$json['isp']}");
    }
    return "Unknown location";
}

$location = getLocationFromIP($ip);

// 3. Prepare log line
$date      = date('Y-m-d H:i:s');
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$page      = $_SERVER['REQUEST_URI'];
$logLine   = "[$date] IP: $ip | Location: $location | Page: $page | Agent: $userAgent" . PHP_EOL;

// 4. Save to text file (creates file if not exists)
$file = __DIR__ . '/visitor-log.txt';   // Saves in same folder as index.php
file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);
?>
<!-- HERO SECTION -->
<?php if(false):?>
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold text-dark mb-4">ROYAL MAN</h1>
                <p class="lead fs-2 text-muted mb-4">Dress Like a King</p>
                <p class="fs-5 text-secondary mb-5">Premium cotton T-shirts, hoodies & jackets made for the modern Bangladeshi man.</p>
                <a href="#shop" class="btn btn-accent btn-lg px-5 py-3 shadow">Shop Collection</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <?php foreach($featured as $f): ?>
                    <div class="col-6">
                        <a href="product-view/<?= htmlspecialchars($f['slug']) ?>">
                            <img src="uploads/products/<?= htmlspecialchars($f['main_image']) ?>" 
                                 class="img-fluid hero-img hover-shadow" 
                                 alt="<?= htmlspecialchars($f['name']) ?>">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif ?>

<?php 
require_once 'includes/db.php'; 

// index.php - Updated with Carousel Slider

// Fetch active carousel items
$carousels = $pdo->query("SELECT * FROM carousels WHERE active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
?>


<style>
    /* Default (Desktop) Styles */
    .hero-slide {
        height: 90vh;
        background-position: center;
        background-size: cover;
        position: relative;
    }
    
    .hero-title {
        font-weight: 700;
        font-size: 3.5rem; /* Large size for desktop */
    }
    
    .hero-subtitle {
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }

    /* Mobile Styles (Screen width less than 768px) */
    @media (max-width: 768px) {
        .hero-slide {
            height: 60vh; /* Smaller height for mobile */
        }
        
        .hero-title {
            font-size: 2rem; /* Significantly smaller text */
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }

        /* Adjust button size for mobile */
        .btn-accent {
                padding: 0.5rem 1.5rem !important;
                font-size: 1rem !important;
            }
        .btn-accent: hover {
            background-color: #c19a2e;
        }
    }
</style>

<section class="carousel-section m-3 rounded">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <?php if (empty($carousels)): ?>
                <div class="carousel-item active hero-slide" style="background-image: url('https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1600');">
                    <div class="container h-100 d-flex align-items-center text-white">
                        <div class="col-12 col-md-8">
                            <h1 class="hero-title">Royal Man</h1>
                            <p class="hero-subtitle">Dress Like Royalty</p>
                            <a href="#shop" class="btn btn-accent btn-lg">Shop Now</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($carousels as $key => $c): ?>
                <div class="carousel-item <?= $key === 0 ? 'active' : '' ?> hero-slide rounded" 
                     style="background-image: url('uploads/carousels/<?= htmlspecialchars($c['image']) ?>');">
                    <div class="container h-100 d-flex align-items-center">
                        <div class="col-12 col-md-8">
                            <h1 class="hero-title"><?= htmlspecialchars($c['big_text']) ?></h1>
                            <p class="hero-subtitle"><?= htmlspecialchars($c['small_text']) ?></p>
                            <a href="<?= htmlspecialchars($c['button_link']) ?>" class="btn btn-accent btn-lg"><?= htmlspecialchars($c['button_text']) ?></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- Rest of your index.php content (e.g., featured products) -->


<!-- ALL PRODUCTS -->
<section id="shop" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark">Our Collection</h2>
            <p class="text-muted fs-5">Handpicked premium wear for you</p>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach($products as $p): ?>
            <div class="col">
                <div class="card h-100 border-0 overflow-hidden">
                    <a href="product-view.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="text-decoration-none">
                        <div class="position-relative overflow-hidden">
                               <img src="uploads/products/<?= htmlspecialchars($p['main_image']) ?>" 
                                   class="card-img-top product-img product-card-img" 
                                   alt="<?= htmlspecialchars($p['name']) ?>">
                            <!-- <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-accent text-dark">New</span>
                            </div> -->
                        </div>
                        <div class="card-body text-center p-4">
                            <h6 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($p['name']) ?></h6>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($p['category_name']) ?></p>
                            <p class="fs-5 text-accent">Tk <?= number_format($p['price'], 0) ?></p>
                        </div>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Two styles section: Top Trending & Your Style -->

<section class="two-style-section py-5">
    <div class="container py-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center style-card bg-white rounded shadow-sm" style="background: url(uploads/carousels/1765302288.jpg) no-repeat center/cover; padding:100px 30px">
                    <div class="style-text me-4">
                        <p class="text-muted mb-3">TOP COLLECTIONS</p>
                        <h3 class="fw-bold">Top Trending Style</h3>
                        <br>
                        <a href="products.php" class="btn btn-accent">Shop Now</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex align-items-center style-card bg-white rounded shadow-sm" style="background: url(uploads/carousels/1766685281.jpg) no-repeat center/cover; padding:100px 30px">
                    <div class="style-text me-4">
                        <p class="text-muted mb-3">TOP COLLECTIONS</p>
                        <h3 class="fw-bold">Top Trending Style</h3>
                        <br>
                        <a href="products.php" class="btn btn-accent">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .two-style-section { background: #f8f9fa; }
    .style-card { gap: 1rem; }
    @media (max-width: 767.98px) {
        .style-card { flex-direction: column; text-align: center; }
        .style-image { text-align: center !important; }
        .style-image img { max-width: 100%; height: auto; }
    }
</style>

<?php include 'footer.php'; ?>