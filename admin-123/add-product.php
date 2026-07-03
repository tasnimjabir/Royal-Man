<?php
session_start();
// 1. Login check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db.php';

// Fetch categories
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$error = '';
$success = '';

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $slug = preg_replace('/[^a-z0-9-]/', '-', strtolower($name));
    // Remove duplicate hyphens
    $slug = preg_replace('/-+/', '-', $slug); 
    
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    // Validation
    if (empty($name) || empty($price)) {
        $error = "Product name and price are required.";
    }

    // Main image handling
    $main_image = '';
    if (!$error && !empty($_FILES['main_image']['name'])) {
        $file = $_FILES['main_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed) && $file['size'] < 5000000) { // 5MB
            $main_image = time() . '_main.' . $ext;
            // Create folder if it doesn't exist
            if (!is_dir('../uploads/products/')) mkdir('../uploads/products/', 0777, true);
            
            move_uploaded_file($file['tmp_name'], '../uploads/products/' . $main_image);
        } else {
            $error = "Main Image: Invalid format (jpg, png, webp) or too large (max 5MB).";
        }
    } elseif (!$error) {
        $error = "Main image is required.";
    }

    // Insert into database
    if (!$error) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, category_id, main_image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $desc, $price, $cat_id, $main_image]);
            $product_id = $pdo->lastInsertId();

            // Gallery image handling (loop)
            if (!empty($_FILES['images']['name'][0])) {
                $files = $_FILES['images'];
                $count = count($files['name']);
                
                $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");

                for ($i = 0; $i < $count; $i++) {
                    if ($files['error'][$i] === 0) {
                        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $files['size'][$i] < 5000000) {
                            $filename = time() . "_{$product_id}_{$i}." . $ext;
                            move_uploaded_file($files['tmp_name'][$i], '../uploads/products/' . $filename);
                            
                            $stmt_img->execute([$product_id, $filename]);
                        }
                    }
                }
            }

            $pdo->commit();
            $success = "Product added successfully!";
            
            // Reset variables to clear the form
            $name = $desc = $price = $cat_id = '';
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/admin-header.php'; ?>

<style>
    /* Custom styles for better UI */
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
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        height: 120px;
        border: 1px solid #e2e8f0;
    }
    .preview-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-remove:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-box-seam me-2"></i>Add New Product</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="productForm">
                        <div class="row g-4">
                            
                            <div class="col-lg-8">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control form-control-lg" 
                                                   value="<?= htmlspecialchars($name ?? '') ?>" placeholder="Enter product name" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Price (৳) <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" name="price" class="form-control" 
                                                       value="<?= htmlspecialchars($price ?? '') ?>" placeholder="0.00" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Category</label>
                                                <select name="category_id" class="form-select">
                                                    <option value="">-- Select Category --</option>
                                                    <?php foreach($cats as $c): ?>
                                                        <option value="<?= $c['id'] ?>" <?= (isset($cat_id) && $cat_id == $c['id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($c['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Description</label>
                                            <textarea name="description" rows="6" class="form-control" placeholder="Product details..."><?= htmlspecialchars($desc ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Main Image <span class="text-danger">*</span></label>
                                    <div class="upload-area p-4 text-center" onclick="document.getElementById('mainImage').click()">
                                        <input type="file" name="main_image" id="mainImage" accept="image/*" hidden required onchange="previewMain(this)">
                                        <div id="mainPreviewBox">
                                            <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                                            <p class="small text-muted mt-2 mb-0">Click to upload cover image</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label fw-bold">Gallery Images</label>
                                    <div class="upload-area p-3 text-center mb-3" onclick="document.getElementById('galleryImages').click()">
                                        <input type="file" name="images[]" id="galleryImages" multiple accept="image/*" hidden>
                                        <i class="bi bi-images fs-3 text-muted"></i>
                                        <p class="small text-muted mb-0">Add multiple images</p>
                                    </div>
                                    <div class="row g-2" id="galleryPreview"></div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <hr>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-lg"></i> Save Product
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
// 1. Main image preview function
function previewMain(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('mainPreviewBox').innerHTML = `
                <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// 2. Gallery image handling (Add + Remove System)
const galleryInput = document.getElementById('galleryImages');
const galleryContainer = document.getElementById('galleryPreview');
// Use DataTransfer object to manage file list
let galleryFiles = new DataTransfer();

galleryInput.addEventListener('change', function(e) {
    const newFiles = e.target.files;

    // Add newly selected files to our custom list
    for (let i = 0; i < newFiles.length; i++) {
        // Duplicate check (optional)
        let isDuplicate = false;
        for(let j=0; j<galleryFiles.files.length; j++){
             if(galleryFiles.files[j].name === newFiles[i].name){
                 isDuplicate = true;
                 break;
             }
        }
        if(!isDuplicate){
            galleryFiles.items.add(newFiles[i]);
        }
    }

    // Update input file list (important for database submission)
    galleryInput.files = galleryFiles.files;

    // Render preview
    renderGallery();
});

function renderGallery() {
    galleryContainer.innerHTML = ''; // Clear previous preview

    Array.from(galleryFiles.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-4'; // 3 images per row
            col.innerHTML = `
                <div class="preview-card">
                    <img src="${e.target.result}">
                    <button type="button" class="btn-remove" onclick="removeImage(${index})">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
            galleryContainer.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

// Function to delete image
window.removeImage = function(index) {
    // Create new DataTransfer and copy all except the deleted one
    const dt = new DataTransfer();
    const inputFiles = galleryFiles.files;

    for (let i = 0; i < inputFiles.length; i++) {
        if (i !== index) {
            dt.items.add(inputFiles[i]); // Keep if index doesn't match
        }
    }

    galleryFiles = dt; // Update global list
    galleryInput.files = galleryFiles.files; // Update input tag (for database submission)
    
    renderGallery(); // Render preview again
}
</script>

<?php include '../footer.php'; ?>