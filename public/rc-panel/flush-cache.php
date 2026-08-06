<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'cache';
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = manage_flush_cache();
}
$versionFile = manage_cache_version_path();
$current = is_file($versionFile) ? manage_read_json('cache-version.json') : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Flush cache | Admin</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Flush cache</h1>
      <p class="help">
        Use this after content updates if the public site still shows old text.
        This refreshes the content cache version and clears PHP OPcache when available.
      </p>

      <?php if ($result): ?>
        <p class="success">Cache flush completed.</p>
        <div class="card">
          <ul>
            <?php foreach ($result['notes'] as $note): ?>
              <li><?= manage_h($note) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card">
        <p><strong>Current cache version:</strong> <?= manage_h((string) ($current['version'] ?? 'not set')) ?></p>
        <p class="small">Updated: <?= manage_h((string) ($current['updatedAt'] ?? '—')) ?></p>
        <form method="post">
          <button type="submit">Flush site cache now</button>
        </form>
      </div>

      <div class="card">
        <h2>Also do this</h2>
        <ol>
          <li>Hard-refresh the website: <strong>Ctrl + Shift + R</strong></li>
          <li>If Hostinger has LiteSpeed Cache, purge it from hPanel too</li>
        </ol>
      </div>
    </main>
  </div>
</body>
</html>
