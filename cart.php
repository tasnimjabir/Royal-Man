<?php
session_start();
require_once 'includes/db.php';

// cart.php - Full Shopping Cart Page (Refined UI)

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart from product page (only when not updating quantity)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && !isset($_POST['update_qty'])) {
    $product_id = (int)$_POST['product_id'];
    $size_id = isset($_POST['size_id']) && !empty($_POST['size_id']) ? (int)$_POST['size_id'] : null;
    $qty = (int)($_POST['quantity'] ?? 1);
    if ($qty < 1) $qty = 1;
    
    // Fetch product details
    $stmt = $pdo->prepare("SELECT id, name, price, main_image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    // Fetch size name if size_id provided
    $size_name = '';
    if ($size_id) {
        $sizeStmt = $pdo->prepare("SELECT size_name FROM sizes WHERE id = ?");
        $sizeStmt->execute([$size_id]);
        $sizeRow = $sizeStmt->fetch();
        $size_name = $sizeRow['size_name'] ?? '';
    }
    
    if ($product) {
        // Create unique cart key combining product_id and size_id
        $cart_key = $size_id ? "{$product_id}_size_{$size_id}" : $product_id;
        
        // Add or update cart item
        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$cart_key] = [
                'product_id' => $product_id,
                'size_id' => $size_id,
                'size_name' => $size_name,
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['main_image'],
                'quantity' => $qty
            ];
        }
    }
    header("Location: cart.php");
    exit;
}

// Handle Update Quantity
if (isset($_POST['update_qty']) && isset($_POST['cart_key'])) {
    $cart_key = $_POST['cart_key'];
    $qty = (int)$_POST['quantity'];
    if ($qty < 1) $qty = 1;
    
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['quantity'] = $qty;
    }
    header("Location: cart.php");
    exit;
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $key = $_GET['remove'];
    unset($_SESSION['cart'][$key]);
    header("Location: cart.php");
    exit;
}

// Calculate Total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Fetch FAQ for cart page
$faqStmt = $pdo->prepare("SELECT id, ques, ans FROM faq WHERE page = 'cart' AND active = 1 ORDER BY sort_order ASC");
$faqStmt->execute();
$faqs = $faqStmt->fetchAll();
?>
<?php include 'header.php'; ?>


<!-- Cart Hero -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h1 class="display-4 fw-bold text-dark">Shopping Cart</h1>
        <p class="lead text-muted">Review and edit your items</p>
    </div>
</section>

<!-- Cart Content -->
<section class="py-5">
    <div class="container">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                <h3 class="fw-bold mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Start adding some premium items!</p>
                <a href="products" class="btn btn-accent btn-lg px-5 py-3 rounded-pill shadow-lg">
                    <i class="bi bi-shop me-2"></i> Shop Now
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded shadow-sm p-4 mb-5">
                <div class="table-responsive">
                    <table class="table align-middle borderless">
                        <thead>
                            <tr class="border-bottom">
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                            <tr class="border-bottom">
                                <td class="d-flex align-items-center gap-3 py-4">
                                     <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                         class="rounded shadow-sm product-thumb" 
                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                        <?php if (!empty($item['size_name'])): ?>
                                        <small class="text-muted">Size: <?= htmlspecialchars($item['size_name']) ?></small><br>
                                        <?php endif; ?>
                                        <small class="text-muted">ID: #<?= htmlspecialchars($key) ?></small>
                                    </div>
                                </td>
                                <td class="fw-bold text-accent">৳<?= number_format($item['price'], 0) ?></td>
                                <td class="td-width-200">
                                    <form method="POST" class="input-group input-group-sm qty-form">
                                        <input type="hidden" name="cart_key" value="<?= htmlspecialchars($key) ?>">
                                        <input type="hidden" name="update_qty" value="1">
                                        <button type="button" class="btn btn-outline-secondary qty-decrease">−</button>
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                               class="form-control text-center qty-input" min="1">
                                        <button type="button" class="btn btn-outline-secondary qty-increase">+</button>
                                    </form>
                                </td>
                                <td class="fw-bold">৳<?= number_format($item['price'] * $item['quantity'], 0) ?></td>
                                <td>
                                    <a href="?remove=<?= urlencode($key) ?>" class="btn btn-danger btn-sm rounded-pill">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row justify-content-end">
                <div class="col-lg-4">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h4 class="fw-bold mb-4">Cart Summary</h4>
                            <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-5 fw-bold text-accent">৳<?= number_format($total, 0) ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-accent w-100 py-3 rounded-pill fw-bold">
                            Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FAQ Section -->
<?php if (!empty($faqs)): ?>
<section class="py-5 bg-gradient-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark mb-2">Frequently Asked Questions</h2>
            <p class="lead text-muted">Find answers to common questions about shipping, returns, and more</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item border rounded mb-3 shadow-sm overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark py-3" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#faq<?= $faq['id'] ?>" 
                                    aria-expanded="false">
                                <i class="bi bi-question-circle me-2 text-primary fs-5"></i>
                                <?= htmlspecialchars($faq['ques']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $faq['id'] ?>" 
                             class="accordion-collapse collapse" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body bg-light text-muted py-4">
                                <i class="bi bi-info-circle text-info me-2"></i>
                                <?= htmlspecialchars($faq['ans']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
    .bg-gradient-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .accordion-item {
        border: 1px solid #e0e0e0 !important;
        transition: all 0.3s ease;
    }
    
    .accordion-item:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #0056b3;
        box-shadow: none;
    }
    
    .accordion-button:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.25rem rgba(0, 86, 179, 0.25);
    }
    
    .accordion-body {
        line-height: 1.8;
        font-size: 0.975rem;
    }
</style>

        
    </div>
</section>

<!-- moved gold styles to assets/css/style.css and replaced with accent classes -->

<script>
// Update qty with + / -
document.querySelectorAll('form.qty-form').forEach(form => {
    const decreaseBtn = form.querySelector('.qty-decrease');
    const increaseBtn = form.querySelector('.qty-increase');
    const qtyInput = form.querySelector('.qty-input');
    
    decreaseBtn.addEventListener('click', function() {
        let qty = parseInt(qtyInput.value) || 1;
        qty = Math.max(1, qty - 1);
        qtyInput.value = qty;
        form.submit();
    });
    
    increaseBtn.addEventListener('click', function() {
        let qty = parseInt(qtyInput.value) || 1;
        qty = qty + 1;
        qtyInput.value = qty;
        form.submit();
    });
});
</script>

<?php include 'footer.php'; ?>