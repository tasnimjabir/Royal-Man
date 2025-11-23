<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

$images = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$images->execute([$id]);
$images = $images->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat_id = $_POST['category_id'];

    $main_image = $product['main_image'];
    if ($_FILES['main_image']['size'] > 0) {
        if (file_exists('../uploads/products/' . $main_image)) unlink('../uploads/products/' . $main_image);
        $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['main_image']['size'] < 5000000) {
            $main_image = time() . '.' . $ext;
            move_uploaded_file($_FILES['main_image']['tmp_name'], '../uploads/products/' . $main_image);
        } else {
            $error = "Invalid main image (JPG/PNG, <5MB)";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, category_id = ?, main_image = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $desc, $price, $cat_id, $main_image, $id]);

        foreach ($_FILES['images']['name'] as $key => $img_name) {
            if ($_FILES['images']['size'][$key] > 0) {
                $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
                if (in_array($img_ext, ['jpg', 'jpeg', 'png']) && $_FILES['images']['size'][$key] < 5000000) {
                    $img_file = time() . '_' . $key . '.' . $img_ext;
                    move_uploaded_file($_FILES['images']['tmp_name'][$key], '../uploads/products/' . $img_file);
                    $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)")->execute([$id, $img_file]);
                }
            }
        }

        header("Location: products.php");
        exit;
    }
}
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Edit Product</h2>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Price (৳)</label>
        <input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" class="form-select">
            <option value="">None</option>
            <?php foreach($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Main Image (Current: <img src="../uploads/products/<?= htmlspecialchars($product['main_image']) ?>" width="50">)</label>
        <input type="file" name="main_image" class="form-control" accept="image/*">
    </div>
    <div class="mb-3">
        <label>Additional Images</label>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php foreach($images as $img): ?>
            <img src="../uploads/products/<?= htmlspecialchars($img['image']) ?>" width="100" class="rounded shadow-sm">
            <?php endforeach; ?>
        </div>
        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
    </div>
    <button type="submit" class="btn btn-accent">Update Product</button>
</form>

<?php include '../footer.php'; ?>
</body>
</html>