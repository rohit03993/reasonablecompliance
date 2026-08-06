<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'testimonials';
$testimonials = manage_ensure_content('testimonials', 'testimonials.json');
$items = [];
foreach (($testimonials['items'] ?? []) as $item) {
    if (is_array($item) && trim((string) ($item['quote'] ?? '')) !== '') {
        $items[] = $item;
    }
}
while (count($items) < 3) {
    $items[] = ['quote' => '', 'name' => '', 'role' => ''];
}
$saved = ($_GET['saved'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Testimonials | Admin</title>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <div class="page-head">
        <div>
          <h1>Testimonials</h1>
          <p class="help" style="margin:0">Shown on the homepage. Keep quotes short for mobile.</p>
        </div>
        <form class="inline-form" method="post" action="/rc-panel/save.php" onsubmit="return confirm('Reload testimonials from packaged website content?');">
          <input type="hidden" name="type" value="content_reload" />
          <input type="hidden" name="key" value="testimonials" />
          <button type="submit" class="btn secondary">Repair / re-sync</button>
        </form>
      </div>

      <?php if ($saved): ?><p class="success">Testimonials saved.</p><?php endif; ?>

      <form method="post" action="/rc-panel/save.php">
        <input type="hidden" name="type" value="testimonials" />
        <div class="card">
          <label>Section title <input name="title" type="text" value="<?= manage_h($testimonials['title'] ?? '') ?>" /></label>
        </div>
        <?php foreach ($items as $i => $item): ?>
          <div class="card">
            <h2>Testimonial <?= $i + 1 ?></h2>
            <label>Quote <textarea name="quote[]"><?= manage_h($item['quote'] ?? '') ?></textarea></label>
            <div class="grid-2">
              <label>Name <input name="name[]" type="text" value="<?= manage_h($item['name'] ?? '') ?>" /></label>
              <label>Role / company <input name="role[]" type="text" value="<?= manage_h($item['role'] ?? '') ?>" /></label>
            </div>
          </div>
        <?php endforeach; ?>
        <button type="submit">Save testimonials</button>
      </form>
    </main>
  </div>
</body>
</html>
