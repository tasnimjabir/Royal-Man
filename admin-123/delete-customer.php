<?php
session_start();
if (!isset($_SESSION['admin_id'])) header("Location: login.php");
require '../includes/db.php';

$id = $_GET['id'];
$pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'")->execute([$id]);
header("Location: customers.php");
exit;
?>