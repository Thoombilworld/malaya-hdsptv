<?php

function hs_admin_nav_items(): array {
    return [
        'dashboard' => ['label' => 'Dashboard', 'href' => hs_base_url('admin/index.php')],
        'content' => ['label' => 'All News', 'href' => hs_base_url('admin/content/index.php')],
        'homepage' => ['label' => 'Homepage', 'href' => hs_base_url('admin/homepage.php')],
        'seo' => ['label' => 'SEO', 'href' => hs_base_url('admin/seo.php')],
        'social' => ['label' => 'Social', 'href' => hs_base_url('admin/social.php')],
        'ads' => ['label' => 'Ads', 'href' => hs_base_url('admin/ads.php')],
        'staff' => ['label' => 'Users & Roles', 'href' => hs_base_url('admin/users.php')],
        'logs' => ['label' => 'Audit Logs', 'href' => hs_base_url('admin/logs.php')],
        'logout' => ['label' => 'Logout', 'href' => hs_base_url('admin/logout.php')],
    ];
}

function hs_admin_shell_start(string $title, string $pageTitle, string $active = 'dashboard'): void {
    $items = hs_admin_nav_items();
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/admin.css') ?>">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-sidebar" aria-label="Admin navigation">
    <div class="admin-brand">
      <strong>HDSPTV Admin</strong>
      <small>International Newsroom Control</small>
    </div>
    <nav class="admin-nav">
      <?php foreach ($items as $key => $item): ?>
        <a class="<?= $active === $key ? 'is-active' : '' ?>" href="<?= $item['href'] ?>"><?= htmlspecialchars($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-topbar-inner">
        <div>
          <h1><?= htmlspecialchars($pageTitle) ?></h1>
          <div class="meta">Welcome, <?= htmlspecialchars($_SESSION['hs_admin_name'] ?? 'Admin') ?> · Editorial operations center</div>
        </div>
        <div class="admin-actions">
          <a class="btn btn-secondary" href="<?= hs_base_url('admin/content/article_add.php') ?>">Create Story</a>
          <a class="btn btn-primary" href="<?= hs_base_url('admin/homepage.php') ?>">Manage Homepage</a>
        </div>
      </div>
    </header>
    <main class="admin-page">
    <?php
}

function hs_admin_shell_end(): void {
    ?>
    </main>
  </div>
</div>
</body>
</html>
    <?php
}
