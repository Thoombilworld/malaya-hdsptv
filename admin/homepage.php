<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('article.publish');
require __DIR__ . '/_layout.php';

$db = hs_db();
$settings = hs_settings();
$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and try again.';
    } else {
        $keys = ['hp_show_breaking','hp_show_featured','hp_show_trending','hp_show_video','hp_show_gallery','hp_show_ads_sidebar'];
        foreach ($keys as $k) {
            $v = isset($_POST[$k]) ? '1' : '0';
            $stmt = mysqli_prepare($db, "INSERT INTO hs_settings (`key`,`value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            mysqli_stmt_bind_param($stmt, 'ss', $k, $v);
            mysqli_stmt_execute($stmt);
        }
        $msg = 'Homepage layout updated.';
        $settings = hs_settings();
    }
}

function hp_checked($settings, $key) {
    return !empty($settings[$key]) && $settings[$key] === '1' ? 'checked' : '';
}

hs_admin_shell_start('Homepage Manager – HDSPTV', 'Homepage Manager', 'homepage');
?>

<section class="card" style="max-width:840px;">
  <h2>Homepage Section Controls</h2>
  <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($msg): ?><div class="badge badge-success" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post" action="<?= hs_admin_url('homepage.php') ?>">
    <?= hs_csrf_input() ?>
    <label style="display:block;margin-bottom:10px;"><input type="checkbox" name="hp_show_breaking" <?= hp_checked($settings,'hp_show_breaking') ?>> Show Breaking Ticker</label>
    <label style="display:block;margin-bottom:10px;"><input type="checkbox" name="hp_show_featured" <?= hp_checked($settings,'hp_show_featured') ?>> Show Featured Stories</label>
    <label style="display:block;margin-bottom:10px;"><input type="checkbox" name="hp_show_trending" <?= hp_checked($settings,'hp_show_trending') ?>> Show Trending Widget</label>
    <label style="display:block;margin-bottom:10px;"><input type="checkbox" name="hp_show_video" <?= hp_checked($settings,'hp_show_video') ?>> Show Video Section</label>
    <label style="display:block;margin-bottom:10px;"><input type="checkbox" name="hp_show_gallery" <?= hp_checked($settings,'hp_show_gallery') ?>> Show Gallery Section</label>
    <label style="display:block;margin-bottom:16px;"><input type="checkbox" name="hp_show_ads_sidebar" <?= hp_checked($settings,'hp_show_ads_sidebar') ?>> Show Sidebar Ads</label>
    <button class="btn btn-primary" type="submit">Save Layout</button>
  </form>
</section>

<?php hs_admin_shell_end(); ?>
