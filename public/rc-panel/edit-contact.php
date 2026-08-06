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
        <a class="active" href="/rc-panel/edit-contact.php">Contact page</a>
        <a href="/rc-panel/edit-services.php">Services</a>
        <a href="/rc-panel/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/rc-panel/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>Contact page</h1>
      <form class="card" method="post" action="/rc-panel/save.php">
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
