<?php 
session_start();
include 'header.php'; 
require_once 'includes/db.php'; 

// Get product by slug
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: /");
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.slug = ?");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: /");
    exit;
}

// Fetch additional images
$images = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id");
$images->execute([$product['id']]);
$additional_images = $images->fetchAll(PDO::FETCH_COLUMN);

// Fetch sizes for this product's category
$sizes = [];
if ($product['category_id']) {
    $sizesStmt = $pdo->prepare("SELECT id, size_name FROM sizes WHERE category_id = ? ORDER BY id");
    $sizesStmt->execute([$product['category_id']]);
    $sizes = $sizesStmt->fetchAll();
}

// Build full image array (main + additional)
$all_images = [];
if (!empty($product['main_image'])) {
    $all_images[] = $product['main_image'];
}
$all_images = array_merge($all_images, $additional_images);

// Fallback if no images at all
if (empty($all_images)) {
    $all_images[] = 'placeholder.jpg'; // Create a placeholder image in uploads/products/
}

// Fetch similar products (same category, excluding current product)
$similar_products = [];
if ($product['category_id']) {
    $similarStmt = $pdo->prepare("SELECT id, name, price, slug, main_image FROM products 
                                  WHERE category_id = ? AND id != ? 
                                  ORDER BY RAND() 
                                  LIMIT 4");
    $similarStmt->execute([$product['category_id'], $product['id']]);
    $similar_products = $similarStmt->fetchAll();
}

// Cart count from session
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'] ?? 1;
    }
}
?>



<!-- Update cart badge in header (if not already dynamic) -->
<script>
// Update cart count in real-time
document.addEventListener('DOMContentLoaded', function() {
    const badge = document.querySelector('.badge.bg-danger');
    if (badge) badge.textContent = '<?= $cart_count ?>';
});
</script>

