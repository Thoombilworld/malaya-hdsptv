<?php
require __DIR__ . '/../bootstrap.php';
$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Please refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        if ($email !== '') {
            $token = bin2hex(random_bytes(16));
            $stmt = mysqli_prepare(hs_db(), "INSERT INTO hs_password_resets (email, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = CURRENT_TIMESTAMP");
            mysqli_stmt_bind_param($stmt, 'ss', $email, $token);
            mysqli_stmt_execute($stmt);
            $msg = 'Reset token generated. Integrate SMTP to email the reset link.';
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Forgot Password</title><link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>"></head>
<body>
<div class="auth-shell">
  <section class="auth-brand"><div><h1>Password Recovery</h1><p>Recover your account securely.</p></div><small>Security first</small></section>
  <section class="auth-panel">
    <form class="auth-card" method="post">
      <h2>Forgot password</h2>
      <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($msg): ?><div class="success-msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <?= hs_csrf_input() ?>
      <div class="auth-field"><label>Email</label><input type="email" name="email" required></div>
      <button class="btn btn-primary" type="submit">Generate Token</button>
      <p class="meta"><a href="<?= hs_base_url('auth/login.php') ?>">Back to login</a></p>
    </form>
  </section>
</div>
</body></html>
