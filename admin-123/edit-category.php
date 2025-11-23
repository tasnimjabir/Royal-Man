<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
    $desc = $_POST['description'];

    $image = $category['image'];
    if ($_FILES['image']['size'] > 0) {
        if (file_exists('../uploads/categories/' . $image) && $image) unlink('../uploads/categories/' . $image);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['image']['size'] < 5000000) {
            $image = time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/categories/' . $image);
        } else {
            $error = "Invalid image (JPG/PNG, <5MB)";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $desc, $image, $id]);
        header("Location: categories.php");
        exit;
    }
}
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Edit Category</h2>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Image (Current: <?= $category['image'] ? '<img src="../uploads/categories/' . htmlspecialchars($category['image']) . '" width="50">' : 'None' ?>)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-accent">Update Category</button>
</form>

<?php include '../footer.php'; ?>
</body>
</html>