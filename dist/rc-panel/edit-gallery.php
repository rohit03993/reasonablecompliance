<?php
require __DIR__ . '/auth.php';
manage_require_login();
$gallery = manage_read_json('gallery.json');
$items = $gallery['items'] ?? [];
while (count($items) < 6) {
    $items[] = ['title' => '', 'image' => '', 'order' => count($items) + 1];
}
$navCurrent = 'gallery';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Gallery | Admin</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Photo gallery</h1>
      <p class="help">Photos only. Upload images via File Manager to <code>/images/</code>, then paste the path like <code>/images/my-photo.jpg</code>.</p>
      <form method="post" action="/rc-panel/save.php">
        <input type="hidden" name="type" value="gallery" />
        <div class="card">
          <label>Gallery title <input name="title" type="text" value="<?= manage_h($gallery['title'] ?? 'Gallery') ?>" /></label>
          <label>Intro text <textarea name="intro"><?= manage_h($gallery['intro'] ?? '') ?></textarea></label>
        </div>
        <?php foreach ($items as $i => $item): ?>
          <div class="card">
            <h2>Photo <?= $i + 1 ?></h2>
            <label>Title <input name="item_title[]" type="text" value="<?= manage_h($item['title'] ?? '') ?>" /></label>
            <label>Image path or URL <input name="item_image[]" type="text" value="<?= manage_h($item['image'] ?? '') ?>" placeholder="/images/photo.jpg" /></label>
            <label>Order <input name="item_order[]" type="text" value="<?= manage_h((string) ($item['order'] ?? ($i + 1))) ?>" /></label>
          </div>
        <?php endforeach; ?>
        <button type="submit">Save gallery</button>
      </form>
    </main>
  </div>
</body>
</html>
