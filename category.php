<?php
require __DIR__ . '/bootstrap.php';

$settings = hs_settings();
$db = hs_db();

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    echo "Category not specified.";
    exit;
}

$stmt = mysqli_prepare($db, "SELECT id, name, slug FROM hs_categories WHERE slug = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$category = $res ? mysqli_fetch_assoc($res) : null;

if (!$category) {
    echo "Category not found.";
    exit;
}

$cat_id = (int)$category['id'];

$posts = [];
$pRes = mysqli_query($db, "SELECT p.*, c.name AS category_name
                           FROM hs_posts p
                           LEFT JOIN hs_categories c ON c.id = p.category_id
                           WHERE p.status='published' AND p.category_id = " . $cat_id . "
                           ORDER BY p.created_at DESC
                           LIMIT 40");
if ($pRes) {
    while ($row = mysqli_fetch_assoc($pRes)) $posts[] = $row;
}

function hs_post_date_local($p) {
    return !empty($p['created_at']) ? date('M j, Y', strtotime($p['created_at'])) : '';
}

$site_title = $settings['site_title'] ?? 'NEWS HDSPTV';
$meta = hs_auto_meta('taxonomy', ['name' => $category['name']], $settings);
$page_title = $meta['title'];
$meta_desc = $meta['desc'];
$meta_keys = $meta['keywords'];
$canonical = hs_category_url($category['slug']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($meta_keys) ?>">
  <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
  <?= hs_hreflang_links('category/' . rawurlencode($category['slug'])) ?>
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
  <?= hs_pwa_head_tags() ?>
  <script defer src="<?= hs_base_url('assets/js/pwa.js') ?>"></script>
  <style>
    :root {
      --hs-primary: #1E3A8A;
      --hs-primary-dark: #0B1120;
      --hs-accent: #FACC15;
      --hs-bg: #F6F7FB;
      --hs-card: #FFFFFF;
      --hs-border-soft: #E5E7EB;
      --hs-text-main: #111827;
      --hs-text-muted: #6B7280;
    }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--hs-bg);
      color: #111827;
    }
    a { color: #1D4ED8; text-decoration: none; }
    a:hover { text-decoration: underline; }

    header {
      position: sticky;
      top: 0;
      z-index: 40;
      backdrop-filter: blur(18px);
      background: rgba(255,255,255,0.96);
      border-bottom: 1px solid #E5E7EB;
      padding: 8px 18px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
    }
    .top-left { display:flex; align-items:center; gap:10px; }
    .logo-mark {
      width:32px; height:32px; border-radius:14px;
      background: #0B1220;
      display:flex; align-items:center; justify-content:center;
      font-weight:800; font-size:16px; color:#FFFFFF;
      box-shadow:0 10px 25px rgba(15,23,42,0.6);
    }
    .logo-text { display:flex; flex-direction:column; }
    .logo-text-main { font-weight:800; letter-spacing:.18em; font-size:13px; }
    .logo-text-tag { font-size:11px; color:#6B7280; opacity:.9; }

    .nav-main {
      display:flex; align-items:center; gap:12px;
      font-size:12px; text-transform:uppercase; letter-spacing:.12em;
    }
    .nav-main a { color:#374151; padding:4px 8px; border-radius:999px; }
    .nav-main a:hover { background:#F3F4F6; color:#1E3A8A; text-decoration:none; }

    .nav-search {
      margin-left:auto;
      margin-right:12px;
      margin-top:4px;
    }
    .nav-search input[type="text"] {
      padding:4px 10px;
      border-radius:999px;
      border:1px solid rgba(148,163,184,0.9);
      font-size:12px;
      background:#FFFFFF;
      color:#111827;
      min-width:200px;
    }
    .nav-search input[type="text"]::placeholder {
      color:#6B7280;
    }
    .nav-search button { display:none; }

    .page {
      width:100%;
      min-height:100vh;
      padding:18px 12px 32px;
      box-sizing:border-box;
    }
    .layout-category {
      max-width:1160px;
      margin:0 auto;
    }
    .category-header {
      margin-bottom:14px;
    }
    .category-title {
      font-size:22px;
      font-weight:800;
      letter-spacing:.08em;
      text-transform:uppercase;
    }
    .category-sub {
      font-size:12px;
      color:#374151;
      margin-top:4px;
    }

    .card-grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap:14px;
      margin-top:16px;
    }
    .news-card {
      background:#F9FAFB;
      border-radius:16px;
      box-shadow:0 16px 36px rgba(15,23,42,0.6);
      color:#111827;
      overflow:hidden;
      display:flex;
      flex-direction:column;
    }
    .news-thumb {
      width:100%;
      height:160px;
      background:#E5E7EB;
      overflow:hidden;
    }
    .news-thumb img {
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }
    .news-inner {
      padding:12px 14px 14px;
      font-size:14px;
    }
    .news-kicker {
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.18em;
      color:#6B7280;
      margin-bottom:4px;
    }
    .news-title {
      font-weight:700;
      margin-bottom:4px;
      color:#111827;
    }
    .news-title a { color:#111827; }
    .news-title a:hover { color:#1D4ED8; text-decoration:none; }
    .news-meta {
      font-size:11px;
      color:#6B7280;
    }

    footer {
      border-top:1px solid #E5E7EB;
      padding:10px 18px 16px;
      font-size:11px;
      color:#6B7280;
      text-align:center;
      background:#FFFFFF;
    }
    @media (max-width:640px) {
      header { padding:8px 10px; }
      .page { padding:14px 8px 24px; }
    }
  </style>
</head>
<body>
<header>
  <div class="top-left">
    <a href="<?= hs_base_url('index.php') ?>" class="logo-link">
      <div class="logo-mark">H</div>
      <div class="logo-text">
      <div class="logo-text-main">NEWS HDSPTV</div>
      <div class="logo-text-tag"><?= htmlspecialchars($settings['tagline'] ?? 'GCC • INDIA • KERALA • WORLD') ?></div>
    </div>
  </div>
  <nav class="nav-main">
    <a href="<?= hs_base_url('index.php#top') ?>">Home</a>
    <a href="<?= hs_category_url('india') ?>">India</a>
    <a href="<?= hs_category_url('gcc') ?>">GCC</a>
    <a href="<?= hs_category_url('kerala') ?>">Kerala</a>
    <a href="<?= hs_category_url('world') ?>">World</a>
    <a href="<?= hs_category_url('sports') ?>">Sports</a>
    <a href="<?= hs_category_url('entertainment') ?>">Entertainment</a>
    <a href="<?= hs_category_url('business') ?>">Business</a>
    <a href="<?= hs_category_url('technology') ?>">Technology</a>
    <a href="<?= hs_category_url('lifestyle') ?>">Lifestyle</a>
    <a href="<?= hs_category_url('health') ?>">Health</a>
    <a href="<?= hs_category_url('travel') ?>">Travel</a>
    <a href="<?= hs_category_url('auto') ?>">Auto</a>
    <a href="<?= hs_category_url('opinion') ?>">Opinion</a>
    <a href="<?= hs_category_url('politics') ?>">Politics</a>
    <a href="<?= hs_category_url('crime') ?>">Crime</a>
    <a href="<?= hs_category_url('education') ?>">Education</a>
    <a href="<?= hs_category_url('religion') ?>">Religion</a>
  </nav>
  <form class="nav-search" action="<?= hs_base_url('search.php') ?>" method="get">
    <input type="text" name="q" placeholder="Search news..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
    <button type="submit">Search</button>
  </form>
</header>

<main class="page">
  <div class="layout-category">
    <div class="category-header">
      <div class="category-title"><?= htmlspecialchars($category['name']) ?></div>
      <div class="category-sub">Latest stories from <?= htmlspecialchars($category['name']) ?></div>
    </div>

    <?php if (empty($posts)): ?>
      <p>No posts in this category yet.</p>
    <?php else: ?>
      <div class="card-grid">
        <?php foreach ($posts as $p): ?>
          <article class="news-card">
            <?php if (!empty($p['image_main'])): ?>
              <div class="news-thumb">
                <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
              </div>
            <?php endif; ?>
            <div class="news-inner">
              <div class="news-kicker">
                <?= htmlspecialchars($p['category_name'] ?: 'News') ?>
              </div>
              <h2 class="news-title">
                <a href="<?= hs_post_url($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a>
              </h2>
              <div class="news-meta">
                <?= hs_post_date_local($p) ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<footer>
  © <?= date('Y') ?> NEWS HDSPTV. All rights reserved.
</footer>
</body>
</html>
