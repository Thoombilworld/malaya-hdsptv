<?php
require __DIR__ . '/../bootstrap.php';
hs_require_admin();
hs_require_permission('settings.manage');
require __DIR__ . '/_layout.php';

$logFile = __DIR__ . '/../writable/logs/app.log';
$entries = [];

if (is_file($logFile) && is_readable($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $lines = array_reverse(array_slice($lines, -200));
    foreach ($lines as $line) {
        $decoded = json_decode($line, true);
        if (!is_array($decoded)) {
            continue;
        }
        $entries[] = [
            'time' => (string) ($decoded['time'] ?? ''),
            'level' => strtolower((string) ($decoded['level'] ?? 'info')),
            'message' => (string) ($decoded['message'] ?? ''),
            'context' => is_array($decoded['context'] ?? null) ? $decoded['context'] : [],
        ];
    }
}

$counts = ['error' => 0, 'warning' => 0, 'info' => 0];
foreach ($entries as $entry) {
    $level = $entry['level'];
    if (isset($counts[$level])) {
        $counts[$level]++;
    }
}

hs_admin_shell_start('System Logs – HDSPTV', 'System Logs', 'logs');
?>

<section class="grid-12" style="margin-bottom:24px;">
  <article class="card col-4 col-md-6 col-sm-12">
    <div class="kpi-label">Total Entries (last 200)</div>
    <div class="kpi-value"><?= count($entries) ?></div>
  </article>
  <article class="card col-4 col-md-6 col-sm-12">
    <div class="kpi-label">Warnings</div>
    <div class="kpi-value"><?= $counts['warning'] ?></div>
  </article>
  <article class="card col-4 col-md-12 col-sm-12">
    <div class="kpi-label">Errors</div>
    <div class="kpi-value"><?= $counts['error'] ?></div>
  </article>
</section>

<section class="card">
  <h2>Application Log Feed</h2>
  <p class="muted">Showing the most recent 200 entries from <code>writable/logs/app.log</code>.</p>
  <?php if (!$entries): ?>
    <p class="muted">No log entries available yet.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th style="width:180px;">Time</th>
            <th style="width:100px;">Level</th>
            <th>Message</th>
            <th>Context</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($entries as $entry): ?>
            <?php
              $badgeClass = 'badge-info';
              if ($entry['level'] === 'warning') {
                  $badgeClass = 'badge-warning';
              } elseif ($entry['level'] === 'error') {
                  $badgeClass = 'badge-error';
              }
              $contextJson = $entry['context'] ? json_encode($entry['context'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '{}';
            ?>
            <tr>
              <td><?= htmlspecialchars($entry['time']) ?></td>
              <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars(strtoupper($entry['level'])) ?></span></td>
              <td><?= htmlspecialchars($entry['message']) ?></td>
              <td><pre class="log-context"><?= htmlspecialchars((string) $contextJson) ?></pre></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php hs_admin_shell_end(); ?>
