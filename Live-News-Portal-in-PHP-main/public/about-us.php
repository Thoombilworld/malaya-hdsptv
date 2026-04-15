<?php
require __DIR__ . '/../../bootstrap.php';
$settings = hs_settings();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About Us - <?= htmlspecialchars($settings['site_title'] ?? 'HDSPTV') ?></title>
  <link rel="stylesheet" href="<?= hs_base_url('assets/css/style.css') ?>">
</head>
<body>
  <main class="page-bg">
    <div class="container">
      <section class="hero-card">
        <div class="hero-content">
          <h1>About HDSPTV</h1>
          <p>HDSPTV is an international digital newsroom focused on trusted reporting across India, GCC, Kerala and world affairs.</p>
          <p class="meta">Built for fast, factual, and multilingual news delivery.</p>
          <a class="btn btn-primary" href="<?= hs_base_url('/') ?>">Back to Homepage</a>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
