<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($settings['site_title'] ?? 'NEWS HDSPTV') ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['seo_meta_description'] ?? ($settings['tagline'] ?? '')) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($settings['seo_meta_keywords'] ?? '') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">

  <style>
    :root {
      --hs-primary: #1E3A8A;
      --hs-primary-dark: #0B1120;
      --hs-accent: #FACC15;
      --hs-bg: #020617;
      --hs-surface: #020617;
      --hs-card: rgba(15,23,42,0.96);
    }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: radial-gradient(circle at top, #1E3A8A 0, #020617 45%, #020617 100%);
      color: #F9FAFB;
    }

    a { color: var(--hs-accent); text-decoration: none; }
    a:hover { text-decoration: underline; }

    header {
      position: sticky;
      top: 0;
      z-index: 40;
      backdrop-filter: blur(18px);
      background: linear-gradient(90deg, rgba(15,23,42,0.92), rgba(15,23,42,0.96));
      border-bottom: 1px solid rgba(15,23,42,0.9);
      padding: 8px 18px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
    }

    .top-left {
      display:flex;
      align-items:center;
      gap:10px;
    }

    .logo-link {
      display:flex;
      align-items:center;
      gap:10px;
      color:inherit;
      text-decoration:none;
    }
    .logo-link:hover {
      text-decoration:none;
      color:#FACC15;
    }

    .logo-mark {
      width:32px;
      height:32px;
      border-radius:14px;
      background: radial-gradient(circle at 20% 0, #FACC15 0, #1E3A8A 45%, #020617 100%);
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:800;
      font-size:16px;
      color:#F9FAFB;
      box-shadow:0 10px 25px rgba(15,23,42,0.6);
    }

    .logo-text {
      display:flex;
      flex-direction:column;
    }
    .logo-text-main {
      font-weight:800;
      letter-spacing:.18em;
      font-size:13px;
    }
    .logo-text-tag {
      font-size:11px;
      color:#E5E7EB;
      opacity:.85;
    }

    .nav-main {
      display:flex;
      align-items:center;
      gap:12px;
      font-size:12px;
      text-transform:uppercase;
      letter-spacing:.12em;
    }
    .nav-main a {
      color:#E5E7EB;
      padding:4px 6px;
      border-radius:999px;
    }
    .nav-main a:hover {
      background:rgba(15,23,42,0.8);
      color:#FACC15;
      text-decoration:none;
    }

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
      color:#9CA3AF;
    }
    .nav-search button { display:none; }

    @media (max-width:640px) {
      .nav-search {
        width:100%;
        margin:6px 0 0;
      }
      .nav-search input[type="text"] {
        width:100%;
      }
    }

    .user-bar {
      font-size:11px;
      color:#E5E7EB;
      text-align:right;
    }
    .user-bar a { color:#FACC15; }

    .page {
      width:100%;
      min-height:100vh;
    }

    .layout-main {
      display:grid;
      grid-template-columns: minmax(0,3.2fr) minmax(0,2fr);
      gap:14px;
      padding:12px 18px 28px;
    }

    .column {
      min-width:0;
    }

    .card {
      background: radial-gradient(circle at top left, rgba(30,64,175,0.45), rgba(15,23,42,0.98));
      border-radius:18px;
      box-shadow:0 18px 45px rgba(15,23,42,0.9);
      border:1px solid rgba(15,23,42,0.9);
      padding:14px 16px;
      margin-bottom:12px;
    }

    .pill {
      display:inline-flex;
      align-items:center;
      gap:6px;
      font-size:10px;
      text-transform:uppercase;
      letter-spacing:.2em;
      background:rgba(15,23,42,0.94);
      color:#FACC15;
      border-radius:999px;
      padding:4px 10px;
      margin-bottom:6px;
    }

    .pill-dot {
      width:6px;
      height:6px;
      border-radius:999px;
      background:#FACC15;
    }

    .section-title {
      font-size:15px;
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
      margin-bottom:8px;
    }

    .ticker {
      width:100%;
      display:flex;
      align-items:center;
      gap:10px;
      padding:6px 18px;
      box-sizing:border-box;
      border-bottom:1px solid rgba(15,23,42,0.85);
      background:linear-gradient(90deg, rgba(15,23,42,0.9), rgba(15,23,42,0.85));
      overflow-x:auto;
      white-space:nowrap;
    }
    .ticker-label {
      font-size:11px;
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:.16em;
      color:#FACC15;
    }
    .ticker-items {
      font-size:12px;
      display:flex;
      gap:18px;
      color:#E5E7EB;
    }
    .ticker-item {
      opacity:.9;
    }

    .hero-grid {
      display:grid;
      grid-template-columns:minmax(0,2.1fr) minmax(0,1.6fr);
      gap:12px;
    }

    .hero-main {
      position:relative;
      border-radius:20px;
      overflow:hidden;
      padding:16px 18px 18px;
      background:
        radial-gradient(circle at top left, rgba(250,204,21,0.25), transparent 55%),
        radial-gradient(circle at bottom right, rgba(30,64,175,0.35), transparent 55%),
        linear-gradient(135deg, #020617, #020617);
      min-height:190px;
      display:flex;
      flex-direction:column;
      justify-content:flex-end;
    }
    .hero-image {
      margin-bottom:8px;
      border-radius:16px;
      overflow:hidden;
      max-height:220px;
    }
    .hero-image img {
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }


    .hero-kicker {
      font-size:11px;
      letter-spacing:.18em;
      text-transform:uppercase;
      color:#FACC15;
      margin-bottom:6px;
    }
    .hero-title {
      font-size:22px;
      font-weight:800;
      line-height:1.2;
      margin-bottom:4px;
      color:#F9FAFB;
      text-shadow:0 8px 25px rgba(15,23,42,0.9);
    }
    .hero-meta {
      font-size:12px;
      color:#E5E7EB;
      opacity:.9;
    }

    .hero-overlay-tag {
      position:absolute;
      top:12px;
      right:14px;
      font-size:11px;
      padding:4px 10px;
      border-radius:999px;
      background:rgba(15,23,42,0.9);
      border:1px solid rgba(250,204,21,0.5);
      color:#FACC15;
      letter-spacing:.14em;
      text-transform:uppercase;
    }

    .hero-list {
      display:flex;
      flex-direction:column;
      gap:8px;
    }
    .hero-list-item {
      padding:8px 10px;
      border-radius:12px;
      background:rgba(15,23,42,0.85);
      border:1px solid rgba(15,23,42,0.9);
      cursor:pointer;
      transition:background .15s, transform .15s;
    }
    .hero-list-item:hover {
      background:rgba(30,64,175,0.45);
      transform:translateY(-1px);
    }
    .hero-list-title {
      font-size:13px;
      font-weight:600;
      color:#F9FAFB;
      margin-bottom:2px;
    }
    .hero-list-meta {
      font-size:11px;
      color:#9CA3AF;
    }

    .region-row {
      display:grid;
      grid-template-columns: repeat(3, minmax(0,1fr));
      gap:10px;
      margin-top:10px;
    }
    .region-block {
      padding:10px 11px;
      border-radius:14px;
      background:rgba(15,23,42,0.92);
      border:1px solid rgba(15,23,42,0.9);
      min-height:120px;
      display:flex;
      flex-direction:column;
    }
    .region-header {
      font-size:11px;
      letter-spacing:.16em;
      text-transform:uppercase;
      color:#E5E7EB;
      margin-bottom:6px;
      display:flex;
      justify-content:space-between;
      align-items:center;
    }
    .region-header span:last-child {
      font-size:10px;
      opacity:.7;
    }
    .region-post-list {
      list-style:none;
      padding:0;
      margin:0;
      font-size:12px;
    }
    .region-post-list li {
      margin-bottom:4px;
    }
    .region-post-title {
      color:#F9FAFB;
    }
    
    .region-thumb {
      margin:4px 0 4px;
      border-radius:8px;
      overflow:hidden;
      max-height:74px;
    }
    .region-thumb img {
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }

    .region-post-meta {
      font-size:10px;
      color:#9CA3AF;
    }

    .side-card {
      margin-bottom:12px;
    }

    .trending-list {
      list-style:none;
      padding:0;
      margin:0;
      font-size:12px;
    }
    .trending-list li {
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      padding:6px 0;
      border-bottom:1px dashed rgba(31,41,55,0.6);
    }
    
    .trending-thumb {
      width:72px;
      height:52px;
      border-radius:10px;
      overflow:hidden;
      margin-right:8px;
      flex-shrink:0;
    }
    .trending-thumb img {
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }

    .trending-title {
      flex:1;
      margin-right:6px;
      color:#F9FAFB;
    }
    .trending-meta {
      font-size:10px;
      color:#9CA3AF;
      text-align:right;
      min-width:70px;
    }

    .video-list, .gallery-list {
      list-style:none;
      padding:0;
      margin:0;
      font-size:12px;
    }
    .video-list li,
    .gallery-list li {
      display:flex;
      align-items:center;
      padding:5px 0;
      border-bottom:1px dashed rgba(31,41,55,0.6);
      gap:8px;
    }
    .video-thumb,
    .gallery-thumb {
      width:42px;
      height:28px;
      border-radius:8px;
      background: radial-gradient(circle at top, rgba(250,204,21,0.28), rgba(15,23,42,0.95));
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:14px;
      color:#FACC15;
    }
    .video-text,
    .gallery-text {
      flex:1;
    }

    .ads-slot {
      border-radius:14px;
      border:1px dashed rgba(55,65,81,0.8);
      padding:18px 10px;
      text-align:center;
      font-size:11px;
      color:#9CA3AF;
      background:radial-gradient(circle at top, rgba(30,64,175,0.34), rgba(15,23,42,0.96));
    }

    footer {
      border-top:1px solid rgba(31,41,55,0.9);
      padding:10px 18px 16px;
      font-size:11px;
      color:#9CA3AF;
      text-align:center;
      background:linear-gradient(180deg, rgba(15,23,42,0.98), #020617);
    }

    @media (max-width:980px) {
      .layout-main {
        grid-template-columns: minmax(0,1fr);
      }
    }

    @media (max-width:640px) {
      header {
        padding:8px 10px;
      }
      .layout-main {
        padding:10px 10px 20px;
      }
      .hero-grid {
        grid-template-columns: minmax(0,1fr);
      }
      .region-row {
        grid-template-columns: minmax(0,1fr);
      }
      .nav-main {
        width:100%;
        justify-content:flex-start;
        margin-top:6px;
        overflow-x:auto;
      }
      .user-bar {
        text-align:left;
        margin-top:6px;
      }
    }
  
    /* === Category Highlights – special section === */

    .category-highlights {
      margin-top: 16px;
    }

    .category-highlights-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 8px;
    }

    .category-highlights-header h2 {
      font-size: 13px;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #FACC15;
    }

    .category-highlights-header small {
      font-size: 11px;
      color: #9CA3AF;
    }

    .ch-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
    }

    .ch-card {
      background: radial-gradient(circle at top, #111827 0%, #020617 65%);
      border-radius: 18px;
      padding: 10px 12px;
      border: 1px solid rgba(148, 163, 184, .25);
      min-height: 90px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .ch-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 4px;
      gap: 6px;
    }

    .ch-card-title {
      font-size: 11px;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: #E5E7EB;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .ch-card-viewall {
      font-size: 10px;
      text-transform: uppercase;
      color: #FACC15;
      white-space: nowrap;
    }

    .ch-card-viewall a {
      color: inherit;
      text-decoration: none;
    }

    .ch-card-viewall a:hover {
      text-decoration: underline;
    }

    .ch-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .ch-list li {
      font-size: 11px;
      margin-bottom: 3px;
    }

    .ch-list a {
      color: #F9FAFB;
      text-decoration: none;
    }

    .ch-list a:hover {
      color: #FACC15;
    }

    .ch-empty {
      font-size: 11px;
      color: #9CA3AF;
    }

    @media (max-width: 1024px) {
      .ch-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 640px) {
      .ch-grid {
        grid-template-columns: repeat(1, minmax(0, 1fr));
      }
    }

</style>
</head>
<body>
<?php
  $india_posts  = [];
  $gcc_posts    = [];
  $kerala_posts = [];
  $world_posts  = [];
  $sports_posts = [];

  // New category collections
  $entertainment_posts = [];
  $business_posts      = [];
  $technology_posts    = [];
  $lifestyle_posts     = [];
  $health_posts        = [];
  $travel_posts        = [];
  $auto_posts          = [];
  $opinion_posts       = [];
  $politics_posts      = [];
  $crime_posts         = [];
  $education_posts     = [];
  $religion_posts      = [];

  foreach ($posts as $p) {
    $region = $p['region'] ?? 'global';
    switch ($region) {
      case 'india':  $india_posts[]  = $p; break;
      case 'gcc':    $gcc_posts[]    = $p; break;
      case 'kerala': $kerala_posts[] = $p; break;
      case 'world':  $world_posts[]  = $p; break;
      case 'sports': $sports_posts[] = $p; break;
    }

    $cat_name = strtolower($p['category_name'] ?? '');
    switch ($cat_name) {
      case 'entertainment': $entertainment_posts[] = $p; break;
      case 'business':      $business_posts[]      = $p; break;
      case 'technology':    $technology_posts[]    = $p; break;
      case 'lifestyle':     $lifestyle_posts[]     = $p; break;
      case 'health':        $health_posts[]        = $p; break;
      case 'travel':        $travel_posts[]        = $p; break;
      case 'auto':          $auto_posts[]          = $p; break;
      case 'opinion':       $opinion_posts[]       = $p; break;
      case 'politics':      $politics_posts[]      = $p; break;
      case 'crime':         $crime_posts[]         = $p; break;
      case 'education':     $education_posts[]     = $p; break;
      case 'religion':      $religion_posts[]      = $p; break;
    }
  }

  $hero = !empty($featured) ? $featured[0] : (!empty($posts) ? $posts[0] : null);
  $hero_list = [];
  if (!empty($featured)) {
    $hero_list = array_slice($featured, 1, 4);
  } elseif (!empty($posts)) {
    $hero_list = array_slice($posts, 1, 5);
  }

  // Most read posts (by views, fallback to latest if no view counts)
  $most_read_posts = $posts;
  usort($most_read_posts, function($a, $b) {
    $av = (int)($a['views'] ?? 0);
    $bv = (int)($b['views'] ?? 0);
    if ($av === $bv) return 0;
    return ($av < $bv) ? 1 : -1;
  });
  $most_read_posts = array_slice($most_read_posts, 0, 6);

  function hs_post_date($p) {
    return !empty($p['created_at']) ? date('M j, Y', strtotime($p['created_at'])) : '';
  }
?>
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
    <a href="<?= hs_base_url('category.php?slug=india') ?>">India</a>
    <a href="<?= hs_base_url('category.php?slug=gcc') ?>">GCC</a>
    <a href="<?= hs_base_url('category.php?slug=kerala') ?>">Kerala</a>
    <a href="<?= hs_base_url('category.php?slug=world') ?>">World</a>
    <a href="<?= hs_base_url('category.php?slug=sports') ?>">Sports</a>
    <a href="<?= hs_base_url('category.php?slug=entertainment') ?>">Entertainment</a>
    <a href="<?= hs_base_url('category.php?slug=business') ?>">Business</a>
    <a href="<?= hs_base_url('category.php?slug=technology') ?>">Technology</a>
    <a href="<?= hs_base_url('category.php?slug=lifestyle') ?>">Lifestyle</a>
    <a href="<?= hs_base_url('category.php?slug=health') ?>">Health</a>
    <a href="<?= hs_base_url('category.php?slug=travel') ?>">Travel</a>
    <a href="<?= hs_base_url('category.php?slug=auto') ?>">Auto</a>
    <a href="<?= hs_base_url('category.php?slug=opinion') ?>">Opinion</a>
    <a href="<?= hs_base_url('category.php?slug=politics') ?>">Politics</a>
    <a href="<?= hs_base_url('category.php?slug=crime') ?>">Crime</a>
    <a href="<?= hs_base_url('category.php?slug=education') ?>">Education</a>
    <a href="<?= hs_base_url('category.php?slug=religion') ?>">Religion</a>
  </nav>
  <form class="nav-search" action="<?= hs_base_url('search.php') ?>" method="get">
    <input type="text" name="q" placeholder="Search news..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
    <button type="submit">Search</button>
  </form>
  <div class="user-bar">
    <?php $u = hs_current_user(); ?>
    <?php if ($u): ?>
      <?= htmlspecialchars($u['name']) ?>
      <?php if (!empty($u['is_premium'])): ?> · <strong>Premium</strong><?php endif; ?>
      · <a href="<?= hs_base_url('auth/logout.php') ?>">Logout</a>
    <?php else: ?>
      <a href="<?= hs_base_url('auth/login.php') ?>">Login</a> ·
      <a href="<?= hs_base_url('auth/register.php') ?>">Register</a>
    <?php endif; ?>
  </div>
</header>

<?php if (!empty($breaking)): ?>
  <div class="ticker">
    <div class="ticker-label">Breaking</div>
    <div class="ticker-items">
      <?php foreach ($breaking as $b): ?>
        <div class="ticker-item">• <?= htmlspecialchars($b['title'] ?? '') ?></div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<main class="page">
  <div class="layout-main">
    <section class="column">
      <div class="card">
        <div class="pill"><span class="pill-dot"></span> Top Stories</div>
        <?php if ($hero): ?>
          <div class="hero-grid">
            <article class="hero-main">
              <?php if (!empty($hero['image_main'])): ?>
                <div class="hero-image">
                  <img src="<?= hs_base_url($hero['image_main']) ?>" alt="<?= htmlspecialchars($hero['title']) ?>">
                </div>
              <?php endif; ?>
              <div class="hero-overlay-tag">Lead Story</div>
              <div class="hero-kicker">
                <?= htmlspecialchars($hero['category_name'] ?: 'News') ?>
                <?php if (!empty($hero['region']) && $hero['region'] !== 'global'): ?>
                  · <?= strtoupper(htmlspecialchars($hero['region'])) ?>
                <?php endif; ?>
              </div>
              <h1 class="hero-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($hero['slug'])) ?>"><?= htmlspecialchars($hero['title']) ?></a></h1>
              <div class="hero-meta">
                <?= hs_post_date($hero) ?>
              </div>
            </article>
            <aside class="hero-list">
              <?php if (empty($hero_list)): ?>
                <div style="font-size:12px; color:#9CA3AF;">Mark posts as <strong>featured</strong> in Content Manager to see more here.</div>
              <?php else: ?>
                <?php foreach ($hero_list as $f): ?>
                  <div class="hero-list-item">
                    <div class="hero-list-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($f['slug'])) ?>"><?= htmlspecialchars($f['title']) ?></a></div>
                    <div class="hero-list-meta">
                      <?= htmlspecialchars($f['category_name'] ?: 'News') ?> · <?= hs_post_date($f) ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </aside>
          </div>
        <?php else: ?>
          <p style="font-size:12px; color:#9CA3AF;">No stories yet. Add posts from Admin → Content Manager.</p>
        <?php endif; ?>
      </div>

      <div class="card" id="regions">
        <div class="pill"><span class="pill-dot"></span> Region Highlights</div>
        <div class="region-row">
          <div class="region-block" id="india">
            <div class="region-header">
              <span>India</span>
              <span><a href="<?= hs_base_url('category.php?slug=india') ?>" style="color:#FACC15;">View All</a></span>
            </div>
            <?php if (empty($india_posts)): ?>
              <div style="font-size:11px; color:#9CA3AF;">No India posts yet.</div>
            <?php else: ?>
              <ul class="region-post-list">
                <?php foreach (array_slice($india_posts, 0, 4) as $p): ?>
                  <li>
                    <div class="region-post-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                                        <?php if (!empty($p['image_main'])): ?>
                      <div class="region-thumb">
                        <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                      </div>
                    <?php endif; ?>
                    <div class="region-post-meta"><?= htmlspecialchars($p['category_name'] ?: 'News') ?> · <?= hs_post_date($p) ?></div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="region-block" id="gcc">
            <div class="region-header">
              <span>GCC</span>
              <span><a href="<?= hs_base_url('category.php?slug=gcc') ?>" style="color:#FACC15;">View All</a></span>
            </div>
            <?php if (empty($gcc_posts)): ?>
              <div style="font-size:11px; color:#9CA3AF;">No GCC posts yet.</div>
            <?php else: ?>
              <ul class="region-post-list">
                <?php foreach (array_slice($gcc_posts, 0, 4) as $p): ?>
                  <li>
                    <div class="region-post-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                                        <?php if (!empty($p['image_main'])): ?>
                      <div class="region-thumb">
                        <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                      </div>
                    <?php endif; ?>
                    <div class="region-post-meta"><?= htmlspecialchars($p['category_name'] ?: 'News') ?> · <?= hs_post_date($p) ?></div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="region-block" id="kerala">
            <div class="region-header">
              <span>Kerala</span>
              <span><a href="<?= hs_base_url('category.php?slug=kerala') ?>" style="color:#FACC15;">View All</a></span>
            </div>
            <?php if (empty($kerala_posts)): ?>
              <div style="font-size:11px; color:#9CA3AF;">No Kerala posts yet.</div>
            <?php else: ?>
              <ul class="region-post-list">
                <?php foreach (array_slice($kerala_posts, 0, 4) as $p): ?>
                  <li>
                    <div class="region-post-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                                        <?php if (!empty($p['image_main'])): ?>
                      <div class="region-thumb">
                        <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                      </div>
                    <?php endif; ?>
                    <div class="region-post-meta"><?= htmlspecialchars($p['category_name'] ?: 'News') ?> · <?= hs_post_date($p) ?></div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>

        <div class="region-row" style="margin-top:10px;">
          <div class="region-block" id="world">
            <div class="region-header">
              <span>World</span>
              <span><a href="<?= hs_base_url('category.php?slug=world') ?>" style="color:#FACC15;">View All</a></span>
            </div>
            <?php if (empty($world_posts)): ?>
              <div style="font-size:11px; color:#9CA3AF;">No World posts yet.</div>
            <?php else: ?>
              <ul class="region-post-list">
                <?php foreach (array_slice($world_posts, 0, 4) as $p): ?>
                  <li>
                    <div class="region-post-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                                        <?php if (!empty($p['image_main'])): ?>
                      <div class="region-thumb">
                        <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                      </div>
                    <?php endif; ?>
                    <div class="region-post-meta"><?= htmlspecialchars($p['category_name'] ?: 'News') ?> · <?= hs_post_date($p) ?></div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="region-block" id="sports">
            <div class="region-header">
              <span>Sports</span>
              <span><a href="<?= hs_base_url('category.php?slug=sports') ?>" style="color:#FACC15;">View All</a></span>
            </div>
            <?php if (
    <!-- === Category Highlights – special full-width section === -->
    <section class="card category-highlights">
      <div class="category-highlights-header">
        <h2>Category Highlights</h2>
        <small>Top stories from each section</small>
      </div>

      <div class="ch-grid">

        <!-- Entertainment -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Entertainment</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=entertainment') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($entertainment_posts)): ?>
            <div class="ch-empty">No entertainment posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($entertainment_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Business -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Business</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=business') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($business_posts)): ?>
            <div class="ch-empty">No business posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($business_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Technology -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Technology</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=technology') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($technology_posts)): ?>
            <div class="ch-empty">No technology posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($technology_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Lifestyle -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Lifestyle</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=lifestyle') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($lifestyle_posts)): ?>
            <div class="ch-empty">No lifestyle posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($lifestyle_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Health -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Health</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=health') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($health_posts)): ?>
            <div class="ch-empty">No health posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($health_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Travel -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Travel</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=travel') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($travel_posts)): ?>
            <div class="ch-empty">No travel posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($travel_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Auto -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Auto</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=auto') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($auto_posts)): ?>
            <div class="ch-empty">No auto posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($auto_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Opinion -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Opinion</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=opinion') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($opinion_posts)): ?>
            <div class="ch-empty">No opinion posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($opinion_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Politics -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Politics</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=politics') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($politics_posts)): ?>
            <div class="ch-empty">No politics posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($politics_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Crime -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Crime</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=crime') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($crime_posts)): ?>
            <div class="ch-empty">No crime posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($crime_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Education -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Education</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=education') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($education_posts)): ?>
            <div class="ch-empty">No education posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($education_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <!-- Religion -->
        <div class="ch-card">
          <div class="ch-card-header">
            <div class="ch-card-title">Religion</div>
            <div class="ch-card-viewall">
              <a href="<?= hs_base_url('category.php?slug=religion') ?>">View All</a>
            </div>
          </div>
          <?php if (empty($religion_posts)): ?>
            <div class="ch-empty">No religion posts yet.</div>
          <?php else: ?>
            <ul class="ch-list">
              <?php foreach (array_slice($religion_posts, 0, 3) as $p): ?>
                <li>
                  <a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>">
                    <?= htmlspecialchars($p['title']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

      </div>
    </section>
size:11px; color:#9CA3AF;">No education posts yet.</div>
          <?php else: ?>
            <ul class="region-post-list">
              <?php foreach (array_slice($education_posts, 0, 3) as $p): ?>
                <li>
                  <div class="region-post-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                  <?php if (!empty($p['image_main'])): ?>
                    <div class="region-thumb">
                      <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                    </div>
                  <?php endif; ?>
                  <div class="region-post-meta"><?= htmlspecialchars($p['category_name'] ?: 'News') ?> · <?= hs_post_date($p) ?></div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="region-block" id="religion">
          <div class="region-header">
            <span>Religion</span>
            <span><a href="<?= hs_base_url('category.php?slug=religion') ?>" style="color:#FACC15;">View All</a></span>
          </div>
          <?php if (empty($religion_posts)): ?>
            <div style="font-size:11px; color:#9CA3AF;">No religion posts yet.</div>
          <?php else: ?>
            <ul class="region-post-list">
              <?php foreach (array_slice($religion_posts, 0, 3) as $p): ?>
                <li>
                  <div class="region-post-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($p['slug'])) ?>"><?= htmlspecialchars($p['title']) ?></a></div>
                  <?php if (!empty($p['image_main'])): ?>
                    <div class="region-thumb">
                      <img src="<?= hs_base_url($p['image_main']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                    </div>
                  <?php endif; ?>
                  <div class="region-post-meta"><?= htmlspecialchars($p['category_name'] ?: 'News') ?> · <?= hs_post_date($p) ?></div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

    </section>


          </div>
        </div>
      </div>
    </section>

    <aside class="column">
      <section class="card side-card">
        <div class="pill"><span class="pill-dot"></span> Trending</div>
        <?php if (empty($trending)): ?>
          <p style="font-size:12px; color:#9CA3AF;">No trending posts yet.</p>
        <?php else: ?>
          <ul class="trending-list">
            <?php foreach ($trending as $t): ?>
              <li>
                <?php if (!empty($t['image_main'])): ?>
                <div class="trending-thumb">
                  <img src="<?= hs_base_url($t['image_main']) ?>" alt="<?= htmlspecialchars($t['title']) ?>">
                </div>
              <?php endif; ?>
              <div class="trending-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($t['slug'])) ?>"><?= htmlspecialchars($t['title']) ?></a></div>
                <div class="trending-meta">
                  <?= htmlspecialchars($t['category_name'] ?: 'News') ?><br>
                  <?= hs_post_date($t) ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

      </section>

      <section class="card side-card">
        <div class="pill"><span class="pill-dot"></span> Most Read</div>
        <?php if (empty($most_read_posts)): ?>
          <p style="font-size:12px; color:#9CA3AF;">No most-read posts yet.</p>
        <?php else: ?>
          <ul class="trending-list">
            <?php foreach ($most_read_posts as $m): ?>
              <li>
                <div class="trending-title"><a href="<?= hs_base_url('post.php?slug=' . urlencode($m['slug'])) ?>"><?= htmlspecialchars($m['title']) ?></a></div>
                <div class="trending-meta">
                  <?= htmlspecialchars($m['category_name'] ?: 'News') ?><br>
                  <?= hs_post_date($m) ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <section class="card side-card">
        <div class="pill"><span class="pill-dot"></span> Video</div>
        <div class="section-title" style="margin-bottom:4px;">Video News</div>
        <?php if (empty($video_posts)): ?>
          <p style="font-size:12px; color:#9CA3AF;">No video posts yet.</p>
        <?php else: ?>
          <ul class="video-list">
            <?php foreach ($video_posts as $v): ?>
              <li>
                <div class="video-thumb">▶</div>
                <div class="video-text">
                  <div><a href="<?= hs_base_url('post.php?slug=' . urlencode($v['slug'])) ?>"><?= htmlspecialchars($v['title']) ?></a></div>
                  <div style="font-size:10px; color:#9CA3AF;"><?= htmlspecialchars($v['category_name'] ?: 'Video') ?></div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <section class="card side-card">
        <div class="pill"><span class="pill-dot"></span> Gallery</div>
        <div class="section-title" style="margin-bottom:4px;">Photo Gallery</div>
        <?php if (empty($gallery_posts)): ?>
          <p style="font-size:12px; color:#9CA3AF;">No gallery posts yet.</p>
        <?php else: ?>
          <ul class="gallery-list">
            <?php foreach ($gallery_posts as $g): ?>
              <li>
                <div class="gallery-thumb">🖼</div>
                <div class="gallery-text">
                  <div><a href="<?= hs_base_url('post.php?slug=' . urlencode($g['slug'])) ?>"><?= htmlspecialchars($g['title']) ?></a></div>
                  <div style="font-size:10px; color:#9CA3AF;"><?= htmlspecialchars($g['category_name'] ?: 'Gallery') ?></div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <section class="card side-card">
        <div class="section-title">Follow NEWS HDSPTV</div>
        <p style="font-size:12px; color:#9CA3AF;">
          <?php if (!empty($settings['social_facebook'])): ?>
            <a href="<?= htmlspecialchars($settings['social_facebook']) ?>" target="_blank">Facebook</a> ·
          <?php endif; ?>
          <?php if (!empty($settings['social_youtube'])): ?>
            <a href="<?= htmlspecialchars($settings['social_youtube']) ?>" target="_blank">YouTube</a> ·
          <?php endif; ?>
          <?php if (!empty($settings['social_instagram'])): ?>
            <a href="<?= htmlspecialchars($settings['social_instagram']) ?>" target="_blank">Instagram</a> ·
          <?php endif; ?>
          <?php if (!empty($settings['social_x'])): ?>
            <a href="<?= htmlspecialchars($settings['social_x']) ?>" target="_blank">X</a> ·
          <?php endif; ?>
          <?php if (!empty($settings['social_tiktok'])): ?>
            <a href="<?= htmlspecialchars($settings['social_tiktok']) ?>" target="_blank">TikTok</a> ·
          <?php endif; ?>
          <?php if (!empty($settings['social_telegram'])): ?>
            <a href="<?= htmlspecialchars($settings['social_telegram']) ?>" target="_blank">Telegram</a>
          <?php endif; ?>
        </p>
      </section>

      <section class="card side-card">
        <div class="section-title">Homepage Sidebar Ad</div>
        <div class="ads-slot">
          Homepage Sidebar Ad Slot<br>
          (Manage this from Admin → Ads)
        </div>
      </section>
    </aside>
  </div>
</main>

<footer>
  © <?= date('Y') ?> NEWS HDSPTV. All rights reserved.
</footer>
</body>
</html>
