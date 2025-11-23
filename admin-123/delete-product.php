<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT main_image FROM products WHERE id = ?");
$stmt->execute([$id]);
$main = $stmt->fetchColumn();
if (file_exists('../uploads/products/' . $main) && $main) unlink('../uploads/products/' . $main);

$imgs = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ?");
$imgs->execute([$id]);
foreach ($imgs->fetchAll(PDO::FETCH_COLUMN) as $img) {
    if (file_exists('../uploads/products/' . $img)) unlink('../uploads/products/' . $img);
}

$pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$id]);

header("Location: products.php");
exit;
?>