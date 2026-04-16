<?php
require __DIR__ . '/../../bootstrap.php';
hs_require_admin();
hs_require_permission('tag.manage');
require __DIR__ . '/../_layout.php';
$db = hs_db();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $stmt = mysqli_prepare($db, "INSERT INTO hs_tags (name, slug) VALUES (?,?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
            mysqli_stmt_bind_param($stmt, 'ss', $name, $slug);
            if (!mysqli_stmt_execute($stmt)) {
                $error = 'Error saving tag: ' . mysqli_error($db);
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        mysqli_query($db, "DELETE FROM hs_post_tags WHERE tag_id=".$id);
        mysqli_query($db, "DELETE FROM hs_tags WHERE id=".$id." LIMIT 1");
    }
    header('Location: ' . hs_admin_content_url('tags.php'));
    exit;
}

$res = mysqli_query($db, "SHOW TABLES LIKE 'hs_tags'");
$has_tags = $res && mysqli_num_rows($res) > 0;
$tags = [];
if ($has_tags) {
    $tr = mysqli_query($db, "SELECT * FROM hs_tags ORDER BY name ASC");
    if ($tr) {
        $tags = mysqli_fetch_all($tr, MYSQLI_ASSOC);
    }
}

hs_admin_shell_start('Tags – HDSPTV', 'Tags', 'content');
?>

<section class="grid-12">
  <article class="card col-4 col-md-12">
    <h2>Add Tag</h2>
    <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" action="<?= hs_admin_content_url('tags.php') ?>">
    <?= hs_csrf_input() ?>
      <div class="field">
        <label>Name</label>
        <input type="text" name="name" required>
      </div>
      <button class="btn btn-primary" type="submit">Save Tag</button>
    </form>
  </article>

  <article class="card col-8 col-md-12">
    <h2>Existing Tags</h2>
    <?php if (!$has_tags): ?>
      <p class="muted"><strong>Note:</strong> Table <code>hs_tags</code> not found. Make sure you ran the latest installer SQL.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($tags as $t): ?>
            <tr>
              <td><?= (int)$t['id'] ?></td>
              <td><?= htmlspecialchars($t['name']) ?></td>
              <td><?= htmlspecialchars($t['slug']) ?></td>
              <td><a href="<?= hs_admin_content_url('tags.php', 'delete='.(int)$t['id']) ?>" onclick="return confirm('Delete this tag?')">Delete</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </article>
</section>

<?php hs_admin_shell_end(); ?>
