<?php
session_start();
require_once 'db.php';

function createSlug($text) {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($text)));
}

function getCart() {
    global $pdo;
    $session_id = session_id();

    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
    }

    $cart = $stmt->fetch();
    if (!$cart) {
        $pdo->prepare("INSERT INTO cart (user_id, session_id) VALUES (?, ?)")->execute([
            $_SESSION['user_id'] ?? null, $session_id
        ]);
        $cart['id'] = $pdo->lastInsertId();
    }
    return $cart['id'];
}

function addToCart($product_id, $variant_id = null, $qty = 1) {
    global $pdo;
    $cart_id = getCart();
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND variant_id " . ($variant_id ? "= ?" : "IS NULL"));
    $stmt->execute($variant_id ? [$cart_id, $product_id, $variant_id] : [$cart_id, $product_id]);
    $item = $stmt->fetch();

    if ($item) {
        $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE id = ?")->execute([$qty, $item['id']]);
    } else {
        $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)")->execute([$cart_id, $product_id, $variant_id, $qty]);
    }
}

function cartCount() {
    global $pdo;
    $cart_id = getCart();
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cart_id]);
    $row = $stmt->fetch();
    return $row['total'] ?? 0;
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>