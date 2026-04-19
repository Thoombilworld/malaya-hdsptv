<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('seo.manage');
require __DIR__ . '/_layout.php';

$settings = hs_settings();
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and try again.';
    } else {
        $pairs = [
            'seo_meta_description'     => trim($_POST['seo_meta_description'] ?? ''),
            'seo_meta_keywords'        => trim($_POST['seo_meta_keywords'] ?? ''),
            'homepage_og_image'        => trim($_POST['homepage_og_image'] ?? ''),
            'default_article_og_image' => trim($_POST['default_article_og_image'] ?? ''),
            'seo_schema_enabled'       => !empty($_POST['seo_schema_enabled']) ? '1' : '0',
            'seo_default_author'       => trim($_POST['seo_default_author'] ?? 'NEWS HDSPTV'),
        ];
        $db = hs_db();
        foreach ($pairs as $k => $v) {
            $stmt = mysqli_prepare($db, "INSERT INTO hs_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            mysqli_stmt_bind_param($stmt, 'ss', $k, $v);
            mysqli_stmt_execute($stmt);
        }
        $msg = 'SEO settings updated.';
        $settings = hs_settings();
    }
}

hs_admin_shell_start('SEO Manager – HDSPTV', 'SEO Manager', 'seo');
?>

<section class="card" style="max-width:900px;">
  <h2>Global SEO Settings</h2>
  <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($msg): ?><div class="badge badge-success" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form method="post" action="<?= hs_admin_url('seo.php') ?>">
    <?= hs_csrf_input() ?>
    <div class="field"><label>Meta Description</label><textarea name="seo_meta_description" style="width:100%;min-height:90px;border:1px solid var(--border);border-radius:12px;padding:12px;"><?= htmlspecialchars($settings['seo_meta_description'] ?? '') ?></textarea></div>
    <div class="field"><label>Meta Keywords</label><textarea name="seo_meta_keywords" style="width:100%;min-height:70px;border:1px solid var(--border);border-radius:12px;padding:12px;"><?= htmlspecialchars($settings['seo_meta_keywords'] ?? '') ?></textarea></div>
    <div class="field"><label>Homepage OG Image URL</label><input type="text" name="homepage_og_image" value="<?= htmlspecialchars($settings['homepage_og_image'] ?? '') ?>"></div>
    <div class="field"><label>Default Article OG Image URL</label><input type="text" name="default_article_og_image" value="<?= htmlspecialchars($settings['default_article_og_image'] ?? '') ?>"></div>
    <div class="field"><label>Default Author</label><input type="text" name="seo_default_author" value="<?= htmlspecialchars($settings['seo_default_author'] ?? 'NEWS HDSPTV') ?>"></div>
    <label style="display:flex;gap:8px;align-items:center;margin:10px 0 16px;"><input type="checkbox" name="seo_schema_enabled" value="1" <?= !empty($settings['seo_schema_enabled']) && $settings['seo_schema_enabled'] === '1' ? 'checked' : '' ?>> Enable JSON-LD NewsArticle schema</label>
    <button class="btn btn-primary" type="submit">Save SEO Settings</button>
  </form>
</section>

<?php hs_admin_shell_end(); ?>
