<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'dashboard';

// Handle delete
if (isset($_GET['delete_order']) && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
    header("Location: index.php?deleted=1");
    exit;
}

$stmt = $pdo->query("SELECT o.*, u.name AS customer, COUNT(oi.id) AS item_count
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    GROUP BY o.id
                    ORDER BY o.created_at DESC LIMIT 10");
$orders = $stmt->fetchAll();
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Dashboard</h2>
<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">Order deleted successfully!</div>
<?php endif; ?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white p-4 rounded-3">
            <h5 class="mb-3">Total Products</h5>
            <h3 class="mb-0"><?= $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white p-4 rounded-3">
            <h5 class="mb-3">Total Orders</h5>
            <h3 class="mb-0"><?= $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white p-4 rounded-3">
            <h5 class="mb-3">Total Customers</h5>
            <h3 class="mb-0"><?= $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn() ?></h3>
        </div>
    </div>
</div>

<h4 class="mt-5">Recent Orders</h4>
<table class="table table-hover bg-white rounded shadow">
    <thead>
        <tr><th>Order ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach($orders as $o): ?>
        <tr>
            <td class="fw-bold">#<?= $o['id'] ?></td>
            <td><?php echo htmlspecialchars($o['customer_name'] ?? 'Guest'); if($o['customer']){echo " (Email)";} ?></td>
            <td><?= $o['item_count'] ?></td>
            <td class="fw-bold text-accent">৳<?= number_format($o['total']) ?></td>
            <td><span class="badge bg-<?= $o['status'] == 'delivered' ? 'success' : ($o['status'] == 'shipped' ? 'info' : ($o['status'] == 'cancelled' ? 'danger' : 'warning')) ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td>
                <a href="order-view.php?id=<?= $o['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> View</a>
                <a href="index.php?delete_order=1&id=<?= $o['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?');"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>

<?php include '../footer.php'; ?>
</body>
</html>
