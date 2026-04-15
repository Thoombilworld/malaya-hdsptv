<?php
require __DIR__ . '/../../bootstrap.php';
hs_require_admin();
hs_require_permission('category.manage');
require __DIR__ . '/../_layout.php';
$db = hs_db();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $parent_id = (int)($_POST['parent_id'] ?? 0);

        if ($name === '') {
            $error = 'Name is required.';
        } else {
            if ($slug === '') {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            }
            $stmt = mysqli_prepare($db, "INSERT INTO hs_categories (name, slug, parent_id) VALUES (?,?,?)");
            mysqli_stmt_bind_param($stmt, 'ssi', $name, $slug, $parent_id);
            if (!mysqli_stmt_execute($stmt)) {
                $error = 'Error saving category: ' . mysqli_error($db);
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        mysqli_query($db, "DELETE FROM hs_categories WHERE id=".$id." LIMIT 1");
    }
    header('Location: ' . hs_base_url('admin/content/categories.php'));
    exit;
}

$res = mysqli_query($db, "SELECT * FROM hs_categories ORDER BY parent_id ASC, name ASC");
$categories = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
$byId = [];
foreach ($categories as $c) $byId[$c['id']] = $c;

hs_admin_shell_start('Categories – HDSPTV', 'Categories', 'content');
?>

<section class="grid-12">
  <article class="card col-4 col-md-12">
    <h2>Add Category</h2>
    <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
    <?= hs_csrf_input() ?>
      <div class="field">
        <label>Name</label>
        <input type="text" name="name" required>
      </div>
      <div class="field">
        <label>Slug (optional)</label>
        <input type="text" name="slug">
      </div>
      <div class="field">
        <label>Parent Category</label>
        <select name="parent_id">
          <option value="0">-- None (Top level) --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary" type="submit">Save Category</button>
    </form>
  </article>

  <article class="card col-8 col-md-12">
    <h2>Existing Categories</h2>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Parent</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $c): ?>
            <?php $parentName = $c['parent_id'] && isset($byId[$c['parent_id']]) ? $byId[$c['parent_id']]['name'] : '—'; ?>
            <tr>
              <td><?= (int)$c['id'] ?></td>
              <td><?= htmlspecialchars($c['name']) ?></td>
              <td><?= htmlspecialchars($c['slug']) ?></td>
              <td><?= htmlspecialchars($parentName) ?></td>
              <td><a href="<?= hs_base_url('admin/content/categories.php?delete='.(int)$c['id']) ?>" onclick="return confirm('Delete this category?')">Delete</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </article>
</section>

<?php hs_admin_shell_end(); ?>
