<?php 
session_start();
require_once 'includes/db.php';
$q = trim($_GET['q'] ?? '');
$results = [];

if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT name, slug, main_image, price, category_id,
               (CASE WHEN name LIKE ? THEN 1 ELSE 0 END +
                CASE WHEN description LIKE ? THEN 0.5 ELSE 0 END) as relevance
        FROM products 
        WHERE name LIKE ? OR description LIKE ?
        ORDER BY relevance DESC, created_at DESC
        LIMIT 24
    ");
    $like = "%$q%";
    $stmt->execute([$like, $like, $like, $like]);
    $results = $stmt->fetchAll();
}
?>

<?php include 'header.php'; ?>

<div class="container py-5 mt-5">
    <h2 class="mb-4">
        Search Results for: 
        <strong class="text-gold">"<?= htmlspecialchars($q) ?>"</strong>
        <?php if ($results): ?>
            <small class="text-muted">(<?= count($results) ?> products found)</small>
        <?php endif; ?>
    </h2>

    <?php if (empty($results)): ?>
        <div class="text-center py-5">
            <i class="bi bi-search fs-1 text-muted"></i>
            <p class="fs-4 mt-3 text-muted">No products found for "<?= htmlspecialchars($q) ?>"</p>
            <a href="index.php" class="btn btn-outline-dark">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach($results as $r): ?>
            <div class="col">
                <a href="product.php?slug=<?= $r['slug'] ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="uploads/products/<?= htmlspecialchars($r['main_image']) ?>" 
                             class="card-img-top" style="height: 320px; object-fit: cover;">
                        <div class="card-body text-center">
                            <h6 class="card-title fw-bold"><?= htmlspecialchars($r['name']) ?></h6>
                            <p class="text-gold fs-5 fw-bold">৳<?= number_format($r['price']) ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>