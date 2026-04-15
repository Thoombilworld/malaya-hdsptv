<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('settings.manage');
require __DIR__ . '/_layout.php';

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

hs_admin_shell_start('Social Dispatcher – HDSPTV', 'Social Dispatcher', 'social');
?>

<section class="card" style="max-width:940px;">
  <h2>Dispatch Published Story</h2>
  <p class="muted">Send published articles to your external webhook automation pipeline.</p>
  <p><a class="btn btn-secondary" href="<?= hs_base_url('admin/social.php') ?>">Back to Social Settings</a></p>
  <?php if ($message): ?><div class="badge badge-success" style="margin-bottom:12px;"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="error-box"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post">
    <?= hs_csrf_input() ?>
    <div class="field">
      <label for="post_id">Select Published Article</label>
      <select id="post_id" name="post_id" required>
        <option value="">Choose article…</option>
        <?php foreach ($recent as $row): ?>
          <option value="<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['title']) ?> (<?= htmlspecialchars($row['created_at']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <p class="muted" style="margin-bottom:12px;">Webhook: <?= htmlspecialchars($settings['social_distribution_webhook'] ?? 'Not configured') ?></p>
    <button class="btn btn-primary" type="submit">Dispatch to Automation</button>
  </form>
</section>

<?php hs_admin_shell_end(); ?>
