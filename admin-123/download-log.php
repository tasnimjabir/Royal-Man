<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Access denied");
}

// Change this path if you used monthly logs
$logFile = __DIR__ . '/../visitor-log.txt';           // <-- single file
// OR for monthly logs:
// $logFile = __DIR__ . '/../logs/visitors-' . date('Y-m') . '.txt';

if (!file_exists($logFile) || filesize($logFile) === 0) {
    die("Log file is empty or not found.");
}

// Force download as plain text
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="RoyalMan-Visitor-Log-' . date('Y-m-d_H-i-s') . '.txt"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($logFile));

readfile($logFile);
exit;
?>