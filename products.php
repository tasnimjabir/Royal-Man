<?php 
session_start();
require_once 'includes/db.php'; 

// all-products.php - Display all products with category grid first and sort options

$sort = $_GET['sort'] ?? 'recent';  // Default to recent (newest)

// Fetch all categories for grid
$categories = $pdo->query("SELECT name, slug, image FROM categories ORDER BY name")->fetchAll();

// Build ORDER BY based on sort
$order_by = 'p.created_at DESC';  // Default recent
if ($sort === 'price_asc') {
    $order_by = 'p.price ASC';
} elseif ($sort === 'price_desc') {
    $order_by = 'p.price DESC';
}

// Fetch all products
$products = $pdo->query("
    SELECT p.name, p.slug, p.price, p.main_image, p.description, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY $order_by
")->fetchAll();
?>

<?php include 'header.php'; ?>

<!-- Categories Grid Section (Sequential Grid) -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center display-5 fw-bold mb-5 text-dark">Browse by Category</h2>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
            <?php foreach($categories as $cat): ?>
            <div class="col">
                <a href="category.php?slug=<?= htmlspecialchars($cat['slug']) ?>" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm rounded-3 text-center py-4 hover-shadow">
                        <?php if ($cat['image']): ?>
                            <img src="uploads/categories/<?= htmlspecialchars($cat['image']) ?>" 
                                 class="mx-auto mb-3" 
                                 style="width: 80px; height: 80px; object-fit: contain;"
                                 alt="<?= htmlspecialchars($cat['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-tags fs-1 text-gold mb-3"></i>
                        <?php endif; ?>
                        <h5 class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></h5>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Sort Options -->
<section class="py-3 bg-light border-bottom">
    <div class="container d-flex justify-content-end">
        <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown">
                Sort by: <?= $sort === 'price_asc' ? 'Price Low to High' : ($sort === 'price_desc' ? 'Price High to Low' : 'Recent') ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?sort=recent">Recent</a></li>
                <li><a class="dropdown-item" href="?sort=price_asc">Price Low to High</a></li>
                <li><a class="dropdown-item" href="?sort=price_desc">Price High to Low</a></li>
            </ul>
        </div>
    </div>
</section>

<!-- Products Grid -->
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

<?php include 'footer.php'; ?>