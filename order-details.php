<?php
session_start();
require_once 'includes/db.php';

// order-details.php - Single Order View (For Logged-in Users)

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Fetch items
$items = $pdo->prepare("SELECT oi.*, p.name, p.main_image, s.size_name FROM order_items oi 
                        LEFT JOIN products p ON oi.product_id = p.id 
                        LEFT JOIN sizes s ON oi.size_id = s.id 
                        WHERE order_id = ?");
$items->execute([$id]);
$items = $items->fetchAll();
?>

<?php include 'header.php'; ?>

<!-- Order Details Hero -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h1 class="display-4 fw-bold text-dark">Order #<?= $id ?></h1>
        <p class="lead text-muted">Details and status</p>
    </div>
</section>

<!-- Order Info -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 sticky-top sticky-offset">
                    <h4 class="fw-bold mb-4">Summary</h4>
                    <p><strong>Status:</strong> <span class="badge bg-<?= $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'shipped' ? 'info' : 'warning') ?>"><?= ucfirst($order['status']) ?></span></p>
                    <p><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                    <p><strong>Total:</strong> ৳<?= number_format($order['total'], 0) ?></p>
                    <hr>
                    <h6 class="fw-bold mb-3">Shipping To</h6>
                    <p class="text-muted"><?= htmlspecialchars($order['customer_name']) ?><br>
                    <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                    Phone: <?= htmlspecialchars($order['phone']) ?><br>
                    Email: <?= htmlspecialchars($order['customer_email']) ?></p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <h4 class="fw-bold mb-4">Order Items</h4>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                               <img src="uploads/products/<?= htmlspecialchars($item['main_image']) ?>" 
                                   class="rounded shadow-sm product-thumb" 
                                   alt="<?= htmlspecialchars($item['name']) ?>">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                <p class="text-muted small mb-1">Qty: <?= $item['quantity'] ?></p>
                                <?php if (!empty($item['size_name'])): ?>
                                <p class="text-muted small mb-1">Size: <?= htmlspecialchars($item['size_name']) ?></p>
                                <?php endif; ?>
                                <p class="fw-bold text-accent">৳<?= number_format($item['price_at_purchase'], 0) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>