<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
require __DIR__ . '/_layout.php';

$publishedToday = hs_count_posts_today();
$pendingReview = hs_count_posts_by_status('draft');
$activeBreaking = hs_count_breaking_active();
$scheduledPosts = hs_count_posts_by_status('scheduled');
$recentStories = hs_latest_posts(8);
$topCategories = hs_top_categories(5);

$stats = [
    ['label' => 'Published Today', 'value' => $publishedToday, 'badge' => 'badge-success', 'trend' => 'Real-time from hs_posts'],
    ['label' => 'Pending Review', 'value' => $pendingReview, 'badge' => 'badge-warning', 'trend' => 'Draft queue'],
    ['label' => 'Active Breaking', 'value' => $activeBreaking, 'badge' => 'badge-error', 'trend' => 'Published + breaking flag'],
    ['label' => 'Scheduled Posts', 'value' => $scheduledPosts, 'badge' => 'badge-info', 'trend' => 'Ready for publish window'],
];

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
    <p class="muted">Live status for the newsroom pipeline from current database state.</p>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px;">
      <span class="badge badge-warning">Submitted/Draft: <?= $pendingReview ?></span>
      <span class="badge badge-info">Scheduled: <?= $scheduledPosts ?></span>
      <span class="badge badge-success">Published Today: <?= $publishedToday ?></span>
      <span class="badge badge-error">Breaking Active: <?= $activeBreaking ?></span>
    </div>
    <p class="muted" style="margin-top:12px;">Auto-refresh every 60 seconds for real-time monitoring.</p>
  </article>

  <article class="card col-6 col-md-12">
    <h2>Top Categories (Published)</h2>
    <?php if (empty($topCategories)): ?>
      <p class="muted">No published category data available yet.</p>
    <?php else: ?>
      <ul class="list-clean">
        <?php foreach ($topCategories as $cat): ?>
          <li style="display:flex;justify-content:space-between;gap:10px;">
            <span><?= htmlspecialchars($cat['name'] ?: 'Uncategorized') ?></span>
            <strong><?= (int)$cat['total'] ?></strong>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
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

<script>
setTimeout(function () { window.location.reload(); }, 60000);
</script>

<?php hs_admin_shell_end(); ?>
