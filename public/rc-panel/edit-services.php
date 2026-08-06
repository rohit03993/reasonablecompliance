<?php
require __DIR__ . '/auth.php';
manage_require_login();
$services = manage_read_json('services.json');
$items = $services['items'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Services | Admin</title>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <h2>Content Admin</h2>
      <nav>
        <a href="/rc-panel/">Dashboard</a>
        <a href="/rc-panel/edit-site.php">Brand & Contact</a>
        <a href="/rc-panel/edit-homepage.php">Homepage</a>
        <a href="/rc-panel/edit-about.php">About</a>
        <a href="/rc-panel/edit-contact.php">Contact page</a>
        <a class="active" href="/rc-panel/edit-services.php">Services</a>
        <a href="/rc-panel/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/rc-panel/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>Services</h1>
      <p class="help">Bullets: one item per line. Keep slug unchanged unless you know the URL.</p>
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
    </main>
  </div>
</body>
</html>
