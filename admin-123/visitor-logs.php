<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
$page = 'visitor-logs';   // This will highlight the menu item
require '../includes/db.php';
?>

<?php include '../includes/admin-header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-globe me-2"></i> Visitor Logs</h2>
    <div>
        <a href="download-log.php" class="btn btn-success btn-sm">
            <i class="bi bi-download"></i> Download Raw Log
        </a>
        <button onclick="clearLog()" class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i> Clear Log
        </button>
    </div>
</div>

<?php

// Clear log file
if (isset($_GET['clear']) && $_GET['clear'] == 'yes') {
    file_put_contents(__DIR__ . '/../visitor-log.txt', '');
    echo '<div class="alert alert-success">Log cleared successfully!</div>';
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="">
                    <tr>
                        <th>Date & Time</th>
                        <th>IP Address</th>
                        <th>Location</th>
                        <th>Page</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logFile = __DIR__ . '/../visitor-log.txt';
                    if (file_exists($logFile) && filesize($logFile) > 0) {
                        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                        $lines = array_reverse($lines); // newest first
                        foreach ($lines as $line) {
                            if (preg_match('/^\[(.+?)\]\s+IP:\s+(.+?)\s+\|\s+Location:\s+(.+?)\s+\|\s+Page:\s+(.+?)\s+\|\s+Agent:\s+(.+)/', $line, $m)) {
                                echo "<tr>
                                    <td><code>{$m[1]}</code></td>
                                    <td><strong>{$m[2]}</strong></td>
                                    <td>{$m[3]}</td>
                                    <td><small>{$m[4]}</small></td>
                                    <td><small class='text-muted'>{$m[5]}</small></td>
                                  </tr>";
                            }
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center py-5 text-muted">No visitor logs yet.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function clearLog() {
    if (confirm('Are you sure you want to delete all visitor logs?')) {
        window.location.href = 'visitor-logs.php?clear=yes';
    }
}
</script>

<?php include '../footer.php'; ?>
</body>
</html>