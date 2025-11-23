<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'categories';

$stmt = $pdo->query("SELECT * FROM categories");
$cats = $stmt->fetchAll();
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Categories <a href="add-category.php" class="btn btn-accent float-end"><i class="bi bi-plus"></i> Add Category</a></h2>

<table class="table table-hover bg-white rounded shadow">
    <thead>
        <tr><th>Name</th><th>Slug</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach($cats as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['slug']) ?></td>
            <td>
                <a href="edit-category.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                <a href="delete-category.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete? Products will lose this category.')"><i class="bi bi-trash"></i> Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../footer.php'; ?>
</body>
</html>