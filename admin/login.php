<?php
session_start();
require __DIR__ . '/../db.php';
require __DIR__ . '/auth.php';
$cfg = require __DIR__ . '/../config.php';
$err = '';

if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $stmt = db()->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();
    if ($user && password_verify($p, $user['password_hash'])) {
        $_SESSION[$cfg['admin']['session_name']] = $user['id'];
        header('Location: index.php');
        exit;
    }
    $err = 'Credenziali errate';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<link rel="stylesheet" href="../asset/style.css">
<style>
    body { display: grid; place-items: center; min-height: 100vh; padding: 20px; }
    .login-card {
        width: 100%; max-width: 380px;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 40px 32px;
        box-shadow: var(--shadow-md);
    }
    .login-card h1 {
        font-size: 28px; font-weight: 700; letter-spacing: -0.025em;
        text-align: center; margin-bottom: 8px;
    }
    .login-card .sub {
        text-align: center; color: var(--text-tertiary);
        font-size: 14px; margin-bottom: 28px;
    }
    .login-card .err {
        background: rgba(255, 59, 48, 0.1); color: #ff3b30;
        padding: 10px 14px; border-radius: var(--radius-sm);
        font-size: 13px; text-align: center; margin-bottom: 16px;
    }
    .login-card button { width: 100%; margin-top: 8px; }
</style>
</head>
<body>

<div class="login-card">
    <h1>Bentornato</h1>
    <p class="sub">Accedi per gestire i tuoi contenuti</p>

    <?php if ($err): ?><div class="err"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="post">
        <p style="margin-bottom:12px;">
            <input name="username" placeholder="Username" required autofocus>
        </p>
        <p style="margin-bottom:20px;">
            <input name="password" type="password" placeholder="Password" required>
        </p>
        <button type="submit">Accedi</button>
    </form>
</div>

</body>
</html>