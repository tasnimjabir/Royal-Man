<?php
session_start();
require_once 'includes/db.php';

// orders.php - User Profile Orders (Only for Logged-in Users)

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<!-- Orders Hero -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h1 class="display-4 fw-bold text-dark">My Orders</h1>
        <p class="lead text-muted">Track your recent purchases</p>
    </div>
</section>

<!-- Orders List -->
<section class="py-5">
    <div class="container">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-box-seam display-1 text-muted mb-4"></i>
                <h3 class="fw-bold mb-3">No orders yet</h3>
                <a href="products" class="btn btn-accent btn-lg px-5 py-3 rounded-pill shadow-lg">
                    <i class="bi bi-shop me-2"></i> Shop Now
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="card border-0 shadow-sm mb-4 p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold">Order #<?= $order['id'] ?></h5>
                            <p class="text-muted small mb-0">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                        </div>
                        <span class="badge bg-<?= $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'shipped' ? 'info' : 'warning') ?> fs-6 px-3 py-2">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1"><strong>Total:</strong> ৳<?= number_format($order['total'], 0) ?></p>
                            <p class="mb-1"><strong>Shipping To:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                        </div>
                        <a href="order-details.php?id=<?= $order['id'] ?>" class="btn btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>