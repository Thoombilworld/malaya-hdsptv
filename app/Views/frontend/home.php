<!doctype html>
<html lang="<?= htmlspecialchars(hs_locale(), ENT_QUOTES, 'UTF-8') ?>" dir="<?= hs_is_rtl() ? 'rtl' : 'ltr' ?>">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($settings['site_title'] ?? 'NEWS HDSPTV') ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['seo_meta_description'] ?? ($settings['tagline'] ?? '')) ?>">
  <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="canonical" href="<?= htmlspecialchars(hs_base_url('/')) ?>">
  <?= hs_hreflang_links('/') ?>
  <link rel="sitemap" type="application/xml" title="Sitemap" href="<?= htmlspecialchars(hs_base_url('sitemap.xml')) ?>">
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
  <?= hs_pwa_head_tags() ?>
  <script defer src="<?= hs_base_url('assets/js/pwa.js') ?>"></script>
  <script defer src="<?= hs_base_url('assets/js/localized-datetime.js') ?>"></script>
  <script defer src="<?= hs_base_url('assets/js/pwa-notifications.js') ?>"></script>
  <script type="application/ld+json">
  {
    "@context":"https://schema.org",
    "@type":"WebSite",
    "name": <?= json_encode($settings['site_title'] ?? 'NEWS HDSPTV') ?>,
    "url": <?= json_encode(hs_base_url('/')) ?>,
    "inLanguage": <?= json_encode(hs_locale()) ?>,
    "potentialAction": {
      "@type": "SearchAction",
      "target": <?= json_encode(hs_base_url('search/{search_term_string}')) ?>,
      "query-input": "required name=search_term_string"
    }
  }
  </script>
</head>
<body>
<?php
  $safePosts = is_array($posts ?? null) ? $posts : [];
  $safeFeatured = is_array($featured ?? null) ? $featured : [];
  $safeTrending = is_array($trending ?? null) ? $trending : [];
  $safeBreaking = is_array($breaking ?? null) ? $breaking : [];
  $safeVideos = is_array($video_posts ?? null) ? $video_posts : [];
  $safeGallery = is_array($gallery_posts ?? null) ? $gallery_posts : [];

  $lead = $safeFeatured[0] ?? ($safePosts[0] ?? null);
  $secondary = array_slice($safeFeatured ?: $safePosts, 1, 4);
  $latest = array_slice($safePosts, 0, 9);
  $editorsPicks = array_slice($safePosts, 9, 5);
  $mostViewed = array_slice($safeTrending ?: $safePosts, 0, 6);

  $grouped = [];
  foreach ($safePosts as $row) {
      $key = $row['category_name'] ?? 'News';
      if (!isset($grouped[$key])) $grouped[$key] = [];
      $grouped[$key][] = $row;
  }
  $topCategories = array_slice(array_keys($grouped), 0, 3);

  $formatDate = static function(array $row): string {
      $date = $row['created_at'] ?? null;
      if (!$date) return 'Latest update';
      $ts = strtotime((string)$date);
      return $ts ? date('M j, Y · g:i A', $ts) : 'Latest update';
  };

  $articleLink = static function(array $row): string {
      return hs_post_url($row['slug'] ?? '');
  };
?>

<div class="top-strip">
  <div class="container top-strip-inner">
    <span data-localized-datetime><?= date('l, F j, Y · g:i A') ?></span>
    <span class="divider-dot">•</span>
    <span><?= htmlspecialchars(hs_t('global_edition')) ?></span>
    <span class="divider-dot">•</span>
    <span><?= htmlspecialchars(hs_t('live_desk_active')) ?></span>
    <span class="divider-dot">•</span>
    <label class="sr-only" for="datetime-timezone">Timezone</label>
    <select id="datetime-timezone" class="datetime-select" data-timezone-override>
      <option value="auto">Timezone: Auto</option>
    </select>
    <label class="sr-only" for="datetime-locale">Format locale</label>
    <select id="datetime-locale" class="datetime-select" data-locale-override>
      <option value="auto">Format: Auto</option>
      <option value="en-US">English (US)</option>
      <option value="en-GB">English (UK)</option>
      <option value="ml-IN">Malayalam</option>
      <option value="hi-IN">Hindi</option>
      <option value="ar-AE">Arabic</option>
    </select>
  </div>
