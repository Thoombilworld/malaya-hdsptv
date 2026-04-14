<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
require __DIR__ . '/_layout.php';

$stats = [
    ['label' => 'Published Today', 'value' => 0, 'badge' => 'badge-success', 'trend' => '+0% vs yesterday'],
    ['label' => 'Pending Review', 'value' => 0, 'badge' => 'badge-warning', 'trend' => 'Queue healthy'],
    ['label' => 'Active Breaking', 'value' => 0, 'badge' => 'badge-error', 'trend' => 'No active alerts'],
    ['label' => 'Live Viewers', 'value' => 0, 'badge' => 'badge-info', 'trend' => 'Awaiting stream data'],
];

$recentStories = [];
if (defined('HS_INSTALLED') && HS_INSTALLED) {
    $query = "SELECT p.title, p.status, p.created_at, c.name AS category_name
              FROM hs_posts p
              LEFT JOIN hs_categories c ON c.id = p.category_id
              ORDER BY p.created_at DESC
              LIMIT 8";
    $result = mysqli_query(hs_db(), $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $recentStories[] = $row;
        }
    }
}

hs_admin_shell_start('Admin Dashboard – HDSPTV', 'Dashboard', 'dashboard');
?>

<section class="grid-12" style="margin-bottom: 32px;">
  <?php foreach ($stats as $item): ?>
    <article class="card col-3 col-md-6 col-sm-12">
      <span class="badge <?= $item['badge'] ?>"><?= htmlspecialchars($item['label']) ?></span>
      <div class="kpi-value"><?= (int)$item['value'] ?></div>
      <div class="kpi-label"><?= htmlspecialchars($item['trend']) ?></div>
    </article>
  <?php endforeach; ?>
</section>

<section class="grid-12" style="margin-bottom: 32px;">
  <article class="card col-6 col-md-12">
    <h2>Editorial Workflow Health</h2>
    <p class="muted">Track draft → review → publish progression with clear ownership and response times.</p>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px;">
      <span class="badge badge-warning">Submitted</span>
      <span class="badge badge-info">Under Review</span>
      <span class="badge badge-warning">Fact Check</span>
      <span class="badge badge-success">Approved</span>
      <span class="badge badge-info">Scheduled</span>
      <span class="badge badge-success">Published</span>
    </div>
  </article>

  <article class="card col-6 col-md-12">
    <h2>Operations & Stream Status</h2>
    <p class="muted">Broadcast controls, stream health and emergency override are grouped in a single operational zone.</p>
    <div style="display:grid; gap:10px; margin-top:12px;">
      <div class="muted">System health: <span class="badge badge-success">Stable</span></div>
      <div class="muted">Stream source: <span class="badge badge-info">Primary Offline</span></div>
      <div class="muted">Backup source: <span class="badge badge-warning">Standby</span></div>
    </div>
  </article>
</section>

<section class="card col-12">
  <h2>Recent Stories</h2>
  <div class="table-wrap">
    <table class="table">
      <thead>
      <tr>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th>Created</th>
      </tr>
      </thead>
      <tbody>
      <?php if (empty($recentStories)): ?>
        <tr>
          <td colspan="4" class="muted">No stories found. Create your first story to begin newsroom operations.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($recentStories as $story): ?>
          <tr>
            <td><?= htmlspecialchars($story['title'] ?? '') ?></td>
            <td><?= htmlspecialchars($story['category_name'] ?: 'Uncategorized') ?></td>
            <td>
              <span class="badge <?= ($story['status'] ?? '') === 'published' ? 'badge-success' : 'badge-warning' ?>">
                <?= htmlspecialchars(ucfirst($story['status'] ?? 'draft')) ?>
              </span>
            </td>
            <td class="muted"><?= htmlspecialchars($story['created_at'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php hs_admin_shell_end(); ?>