<!-- Product View Section -->
<section class="py-5 mt-5">
    <div class="container">
        <div class="row g-5 align-items-start">

            <!-- Image Gallery -->
            <div class="col-lg-6">
                <div class="sticky-top sticky-offset">
                    <!-- Main Image -->
                    <div class="main-image mb-4 border rounded-3 overflow-hidden shadow-sm bg-white p-3">
                            <img id="mainImg" 
                                src="uploads/products/<?= htmlspecialchars($all_images[0] ?? 'placeholder.jpg') ?>" 
                                class="img-fluid rounded w-100 product-main" 
                                alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>

                    <!-- Thumbnails -->
                    <?php if (count($all_images) > 1): ?>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <?php foreach($all_images as $index => $img): ?>
                        <div class="thumbnail-wrapper <?= $index === 0 ? 'active' : '' ?>">
                               <img src="uploads/products/<?= htmlspecialchars($img) ?>" 
                                   class="img-thumbnail cursor-pointer border-3 transition-all product-thumb-sm" 
                                   onclick="changeMainImage(this.src, this)"
                                   alt="Thumbnail">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
                    </ol>
                </nav>

                <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <?php if ($product['category_name']): ?>
                    <p class="text-muted mb-3">
                        <a href="/category/<?= $product['category_slug'] ?? '#' ?>" class="text-decoration-none">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </a>
                    </p>
                <?php endif; ?>

                <div class="fs-1 fw-bold text-accent mb-4">৳<?= number_format($product['price'], 0) ?></div>

                <div class="mb-5 text-secondary lh-lg">
                    <?= nl2br(htmlspecialchars($product['description'] ?: 'No description available.')) ?>
                </div>

                <!-- Size Selector -->
                <?php if (!empty($sizes)): ?>
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">Select Size</label>
                    <div class="size-selector d-flex flex-wrap gap-2" id="sizeSelector">
                        <?php foreach ($sizes as $size): ?>
                        <input type="radio" id="size_<?= $size['id'] ?>" name="size_id" value="<?= $size['id'] ?>" class="d-none size-radio">
                        <label for="size_<?= $size['id'] ?>" class="size-btn btn btn-outline-dark py-2 px-3 rounded-2">
                            <?= htmlspecialchars($size['size_name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" id="selectedSize" value="">
                </div>
                <?php endif; ?>

                <!-- Add to Cart -->
<div class="card border-0 shadow-sm p-4 mt-5">
    <form action="cart.php" method="POST" class="row g-3 align-items-end">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <?php if (!empty($sizes)): ?>
        <input type="hidden" name="size_id" id="sizeInput" value="">
        <?php endif; ?>

            <div class="col-auto">
            <label class="form-label fw-bold mb-2">Quantity</label>
            <div class="input-group input-group-lg input-w-160">
                <button type="button" class="btn btn-outline-secondary rounded-start" onclick="changeQty(-1)">
                    <i class="bi bi-dash"></i>
                </button>
                <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="50" 
                       class="form-control text-center fw-bold p-0 border" readonly>
                <button type="button" class="btn btn-outline-secondary rounded-end" onclick="changeQty(1)">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
        </div>

            <div class="col-auto d-flex align-items-end">
            <button type="submit" class="btn btn-accent btn-lg px-5 py-3 shadow-lg hover-lift">
                <i class="bi bi-bag-plus me-2"></i> Add to Cart
            </button>
        </div>
    </form>

    <!-- Stock Info (Optional) -->
    <div class="mt-3 text-success small">
        <i class="bi bi-check2-circle"></i> In Stock • Ready to Ship
    </div>
</div>
            </div>
        </div>
    </div>
</section>

<style>
    /* UI Specific Styles */
    .main-image {
        /* Ensures the main image box is slightly recessed and styled */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
    }
    .thumbnail-wrapper {
        border: 3px solid transparent;
        border-radius: 8px;
        transition: all 0.3s ease;
        line-height: 0;
        flex-shrink: 0; /* Prevents shrinking */
    }
    .img-thumbnail {
        padding: 0 !important;
        border-radius: 4px;
    }
    .thumbnail-wrapper.active,
    .thumbnail-wrapper:hover {
        border-color: var(--accent-foreground) !important;
        transform: scale(1.05);
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    }
    
    /* Size Selector Styles */
    .size-btn {
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        min-width: 60px;
        text-align: center;
        border: 2px solid #dee2e6;
    }
    
    .size-btn:hover {
        border-color: var(--bs-dark);
        background-color: #1b1b1bff;
    }
    
    .size-radio:checked + .size-btn {
        background-color: var(--accent-foreground) !important;
        border-color: var(--accent-foreground) !important;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* legacy classes moved to assets/css/style.css: .text-accent, .btn-accent */
</style>

<!-- Recommended Products Section -->
<?php if (!empty($similar_products)): ?>
<section class="py-5 bg-light mt-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3">Recommended Products</h2>
            <p class="text-muted lead">You might also like these items</p>
        </div>
        <div class="row g-4">
            <?php foreach ($similar_products as $rec_product): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 transition-all hover-card">
                    <!-- Product Image -->
                    <div class="position-relative overflow-hidden" style="height: 280px;">
                        <img src="uploads/products/<?= htmlspecialchars($rec_product['main_image']) ?>" 
                             class="card-img-top img-fluid w-100 h-100 object-fit-cover" 
                             alt="<?= htmlspecialchars($rec_product['name']) ?>"
                             style="object-fit: cover;">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-0 transition-all" style="transition: opacity 0.3s ease;">
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-2">
                            <a href="/product-view?slug=<?= htmlspecialchars($rec_product['slug']) ?>" 
                               class="text-decoration-none text-dark hover-accent">
                                <?= htmlspecialchars(strlen($rec_product['name']) > 40 ? substr($rec_product['name'], 0, 40) . '...' : $rec_product['name']) ?>
                            </a>
                        </h5>
                        <div class="fs-5 fw-bold text-accent mb-3">
                            ৳<?= number_format($rec_product['price'], 0) ?>
                        </div>
                        <div class="mt-auto">
                            <a href="/product-view?slug=<?= htmlspecialchars($rec_product['slug']) ?>" 
                               class="btn btn-outline-dark w-100 py-2 rounded-2">
                                <i class="bi bi-eye me-2"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
    /* Product Card Hover Effects */
    .hover-card {
        transition: all 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .hover-accent {
        transition: color 0.3s ease;
    }
    
    .hover-accent:hover {
        color: var(--accent-foreground) !important;
    }
    
    .transition-all {
        transition: all 0.3s ease;
    }
</style>



<script>
// Change main image + highlight thumbnail
function changeMainImage(src, element) {
    document.getElementById('mainImg').src = src;
    
    // Remove active class from all wrappers
    document.querySelectorAll('.thumbnail-wrapper').forEach(t => t.classList.remove('active'));
    // Add to clicked element's wrapper
    element.closest('.thumbnail-wrapper').classList.add('active');
}

// Quantity Control Logic
const qtyInput = document.getElementById('qtyInput');

function changeQty(change) {
    let currentVal = parseInt(qtyInput.value);
    let newVal = currentVal + change;
    const min = parseInt(qtyInput.min);
    const max = parseInt(qtyInput.max);

    if (newVal >= min && newVal <= max) {
        qtyInput.value = newVal;
    }
}

// Size Selector Logic
document.addEventListener('DOMContentLoaded', function() {
    // Update Cart Badge
    const badge = document.querySelector('.badge.bg-danger');
    if (badge) badge.textContent = '<?= $cart_count ?>';
    
    // Auto-select first thumbnail on load
    const firstThumb = document.querySelector('.thumbnail-wrapper');
    if (firstThumb) firstThumb.classList.add('active');
    
    // Size selector
    const sizeRadios = document.querySelectorAll('.size-radio');
    const sizeInput = document.getElementById('sizeInput');
    const form = document.querySelector('form');
    
    sizeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (sizeInput) {
                sizeInput.value = this.value;
            }
        });
    });
    
    // Validate size selection before submit
    if (form && sizeRadios.length > 0) {
        form.addEventListener('submit', function(e) {
            const checked = document.querySelector('.size-radio:checked');
            if (!checked) {
                e.preventDefault();
                alert('Please select a size');
                return false;
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>