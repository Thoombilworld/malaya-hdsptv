<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('settings.manage');

$settings = hs_settings();
$db = hs_db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hs_csrf_validate()) {
        $error = 'Invalid security token.';
    } else {
        $postId = (int)($_POST['post_id'] ?? 0);
        $res = mysqli_query($db, "SELECT id, title, slug, excerpt, image_main, created_at FROM hs_posts WHERE id = {$postId} LIMIT 1");
        $post = $res ? mysqli_fetch_assoc($res) : null;
        $webhook = trim((string)($settings['social_distribution_webhook'] ?? ''));

        if (!$post) {
            $error = 'Article not found.';
        } elseif ($webhook === '') {
            $error = 'Distribution webhook is not configured.';
        } elseif (!function_exists('curl_init')) {
            $error = 'cURL extension is required for webhook distribution.';
        } else {
            $payload = [
                'event' => 'article.publish',
                'site' => $settings['site_title'] ?? 'NEWS HDSPTV',
                'locale' => hs_locale(),
                'article' => [
                    'id' => (int)$post['id'],
                    'title' => $post['title'],
                    'slug' => $post['slug'],
                    'excerpt' => $post['excerpt'] ?? '',
                    'url' => hs_post_url($post['slug']),
                    'image' => !empty($post['image_main']) ? hs_base_url($post['image_main']) : null,
                    'created_at' => $post['created_at'] ?? null,
                ],
                'channels' => ['facebook', 'whatsapp', 'x', 'linkedin', 'telegram'],
            ];

            $ch = curl_init($webhook);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 8,
            ]);
            $response = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false || $status >= 400 || $status === 0) {
                $error = 'Dispatch failed. ' . ($curlError ?: ('HTTP ' . $status));
                hs_log_event('error', 'Social dispatch failed', ['post_id' => $postId, 'status' => $status, 'error' => $curlError]);
            } else {
                $message = 'Article dispatched to automation webhook.';
                hs_log_event('info', 'Social dispatch sent', ['post_id' => $postId, 'status' => $status]);
            }
        }
    }
}

$recent = [];
$recentRes = mysqli_query($db, "SELECT id, title, slug, created_at FROM hs_posts WHERE status='published' ORDER BY created_at DESC LIMIT 50");
if ($recentRes) {
    while ($row = mysqli_fetch_assoc($recentRes)) {
        $recent[] = $row;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Social Dispatcher – NEWS HDSPTV</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
</head>
<body style="max-width:860px;margin:20px auto;padding:0 16px;">
  <h1>Social Dispatcher</h1>
  <p><a href="<?= hs_base_url('admin/social.php') ?>">← Back to Social Settings</a></p>
  <?php if ($message): ?><div class="success-msg"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post" class="auth-card" style="max-width:100%;">
    <?= hs_csrf_input() ?>
    <div class="auth-field">
      <label for="post_id">Select Published Article</label>
      <select id="post_id" name="post_id" required style="height:48px;border:1px solid #d1d5db;border-radius:12px;padding:0 10px;">
        <option value="">Choose article…</option>
        <?php foreach ($recent as $row): ?>
          <option value="<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['title']) ?> (<?= htmlspecialchars($row['created_at']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="meta" style="margin-bottom:12px;">Webhook: <?= htmlspecialchars($settings['social_distribution_webhook'] ?? 'Not configured') ?></div>
    <button class="btn btn-primary" type="submit">Dispatch to Automation</button>
  </form>
</body>
</html>
