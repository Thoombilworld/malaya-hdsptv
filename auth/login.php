<?php
require __DIR__ . '/../bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Please refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $stmt = mysqli_prepare(hs_db(), "SELECT id, name, password_hash FROM hs_frontend_users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $user = $res ? mysqli_fetch_assoc($res) : null;
        if ($user && password_verify($pass, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['hs_user_id'] = $user['id'];
            header('Location: ' . hs_base_url('/'));
            exit;
        }
        $error = 'Invalid login details.';
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>User Login</title><link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>"></head>
<body>
<div class="auth-shell">
  <section class="auth-brand"><div><h1>HDSPTV</h1><p>Trusted international newsroom access for readers.</p></div><small>Fast. Reliable. Global.</small></section>
  <section class="auth-panel">
    <form class="auth-card" method="post">
      <h2>Sign in</h2>
      <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?= hs_csrf_input() ?>
      <div class="auth-field"><label>Email</label><input type="email" name="email" required></div>
      <div class="auth-field"><label>Password</label><input type="password" name="password" required></div>
      <button class="btn btn-primary" type="submit">Login</button>
      <p class="meta"><a href="<?= hs_base_url('auth/forgot.php') ?>">Forgot password?</a> · <a href="<?= hs_base_url('auth/register.php') ?>">Create account</a></p>
    </form>
  </section>
</div>
</body></html>
