<?php
require __DIR__ . '/../bootstrap.php';
if (hs_is_admin_logged_in()) {
    header('Location: ' . hs_base_url('admin/index.php'));
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (hs_auth_is_locked($email)) {
        $error = 'Too many failed attempts. Please wait 15 minutes and try again.';
    } elseif (defined('HS_INSTALLED') && HS_INSTALLED) {
        $stmt = mysqli_prepare(hs_db(), "SELECT id, password_hash, name, role FROM hs_users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $user = $res ? mysqli_fetch_assoc($res) : null;

        $allowedRoles = ['admin', 'editor', 'reporter'];
        if ($user && in_array($user['role'], $allowedRoles, true) && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            hs_auth_clear_attempts($email);
            $_SESSION['hs_admin_id'] = $user['id'];
            $_SESSION['hs_admin_name'] = $user['name'];
            $_SESSION['hs_admin_role'] = $user['role'];

            if ($user['role'] === 'reporter') {
                header('Location: ' . hs_base_url('admin/content/article_add.php'));
            } else {
                header('Location: ' . hs_base_url('admin/index.php'));
            }
            exit;
        }
        hs_auth_record_failure($email);
        $error = 'Invalid credentials or insufficient role permissions.';
    } else {
        $error = 'System is not installed. Run /install first.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login – HDSPTV</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-body">
<div class="auth-layout">
  <section class="auth-panel">
    <div>
      <h1>HDSPTV Newsroom Control</h1>
      <p>Enterprise-grade publishing workflow for editors, producers, moderators and live stream operators.</p>
    </div>
    <div class="muted" style="color:#CBD5E1;">Secure access · Role-based permissions · Audit-ready actions</div>
  </section>

  <section class="auth-card-wrap">
    <div class="auth-card">
      <h2 style="margin-top:0; margin-bottom:6px;">Sign in to Admin Panel</h2>
      <p class="muted" style="margin-top:0; margin-bottom:16px;">Use your newsroom credentials.</p>

      <?php if ($error): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required autocomplete="email">
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:8px;">
          <label style="display:flex; align-items:center; gap:8px; font-size:14px; color:#374151;">
            <input type="checkbox" name="remember" value="1"> Remember me
          </label>
          <a href="<?= hs_base_url('auth/forgot.php') ?>" style="font-size:14px; color:#2563EB;">Forgot password?</a>
        </div>

        <div style="display:grid; grid-template-columns:1fr auto; gap:10px;">
          <button class="btn btn-primary" type="submit">Sign In</button>
          <button class="btn btn-secondary" type="button" onclick="togglePassword()">Show/Hide</button>
        </div>
      </form>

      <p class="muted" style="margin-top:16px; margin-bottom:0;">Need help? Contact newsroom support.</p>
    </div>
  </section>
</div>
<script>
function togglePassword() {
  const input = document.getElementById('password');
  input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
