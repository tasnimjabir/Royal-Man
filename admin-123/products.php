<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'products';

$stmt = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
$products = $stmt->fetchAll();
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Products <a href="add-product.php" class="btn btn-accent float-end"><i class="bi bi-plus"></i> Add Product</a></h2>

<table class="table table-hover bg-white rounded shadow">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Views</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($products as $p): ?>
        <tr>
            <td><img src="../uploads/products/<?= htmlspecialchars($p['main_image']) ?>" width="80" class="rounded"></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>৳<?= number_format($p['price']) ?></td>
            <td><?= htmlspecialchars($p['cat_name'] ?? 'None') ?></td>
            <td><?= (int)($p['views'] ?? 0) ?></td>
            <td>
                <a href="edit-product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                <a href="delete-product.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i> Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../footer.php'; ?>
</body>
</html>