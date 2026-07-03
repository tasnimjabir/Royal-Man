<?php
session_start();
require_once 'includes/db.php';
// order-success.php - Order Confirmation Page
$order_id = $_GET['order_id'] ?? 0;

if (!$order_id) {
    header("Location: index.php");
    exit;
}

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

// If logged in, link to profile orders
if (isset($_SESSION['user_id'])) {
    // Optional: Add Orders link to header if not already
    // In header.php: <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
}
?>

<?php include 'header.php'; ?>

<!-- Success Hero -->
<section class="py-5 text-center bg-light">
    <div class="container">
        <i class="bi bi-check-circle-fill display-1 text-success mb-4"></i>
        <h1 class="display-4 fw-bold text-dark">Thank You for Your Order!</h1>
        <p class="lead text-muted mb-4">Order #<?= $order_id ?> has been placed successfully.</p>
    </div>
</section>

<!-- Order Details -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5 bg-white">
                    <h4 class="fw-bold mb-4">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Order ID</span>
                        <span class="fw-bold">#<?= $order_id ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Date</span>
                        <span><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total</span>
                        <span class="fw-bold text-accent">৳<?= number_format($order['total'], 0) ?></span>
                    </div>
                    <hr>
                    <h6 class="fw-bold mb-3">Shipping To</h6>
                    <p class="text-muted mb-0"><?= htmlspecialchars($order['customer_name']) ?></p>
                    <p class="text-muted mb-0"><?= htmlspecialchars($order['shipping_address']) ?></p>
                    <p class="text-muted mb-0">Phone: <?= htmlspecialchars($order['phone']) ?></p>
                    <p class="text-muted">Email: <?= htmlspecialchars($order['customer_email']) ?></p>

                    <div class="mt-5 text-center">
                        <a href="index.php" class="btn btn-outline-dark px-5 py-3 rounded-pill">
                            <i class="bi bi-shop me-2"></i> Continue Shopping
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="orders.php" class="btn btn-accent px-5 py-3 rounded-pill ms-3">
                                <i class="bi bi-list-check me-2"></i> View My Orders
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>