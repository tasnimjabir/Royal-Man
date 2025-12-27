<?php
session_start();
require_once 'includes/db.php';

// checkout.php - Process Order Page (Guest or Logged-in)

// If cart empty, redirect
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Calculate Total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $notes = trim($_POST['notes']);
    $error = null;

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $error = "All fields are required";
    } else {
        $user_id = $_SESSION['user_id'] ?? null;
        $session_id = session_id();

        // Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, session_id, total, status, customer_name, customer_email, phone, shipping_address, notes) 
                               VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $session_id, $total, $name, $email, $phone, $address, $notes]);
        $order_id = $pdo->lastInsertId();

        // Insert Order Items
        foreach ($_SESSION['cart'] as $key => $item) {
            $pdo->prepare("INSERT INTO order_items (order_id, product_id, size_id, quantity, price_at_purchase) 
                           VALUES (?, ?, ?, ?, ?)")
                ->execute([$order_id, $item['product_id'], $item['size_id'] ?? null, $item['quantity'], $item['price']]);
        }

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to success page (create order-success.php)
        header("Location: order-success.php?order_id=$order_id");
        exit;
    }
}

// Pre-fill for logged-in users
$name = $email = $phone = $address = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $name = $user['name'] ?? '';
    $email = $user['email'] ?? '';
}
?>

<?php include 'header.php'; ?>

<!-- Checkout Hero -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h1 class="display-4 fw-bold text-dark">Checkout</h1>
        <p class="lead text-muted">Almost there! Complete your order</p>
    </div>
</section>

<!-- Checkout Form -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Order Summary -->
            <div class="col-lg-4 order-lg-last">
                <div class="sticky-top sticky-offset">
                    <div class="card border-0 shadow-sm p-4 bg-white">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                    <?php if (!empty($item['size_name'])): ?>
                                    <br><small class="text-muted">Size: <?= htmlspecialchars($item['size_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="fw-bold">৳<?= number_format($item['price'] * $item['quantity'], 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5 fw-bold">Total</span>
                            <span class="fs-5 fw-bold text-accent">৳<?= number_format($total, 0) ?></span>
                        </div>
                        <p class="text-success small text-center"><i class="bi bi-truck"></i> Free Shipping</p>
                    </div>
                </div>
            </div>

            <!-- Billing Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-5 bg-white">
                    <h4 class="fw-bold mb-4">Billing & Shipping Details</h4>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg" value="<?= htmlspecialchars($name) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="tel" name="phone" class="form-control form-control-lg" value="<?= htmlspecialchars($phone) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Shipping Address</label>
                                <textarea name="address" class="form-control form-control-lg" rows="4" required><?= htmlspecialchars($address) ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Order Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent w-100 py-3 rounded-pill fw-bold fs-5 shadow-lg">
                                    <i class="bi bi-credit-card me-2"></i> Place Order
                                </button>
                            </div>
                        </div>
                    </form>

                            <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="text-center mt-4">
                            <p class="text-muted">Create an account for faster checkout? <a href="register.php" class="text-accent fw-bold">Sign Up</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- moved per-page gold styles to assets/css/style.css; kept form control and shadow rules in central CSS if needed -->

<?php include 'footer.php'; ?>