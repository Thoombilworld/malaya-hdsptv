<?php
require __DIR__ . '/../bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Please refresh and try again.';
    } else {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $pass2 = $_POST['password_confirm'] ?? '';
        if ($name === '' || $email === '' || $pass === '') {
            $error = 'Please fill all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email.';
        } elseif (strlen($pass) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($pass !== $pass2) {
            $error = 'Passwords do not match.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare(hs_db(), "INSERT INTO hs_frontend_users (name, email, password_hash) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash);
            if (@mysqli_stmt_execute($stmt)) {
                header('Location: ' . hs_base_url('auth/login.php'));
                exit;
            }
            $error = 'Email already in use.';
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Register</title><link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>"></head>
<body>
<div class="auth-shell">
  <section class="auth-brand"><div><h1>Join HDSPTV</h1><p>Create your account for saved stories and personalized notifications.</p></div><small>Reader account portal</small></section>
  <section class="auth-panel">
    <form class="auth-card" method="post">
      <h2>Create account</h2>
      <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?= hs_csrf_input() ?>
      <div class="auth-field"><label>Name</label><input type="text" name="name" required></div>
      <div class="auth-field"><label>Email</label><input type="email" name="email" required></div>
      <div class="auth-field"><label>Password</label><input type="password" name="password" required></div>
      <div class="auth-field"><label>Confirm Password</label><input type="password" name="password_confirm" required></div>
      <button class="btn btn-primary" type="submit">Register</button>
      <p class="meta"><a href="<?= hs_base_url('auth/login.php') ?>">Already have an account?</a></p>
    </form>
  </section>
</div>
</body></html>
