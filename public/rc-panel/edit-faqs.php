<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'faqs';
$faqs = manage_ensure_content('faqs', 'faqs.json');
$items = [];
foreach (($faqs['items'] ?? []) as $item) {
    if (is_array($item) && trim((string) ($item['question'] ?? '')) !== '') {
        $items[] = $item;
    }
}
usort($items, fn($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));
while (count($items) < 4) {
    $items[] = ['question' => '', 'answer' => '', 'order' => count($items) + 1];
}
$saved = ($_GET['saved'] ?? '') === '1';
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
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <div class="page-head">
        <div>
          <h1>FAQs</h1>
          <p class="help" style="margin:0">Same FAQs shown on the homepage.</p>
        </div>
        <form class="inline-form" method="post" action="/rc-panel/save.php" onsubmit="return confirm('Reload FAQs from packaged website content?');">
          <input type="hidden" name="type" value="content_reload" />
          <input type="hidden" name="key" value="faqs" />
          <button type="submit" class="btn secondary">Repair / re-sync</button>
        </form>
      </div>

      <?php if ($saved): ?><p class="success">FAQs saved.</p><?php endif; ?>

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
