<?php
// require '../config.php';
try {
    $pdo = new PDO('mysql:host=localhost;dbname=royal_man', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('DB Error');
}
?>