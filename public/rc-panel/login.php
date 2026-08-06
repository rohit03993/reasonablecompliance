<?php
require __DIR__ . '/auth.php';
$config = require __DIR__ . '/config.php';

$error = '';

if (!empty($_SESSION['rc_logged_in'])) {
    header('Location: /rc-panel/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim((string) ($_POST['username'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');

    if ($user === $config['username'] && password_verify($pass, $config['password_hash'])) {
        $_SESSION['rc_logged_in'] = true;
        header('Location: /rc-panel/');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Login | Content Admin</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body class="login-page">
  <form class="login-card" method="post" action="/rc-panel/login.php">
    <h1>Reasonable Compliance</h1>
    <p class="muted">Content Admin</p>
    <?php if ($error): ?>
      <p class="error"><?= manage_h($error) ?></p>
    <?php endif; ?>
    <label>Username
      <input type="text" name="username" required autocomplete="username" />
    </label>
    <label>Password
      <input type="password" name="password" required autocomplete="current-password" />
    </label>
    <button type="submit">Log in</button>
  </form>
</body>
</html>
