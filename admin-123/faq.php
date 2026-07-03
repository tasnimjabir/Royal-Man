<?php
session_start();
// Admin check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db.php';

$error = '';
$success = '';

// Handle Delete FAQ
if (isset($_GET['delete'])) {
    $faq_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM faq WHERE id = ?");
    if ($stmt->execute([$faq_id])) {
        $success = "FAQ deleted successfully!";
    } else {
        $error = "Failed to delete FAQ.";
    }
}

// Handle Add/Update FAQ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faq_id = !empty($_POST['faq_id']) ? (int)$_POST['faq_id'] : null;
    $ques = trim($_POST['ques']);
    $ans = trim($_POST['ans']);
    $page = trim($_POST['page']);
    $sort_order = (int)$_POST['sort_order'];

    if (empty($ques) || empty($ans) || empty($page)) {
        $error = "All fields are required.";
    } else {
        try {
            if ($faq_id) {
                // Update existing FAQ
                $stmt = $pdo->prepare("UPDATE faq SET ques = ?, ans = ?, page = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([$ques, $ans, $page, $sort_order, $faq_id]);
                $success = "FAQ updated successfully!";
            } else {
                // Add new FAQ
                $stmt = $pdo->prepare("INSERT INTO faq (ques, ans, page, sort_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$ques, $ans, $page, $sort_order]);
                $success = "FAQ added successfully!";
            }
            $faq_id = null;
            $ques = $ans = $page = '';
            $sort_order = 0;
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all FAQs
$faqStmt = $pdo->query("SELECT * FROM faq ORDER BY page, sort_order ASC");
$faqs = $faqStmt->fetchAll();

// Get distinct pages
$pagesStmt = $pdo->query("SELECT DISTINCT page FROM faq ORDER BY page");
$pages = $pagesStmt->fetchAll(PDO::FETCH_COLUMN);

// If editing, fetch that FAQ data
$edit_faq = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_faq = $stmt->fetch();
}
?>

<?php include '../includes/admin-header.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-chat-dots me-2"></i>FAQ Management
                    </h4>
                </div>
            </div>

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

            <div class="row g-4">
                <!-- Add/Edit FAQ Form -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>
                                <?= $edit_faq ? 'Edit FAQ' : 'Add New FAQ' ?>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <?php if ($edit_faq): ?>
                                    <input type="hidden" name="faq_id" value="<?= $edit_faq['id'] ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Question <span class="text-danger">*</span></label>
                                    <input type="text" name="ques" class="form-control" 
                                           value="<?= htmlspecialchars($edit_faq['ques'] ?? $_POST['ques'] ?? '') ?>" 
                                           placeholder="Enter question" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Answer <span class="text-danger">*</span></label>
                                    <textarea name="ans" rows="6" class="form-control" 
                                              placeholder="Enter detailed answer..." required><?= htmlspecialchars($edit_faq['ans'] ?? $_POST['ans'] ?? '') ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Page <span class="text-danger">*</span></label>
                                    <select name="page" class="form-select" required>
                                        <option value="cart" <?= ($edit_faq['page'] ?? '') === 'cart' ? 'selected' : '' ?>>Cart</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" 
                                           value="<?= htmlspecialchars($edit_faq['sort_order'] ?? $_POST['sort_order'] ?? '0') ?>" 
                                           placeholder="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="bi bi-check-lg me-2"></i><?= $edit_faq ? 'Update' : 'Add' ?>
                                    </button>
                                    <?php if ($edit_faq): ?>
                                        <a href="faq.php" class="btn btn-secondary">
                                            <i class="bi bi-x-lg me-2"></i>Cancel
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- FAQs List -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-list-ul me-2"></i>All FAQs
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($faqs)): ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                    <p>No FAQs created yet. Add your first FAQ!</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-bold">Question</th>
                                                <th class="fw-bold">Page</th>
                                                <th class="fw-bold text-center" style="width: 100px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($faqs as $faq): ?>
                                            <tr>
                                                <td>
                                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars(substr($faq['ques'], 0, 50)) ?><?= strlen($faq['ques']) > 50 ? '...' : '' ?></h6>
                                                    <small class="text-muted">Sort: <?= $faq['sort_order'] ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= htmlspecialchars($faq['page']) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="?edit=<?= $faq['id'] ?>" class="btn btn-sm btn-primary rounded-pill me-2" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="?delete=<?= $faq['id'] ?>" class="btn btn-sm btn-danger rounded-pill" title="Delete" onclick="return confirm('Are you sure?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
