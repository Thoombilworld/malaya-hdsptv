<?php
require __DIR__ . '/bootstrap.php';
$rows=[];
if(defined('HS_INSTALLED')&&HS_INSTALLED){$r=mysqli_query(hs_db(),"SELECT title,slug,excerpt,created_at FROM hs_posts WHERE status='published' AND is_breaking=1 ORDER BY created_at DESC LIMIT 20");if($r){$rows=mysqli_fetch_all($r,MYSQLI_ASSOC);}}
?>
<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Breaking News</title><link rel='stylesheet' href='<?= hs_base_url('assets/css/style.css') ?>'></head><body><main class='page-bg'><div class='container'><div class='section-head'><h1>Breaking News</h1></div><div class='card-grid'><?php foreach($rows as $p): ?><article class='news-card'><span class='badge badge-breaking'>Breaking</span><h3><a href='<?= hs_base_url('post.php?slug='.urlencode($p['slug'])) ?>'><?= htmlspecialchars($p['title']) ?></a></h3><p><?= htmlspecialchars($p['excerpt']??'') ?></p><div class='meta'><?= htmlspecialchars($p['created_at']) ?></div></article><?php endforeach; if(empty($rows)):?><div class='panel'>No active breaking stories.</div><?php endif;?></div></div></main></body></html>
