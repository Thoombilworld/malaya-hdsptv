<?php
require __DIR__ . '/../bootstrap.php';
$token = $_GET['token'] ?? '';
$error = '';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Please refresh and try again.';
    } else {
        $token = $_POST['token'] ?? '';
        $pass = $_POST['password'] ?? '';
        $pass2 = $_POST['password_confirm'] ?? '';
        if ($pass === '' || strlen($pass) < 8 || $pass !== $pass2) {
            $error = 'Passwords must match and contain at least 8 characters.';
        } else {
            $stmt = mysqli_prepare(hs_db(), "SELECT email FROM hs_password_resets WHERE token = ? AND created_at >= (NOW() - INTERVAL 1 DAY) LIMIT 1");
            mysqli_stmt_bind_param($stmt, 's', $token);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = $res ? mysqli_fetch_assoc($res) : null;
            if ($row) {
                $hash = password_hash($pass, PASSWORD_BCRYPT);
                $up = mysqli_prepare(hs_db(), "UPDATE hs_frontend_users SET password_hash = ? WHERE email = ? LIMIT 1");
                mysqli_stmt_bind_param($up, 'ss', $hash, $row['email']);
                mysqli_stmt_execute($up);
                $msg = 'Password updated successfully.';
            } else {
                $error = 'Invalid or expired token.';
            }
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Reset Password</title><link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>"></head>
<body>
<div class="auth-shell">
  <section class="auth-brand"><div><h1>Set new password</h1><p>Create a strong password to secure your account.</p></div><small>Account recovery</small></section>
  <section class="auth-panel">
    <form class="auth-card" method="post">
      <h2>Reset password</h2>
      <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($msg): ?><div class="success-msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <?= hs_csrf_input() ?>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <div class="auth-field"><label>New Password</label><input type="password" name="password" required></div>
      <div class="auth-field"><label>Confirm Password</label><input type="password" name="password_confirm" required></div>
      <button class="btn btn-primary" type="submit">Update Password</button>
    </form>
  </section>
</div>
</body></html>
