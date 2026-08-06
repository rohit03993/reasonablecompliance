<?php
require __DIR__ . '/auth.php';
manage_require_login();
$about = manage_read_json('about.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>About | Admin</title>
  <link rel="stylesheet" href="/manage/manage.css" />
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <h2>Content Admin</h2>
      <nav>
        <a href="/manage/">Dashboard</a>
        <a href="/manage/edit-site.php">Brand & Contact</a>
        <a href="/manage/edit-homepage.php">Homepage</a>
        <a class="active" href="/manage/edit-about.php">About</a>
        <a href="/manage/edit-contact.php">Contact page</a>
        <a href="/manage/edit-services.php">Services</a>
        <a href="/manage/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/manage/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>About page</h1>
      <form class="card" method="post" action="/manage/save.php">
        <input type="hidden" name="type" value="about" />
        <label>Title <input name="title" type="text" value="<?= manage_h($about['title'] ?? '') ?>" /></label>
        <label>SEO title <input name="seoTitle" type="text" value="<?= manage_h($about['seoTitle'] ?? '') ?>" /></label>
        <label>SEO description <textarea name="seoDescription"><?= manage_h($about['seoDescription'] ?? '') ?></textarea></label>
        <label>Paragraphs (one paragraph per line) <textarea name="paragraphs" style="min-height:160px"><?= manage_h(implode("\n", $about['paragraphs'] ?? [])) ?></textarea></label>
        <label>Why choose title <input name="whyChooseTitle" type="text" value="<?= manage_h($about['whyChooseTitle'] ?? '') ?>" /></label>
        <label>Why choose points (one per line) <textarea name="whyChooseBullets"><?= manage_h(implode("\n", $about['whyChooseBullets'] ?? [])) ?></textarea></label>
        <button type="submit">Save about</button>
      </form>
    </main>
  </div>
</body>
</html>
