<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
    $desc = $_POST['description'];
    $sizes = $_POST['sizes'] ?? [];
    $sizes = array_filter($sizes);

    $image = '';
    if ($_FILES['image']['size'] > 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['image']['size'] < 5000000) {
            // Create directory if it doesn't exist
            $uploadDir = '../uploads/categories/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
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
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $desc, $image]);
        $cat_id = $pdo->lastInsertId();

        // Insert sizes
        foreach ($sizes as $size_name) {
            $pdo->prepare("INSERT INTO sizes (category_id, size_name) VALUES (?, ?)")->execute([$cat_id, $size_name]);
        }

        header("Location: categories.php");
        exit;
    }
}
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Add Category</h2>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label>Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    
    <!-- Sizes Section -->
    <div class="mb-3">
        <label class="fw-bold mb-2">Sizes for this Category</label>
        <div id="sizesContainer">
            <div class="input-group mb-2">
                <input type="text" name="sizes[]" class="form-control" placeholder="e.g., S, M, L, XL" value="">
                <button type="button" class="btn btn-outline-danger removeSize" style="display:none;">Remove</button>
            </div>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addSizeField()">+ Add Size</button>
    </div>

    <button type="submit" class="btn btn-accent">Create Category</button>
</form>

<script>
function addSizeField() {
    const container = document.getElementById('sizesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" name="sizes[]" class="form-control" placeholder="e.g., S, M, L, XL" value="">
        <button type="button" class="btn btn-outline-danger removeSize" onclick="this.parentElement.remove()">Remove</button>
    `;
    container.appendChild(div);
    updateRemoveButtons();
}

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
