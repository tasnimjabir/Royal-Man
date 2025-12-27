<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db.php';

// ১. প্রোডাক্ট আইডি চেক করা
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php"); // প্রোডাক্ট লিস্ট পেজে রিডাইরেক্ট
    exit();
}

$id = $_GET['id'];
$success = $error = '';

// ২. বর্তমান প্রোডাক্টের তথ্য আনা
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found!");
}

// ৩. গ্যালারি থেকে পুরনো ছবি ডিলিট করার লজিক (GET Request)
if (isset($_GET['delete_img'])) {
    $img_id = $_GET['delete_img'];
    
    // ছবির নাম আনা ফোল্ডার থেকে ডিলিট করার জন্য
    $stmt_img = $pdo->prepare("SELECT image FROM product_images WHERE id = ? AND product_id = ?");
    $stmt_img->execute([$img_id, $id]);
    $img_data = $stmt_img->fetch();

    if ($img_data) {
        $path = '../uploads/products/' . $img_data['image'];
        if (file_exists($path)) unlink($path); // ফাইল ডিলিট
        
        // ডাটাবেস থেকে ডিলিট
        $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$img_id]);
        
        // রিফ্রেশ করা যাতে URL থেকে প্যারামিটার চলে যায়
        header("Location: edit-product.php?id=" . $id . "&msg=deleted");
        exit();
    }
}

