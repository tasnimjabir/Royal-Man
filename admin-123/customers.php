<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'customers';

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
$customers = $stmt->fetchAll();
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Customers</h2>

<table class="table table-hover bg-white rounded shadow">
    <thead>
        <tr><th>Name</th><th>Email</th><th>Joined</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach($customers as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
            <td>
                <a href="delete-customer.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete customer?')"><i class="bi bi-trash"></i> Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../footer.php'; ?>
</body>
</html>