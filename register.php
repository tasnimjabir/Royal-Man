<?php 
session_start();
require_once 'includes/db.php'; 
require 'auth/PHPMailer-master/src/PHPMailer.php';
require 'auth/PHPMailer-master/src/SMTP.php';
require 'auth/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
        
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id, email_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['email_verified'] == 1) {
            $error = "Email already registered";
        } else {
                $user_id = $user['id'];
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expires = date('Y-m-d H:i:s', time() + 600);  // 10 minutes

                $pdo->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE id = ?")->execute([$otp, $expires, $user_id]);

                // Send token via Gmail SMTP using App Password
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'shadmanshakibdip18@gmail.com'; // আপনার Gmail
                    $mail->Password = 'gddo zbef hjjv nntv';   // Gmail App Password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('shadmanshakibdip18@gmail.com', 'East West Hospital');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Email';
                    $mail->Body    = "<p>Dear {$name},</p>
                                    <p>Your email verification token is: <b>{$otp}</b></p>
                                    <p>Enter this token in the website to verify your email.</p>";

                    $mail->send();
                $_SESSION['verify_user_id'] = $user_id;
                header("Location: verify-otp.php");
                exit;
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
        }
    }
    else{
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, email_verified) VALUES (?, ?, ?, 0)");
        $stmt->execute([$name, $email, $password]);
        $user_id = $pdo->lastInsertId();
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expires = date('Y-m-d H:i:s', time() + 600);  // 10 minutes

            $pdo->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE id = ?")->execute([$otp, $expires, $user_id]);

            // Send token via Gmail SMTP using App Password
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'shadmanshakibdip18@gmail.com'; // আপনার Gmail
                $mail->Password = 'gddo zbef hjjv nntv';   // Gmail App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('shadmanshakibdip18@gmail.com', 'East West Hospital');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Email';
                $mail->Body    = "<p>Dear {$name},</p>
                                <p>Your email verification token is: <b>{$otp}</b></p>
                                <p>Enter this token in the website to verify your email.</p>";

                $mail->send();
            $_SESSION['verify_user_id'] = $user_id;
            header("Location: verify-otp.php");
            exit;
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - Royal Man</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .signup-card { max-width: 450px; margin: auto; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .signup-header { background: #d4af37; color: #000; padding: 40px 30px; text-center; }
        .signup-body { padding: 40px 30px; background: white; }
        .btn-gold { background: #d4af37; color: #000; border: none; font-weight: 600; }
        .btn-gold:hover { background: #c19a2e; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

<div class="signup-card">
    <div class="signup-header">
        <h2 class="fw-bold mb-0">Join Royal Man</h2>
        <p class="small">Premium fashion awaits</p>
    </div>
    <div class="signup-body">
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-500">Full Name</label>
                <input type="text" name="name" class="form-control rounded-pill py-3" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-500">Email</label>
                <input type="email" name="email" class="form-control rounded-pill py-3" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-500">Password</label>
                <input type="password" name="password" class="form-control rounded-pill py-3" required>
            </div>
            <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill">Sign Up</button>
        </form>
        <p class="text-center mt-3 small text-muted">Already have an account? <a href="login.php" class="text-gold fw-500">Login</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>