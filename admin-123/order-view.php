<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header("Location: index.php");
    exit;
}

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_status'])) {
    $new_status = $_POST['new_status'];
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($new_status, $valid_statuses)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
        header("Location: order-view.php?id=$order_id&updated=1");
        exit;
    }
}

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] == 1) {
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
    header("Location: index.php?deleted=1");
    exit;
}

// Fetch items with size info
$items = $pdo->prepare("SELECT oi.*, p.name, p.main_image, s.size_name FROM order_items oi 
                        LEFT JOIN products p ON oi.product_id = p.id 
                        LEFT JOIN sizes s ON oi.size_id = s.id 
                        WHERE order_id = ?");
$items->execute([$order_id]);
$items = $items->fetchAll();

// Refresh order data in case it was updated
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();
?>

<?php include '../includes/admin-header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Order #<?= $order['id'] ?></h2>
    <a href="index.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
</div>

<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success">Order status updated successfully!</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Order Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3">Order Info</h5>
            <p><strong>Status:</strong><br>
                <form method="POST" class="mt-2">
                    <select name="new_status" class="form-select form-select-sm" onchange="if(confirm('Change status?')) this.form.submit();">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </form>
            </p>
            <p><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
            <p><strong>Total:</strong> <span class="fs-5 text-accent fw-bold">৳<?= number_format($order['total'], 0) ?></span></p>
            
            <hr>
            
            <h5 class="fw-bold mb-3">Customer Info</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
            <?php if (!empty($order['notes'])): ?>
            <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
            <?php endif; ?>
            
            <hr>
            
            <a href="order-view.php?id=<?= $order['id'] ?>&delete=1" class="btn btn-danger w-100" onclick="return confirm('Are you sure? This cannot be undone.');">
                <i class="bi bi-trash me-2"></i> Delete Order
            </a>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4">Order Items</h5>
            <?php if (empty($items)): ?>
            <p class="text-muted">No items in this order.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="border-bottom">
                            <th>Product</th>
                            <th>Size</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr class="border-bottom">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="../uploads/products/<?= htmlspecialchars($item['main_image']) ?>" 
                                         class="rounded" width="50" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <span><?= htmlspecialchars($item['name']) ?></span>
                                </div>
                            </td>
                            <td><?= !empty($item['size_name']) ? htmlspecialchars($item['size_name']) : 'N/A' ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>৳<?= number_format($item['price_at_purchase'], 0) ?></td>
                            <td class="fw-bold">৳<?= number_format($item['price_at_purchase'] * $item['quantity'], 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">TOTAL:</td>
                            <td class="text-accent fs-5">৳<?= number_format($order['total'], 0) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
</body>
</html>
