<?php 
session_start();
require_once 'includes/db.php'; 

// category.php - Display all products in a category with sort options
$slug = $_GET['slug'] ?? '';
$sort = $_GET['sort'] ?? 'recent';  // Default to recent (newest)

$stmt = $pdo->prepare("SELECT id, name FROM categories WHERE slug = ?");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    header("Location: /");
    exit;
}

// Build ORDER BY based on sort
$order_by = 'p.created_at DESC';  // Default recent
if ($sort === 'price_asc') {
    $order_by = 'p.price ASC';
} elseif ($sort === 'price_desc') {
    $order_by = 'p.price DESC';
}

// Fetch products
$products = $pdo->prepare("
    SELECT p.name, p.slug, p.price, p.main_image, p.description 
    FROM products p 
    WHERE category_id = ?
    ORDER BY $order_by
");
$products->execute([$category['id']]);
$products = $products->fetchAll();
?>

<?php include 'header.php'; ?>

<!-- Category Hero -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h1 class="display-4 fw-bold text-dark"><?= htmlspecialchars($category['name']) ?></h1>
        <p class="lead text-muted">Explore our premium collection of <?= strtolower(htmlspecialchars($category['name'])) ?></p>
    </div>
</section>

<!-- Sort Options -->
<section class="py-3 bg-white border-bottom">
    <div class="container d-flex justify-content-end">
        <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown">
                Sort by: <?= $sort === 'price_asc' ? 'Price Low to High' : ($sort === 'price_desc' ? 'Price High to Low' : 'Recent') ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?slug=<?= htmlspecialchars($slug) ?>&sort=recent">Recent</a></li>
                <li><a class="dropdown-item" href="?slug=<?= htmlspecialchars($slug) ?>&sort=price_asc">Price Low to High</a></li>
                <li><a class="dropdown-item" href="?slug=<?= htmlspecialchars($slug) ?>&sort=price_desc">Price High to Low</a></li>
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
                            <p class="text-muted small mb-2"><?= strtolower(htmlspecialchars($category['name'])) ?></p>
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