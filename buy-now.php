<?php
session_start();
require_once 'includes/db.php';

// Get POST values
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$size_id = isset($_POST['size_id']) && $_POST['size_id'] !== '' ? (int)$_POST['size_id'] : null;

if (!$product_id) {
    header('Location: index.php');
    exit;
}

// Validate product exists and fetch necessary data
$stmt = $pdo->prepare('SELECT id, name, price, main_image FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch size name if provided
$size_name = '';
if ($size_id) {
    $sizeStmt = $pdo->prepare("SELECT size_name FROM sizes WHERE id = ?");
    $sizeStmt->execute([$size_id]);
    $sizeRow = $sizeStmt->fetch();
    $size_name = $sizeRow['size_name'] ?? '';
}

// Create cart key same as cart.php: productId or productId_size_sizeId
$cart_key = $size_id ? "{$product_id}_size_{$size_id}" : (string)$product_id;

// Add or update item in associative cart
if (isset($_SESSION['cart'][$cart_key])) {
    $_SESSION['cart'][$cart_key]['quantity'] += max(1, $quantity);
} else {
    $_SESSION['cart'][$cart_key] = [
        'product_id' => $product_id,
        'size_id' => $size_id,
        'size_name' => $size_name,
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['main_image'] ?? '',
        'quantity' => max(1, $quantity)
    ];
}

// Redirect to checkout for instant purchase
header('Location: checkout.php');
exit;
