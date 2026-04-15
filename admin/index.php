<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
require __DIR__ . '/_layout.php';

$publishedToday = hs_count_posts_today();
$pendingReview = hs_count_posts_by_status('draft');
$activeBreaking = hs_count_breaking_active();
$scheduledPosts = hs_count_posts_by_status('scheduled');
$publishedTotal = hs_count_posts_by_status('published');
$recentStories = hs_latest_posts(8);
$topCategories = hs_top_categories(5);

$stats = [
    ['label' => 'Total Published Today', 'value' => $publishedToday, 'badge' => 'badge-success', 'trend' => 'Real-time from hs_posts'],
    ['label' => 'Drafts Pending', 'value' => $pendingReview, 'badge' => 'badge-warning', 'trend' => 'Workflow queue'],
    ['label' => 'Breaking Alerts Active', 'value' => $activeBreaking, 'badge' => 'badge-error', 'trend' => 'Live alert feed'],
    ['label' => 'Live Viewers', 'value' => 0, 'badge' => 'badge-info', 'trend' => 'Stream analytics feed'],
    ['label' => 'Video Uploads Today', 'value' => hs_count_posts_by_status('published'), 'badge' => 'badge-info', 'trend' => 'Media desk throughput'],
    ['label' => 'Ad Revenue Today', 'value' => 0, 'badge' => 'badge-success', 'trend' => 'Awaiting ad module'],
];

hs_admin_shell_start('Admin Dashboard – HDSPTV', 'Dashboard', 'dashboard');
?>

<section class="grid-12" style="margin-bottom: 32px;">
  <?php foreach ($stats as $item): ?>
    <article class="card col-4 col-md-6 col-sm-12">
      <span class="badge <?= $item['badge'] ?>"><?= htmlspecialchars($item['label']) ?></span>
      <div class="kpi-value"><?= (int)$item['value'] ?></div>
      <div class="kpi-label"><?= htmlspecialchars($item['trend']) ?></div>
    </article>
  <?php endforeach; ?>
</section>

<section class="grid-12" style="margin-bottom: 32px;">
  <article class="card col-7 col-md-12 control-center">
    <h2>Breaking News Control Center</h2>
    <p class="muted">High-priority desk for urgent updates and one-click broadcast actions.</p>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px;">
      <span class="badge badge-error">Status: <?= $activeBreaking > 0 ? 'Active' : 'Standby' ?></span>
      <span class="badge badge-warning">Scheduled: <?= $scheduledPosts ?></span>
      <span class="badge badge-success">Published: <?= $publishedTotal ?></span>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:14px;">
      <a class="btn btn-secondary" href="<?= hs_base_url('admin/content/articles.php') ?>">Edit</a>
      <a class="btn btn-secondary" href="<?= hs_base_url('admin/content/articles.php?status=scheduled') ?>">Pause</a>
      <a class="btn btn-secondary" href="<?= hs_base_url('admin/content/article_add.php') ?>">Replace</a>
      <a class="btn btn-primary" href="<?= hs_base_url('admin/social_dispatch.php') ?>">Push Notification</a>
    </div>
  </article>

  <article class="card col-5 col-md-12">
    <h2>Live TV Status Panel</h2>
    <p class="muted">Stream status: <span class="badge badge-warning">Standby</span></p>
    <p class="muted">Current Program: Newsroom Bulletin</p>
    <p class="muted">Next Program: Regional Roundup</p>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:14px;">
      <a class="btn btn-primary" href="<?= hs_base_url('admin/homepage.php') ?>">Start Live</a>
      <a class="btn btn-secondary" href="<?= hs_base_url('admin/homepage.php') ?>">Stop Live</a>
      <a class="btn btn-secondary" href="<?= hs_base_url('admin/homepage.php') ?>">Switch Feed</a>
    </div>
  </article>
</section>

<section class="grid-12" style="margin-bottom: 32px;">
  <article class="card col-8 col-md-12">
    <h2>Recent News Workflow</h2>
    <div class="table-wrap">
      <table class="table">
        <thead>
        <tr>
          <th>Title</th>
          <th>Category</th>
          <th>Status</th>
          <th>Publish Time</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($recentStories)): ?>
          <tr><td colspan="5" class="muted">No stories found. Create your first story.</td></tr>
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
              <td class="workflow-actions">
                <a href="<?= hs_base_url('admin/content/articles.php') ?>">Edit</a>
                <a href="<?= hs_base_url('admin/content/articles.php?status=draft') ?>">Review</a>
                <a href="<?= hs_base_url('admin/content/articles.php?status=published') ?>">Publish</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>

  <article class="card col-4 col-md-12">
    <h2>Trending Stories Widget</h2>
    <p class="muted">Top categories currently driving engagement.</p>
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

<section class="grid-12">
  <article class="card col-4 col-md-12">
    <h2>Reporter Activity Widget</h2>
    <p class="muted">Submitted today: <?= $pendingReview ?></p>
    <p class="muted">Approval rate: n/a</p>
    <p class="muted">Pending edits: <?= $scheduledPosts ?></p>
  </article>
  <article class="card col-4 col-md-12">
    <h2>Notifications Performance</h2>
    <p class="muted">Push sent: 0</p>
    <p class="muted">CTR: n/a</p>
    <p class="muted">Delivery: standby</p>
  </article>
  <article class="card col-4 col-md-12">
    <h2>Server / Stream Health</h2>
    <p class="muted">Database: <span class="badge badge-success">Connected</span></p>
    <p class="muted">Live stream: <span class="badge badge-warning">Standby</span></p>
    <p class="muted">Auto-refresh: 60 sec</p>
  </article>
</section>

<script>
setTimeout(function () { window.location.reload(); }, 60000);
</script>

<?php hs_admin_shell_end(); ?>
