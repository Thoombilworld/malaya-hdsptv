<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
$db = hs_db();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot = trim($_POST['slot'] ?? 'homepage_right');
    $image = trim($_POST['image_url'] ?? '');
    $link  = trim($_POST['link_url'] ?? '');
    $stmt = mysqli_prepare($db, "INSERT INTO hs_ads (slot, image_url, link_url) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE image_url = VALUES(image_url), link_url = VALUES(link_url)");
    mysqli_stmt_bind_param($stmt, 'sss', $slot, $image, $link);
    mysqli_stmt_execute($stmt);
    $msg = 'Ad updated.';
}
$res = mysqli_query($db, "SELECT * FROM hs_ads ORDER BY slot ASC");
$ads = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ads Manager – NEWS HDSPTV</title>
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
</head>
<body style="max-width:900px;margin:20px auto;padding:0 16px;">
  <h1>Banner Ads Manager</h1>
  <?php if ($msg): ?><div style="color:green;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post">
    <label>Slot</label><br>
    <select name="slot">
      <option value="homepage_right">Homepage Right Sidebar</option>
      <option value="homepage_top">Homepage Top</option>
      <option value="homepage_inline">Homepage Inline</option>
    </select><br><br>
    <label>Ad Image URL (uploaded path)</label><br>
    <input type="text" name="image_url" style="width:100%;"><br><br>
    <label>Click Link URL</label><br>
    <input type="text" name="link_url" style="width:100%;"><br><br>
    <button type="submit">Save Ad</button>
  </form>
  <h2>Current Ads</h2>
  <table border="1" cellpadding="4" cellspacing="0">
    <tr><th>Slot</th><th>Image</th><th>Link</th></tr>
    <?php foreach ($ads as $ad): ?>
      <tr>
        <td><?= htmlspecialchars($ad['slot']) ?></td>
        <td><?= htmlspecialchars($ad['image_url']) ?></td>
        <td><?= htmlspecialchars($ad['link_url']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
