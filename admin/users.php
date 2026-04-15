<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('user.manage');
require __DIR__ . '/_layout.php';

$db = hs_db();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and submit again.';
    } else {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'reporter';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Name, email, and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (!in_array($role, ['admin','editor','reporter'], true)) {
        $error = 'Invalid role selected.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($db, "INSERT INTO hs_users (name, email, password_hash, role) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hash, $role);
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Staff user created successfully.';
            hs_log_event('info', 'Admin created staff user', ['email' => $email, 'role' => $role]);
        } else {
            $error = 'Could not create user: ' . mysqli_error($db);
        }
    }
    }
}

$res = mysqli_query($db, "SELECT id, name, email, role, created_at FROM hs_users ORDER BY created_at DESC");
$staff = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];

hs_admin_shell_start('Staff Users – HDSPTV', 'Users & Roles', 'staff');
?>

<section class="grid-12" style="margin-bottom:24px;">
  <article class="card col-5 col-md-12">
    <h2>Add Staff User</h2>
    <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="badge badge-success" style="margin-bottom:12px;"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="post">
      <?= hs_csrf_input() ?>
      <div class="field"><label>Name</label><input type="text" name="name" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" required></div>
      <div class="field">
        <label>Role</label>
        <select name="role" style="height:50px;border:1px solid var(--border);border-radius:12px;padding:0 12px;">
          <option value="reporter">Reporter</option>
          <option value="editor">Editor</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div class="field"><label>Temporary Password</label><input type="password" name="password" required></div>
      <button class="btn btn-primary" type="submit">Create User</button>
    </form>
  </article>

  <article class="card col-7 col-md-12">
    <h2>Staff Directory</h2>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr></thead>
        <tbody>
        <?php foreach ($staff as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="badge badge-info"><?= htmlspecialchars($u['role']) ?></span></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
</section>

<?php hs_admin_shell_end(); ?>