// ৪. প্রোডাক্ট আপডেট লজিক (POST Request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
    $slug = preg_replace('/-+/', '-', $slug);
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    if (empty($name) || empty($price)) {
        $error = "Name and Price are required.";
    }

    if (!$error) {
        try {
            // মেইন ইমেজ আপডেট (যদি নতুন সিলেক্ট করা হয়)
            $main_image = $product['main_image']; // ডিফল্ট হিসেবে পুরনোটা থাকবে
            
            if (!empty($_FILES['main_image']['name'])) {
                $file = $_FILES['main_image'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $file['size'] < 5000000) {
                    // পুরনো ছবি ডিলিট করা
                    $old_path = '../uploads/products/' . $product['main_image'];
                    if (file_exists($old_path) && !empty($product['main_image'])) unlink($old_path);

                    // নতুন ছবি আপলোড
                    $main_image = time() . '_main.' . $ext;
                    move_uploaded_file($file['tmp_name'], '../uploads/products/' . $main_image);
                } else {
                    $error = "Main image error: Invalid format or too large.";
                }
            }

            if (!$error) {
                // ডাটাবেস আপডেট
                $sql = "UPDATE products SET name=?, slug=?, description=?, price=?, category_id=?, main_image=? WHERE id=?";
                $pdo->prepare($sql)->execute([$name, $slug, $desc, $price, $cat_id, $main_image, $id]);

                // নতুন গ্যালারি ইমেজ যোগ করা
                if (!empty($_FILES['images']['name'][0])) {
                    $files = $_FILES['images'];
                    $count = count($files['name']);
                    $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");

                    for ($i = 0; $i < $count; $i++) {
                        if ($files['error'][$i] === 0) {
                            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                                $filename = time() . "_{$id}_{$i}." . $ext;
                                move_uploaded_file($files['tmp_name'][$i], '../uploads/products/' . $filename);
                                $stmt_img->execute([$id, $filename]);
                            }
                        }
                    }
                }

                $success = "Product updated successfully!";
                // রিফ্রেশ ডাটা
                $stmt->execute([$id]);
                $product = $stmt->fetch();
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// বর্তমান গ্যালারি ইমেজগুলো আনা
$gallery_images = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
$gallery_images->execute([$id]);
$existing_images = $gallery_images->fetchAll();
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<?php include '../includes/admin-header.php'; ?>

<style>
    /* Add Product এর স্টাইল কপি করা হয়েছে */
    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
    }
    .upload-area:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .preview-card {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: 100px;
        border: 1px solid #e2e8f0;
    }
    .preview-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-delete-db {
        position: absolute;
        top: 2px;
        right: 2px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Product</h4>
                    <a href="products.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($success || isset($_GET['msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $success ?: "Action completed successfully!" ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-4">
                            
                            <div class="col-lg-8">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Product Name</label>
                                            <input type="text" name="name" class="form-control form-control-lg" 
                                                   value="<?= htmlspecialchars($product['name']) ?>" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Price (৳)</label>
                                                <input type="number" step="0.01" name="price" class="form-control" 
                                                       value="<?= htmlspecialchars($product['price']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Category</label>
                                                <select name="category_id" class="form-select">
                                                    <option value="">-- Select Category --</option>
                                                    <?php foreach($cats as $c): ?>
                                                        <option value="<?= $c['id'] ?>" 
                                                            <?= ($product['category_id'] == $c['id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($c['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea name="description" rows="6" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Main Image</label>
                                    <div class="upload-area p-3 text-center" onclick="document.getElementById('mainImage').click()">
                                        <input type="file" name="main_image" id="mainImage" accept="image/*" hidden onchange="previewMain(this)">
                                        <div id="mainPreviewBox">
                                            <?php if (!empty($product['main_image'])): ?>
                                                <img src="../uploads/products/<?= $product['main_image'] ?>" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                                                <p class="small text-primary mt-2 mb-0">Click to change image</p>
                                            <?php else: ?>
                                                <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                                                <p class="small text-muted mt-2 mb-0">Upload Main Image</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold d-flex justify-content-between">
                                        <span>Current Gallery</span>
                                        <span class="badge bg-secondary"><?= count($existing_images) ?></span>
                                    </label>
                                    <div class="row g-2">
                                        <?php foreach($existing_images as $img): ?>
                                            <div class="col-4">
                                                <div class="preview-card">
                                                    <img src="../uploads/products/<?= $img['image'] ?>">
                                                    <a href="edit-product.php?id=<?= $id ?>&delete_img=<?= $img['id'] ?>" 
                                                       class="btn-delete-db" 
                                                       onclick="return confirm('Delete this image permanently?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if(empty($existing_images)): ?>
                                            <p class="small text-muted fst-italic">No gallery images found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label fw-bold">Add More Images</label>
                                    <div class="upload-area p-2 text-center mb-2" onclick="document.getElementById('newGallery').click()">
                                        <input type="file" name="images[]" id="newGallery" multiple accept="image/*" hidden>
                                        <i class="bi bi-plus-circle fs-3 text-success"></i>
                                        <p class="small text-muted mb-0">Click to add new</p>
                                    </div>
                                    <div class="row g-2" id="newGalleryPreview"></div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <hr>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check2-all"></i> Update Product
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Main Image Preview (Changes instantly)
function previewMain(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('mainPreviewBox').innerHTML = `
                <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 180px;">
                <p class="small text-success mt-2 mb-0">New image selected</p>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// New Gallery Upload Preview Logic
const galleryInput = document.getElementById('newGallery');
const galleryContainer = document.getElementById('newGalleryPreview');
let galleryFiles = new DataTransfer();

galleryInput.addEventListener('change', function(e) {
    const newFiles = e.target.files;
    for (let i = 0; i < newFiles.length; i++) {
        galleryFiles.items.add(newFiles[i]);
    }
    galleryInput.files = galleryFiles.files;
    renderNewGallery();
});

function renderNewGallery() {
    galleryContainer.innerHTML = '';
    Array.from(galleryFiles.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-4';
            col.innerHTML = `
                <div class="preview-card border-success">
                    <img src="${e.target.result}">
                    <button type="button" class="btn-delete-db bg-success" onclick="removeNewImage(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
            galleryContainer.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

window.removeNewImage = function(index) {
    const dt = new DataTransfer();
    const inputFiles = galleryFiles.files;
    for (let i = 0; i < inputFiles.length; i++) {
        if (i !== index) dt.items.add(inputFiles[i]);
    }
    galleryFiles = dt;
    galleryInput.files = galleryFiles.files;
    renderNewGallery();
}
</script>

<?php include '../footer.php'; ?>