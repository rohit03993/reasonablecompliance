<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'blog';
$blog = manage_read_json('blog.json');
$items = $blog['items'] ?? [];
$target = max(4, count($items) + 1);
while (count($items) < $target) {
    $items[] = [
        'slug' => '',
        'title' => '',
        'excerpt' => '',
        'date' => date('Y-m-d'),
        'author' => 'Reasonable Compliance',
        'seoTitle' => '',
        'seoDescription' => '',
        'body' => '',
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Blog | Admin</title>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Blog</h1>
      <p class="help">
        Edit or add posts below. Leave a post title blank to remove it.
        Body: separate paragraphs with a blank line. Use lines starting with <code>- </code> for bullet lists.
        New posts appear on the Blog page after save (hard-refresh the site).
      </p>
      <form method="post" action="/rc-panel/save.php">
        <input type="hidden" name="type" value="blog" />
        <input type="hidden" name="count" value="<?= count($items) ?>" />
        <div class="card">
          <label>Section title <input name="title" type="text" value="<?= manage_h($blog['title'] ?? 'Our Blog') ?>" /></label>
          <label>Intro <textarea name="intro"><?= manage_h($blog['intro'] ?? '') ?></textarea></label>
        </div>
        <?php foreach ($items as $i => $item): ?>
          <div class="card">
            <h2>Post <?= $i + 1 ?></h2>
            <div class="grid-2">
              <label>Title <input name="title_<?= $i ?>" type="text" value="<?= manage_h($item['title'] ?? '') ?>" /></label>
              <label>Slug (URL) <input name="slug_<?= $i ?>" type="text" value="<?= manage_h($item['slug'] ?? '') ?>" placeholder="my-post-url" /></label>
            </div>
            <div class="grid-2">
              <label>Date (YYYY-MM-DD) <input name="date_<?= $i ?>" type="text" value="<?= manage_h($item['date'] ?? '') ?>" /></label>
              <label>Author <input name="author_<?= $i ?>" type="text" value="<?= manage_h($item['author'] ?? '') ?>" /></label>
            </div>
            <label>Excerpt <textarea name="excerpt_<?= $i ?>"><?= manage_h($item['excerpt'] ?? '') ?></textarea></label>
            <label>Full article body <textarea name="body_<?= $i ?>" rows="10"><?= manage_h($item['body'] ?? '') ?></textarea></label>
            <label>SEO title <input name="seoTitle_<?= $i ?>" type="text" value="<?= manage_h($item['seoTitle'] ?? '') ?>" /></label>
            <label>SEO description <textarea name="seoDescription_<?= $i ?>"><?= manage_h($item['seoDescription'] ?? '') ?></textarea></label>
          </div>
        <?php endforeach; ?>
        <button type="submit">Save blog</button>
      </form>
    </main>
  </div>
</body>
</html>
