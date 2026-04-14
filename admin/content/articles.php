<?php
require __DIR__ . '/../../bootstrap.php';
hs_require_admin();
hs_require_permission('article.create');
require __DIR__ . '/../_layout.php';

$db = hs_db();
$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$where = [];
if ($q !== '') {
    $qEsc = mysqli_real_escape_string($db, $q);
    $where[] = "p.title LIKE '%{$qEsc}%'";
}
if ($status !== '') {
    $statusEsc = mysqli_real_escape_string($db, $status);
    $where[] = "p.status='{$statusEsc}'";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT p.id, p.title, p.type, p.status, p.is_featured, p.is_breaking, p.is_trending, p.region, c.name AS category_name, p.created_at
        FROM hs_posts p
        LEFT JOIN hs_categories c ON c.id = p.category_id
        {$whereSql}
        ORDER BY p.created_at DESC
        LIMIT 250";
$res = mysqli_query($db, $sql);
$posts = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];

hs_admin_shell_start('Articles – HDSPTV', 'All News', 'content');
?>

<section class="card" style="margin-bottom:24px;">
  <form method="get" style="display:grid;grid-template-columns:2fr 1fr auto auto;gap:10px;align-items:end;">
    <div class="field" style="margin:0;">
      <label for="q">Search headline</label>
      <input id="q" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by title">
    </div>
    <div class="field" style="margin:0;">
      <label for="status">Status</label>
      <select id="status" name="status" style="height:50px;border:1px solid var(--border);border-radius:12px;padding:0 12px;">
        <option value="">All statuses</option>
        <option value="draft" <?= $status==='draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $status==='published' ? 'selected' : '' ?>>Published</option>
        <option value="scheduled" <?= $status==='scheduled' ? 'selected' : '' ?>>Scheduled</option>
      </select>
    </div>
    <button class="btn btn-secondary" type="submit">Filter</button>
    <a class="btn btn-primary" href="<?= hs_base_url('admin/content/article_add.php') ?>">Create News</a>
  </form>
</section>

<section class="card">
  <h2 style="margin-bottom:12px;">Articles</h2>
  <div class="table-wrap">
    <table class="table">
      <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Category</th>
        <th>Type</th>
        <th>Region</th>
        <th>Status</th>
        <th>Flags</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <?php if (empty($posts)): ?>
        <tr><td colspan="9" class="muted">No articles found for this filter.</td></tr>
      <?php else: ?>
        <?php foreach ($posts as $p): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['category_name'] ?: 'News') ?></td>
            <td><?= htmlspecialchars($p['type']) ?></td>
            <td><?= htmlspecialchars($p['region']) ?></td>
            <td>
              <span class="badge <?= $p['status']==='published' ? 'badge-success' : ($p['status']==='scheduled' ? 'badge-info' : 'badge-warning') ?>">
                <?= htmlspecialchars(ucfirst($p['status'])) ?>
              </span>
            </td>
            <td>
              <?= $p['is_breaking'] ? 'B' : '-' ?>
              <?= $p['is_featured'] ? 'F' : '-' ?>
              <?= $p['is_trending'] ? 'T' : '-' ?>
            </td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
            <td>
              <a href="<?= hs_base_url('admin/content/article_edit.php?id='.(int)$p['id']) ?>">Edit</a>
              ·
              <a href="<?= hs_base_url('admin/content/article_delete.php?id='.(int)$p['id']) ?>" onclick="return confirm('Delete this article?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php hs_admin_shell_end(); ?>