</div>

<header class="site-header sticky-header">
  <div class="container nav-shell">
    <a class="brand" href="<?= hs_base_url('/') ?>">
      <span class="brand-mark">H</span>
      <span>
        <strong><?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?></strong>
        <small><?= htmlspecialchars(hs_t('international_news_network')) ?></small>
      </span>
    </a>
    <button class="mobile-menu-btn" data-nav-toggle aria-controls="top-nav" aria-expanded="false">Menu</button>

    <nav class="top-nav" id="top-nav" data-top-nav aria-label="Main navigation">
      <a href="<?= hs_base_url('/') ?>"><?= htmlspecialchars(hs_t('home')) ?></a>
      <a href="<?= hs_category_url('india') ?>"><?= htmlspecialchars(hs_t('india')) ?></a>
      <a href="<?= hs_category_url('gcc') ?>"><?= htmlspecialchars(hs_t('gcc')) ?></a>
      <a href="<?= hs_category_url('world') ?>"><?= htmlspecialchars(hs_t('world')) ?></a>
      <a href="<?= hs_category_url('sports') ?>"><?= htmlspecialchars(hs_t('sports')) ?></a>
      <a class="live-btn" href="<?= hs_base_url('live') ?>"><?= htmlspecialchars(hs_t('live_tv')) ?></a>
    </nav>

    <form class="search-inline" method="get" action="<?= hs_base_url('search') ?>">
      <input type="text" name="q" placeholder="<?= htmlspecialchars(hs_t('search_stories')) ?>" aria-label="<?= htmlspecialchars(hs_t('search_stories')) ?>">
    </form>

    <div class="header-utils">
      <button class="install-app-btn" data-install-app hidden><?= htmlspecialchars(hs_t('install_app')) ?></button>
      <button class="install-app-btn" data-enable-notifications hidden>Enable Alerts</button>
      <form method="get" class="lang-form" action="<?= hs_base_url('/') ?>">
        <label class="sr-only" for="language-picker">Language</label>
        <select id="language-picker" name="lang" aria-label="Language selector" class="lang-selector" onchange="this.form.submit()">
          <?php foreach (hs_available_locales() as $code => $label): ?>
            <option value="<?= htmlspecialchars($code) ?>" <?= hs_locale() === $code ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a href="<?= hs_base_url('auth/login.php') ?>"><?= htmlspecialchars(hs_t('login')) ?></a>
      <a href="<?= hs_base_url('auth/register.php') ?>"><?= htmlspecialchars(hs_t('register')) ?></a>
    </div>
  </div>
</header>

