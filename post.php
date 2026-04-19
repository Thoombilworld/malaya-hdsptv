<?php
require __DIR__ . '/bootstrap.php';

$settings = hs_settings();
$db = hs_db();

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    http_response_code(404);
    echo "Article not found.";
    exit;
}

$stmt = mysqli_prepare($db, "SELECT p.*, c.name AS category_name
                             FROM hs_posts p
                             LEFT JOIN hs_categories c ON c.id = p.category_id
                             WHERE p.slug = ? AND p.status = 'published'
                             LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = $res ? mysqli_fetch_assoc($res) : null;

if (!$post) {
    http_response_code(404);
    echo "Article not found.";
    exit;
}

// Tags
$tags = [];
$tagRes = mysqli_query($db, "SELECT t.name, t.slug
                             FROM hs_tags t
                             JOIN hs_post_tags pt ON pt.tag_id = t.id
                             WHERE pt.post_id = " . (int)$post['id'] . "
                             ORDER BY t.name ASC");
if ($tagRes) {
    while ($row = mysqli_fetch_assoc($tagRes)) $tags[] = $row;
}

// Related posts (same category or region)
$related = [];
if (!empty($post['category_id'])) {
    $relStmt = mysqli_prepare($db, "SELECT p.id, p.title, p.slug, p.created_at
                                    FROM hs_posts p
                                    WHERE p.status='published'
                                      AND p.id != ?
                                      AND (p.category_id = ? OR p.region = ?)
                                    ORDER BY p.created_at DESC
                                    LIMIT 6");
    $region = $post['region'] ?? 'global';
    mysqli_stmt_bind_param($relStmt, 'iis', $post['id'], $post['category_id'], $region);
    mysqli_stmt_execute($relStmt);
    $relRes = mysqli_stmt_get_result($relStmt);
    if ($relRes) {
        while ($r = mysqli_fetch_assoc($relRes)) $related[] = $r;
    }
}

// Trending for sidebar
$trending = [];
$tRes = mysqli_query($db, "SELECT p.id, p.title, p.slug, p.created_at
                           FROM hs_posts p
                           WHERE p.status='published' AND p.is_trending=1
                           ORDER BY p.created_at DESC
                           LIMIT 6");
if ($tRes) {
    while ($r = mysqli_fetch_assoc($tRes)) $trending[] = $r;
}

function hs_post_date_local($p) {
    return !empty($p['created_at']) ? date('M j, Y', strtotime($p['created_at'])) : '';
}

// SEO meta
$site_title = $settings['site_title'] ?? 'NEWS HDSPTV';
$meta = hs_auto_meta('post', $post, $settings);
$page_title = $meta['title'];
$meta_desc = $meta['desc'];
$meta_keys = $meta['keywords'];
if (!empty($tags)) {
    $tag_names = array_column($tags, 'name');
    $meta_keys = $meta_keys . ', ' . implode(', ', $tag_names);
}
$categoryName = $post['category_name'] ?: 'News';
$canonical = hs_post_url($post['slug']);
$shareCanonical = $canonical . (strpos($canonical, '?') !== false ? '&' : '?') . 'lang=' . rawurlencode(hs_locale());
$shareTitle = $post['title'] . ' - ' . $site_title;
$ogLocale = hs_locale_to_og();

$og_image = '';
if (!empty($post['image_main'])) {
    $og_image = hs_base_url($post['image_main']);
} elseif (!empty($settings['default_article_og_image'])) {
    $og_image = $settings['default_article_og_image'];
}
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
  <?= hs_hreflang_links('post/' . rawurlencode($post['slug'])) ?>

  <?php if ($og_image): ?>
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>">
  <?php endif; ?>
  <meta property="og:title" content="<?= htmlspecialchars($post['title']) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($meta_desc) ?>">
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
  <meta property="og:site_name" content="<?= htmlspecialchars($site_title) ?>">
  <meta property="og:locale" content="<?= htmlspecialchars($ogLocale) ?>">
  <?php foreach (array_keys(hs_available_locales()) as $altLocale): ?>
    <?php if ($altLocale !== hs_locale()): ?>
      <meta property="og:locale:alternate" content="<?= htmlspecialchars(hs_locale_to_og($altLocale)) ?>">
    <?php endif; ?>
  <?php endforeach; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($post['title']) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($meta_desc) ?>">
  <meta name="twitter:url" content="<?= htmlspecialchars($canonical) ?>">

  <?php if (!empty($settings['seo_schema_enabled']) && $settings['seo_schema_enabled'] === '1'): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": <?= json_encode($post['title']) ?>,
    "datePublished": "<?= !empty($post['created_at']) ? date('c', strtotime($post['created_at'])) : '' ?>",
    "dateModified": "<?= !empty($post['updated_at']) ? date('c', strtotime($post['updated_at'])) : (!empty($post['created_at']) ? date('c', strtotime($post['created_at'])) : '') ?>",
    "description": <?= json_encode($meta_desc) ?>,
    "image": <?= json_encode($og_image ?: '') ?>,
    "author": {
      "@type": "Organization",
      "name": <?= json_encode($settings['seo_default_author'] ?? $site_title) ?>

    },
    "publisher": {
      "@type": "Organization",
      "name": <?= json_encode($site_title) ?>
    }
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": <?= json_encode(hs_base_url('/')) ?>
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": <?= json_encode($post['category_name'] ?? 'News') ?>,
        "item": <?= json_encode(hs_base_url('/')) ?>
      },
      {
        "@type": "ListItem",
        "position": 3,
        "name": <?= json_encode($post['title']) ?>,
        "item": <?= json_encode($canonical) ?>
      }
    ]
  }
  </script>
  <?php endif; ?>

  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
  <?= hs_pwa_head_tags() ?>
  <script defer src="<?= hs_base_url('assets/js/pwa.js') ?>"></script>
  <script defer src="<?= hs_base_url('assets/js/localized-datetime.js') ?>"></script>
  <script defer src="<?= hs_base_url('assets/js/social-share.js') ?>"></script>

  <style>
    :root {
      --primary: #D60000;
      --dark: #111111;
      --navy: #0B1220;
      --bg: #F6F7FB;
      --card: #FFFFFF;
      --border: #E5E7EB;
      --text: #111111;
      --text-muted: #6B7280;
    }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--bg);
      color: #111827;
    }
    a { color: var(--primary); text-decoration: none; }
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
      box-shadow:0 10px 24px rgba(11,18,32,0.12);
    }
    .logo-text { display:flex; flex-direction:column; }
    .logo-text-main { font-weight:800; letter-spacing:.18em; font-size:13px; }
    .logo-text-tag { font-size:11px; color:#6B7280; opacity:.9; }

    .nav-main {
      display:flex; align-items:center; gap:12px;
      font-size:12px; text-transform:uppercase; letter-spacing:.12em;
    }
    .nav-main a { color:#374151; padding:4px 8px; border-radius:999px; }
    .nav-main a:hover { background:#F3F4F6; color:var(--primary); text-decoration:none; }

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

    .user-bar { font-size:11px; color:#374151; text-align:right; }
    .user-bar a { color:var(--primary); }

    .page {
      width:100%;
      min-height:100vh;
      padding:18px 12px 32px;
      box-sizing:border-box;
    }

    .layout-article {
      max-width:1160px;
      margin:0 auto;
      display:grid;
      grid-template-columns:minmax(0,3fr) minmax(0,1.3fr);
      gap:18px;
    }

    .article-card {
      background:var(--card);
      border-radius:18px;
      box-shadow:0 12px 28px rgba(11,18,32,0.14);
      color:var(--text);
      overflow:hidden;
    }
    .article-hero-image {
      width:100%;
      max-height:420px;
      background:#e5e7eb;
      overflow:hidden;
    }
    .article-hero-image img {
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }

    .article-inner {
      padding:18px 20px 20px;
    }
    .breadcrumb {
      font-size:11px;
      color:#6B7280;
      margin-bottom:6px;
    }
    .breadcrumb a { color:#6B7280; }
    .breadcrumb a:hover { color:var(--primary); text-decoration:none; }

    .article-kicker {
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.18em;
      color:var(--primary);
      margin-bottom:6px;
    }
    .article-title {
      font-size:26px;
      font-weight:800;
      line-height:1.25;
      margin-bottom:8px;
      color:#0F172A;
    }
    .article-meta {
      font-size:12px;
      color:var(--text-muted);
      margin-bottom:12px;
    }

    .article-tags {
      margin-top:12px;
      font-size:12px;
    }
    .article-tags a {
      display:inline-block;
      padding:3px 8px;
      border-radius:999px;
      background:#EFF6FF;
      color:var(--primary);
      margin:0 4px 4px 0;
      font-size:11px;
    }

    .article-body {
      margin-top:14px;
      font-size:15px;
      line-height:1.7;
      color:var(--text);
    }
    .article-body p { margin:0 0 1em; }
    .article-body h2,
    .article-body h3,
    .article-body h4 {
      margin-top:1.4em;
      margin-bottom:0.6em;
      color:#111827;
    }
    .article-body img {
      max-width:100%;
      height:auto;
      margin:12px auto;
      display:block;
      border-radius:8px;
    }

    .share-block {
      margin-top:20px;
      padding-top:12px;
      border-top:1px solid var(--border);
      font-size:12px;
      color:var(--text-muted);
    }
    .share-links {
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      margin-top:8px;
    }
    .share-links a,
    .share-links button {
      display:inline-block;
      padding:6px 10px;
      border-radius:999px;
      border:1px solid #E5E7EB;
      font-size:11px;
      color:#111827;
      background:#FFFFFF;
      cursor:pointer;
    }
    .share-links button:hover,
    .share-links a:hover {
      border-color:#CBD5E1;
      color:#0B1220;
    }
    .share-feedback {
      min-height:18px;
      margin-top:6px;
      font-size:11px;
      color:#475569;
    }

    .related-block {
      margin-top:18px;
      padding-top:14px;
      border-top:1px solid var(--border);
    }
    .related-title {
      font-size:13px;
      text-transform:uppercase;
      letter-spacing:.16em;
      color:#6B7280;
      margin-bottom:8px;
    }
    .related-list {
      list-style:none;
      padding:0;
      margin:0;
      font-size:14px;
    }
    .related-list li {
      margin-bottom:6px;
    }

    .sidebar {
      display:flex;
      flex-direction:column;
      gap:14px;
    }
    .sidebar-card {
      background:#FFFFFF;
      border-radius:16px;
      border:1px solid #E5E7EB;
      padding:14px 14px 16px;
      color:#374151;
      box-shadow:0 12px 28px rgba(15,23,42,0.12);
    }
    .sidebar-title {
      font-size:13px;
      text-transform:uppercase;
      letter-spacing:.16em;
      margin-bottom:8px;
      color:var(--primary);
    }

    .sidebar-list {
      list-style:none;
      padding:0;
      margin:0;
      font-size:13px;
    }
    .sidebar-list li {
      margin-bottom:6px;
    }
    .sidebar-list a { color:#374151; }
    .sidebar-list a:hover { color:var(--primary); text-decoration:none; }

    footer {
      border-top:1px solid #E5E7EB;
      padding:10px 18px 16px;
      font-size:11px;
      color:#6B7280;
      text-align:center;
      background:#FFFFFF;
    }

    @media (max-width:960px) {
      .layout-article {
        grid-template-columns:minmax(0,1fr);
      }
    }
    @media (max-width:640px) {
      header { padding:8px 10px; }
      .page { padding:14px 8px 24px; }
      .article-inner { padding:14px 14px 16px; }
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

<main class="page">
  <div class="layout-article">
    <article class="article-card">
      <?php if (!empty($post['image_main'])): ?>
        <div class="article-hero-image">
          <img src="<?= hs_base_url($post['image_main']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>
      <?php endif; ?>
      <div class="article-inner">
        <nav class="breadcrumb">
          <a href="<?= hs_base_url('/') ?>">Home</a>
          <?php if (!empty($categoryName)): ?>
            › <a href="<?= hs_category_url(strtolower($categoryName)) ?>"><?= htmlspecialchars($categoryName) ?></a>
          <?php endif; ?>
        </nav>
        <div class="article-kicker">
          <?= htmlspecialchars($categoryName) ?>
          <?php if (!empty($post['region']) && $post['region'] !== 'global'): ?>
            · <?= strtoupper(htmlspecialchars($post['region'])) ?>
          <?php endif; ?>
        </div>
        <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>
        <div class="article-meta">
          <span data-localized-datetime data-timestamp="<?= htmlspecialchars((string)($post['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><?= hs_post_date_local($post) ?></span>
          <?php if (!empty($post['author_name'])): ?>
            · By <?= htmlspecialchars($post['author_name']) ?>
          <?php endif; ?>
        </div>

        <div class="article-body">
          <?php if (!empty($post['content'])): ?>
            <?= $post['content'] ?>
          <?php else: ?>
            <p>No content.</p>
          <?php endif; ?>
        </div>

        <?php
          // Optional article gallery: expects $post['gallery_images'] as JSON or comma-separated list
          $galleryItems = [];
          if (!empty($post['gallery_images'])) {
            if (is_string($post['gallery_images'])) {
              $decoded = json_decode($post['gallery_images'], true);
              if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $galleryItems = $decoded;
              } else {
                $galleryItems = array_map('trim', explode(',', $post['gallery_images']));
              }
            } elseif (is_array($post['gallery_images'])) {
              $galleryItems = $post['gallery_images'];
            }
          }
        ?>

        <?php if (!empty($galleryItems)): ?>
          <div class="article-gallery" style="margin-top:16px;">
            <div style="font-size:12px; text-transform:uppercase; letter-spacing:.08em; color:#6B7280; margin-bottom:6px;">
              Photo Gallery
            </div>
            <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:4px;">
              <?php foreach ($galleryItems as $img): ?>
                <?php $src = is_array($img) ? ($img['src'] ?? '') : $img; ?>
                <?php if (!empty($src)): ?>
                  <div style="min-width:140px; max-width:180px; border-radius:10px; overflow:hidden; border:1px solid rgba(148,163,184,.35);">
                    <img src="<?= hs_base_url($src) ?>" alt="" style="width:100%; height:100%; object-fit:cover; display:block;">
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>


        <?php if (!empty($tags)): ?>
          <div class="article-tags">
            <strong>Tags:</strong>
            <?php foreach ($tags as $tag): ?>
              <a href="<?= hs_tag_url($tag['slug']) ?>"><?= htmlspecialchars($tag['name']) ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="share-block">
          <div>Share this article</div>
          <div class="share-links">
            <a href="<?= htmlspecialchars(hs_social_share_url($shareCanonical, $shareTitle, 'whatsapp')) ?>" target="_blank" rel="noopener">WhatsApp</a>
            <a href="<?= htmlspecialchars(hs_social_share_url($shareCanonical, $shareTitle, 'facebook')) ?>" target="_blank" rel="noopener">Facebook</a>
            <a href="<?= htmlspecialchars(hs_social_share_url($shareCanonical, $shareTitle, 'x')) ?>" target="_blank" rel="noopener">X</a>
            <a href="<?= htmlspecialchars(hs_social_share_url($shareCanonical, $shareTitle, 'linkedin')) ?>" target="_blank" rel="noopener">LinkedIn</a>
            <a href="<?= htmlspecialchars(hs_social_share_url($shareCanonical, $shareTitle, 'telegram')) ?>" target="_blank" rel="noopener">Telegram</a>
            <a href="<?= htmlspecialchars(hs_social_share_url($shareCanonical, $shareTitle, 'email')) ?>">Email</a>
            <button type="button" class="share-native-btn" data-native-share data-share-url="<?= htmlspecialchars($shareCanonical) ?>" data-share-title="<?= htmlspecialchars($shareTitle) ?>" data-share-text="<?= htmlspecialchars($meta_desc) ?>">Share</button>
            <button type="button" class="share-copy-btn" data-copy-share-url="<?= htmlspecialchars($shareCanonical) ?>">Copy Link</button>
          </div>
          <div class="share-feedback" data-share-feedback aria-live="polite"></div>
        </div>

        <?php if (!empty($related)): ?>
          <div class="related-block">
            <div class="related-title">More from <?= htmlspecialchars($categoryName) ?></div>
            <ul class="related-list">
              <?php foreach ($related as $r): ?>
                <li>
                  <a href="<?= hs_post_url($r['slug']) ?>"><?= htmlspecialchars($r['title']) ?></a>
                  <span style="font-size:11px;color:#6B7280;"> · <?= hs_post_date_local($r) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </article>

    <aside class="sidebar">
      <section class="sidebar-card">
        <div class="sidebar-title">Trending</div>
        <?php if (empty($trending)): ?>
          <p style="font-size:12px;color:#6B7280;">No trending posts.</p>
        <?php else: ?>
          <ul class="sidebar-list">
            <?php foreach ($trending as $t): ?>
              <li>
                <a href="<?= hs_post_url($t['slug']) ?>"><?= htmlspecialchars($t['title']) ?></a>
                <div style="font-size:11px;color:#6B7280;"><?= hs_post_date_local($t) ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <section class="sidebar-card">
        <div class="sidebar-title">Homepage</div>
        <p style="font-size:12px;color:#374151;">
          <a href="<?= hs_base_url('index.php') ?>" style="color:var(--primary);">← Back to homepage</a>
        </p>
      </section>
    </aside>
  </div>
</main>

<footer>
  © <?= date('Y') ?> NEWS HDSPTV. All rights reserved.
</footer>
</body>
</html>
