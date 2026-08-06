<?php
require __DIR__ . '/auth.php';
manage_require_login();
$faqs = manage_read_json('faqs.json');
$items = $faqs['items'] ?? [];
while (count($items) < 4) {
    $items[] = ['question' => '', 'answer' => '', 'order' => count($items) + 1];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>FAQs | Admin</title>
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
        <a href="/rc-panel/edit-services.php">Services</a>
        <a class="active" href="/rc-panel/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/rc-panel/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>FAQs</h1>
      <form method="post" action="/rc-panel/save.php">
        <input type="hidden" name="type" value="faqs" />
        <?php foreach ($items as $i => $item): ?>
          <div class="card">
            <h2>FAQ <?= $i + 1 ?></h2>
            <label>Question <input name="question[]" type="text" value="<?= manage_h($item['question'] ?? '') ?>" /></label>
            <label>Answer <textarea name="answer[]"><?= manage_h($item['answer'] ?? '') ?></textarea></label>
          </div>
        <?php endforeach; ?>
        <button type="submit">Save FAQs</button>
      </form>
    </main>
  </div>
</body>
</html>
