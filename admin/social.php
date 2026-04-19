<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('settings.manage');
require __DIR__ . '/_layout.php';

$settings = hs_settings();
$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid form session. Refresh and submit again.';
    } else {
        $keys = [
            'social_facebook',
            'social_youtube',
            'social_instagram',
            'social_x',
            'social_tiktok',
            'social_telegram',
            'social_threads',
            'social_linkedin',
            'social_auto_enabled',
            'social_distribution_webhook',
        ];
        $db = hs_db();
        foreach ($keys as $k) {
            $v = $_POST[$k] ?? '';
            $stmt = mysqli_prepare($db, "INSERT INTO hs_settings (`key`,`value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            mysqli_stmt_bind_param($stmt, 'ss', $k, $v);
            mysqli_stmt_execute($stmt);
        }
        $msg = 'Social links updated.';
        $settings = hs_settings();
    }
}

hs_admin_shell_start('Social Manager – HDSPTV', 'Social Channels', 'social');
?>

<section class="card" style="max-width:940px;">
  <h2>Social Media Links</h2>
  <p class="muted">Configure channel URLs and optional webhook-based social automation.</p>
  <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($msg): ?><div class="badge badge-success" style="margin-bottom:12px;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <p><a class="btn btn-secondary" href="<?= hs_admin_url('social_dispatch.php') ?>">Open Social Dispatcher</a></p>

  <form method="post" action="<?= hs_admin_url('social.php') ?>">
    <?= hs_csrf_input() ?>
    <?php
      $fields = [
        'social_facebook' => 'Facebook',
        'social_youtube'  => 'YouTube',
        'social_instagram'=> 'Instagram',
        'social_x'        => 'X (Twitter)',
        'social_tiktok'   => 'TikTok',
        'social_telegram' => 'Telegram',
        'social_threads'  => 'Threads',
        'social_linkedin' => 'LinkedIn',
      ];
      foreach ($fields as $key => $label):
    ?>
      <div class="field">
        <label><?= $label ?> URL</label>
        <input type="url" name="<?= $key ?>" value="<?= htmlspecialchars($settings[$key] ?? '') ?>" placeholder="https://<?= strtolower($label) ?>.com/...">
      </div>
    <?php endforeach; ?>

    <label style="display:flex;gap:8px;align-items:center;margin:4px 0 12px;">
      <input type="checkbox" name="social_auto_enabled" value="1" <?= !empty($settings['social_auto_enabled']) ? 'checked' : '' ?>>
      Enable automated social distribution
    </label>

    <div class="field">
      <label>Distribution Webhook URL</label>
      <input type="url" name="social_distribution_webhook" value="<?= htmlspecialchars($settings['social_distribution_webhook'] ?? '') ?>" placeholder="https://your-automation.example/webhook">
    </div>

    <button class="btn btn-primary" type="submit">Save Links</button>
  </form>
</section>

<?php hs_admin_shell_end(); ?>
