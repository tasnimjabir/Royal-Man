<?php 
session_start();
require_once 'includes/db.php'; 

// login.php - Login with Password & Nice UI

if (isset($_SESSION['user_id'])) header("Location: index.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND email_verified = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email, password, or unverified account";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Royal Man</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .login-card { width: 410px; margin: auto; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .login-header { background: #d4af37; color: #000; padding: 40px 30px; text-center; }
        .login-body { padding: 40px 30px; background: white; }
        .btn-gold { background: #d4af37; color: #000; border: none; font-weight: 600; }
        .btn-gold:hover { background: #c19a2e; }
        .btn-google { background: #fff; border: 1px solid #ddd; color: #000; }
        .btn-google img { width: 20px; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

<div class="login-card">
    <div class="login-header">
        <h2 class="fw-bold mb-0">Welcome Back</h2>
        <p class="small">Login to Royal Man</p>
    </div>
    <div class="login-body">
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-500">Email</label>
                <input type="email" name="email" class="form-control rounded-pill py-3" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-500">Password</label>
                <input type="password" name="password" class="form-control rounded-pill py-3" required>
            </div>
            <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill mb-3">Login</button>
        </form>
        <!-- <a href="google-login.php" class="btn btn-google w-100 py-3 rounded-pill">
            <img src="https://developers.google.com/identity/images/g-logo.png" class="me-2"> Continue with Google
        </a> -->
        <p class="text-center mt-3 small text-muted">New here? <a href="register.php" class="text-gold fw-500">Sign Up</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>