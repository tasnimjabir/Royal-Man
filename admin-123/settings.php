<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';
$page = 'settings';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "New passwords do not match";
    } else if (strlen($new_pass) < 8) {
        $error = "New password must be at least 8 characters";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? AND role = 'admin'");
        $stmt->execute([$_SESSION['admin_id']]);
        $hash = $stmt->fetchColumn();

        if (password_verify($old_pass, $hash)) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$new_hash, $_SESSION['admin_id']]);
            $success = "Password changed successfully";
        } else {
            $error = "Incorrect old password";
        }
    }
}

// Handle shipping metadata update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_shipping'])) {
    $in_dhaka = trim($_POST['shipping_in_dhaka']);
    $outside = trim($_POST['shipping_outside_dhaka']);

    if (!is_numeric($in_dhaka) || !is_numeric($outside)) {
        $error = "Shipping values must be numeric";
    } else {
        // Upsert metadata values
        $ins = $pdo->prepare("INSERT INTO metadata (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
        $ins->execute(['in_dhaka', $in_dhaka, $in_dhaka]);
        $ins->execute(['outside_dhaka', $outside, $outside]);
        $success = "Shipping prices updated";
    }
}

// Load current shipping metadata
$shipping_in_dhaka = '0';
$shipping_outside = '0';
try {
    $stmt = $pdo->prepare("SELECT value FROM metadata WHERE name = ?");
    $stmt->execute(['in_dhaka']);
    $shipping_in_dhaka = $stmt->fetchColumn() ?: '0';
    $stmt->execute(['outside_dhaka']);
    $shipping_outside = $stmt->fetchColumn() ?: '0';
} catch (Exception $e) {
    // metadata table may not exist yet
}

// Handle Two Styles Section images upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_trends'])) {
    $upload_dir = '../uploads/trends/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Process trend section 1 image
    if (isset($_FILES['trendsection1_img']) && $_FILES['trendsection1_img']['error'] == 0) {
        $file = $_FILES['trendsection1_img'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($ext), $allowed) && $file['size'] <= 5242880) { // 5MB max
            $filename = 'trend1_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                $stmt = $pdo->prepare("INSERT INTO metadata (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute(['trendsection1', $filename, $filename]);
                $success = "Trend Section 1 image updated";
            } else {
                $error = "Failed to upload Trend Section 1 image";
            }
        } else {
            $error = "Invalid file format or size for Trend Section 1. Max 5MB, allowed: JPG, PNG, GIF, WebP";
        }
    }

    // Process trend section 2 image
    if (isset($_FILES['trendsection2_img']) && $_FILES['trendsection2_img']['error'] == 0) {
        $file = $_FILES['trendsection2_img'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($ext), $allowed) && $file['size'] <= 5242880) { // 5MB max
            $filename = 'trend2_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                $stmt = $pdo->prepare("INSERT INTO metadata (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute(['trendsection2', $filename, $filename]);
                $success = "Trend Section 2 image updated";
            } else {
                $error = "Failed to upload Trend Section 2 image";
            }
        } else {
            $error = "Invalid file format or size for Trend Section 2. Max 5MB, allowed: JPG, PNG, GIF, WebP";
        }
    }
}

// Load current trend section images
$trendsection1_img = 'trends/trend1_default.jpg';
$trendsection2_img = 'trends/trend2_default.jpg';
try {
    $stmt = $pdo->prepare("SELECT value FROM metadata WHERE name = ?");
    $stmt->execute(['trendsection1']);
    $trendsection1_img = $stmt->fetchColumn() ? 'trends/' . $stmt->fetchColumn() : $trendsection1_img;
    $stmt->execute(['trendsection2']);
    $trendsection2_img = $stmt->fetchColumn() ? 'trends/' . $stmt->fetchColumn() : $trendsection2_img;
} catch (Exception $e) {
    // metadata table may not exist yet
}
?>

<?php include '../includes/admin-header.php'; ?>

<h2 class="mb-4">Settings</h2>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div class="card p-4 bg-white rounded shadow">
    <h4 class="mb-4 text-primary">Change Password</h4>

    <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <div class="mb-3">
            <label>Old Password</label>
            <input type="password" name="old_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

<!-- Shipping Settings -->
<div class="card p-4 bg-white rounded shadow mt-4">
    <h4 class="mb-4 text-primary">Shipping Prices</h4>

    <form method="POST">
        <input type="hidden" name="save_shipping" value="1">
        <div class="mb-3">
            <label>Dhaka Shipping Price (৳)</label>
            <input type="text" name="shipping_in_dhaka" class="form-control" value="<?= htmlspecialchars($shipping_in_dhaka) ?>" required>
        </div>
        <div class="mb-3">
            <label>Outside Dhaka Shipping Price (৳)</label>
            <input type="text" name="shipping_outside_dhaka" class="form-control" value="<?= htmlspecialchars($shipping_outside) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Shipping Prices</button>
    </form>
</div>

<!-- Two Styles Section Images -->
<div class="card p-4 bg-white rounded shadow mt-4">
    <h4 class="mb-4 text-primary">Two Styles Section - Background Images</h4>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="save_trends" value="1">
        
        <div class="mb-4">
            <h6 class="text-secondary">Trend Section 1 Image</h6>
            <?php if (file_exists("../$trendsection1_img")): ?>
                <div class="mb-3">
                    <img src="../<?= htmlspecialchars($trendsection1_img) ?>" class="img-fluid rounded" style="max-width: 300px; height: auto;">
                </div>
            <?php endif; ?>
            <label>Upload New Image</label>
            <input type="file" name="trendsection1_img" class="form-control" accept="image/*">
            <small class="text-muted">Max 5MB. Allowed: JPG, PNG, GIF, WebP</small>
        </div>

        <hr>

        <div class="mb-4">
            <h6 class="text-secondary">Trend Section 2 Image</h6>
            <?php if (file_exists("../$trendsection2_img")): ?>
                <div class="mb-3">
                    <img src="../<?= htmlspecialchars($trendsection2_img) ?>" class="img-fluid rounded" style="max-width: 300px; height: auto;">
                </div>
            <?php endif; ?>
            <label>Upload New Image</label>
            <input type="file" name="trendsection2_img" class="form-control" accept="image/*">
            <small class="text-muted">Max 5MB. Allowed: JPG, PNG, GIF, WebP</small>
        </div>

        <button type="submit" class="btn btn-primary">Save Trend Section Images</button>
    </form>
</div>

<?php include '../footer.php'; ?>
</body>
</html>