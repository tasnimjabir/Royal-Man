<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royal Man Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #d4af37; --secondary: #1a1a1a; --accent: #28a745; --danger: #dc3545; }
        body { font-family: 'Inter', sans-serif; background: #f4f7fc; color: var(--secondary);}
        .sidebar { background: #fff; border-right: 1px solid #e9ecef; height: 100vh; position: fixed; width: 250px; padding-top: 60px; }
        .sidebar .nav-link { color: var(--secondary); font-weight: 500; padding: 15px 20px; border-radius: 8px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #f8f9fa; color: var(--primary); }
        .main-content { margin-left: 250px; padding: 80px 30px; min-height:800px }
        .admin-header { background: #fff; border-bottom: 1px solid #e9ecef; position: fixed; width: 100%; z-index: 1000; padding: 15px 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; }
        .card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: #c19a2e; }
        .btn-accent { background: var(--accent); color: white; }
        .table th { background: #f8f9fa; }
        .badge-primary { background: var(--primary); color: #000; }
    </style>
</head>
<?php
session_start();
require '../includes/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    //echo  $user['password'];

    if ($user) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-5 shadow-lg" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4 text-primary">Admin Login</h3>
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>