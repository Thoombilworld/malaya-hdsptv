<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($settings['site_title'] ?? 'NEWS HDSPTV') ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['seo_meta_description'] ?? ($settings['tagline'] ?? '')) ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
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
  $editorsPicks = array_slice($safePosts, 9, 4);
  $mostViewed = array_slice($safeTrending ?: $safePosts, 0, 5);

  $formatDate = static function(array $row): string {
      $date = $row['created_at'] ?? null;
      if (!$date) return 'Latest update';
      $ts = strtotime((string)$date);
      return $ts ? date('M j, Y · g:i A', $ts) : 'Latest update';
  };

  $articleLink = static function(array $row): string {
      return hs_base_url('post.php?slug=' . urlencode($row['slug'] ?? ''));
  };
?>

<div class="top-strip">
  <div class="container top-strip-inner">
    <span><?= date('l, F j, Y') ?></span>
    <span class="divider-dot">•</span>
    <span>Weather: --°</span>
    <span class="divider-dot">•</span>
    <span>Global Edition</span>
  </div>
</div>

<header class="site-header sticky-header">
  <div class="container nav-shell">
    <a class="brand" href="<?= hs_base_url('/') ?>">
      <span class="brand-mark">H</span>
      <span>
        <strong><?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?></strong>
        <small>International News Network</small>
      </span>
    </a>

    <nav class="top-nav" aria-label="Main navigation">
      <a href="<?= hs_base_url('/') ?>">Home</a>
      <a href="<?= hs_base_url('category.php?slug=india') ?>">India</a>
      <a href="<?= hs_base_url('category.php?slug=gcc') ?>">GCC</a>
      <a href="<?= hs_base_url('category.php?slug=world') ?>">World</a>
      <a href="<?= hs_base_url('category.php?slug=sports') ?>">Sports</a>
      <a class="live-btn" href="#">Live TV</a>
    </nav>

    <form class="search-inline" method="get" action="<?= hs_base_url('search.php') ?>">
      <input type="text" name="q" placeholder="Search stories" aria-label="Search stories">
    </form>

    <div class="header-utils">
      <select aria-label="Language selector" class="lang-selector">
        <option>EN</option>
        <option>ML</option>
        <option>AR</option>
      </select>
      <a href="<?= hs_base_url('auth/login.php') ?>">Login</a>
      <a href="<?= hs_base_url('auth/register.php') ?>">Register</a>
    </div>
  </div>
</header>

<div class="breaking-strip">
  <div class="container breaking-inner">
    <span class="badge badge-breaking">Breaking</span>
    <?php if (empty($safeBreaking)): ?>
      <span class="meta">No active breaking headlines.</span>
    <?php else: ?>
      <?php foreach (array_slice($safeBreaking, 0, 5) as $b): ?>
        <span class="breaking-item"><?= htmlspecialchars($b['title'] ?? '') ?></span>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<main class="page-bg">
  <div class="container grid-12 main-home">
    <section class="col-8 col-md-12 stack-32">
      <section class="hero-card">
        <?php if ($lead): ?>
          <?php if (!empty($lead['image_main'])): ?>
            <img class="hero-media" src="<?= hs_base_url($lead['image_main']) ?>" alt="<?= htmlspecialchars($lead['title']) ?>">
          <?php endif; ?>
          <div class="hero-content">
            <span class="badge"><?= htmlspecialchars($lead['category_name'] ?? 'Top Story') ?></span>
            <h1><?= htmlspecialchars($lead['title']) ?></h1>
            <p><?= htmlspecialchars($lead['excerpt'] ?? ($settings['tagline'] ?? 'Trusted global coverage from HDSPTV newsroom.')) ?></p>
            <div class="meta"><?= $formatDate($lead) ?></div>
            <a class="btn btn-primary" href="<?= $articleLink($lead) ?>">Read full story</a>
          </div>
        <?php else: ?>
          <div class="hero-content">
            <h1>Welcome to HDSPTV</h1>
            <p>Publish your first story to start building your editorial front page.</p>
          </div>
        <?php endif; ?>
      </section>

      <section>
        <div class="section-head">
          <h2>Top Stories</h2>
        </div>
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
        <div class="section-head"><h2>Video News</h2></div>
        <div class="card-grid card-grid-2">
          <?php foreach (array_slice($safeVideos, 0, 4) as $item): ?>
            <article class="news-card">
              <span class="badge badge-info">Video</span>
              <h3><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <div class="meta"><?= $formatDate($item) ?></div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="live-promo">
        <h2>Watch HDSPTV Live</h2>
        <p>Follow live programs, breaking updates, and rolling coverage from our international desk.</p>
        <a class="btn btn-primary" href="#">Open Live TV</a>
      </section>
    </section>

    <aside class="col-4 col-md-12 stack-24">
      <section class="panel">
        <div class="section-head"><h2>Trending Now</h2></div>
        <ul class="list-clean">
          <?php foreach (array_slice($safeTrending, 0, 6) as $item): ?>
            <li>
              <a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a>
              <div class="meta"><?= $formatDate($item) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel">
        <div class="section-head"><h2>Most Viewed</h2></div>
        <ul class="list-clean">
          <?php foreach ($mostViewed as $item): ?>
            <li>
              <a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a>
              <div class="meta"><?= htmlspecialchars($item['category_name'] ?? 'News') ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel">
        <div class="section-head"><h2>Editor’s Picks</h2></div>
        <ul class="list-clean">
          <?php foreach ($editorsPicks as $item): ?>
            <li><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel">
        <div class="section-head"><h2>Photo Gallery</h2></div>
        <ul class="list-clean">
          <?php foreach (array_slice($safeGallery, 0, 4) as $item): ?>
            <li><a href="<?= $articleLink($item) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="panel newsletter">
        <div class="section-head"><h2>Newsletter</h2></div>
        <p class="meta">Get top headlines and breaking updates delivered daily.</p>
        <form class="newsletter-form">
          <input type="email" placeholder="Enter your email" aria-label="Email">
          <button class="btn btn-primary" type="button">Subscribe</button>
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
    <div class="footer-links">
      <a href="#">About</a>
      <a href="#">Contact</a>
      <a href="#">Privacy</a>
    </div>
  </div>
</footer>
</body>
</html>
