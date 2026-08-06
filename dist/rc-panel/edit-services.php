<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'services';
$services = manage_ensure_content('services', 'services.json');
$items = [];
foreach (($services['items'] ?? []) as $item) {
    if (is_array($item) && trim((string) ($item['title'] ?? '')) !== '') {
        $items[] = $item;
    }
}
usort($items, fn($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));
$saved = ($_GET['saved'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Services | Admin</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <div class="page-head">
        <div>
          <h1>Services</h1>
          <p class="help" style="margin:0">These are the same services shown on the website.</p>
        </div>
        <div class="actions">
          <a class="btn secondary" href="/services" target="_blank" rel="noopener">View page ↗</a>
          <form class="inline-form" method="post" action="/rc-panel/save.php" onsubmit="return confirm('Reload services from packaged website content?');">
            <input type="hidden" name="type" value="content_reload" />
            <input type="hidden" name="key" value="services" />
            <button type="submit" class="btn secondary">Repair / re-sync</button>
          </form>
        </div>
      </div>

      <?php if ($saved): ?><p class="success">Services saved.</p><?php endif; ?>

      <?php if (count($items) === 0): ?>
        <div class="card">
          <p class="error">No services found in live data. Click <strong>Repair / re-sync</strong> to restore them.</p>
        </div>
      <?php else: ?>
        <form method="post" action="/rc-panel/save.php">
          <input type="hidden" name="type" value="services" />
          <input type="hidden" name="count" value="<?= count($items) ?>" />
          <?php foreach ($items as $i => $item): ?>
            <div class="card">
              <h2><?= manage_h($item['title'] ?? ('Service ' . ($i + 1))) ?></h2>
              <div class="grid-2">
                <label>Title <input name="title_<?= $i ?>" type="text" value="<?= manage_h($item['title'] ?? '') ?>" /></label>
                <label>Slug <input name="slug_<?= $i ?>" type="text" value="<?= manage_h($item['slug'] ?? '') ?>" /></label>
              </div>
              <label>Summary <textarea name="summary_<?= $i ?>"><?= manage_h($item['summary'] ?? '') ?></textarea></label>
              <label>Order <input name="order_<?= $i ?>" type="text" value="<?= manage_h((string) ($item['order'] ?? ($i + 1))) ?>" /></label>
              <label>SEO title <input name="seoTitle_<?= $i ?>" type="text" value="<?= manage_h($item['seoTitle'] ?? '') ?>" /></label>
              <label>SEO description <textarea name="seoDescription_<?= $i ?>"><?= manage_h($item['seoDescription'] ?? '') ?></textarea></label>
              <label>Bullets (one per line) <textarea name="bullets_<?= $i ?>"><?= manage_h(implode("\n", $item['bullets'] ?? [])) ?></textarea></label>
            </div>
          <?php endforeach; ?>
          <button type="submit">Save services</button>
        </form>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
