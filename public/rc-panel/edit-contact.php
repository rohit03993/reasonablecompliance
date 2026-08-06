<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'contact';
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
    <?php require __DIR__ . '/partials/nav.php'; ?>
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
