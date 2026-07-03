<?php 
session_start();
require_once 'includes/db.php'; 

// verify-otp.php - Verify OTP after signup

if (!isset($_SESSION['verify_user_id'])) header("Location: register.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND otp_code = ? AND otp_expires > NOW()");
    $stmt->execute([$_SESSION['verify_user_id'], $otp]);
    $user = $stmt->fetch();

    if ($user) {
        $pdo->prepare("UPDATE users SET email_verified = 1, otp_code = NULL, otp_expires = NULL WHERE id = ?")->execute([$user['id']]);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        unset($_SESSION['verify_user_id']);
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid or expired OTP";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP - Royal Man</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .verify-card { max-width: 450px; margin: auto; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .verify-header { background: #d4af37; color: #000; padding: 40px 30px; text-center; }
        .verify-body { padding: 40px 30px; background: white; }
        .otp-input { font-size: 2rem; letter-spacing: 1rem; text-align: center; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

<div class="verify-card">
    <div class="verify-header">
        <h2 class="fw-bold mb-0">Verify Your Email</h2>
        <p class="small">Enter the 6-digit OTP sent to your email</p>
    </div>
    <div class="verify-body">
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <input type="text" name="otp" class="form-control otp-input rounded-pill py-3" maxlength="6" pattern="\d{6}" required>
            </div>
            <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill">Verify</button>
        </form>
        <p class="text-center mt-3 small text-muted"><a href="resend-otp.php" class="text-gold fw-500">Resend OTP</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>