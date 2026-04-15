<?php
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../app/Modules/Admin/module.php';
hs_require_admin();
$settings = hs_settings();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Social Links – NEWS HDSPTV</title>
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
</head>
<body style="max-width:800px;margin:20px auto;padding:0 16px;">
  <?= hs_admin_back_link() ?>
  <h1>Social Media Links</h1>
  <?php if ($msg): ?><div style="color:green;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <p style="font-size:12px;color:#555;">Optional distribution tool: configure a webhook and trigger article dispatches from the social dispatcher.</p>
  <p><a href="<?= hs_base_url('admin/social_dispatch.php') ?>">Open Social Dispatcher</a></p>
  <form method="post">
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
      <label><?= $label ?> URL</label><br>
      <input type="text" name="<?= $key ?>" style="width:100%;" value="<?= htmlspecialchars($settings[$key] ?? '') ?>"><br><br>
    <?php endforeach; ?>
    <label><input type="checkbox" name="social_auto_enabled" value="1" <?= !empty($settings['social_auto_enabled']) ? 'checked' : '' ?>> Enable automated social distribution</label><br><br>
    <label>Distribution Webhook URL</label><br>
    <input type="url" name="social_distribution_webhook" style="width:100%;" value="<?= htmlspecialchars($settings['social_distribution_webhook'] ?? '') ?>" placeholder="https://your-automation.example/webhook"><br><br>
    <button type="submit">Save Links</button>
  </form>
</body>
</html>
