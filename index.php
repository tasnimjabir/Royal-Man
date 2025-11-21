<?php 
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

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold text-dark mb-4">ROYAL MAN</h1>
                <p class="lead fs-2 text-muted mb-4">Dress Like a King</p>
                <p class="fs-5 text-secondary mb-5">Premium cotton T-shirts, hoodies & jackets made for the modern Bangladeshi man.</p>
                <a href="#shop" class="btn btn-gold btn-lg px-5 py-3 shadow">Shop Collection</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <?php foreach($featured as $f): ?>
                    <div class="col-6">
                        <a href="product.php?slug=<?= htmlspecialchars($f['slug']) ?>">
                            <!-- <img src="uploads/products/<?php //htmlspecialchars($f['main_image']) ?>"  -->
                             <img src="uploads/products/T-shirt.jpg"
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

<!-- ALL PRODUCTS -->
<section id="shop" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark">Our Collection</h2>
            <p class="text-muted fs-5">Handpicked premium wear for you</p>
        </div>

        <div class="row row-cols-2 row-cols-md-2 row-cols-lg-3 g-4 g-lg-5">
            <?php foreach($products as $p): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                    <a href="product.php?slug=<?= htmlspecialchars($p['slug']) ?>" class="text-decoration-none">
                        <div class="position-relative overflow-hidden">
                            <img src="uploads/products/T-shirt.jpg" 
                                 class="card-img-top" 
                                 style="height: 380px; object-fit: cover; transition: transform 0.5s;"
                                 alt="<?= htmlspecialchars($p['name']) ?>">
                            <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-gold text-dark">New</span>
                            </div>
                        </div>
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($p['name']) ?></h5>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($p['category_name']) ?></p>
                            <p class="fs-4 fw-bold text-gold">৳<?= number_format($p['price'], 0) ?></p>
                        </div>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>