<?php

function hs_admin_nav_items(): array {
    $all = [
        'dashboard' => ['label' => 'Dashboard', 'href' => hs_admin_url('index.php'), 'permission' => 'dashboard.view'],
        'content' => ['label' => 'News', 'href' => hs_admin_content_url('index.php'), 'permission' => 'article.create'],
        'breaking' => ['label' => 'Breaking News', 'href' => hs_admin_content_url('articles.php', 'status=published'), 'permission' => 'article.publish'],
        'live' => ['label' => 'Live TV', 'href' => hs_admin_url('homepage.php'), 'permission' => 'article.publish'],
        'categories' => ['label' => 'Categories', 'href' => hs_admin_content_url('categories.php'), 'permission' => 'category.manage'],
        'media' => ['label' => 'Media Library', 'href' => hs_admin_content_url('article_add.php'), 'permission' => 'article.create'],
        'videos' => ['label' => 'Videos', 'href' => hs_admin_content_url('articles.php'), 'permission' => 'article.create'],
        'homepage' => ['label' => 'Homepage', 'href' => hs_admin_url('homepage.php'), 'permission' => 'article.publish'],
        'seo' => ['label' => 'SEO', 'href' => hs_admin_url('seo.php'), 'permission' => 'seo.manage'],
        'social' => ['label' => 'Social', 'href' => hs_admin_url('social.php'), 'permission' => 'seo.manage'],
        'ads' => ['label' => 'Ads', 'href' => hs_admin_url('ads.php'), 'permission' => 'ads.manage'],
        'staff' => ['label' => 'Users & Roles', 'href' => hs_admin_url('users.php'), 'permission' => 'user.manage'],
        'analytics' => ['label' => 'Analytics', 'href' => hs_admin_url('index.php'), 'permission' => 'dashboard.view'],
        'settings' => ['label' => 'Settings', 'href' => hs_admin_url('seo.php'), 'permission' => 'settings.manage'],
        'logs' => ['label' => 'Audit Logs', 'href' => hs_admin_url('logs.php'), 'permission' => 'settings.manage'],
        'logout' => ['label' => 'Logout', 'href' => hs_admin_url('logout.php'), 'permission' => null],
    ];

    $items = [];
    foreach ($all as $key => $item) {
        if ($item['permission'] === null || hs_can($item['permission'])) {
            $items[$key] = $item;
        }
    }
    return $items;
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
<div class="admin-sidebar-overlay" data-admin-overlay></div>
<div class="admin-shell">
  <aside class="admin-sidebar" id="admin-sidebar" data-admin-sidebar aria-label="Admin navigation">
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
        <button class="admin-menu-toggle" type="button" data-admin-menu-toggle aria-controls="admin-sidebar" aria-expanded="false">Menu</button>
        <div>
          <h1><?= htmlspecialchars($pageTitle) ?></h1>
          <div class="meta">Welcome, <?= htmlspecialchars($_SESSION['hs_admin_name'] ?? 'Admin') ?> · Broadcast-grade editorial control</div>
        </div>
        <div class="admin-actions">
          <div class="admin-search">
            <form method="get" action="<?= hs_admin_content_url('articles.php') ?>">
              <input type="text" name="q" placeholder="Global search stories...">
            </form>
          </div>
          <a class="btn btn-secondary" href="<?= hs_admin_content_url('article_add.php') ?>">+ Create News</a>
          <a class="btn btn-secondary" href="<?= hs_admin_url('homepage.php') ?>">Go Live</a>
          <a class="btn btn-secondary" href="<?= hs_admin_url('social_dispatch.php') ?>">Send Alert</a>
          <a class="btn btn-primary" href="<?= hs_admin_content_url('article_add.php') ?>">Upload Video</a>
          <span class="admin-live-pill">LIVE DESK</span>
          <button class="btn btn-secondary" type="button" data-theme-toggle>Dark/Light</button>
          <div class="admin-time" data-admin-time></div>
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
<script defer src="<?= hs_base_url('assets/js/admin-shell.js') ?>"></script>
</body>
</html>
    <?php
}
