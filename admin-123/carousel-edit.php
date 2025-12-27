<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'carousel';  // Or add a new menu item for 'carousel'

$stmt = $pdo->query("SELECT * FROM carousels ORDER BY sort_order ASC, id ASC");
$items = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add' || $_POST['action'] == 'update') {
        $big_text = $_POST['big_text'];
        $small_text = $_POST['small_text'];
        $button_text = $_POST['button_text'];
        $button_link = $_POST['button_link'];
        $sort_order = (int)$_POST['sort_order'];
        $active = isset($_POST['active']) ? 1 : 0;

        $image = $_POST['existing_image'] ?? '';
        if ($_FILES['image']['size'] > 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['image']['size'] < 5000000) {
                if ($image && file_exists('../uploads/carousels/' . $image)) unlink('../uploads/carousels/' . $image);
                $image = time() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/carousels/' . $image);
            } else {
                $error = "Invalid image";
            }
        }

        if (!isset($error)) {
            if ($_POST['action'] == 'add') {
                $stmt = $pdo->prepare("INSERT INTO carousels (image, big_text, small_text, button_text, button_link, sort_order, active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$image, $big_text, $small_text, $button_text, $button_link, $sort_order, $active]);
            } else {
                $id = $_POST['id'];
                $stmt = $pdo->prepare("UPDATE carousels SET image = ?, big_text = ?, small_text = ?, button_text = ?, button_link = ?, sort_order = ?, active = ? WHERE id = ?");
                $stmt->execute([$image, $big_text, $small_text, $button_text, $button_link, $sort_order, $active, $id]);
            }
            header("Location: carousel-edit.php");
            exit;
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT image FROM carousels WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetchColumn();
        if ($image && file_exists('../uploads/carousels/' . $image)) unlink('../uploads/carousels/' . $image);
        $pdo->prepare("DELETE FROM carousels WHERE id = ?")->execute([$id]);
        header("Location: carousel-edit.php");
        exit;
    }
}
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Carousel Edit</h2>

<!-- List Items -->
<table class="table table-hover bg-white rounded shadow mb-5">
    <thead>
        <tr><th>Image</th><th>Big Text</th><th>Small Text</th><th>Button</th><th>Order</th><th>Active</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td><img src="../uploads/carousels/<?= htmlspecialchars($item['image']) ?>" width="100"></td>
            <td><?= htmlspecialchars($item['big_text']) ?></td>
            <td><?= substr(htmlspecialchars($item['small_text']), 0, 50) ?>...</td>
            <td><?= htmlspecialchars($item['button_text']) ?></td>
            <td><?= $item['sort_order'] ?></td>
            <td><span class="badge bg-<?= $item['active'] ? 'success' : 'danger' ?>"><?= $item['active'] ? 'Yes' : 'No' ?></span></td>
            <td>
                <a href="?edit=<?= $item['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Add/Edit Form -->
<h4><?= isset($_GET['edit']) ? 'Edit' : 'Add' ?> Slide</h4>
<?php 
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM carousels WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_item = $stmt->fetch();
}
?>
<form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    <input type="hidden" name="action" value="<?= $edit_item ? 'update' : 'add' ?>">
    <?php if ($edit_item): ?><input type="hidden" name="id" value="<?= $edit_item['id'] ?>"><?php endif; ?>
    <input type="hidden" name="existing_image" value="<?= $edit_item['image'] ?? '' ?>">
    <div class="mb-3">
        <label>Big Text</label>
        <input type="text" name="big_text" class="form-control" value="<?= htmlspecialchars($edit_item['big_text'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label>Small Text</label>
        <textarea name="small_text" class="form-control" rows="3"><?= htmlspecialchars($edit_item['small_text'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
        <label>Button Text</label>
        <input type="text" name="button_text" class="form-control" value="<?= htmlspecialchars($edit_item['button_text'] ?? 'Shop Now') ?>" required>
    </div>
    <div class="mb-3">
        <label>Button Link</label>
        <input type="text" name="button_link" class="form-control" value="<?= htmlspecialchars($edit_item['button_link'] ?? '#shop') ?>" required>
    </div>
    <div class="mb-3">
        <label>Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="<?= $edit_item['sort_order'] ?? 0 ?>" required>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="active" class="form-check-input" id="active" <?= isset($edit_item['active']) && $edit_item['active'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="active">Active</label>
    </div>
    <div class="mb-3">
        <label>Background Image <?= $edit_item ? '(Current: <img src="../uploads/carousels/' . htmlspecialchars($edit_item['image']) . '" width="100">)' : '' ?></label>
        <input type="file" name="image" class="form-control" accept="image/*" <?= !$edit_item ? 'required' : '' ?>>
    </div>
    <button type="submit" class="btn btn-accent"><?= $edit_item ? 'Update' : 'Add' ?> Slide</button>
</form>

<?php include '../footer.php'; ?>
</body>
</html>