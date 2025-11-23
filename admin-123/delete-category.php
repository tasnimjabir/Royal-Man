<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
$stmt->execute([$id]);
$image = $stmt->fetchColumn();
if (file_exists('../uploads/categories/' . $image) && $image) unlink('../uploads/categories/' . $image);

$pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);

header("Location: categories.php");
exit;
?>