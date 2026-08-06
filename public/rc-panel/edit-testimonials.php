<?php
require __DIR__ . '/auth.php';
manage_require_login();
$testimonials = manage_read_json('testimonials.json');
$items = $testimonials['items'] ?? [];
while (count($items) < 3) {
    $items[] = ['quote' => '', 'name' => '', 'role' => ''];
}
$navCurrent = 'testimonials';
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
      <h1>Testimonials</h1>
      <p class="help">Shown on the homepage. Keep quotes short for mobile.</p>
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
