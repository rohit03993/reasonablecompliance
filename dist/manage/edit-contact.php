<?php
require __DIR__ . '/auth.php';
manage_require_login();
$contact = manage_read_json('contact.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Contact | Admin</title>
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
        <a href="/manage/edit-about.php">About</a>
        <a class="active" href="/manage/edit-contact.php">Contact page</a>
        <a href="/manage/edit-services.php">Services</a>
        <a href="/manage/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/manage/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>Contact page</h1>
      <form class="card" method="post" action="/manage/save.php">
        <input type="hidden" name="type" value="contact" />
        <label>Headline <input name="headline" type="text" value="<?= manage_h($contact['headline'] ?? '') ?>" /></label>
        <label>Supporting text <textarea name="supportingText"><?= manage_h($contact['supportingText'] ?? '') ?></textarea></label>
        <label>Form success message <input name="formSuccessMessage" type="text" value="<?= manage_h($contact['formSuccessMessage'] ?? '') ?>" /></label>
        <label>SEO title <input name="seoTitle" type="text" value="<?= manage_h($contact['seoTitle'] ?? '') ?>" /></label>
        <label>SEO description <textarea name="seoDescription"><?= manage_h($contact['seoDescription'] ?? '') ?></textarea></label>
        <button type="submit">Save contact</button>
      </form>
    </main>
  </div>
</body>
</html>
