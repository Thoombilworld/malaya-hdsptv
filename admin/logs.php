<?php
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../app/Modules/Admin/module.php';
hs_require_admin();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>System Logs – NEWS HDSPTV</title>
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
</head>
<body style="max-width:900px;margin:20px auto;padding:0 16px;">
  <?= hs_admin_back_link() ?>
  <h1>System Logs (Placeholder)</h1>
  <p>This page is a placeholder for integrating error logs or custom activity logs.</p>
  <p>For now, check your server's PHP error log in cPanel.</p>
</body>
</html>
