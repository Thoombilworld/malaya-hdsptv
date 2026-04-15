<?php
require __DIR__ . '/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [];
$now = gmdate('c');
$addUrl = static function (&$urls, $loc, $lastmod, $changefreq = 'daily', $priority = '0.7') {
    $urls[] = [
        'loc' => $loc,
        'lastmod' => $lastmod,
        'changefreq' => $changefreq,
        'priority' => $priority,
    ];
};

$addUrl($urls, hs_base_url('/'), $now, 'hourly', '1.0');
$addUrl($urls, hs_base_url('breaking.php'), $now, 'hourly', '0.9');
$addUrl($urls, hs_base_url('trending.php'), $now, 'hourly', '0.9');
$addUrl($urls, hs_base_url('video.php'), $now, 'daily', '0.8');
$addUrl($urls, hs_base_url('gallery.php'), $now, 'daily', '0.8');
$addUrl($urls, hs_base_url('about.php'), $now, 'monthly', '0.4');
$addUrl($urls, hs_base_url('contact.php'), $now, 'monthly', '0.4');

if (defined('HS_INSTALLED') && HS_INSTALLED) {
    $db = hs_db();

    $res = mysqli_query($db, "SELECT slug, updated_at, created_at FROM hs_posts WHERE status='published' ORDER BY created_at DESC LIMIT 5000");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $last = !empty($row['updated_at']) ? strtotime($row['updated_at']) : strtotime($row['created_at'] ?? 'now');
            $addUrl(
                $urls,
                hs_base_url('post.php?slug=' . urlencode($row['slug'] ?? '')),
                gmdate('c', $last ?: time()),
                'daily',
                '0.8'
            );
        }
    }

    $catRes = mysqli_query($db, "SELECT slug, updated_at, created_at FROM hs_categories ORDER BY id DESC LIMIT 500");
    if ($catRes) {
        while ($row = mysqli_fetch_assoc($catRes)) {
            $last = !empty($row['updated_at']) ? strtotime($row['updated_at']) : strtotime($row['created_at'] ?? 'now');
            $addUrl(
                $urls,
                hs_base_url('category.php?slug=' . urlencode($row['slug'] ?? '')),
                gmdate('c', $last ?: time()),
                'daily',
                '0.7'
            );
        }
    }

    $tagRes = mysqli_query($db, "SELECT slug, created_at FROM hs_tags ORDER BY id DESC LIMIT 1000");
    if ($tagRes) {
        while ($row = mysqli_fetch_assoc($tagRes)) {
            $last = strtotime($row['created_at'] ?? 'now');
            $addUrl(
                $urls,
                hs_base_url('tag.php?slug=' . urlencode($row['slug'] ?? '')),
                gmdate('c', $last ?: time()),
                'weekly',
                '0.6'
            );
        }
    }
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $item): ?>
  <url>
    <loc><?= htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($item['lastmod'], ENT_XML1, 'UTF-8') ?></lastmod>
    <changefreq><?= htmlspecialchars($item['changefreq'], ENT_XML1, 'UTF-8') ?></changefreq>
    <priority><?= htmlspecialchars($item['priority'], ENT_XML1, 'UTF-8') ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
