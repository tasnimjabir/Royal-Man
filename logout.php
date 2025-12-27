<?php 
session_start();

// logout.php - Simple Logout
session_destroy();
header("Location: /");
exit;
?>