<section class="breaking-strip">
  <div class="container breaking-inner ticker-track">
    <span class="badge badge-breaking">Breaking</span>
    <?php if (empty($safeBreaking)): ?>
      <span class="meta">No active breaking headlines.</span>
    <?php else: ?>
      <?php foreach (array_slice($safeBreaking, 0, 8) as $b): ?>
        <span class="breaking-item"><?= htmlspecialchars($b['title'] ?? '') ?></span>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<main class="page-bg">
  <div class="container grid-12 main-home">
    <section class="col-8 col-md-12 stack-32">
      <section class="hero-card">
        <?php if ($lead && !empty($lead['image_main'])): ?>
          <img class="hero-media" src="<?= hs_base_url($lead['image_main']) ?>" alt="<?= htmlspecialchars($lead['title']) ?>">
        <?php endif; ?>
        <div class="hero-content">
          <span class="badge"><?= htmlspecialchars($lead['category_name'] ?? 'Top Story') ?></span>
          <h1><?= htmlspecialchars($lead['title'] ?? 'Welcome to HDSPTV') ?></h1>
          <p><?= htmlspecialchars($lead['excerpt'] ?? ($settings['tagline'] ?? 'Trusted global coverage from HDSPTV newsroom.')) ?></p>
          <?php if ($lead): ?>
            <div class="meta"><?= $formatDate($lead) ?></div>
            <a class="btn btn-primary" href="<?= $articleLink($lead) ?>">Read full story</a>
          <?php endif; ?>
        </div>
      </section>

      <section>
        <div class="section-head"><h2>Featured Stories</h2></div>
        <div class="card-grid card-grid-2">
          <?php foreach ($secondary as $item): ?>
            <article class="news-card">
              <h3><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <p><?= htmlspecialchars($item['excerpt'] ?? 'Coverage from the HDSPTV editorial desk.') ?></p>
              <div class="meta"><?= $formatDate($item) ?></div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section>
        <div class="section-head"><h2>Latest News</h2></div>
        <div class="card-grid card-grid-3">
          <?php foreach ($latest as $item): ?>
            <article class="news-card news-card-compact">
              <?php if (!empty($item['image_main'])): ?><img src="<?= hs_base_url($item['image_main']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"><?php endif; ?>
              <h3><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <div class="meta"><?= htmlspecialchars($item['category_name'] ?? 'News') ?> · <?= $formatDate($item) ?></div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section>
        <div class="section-head"><h2>Category Blocks</h2></div>
        <div class="card-grid card-grid-3">
          <?php foreach ($topCategories as $cat): ?>
            <article class="panel">
              <h3><?= htmlspecialchars($cat) ?></h3>
              <ul class="list-clean">
                <?php foreach (array_slice($grouped[$cat], 0, 3) as $row): ?>
                  <li><a href="<?= $articleLink($row) ?>"><?= htmlspecialchars($row['title']) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="live-promo">
        <h2>Watch HDSPTV Live</h2>
        <p>Follow live programs, breaking updates, and rolling coverage from our international desk.</p>
        <a class="btn btn-primary" href="<?= hs_base_url('live') ?>">Open Live TV</a>
      </section>
    </section>

    <aside class="col-4 col-md-12 stack-24">
      <section class="panel">
        <div class="section-head"><h2><a href="<?= hs_base_url('trending') ?>">Trending Now</a></h2></div>
        <ul class="list-clean">
          <?php foreach (array_slice($safeTrending, 0, 6) as $item): ?>
            <li><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a><div class="meta"><?= $formatDate($item) ?></div></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel">
        <div class="section-head"><h2>Most Viewed</h2></div>
        <ul class="list-clean">
          <?php foreach ($mostViewed as $item): ?>
            <li><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel">
        <div class="section-head"><h2><a href="<?= hs_base_url('video') ?>">Video News</a></h2></div>
        <ul class="list-clean">
          <?php foreach (array_slice($safeVideos, 0, 4) as $item): ?>
            <li><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel newsletter">
        <div class="section-head"><h2>Newsletter</h2></div>
        <p class="meta">Get top headlines and breaking updates delivered daily.</p>
        <form class="newsletter-form" method="post" action="#">
          <input type="email" placeholder="Enter your email" aria-label="Email">
          <button class="btn btn-primary" type="submit">Subscribe</button>
        </form>
      </section>
    </aside>
  </div>
</main>

<footer class="site-footer">
  <div class="container footer-row">
    <div>
      <strong><?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?></strong>
      <p class="meta"><?= htmlspecialchars($settings['tagline'] ?? 'International newsroom coverage across India, GCC, Kerala and beyond.') ?></p>
    </div>
    <div class="footer-links"><a href="<?= hs_base_url('about') ?>">About</a><a href="<?= hs_base_url('contact') ?>">Contact</a><a href="<?= hs_base_url('profile') ?>">Profile</a></div>
  </div>
</footer>
</body>
</html>
