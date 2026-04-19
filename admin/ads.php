<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('ads.manage');
require __DIR__ . '/_layout.php';

$db = hs_db();
$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and try again.';
    } else {
        $slot = trim($_POST['slot'] ?? 'homepage_right');
        $image = trim($_POST['image_url'] ?? '');
        $link  = trim($_POST['link_url'] ?? '');
        $stmt = mysqli_prepare($db, "INSERT INTO hs_ads (slot, image_url, link_url) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE image_url = VALUES(image_url), link_url = VALUES(link_url)");
        mysqli_stmt_bind_param($stmt, 'sss', $slot, $image, $link);
        mysqli_stmt_execute($stmt);
        $msg = 'Ad updated.';
    }
}
$res = mysqli_query($db, "SELECT * FROM hs_ads ORDER BY slot ASC");
$ads = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];

hs_admin_shell_start('Ads Manager – HDSPTV', 'Ads & Banners', 'ads');
?>

<section class="grid-12">
  <article class="card col-5 col-md-12">
    <h2>Update Ad Slot</h2>
    <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($msg): ?><div class="badge badge-success" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post" action="<?= hs_admin_url('ads.php') ?>">
      <?= hs_csrf_input() ?>
      <div class="field"><label>Slot</label><select name="slot" style="height:50px;border:1px solid var(--border);border-radius:12px;padding:0 12px;"><option value="homepage_right">Homepage Right Sidebar</option><option value="homepage_top">Homepage Top</option><option value="homepage_inline">Homepage Inline</option></select></div>
      <div class="field"><label>Ad Image URL</label><input type="text" name="image_url"></div>
      <div class="field"><label>Click Link URL</label><input type="text" name="link_url"></div>
      <button class="btn btn-primary" type="submit">Save Ad</button>
    </form>
  </article>

  <article class="card col-7 col-md-12">
    <h2>Current Ad Slots</h2>
    <div class="table-wrap">
      <table class="table"><thead><tr><th>Slot</th><th>Image</th><th>Link</th></tr></thead><tbody>
      <?php foreach ($ads as $ad): ?>
        <tr><td><?= htmlspecialchars($ad['slot']) ?></td><td><?= htmlspecialchars($ad['image_url']) ?></td><td><?= htmlspecialchars($ad['link_url']) ?></td></tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </article>
</section>

<?php hs_admin_shell_end(); ?>
