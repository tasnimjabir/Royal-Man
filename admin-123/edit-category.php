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
    $sizes = $_POST['sizes'] ?? [];
    $sizes = array_filter($sizes);

    $image = $category['image'];
    if ($_FILES['image']['size'] > 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['image']['size'] < 5000000) {
            // Create directory if it doesn't exist
            $uploadDir = '../uploads/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            // Delete old image if it exists
            if (file_exists($uploadDir . $image) && $image) {
                unlink($uploadDir . $image);
            }
            $image = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image)) {
                // File uploaded successfully
            } else {
                $error = "Failed to upload image. Please try again.";
            }
        } else {
            $error = "Invalid image (JPG/PNG, <5MB)";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $desc, $image, $id]);
        
        // Delete old sizes and insert new ones
        $pdo->prepare("DELETE FROM sizes WHERE category_id = ?")->execute([$id]);
        foreach ($sizes as $size_name) {
            $pdo->prepare("INSERT INTO sizes (category_id, size_name) VALUES (?, ?)")->execute([$id, $size_name]);
        }
        
        header("Location: categories.php");
        exit;
    }
}

// Fetch existing sizes for this category
$sizesStmt = $pdo->prepare("SELECT size_name FROM sizes WHERE category_id = ? ORDER BY id");
$sizesStmt->execute([$id]);
$existingSizes = $sizesStmt->fetchAll(PDO::FETCH_COLUMN);
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
    
    <!-- Sizes Section -->
    <div class="mb-3">
        <label class="fw-bold mb-2">Sizes for this Category</label>
        <div id="sizesContainer">
            <?php foreach ($existingSizes as $size): ?>
            <div class="input-group mb-2">
                <input type="text" name="sizes[]" class="form-control" placeholder="e.g., S, M, L, XL" value="<?= htmlspecialchars($size) ?>">
                <button type="button" class="btn btn-outline-danger removeSize">Remove</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addSizeField()">+ Add Size</button>
    </div>
    
    <button type="submit" class="btn btn-accent">Update Category</button>
</form>

<script>
function addSizeField() {
    const container = document.getElementById('sizesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" name="sizes[]" class="form-control" placeholder="e.g., S, M, L, XL" value="">
        <button type="button" class="btn btn-outline-danger removeSize" onclick="this.parentElement.remove(); updateRemoveButtons();">Remove</button>
    `;
    container.appendChild(div);
    updateRemoveButtons();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.removeSize').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.parentElement.remove();
            updateRemoveButtons();
        });
    });
});

function updateRemoveButtons() {
    const removes = document.querySelectorAll('.removeSize');
    removes.forEach((btn, idx) => {
        btn.style.display = removes.length > 1 ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', updateRemoveButtons);
</script>

<?php include '../footer.php'; ?>
</body>
</html>