<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'dashboard';

$stmt = $pdo->query("SELECT o.*, u.name AS customer FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY created_at DESC LIMIT 5");
$orders = $stmt->fetchAll();
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Dashboard</h2>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white p-4 rounded-3">
            <h5 class="mb-3">Total Products</h5>
            <h3 class="mb-0"><?= $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-accent text-white p-4 rounded-3">
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
        <tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
    </thead>
    <tbody>
        <?php foreach($orders as $o): ?>
        <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= $o['customer'] ?? 'Guest' ?></td>
            <td>৳<?= number_format($o['total']) ?></td>
            <td><span class="badge bg-<?= $o['status'] == 'delivered' ? 'success' : 'warning' ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>

<?php include '../footer.php'; ?>
</body>
</html>