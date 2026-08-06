<?php
require __DIR__ . '/auth.php';
manage_require_login();
$site = manage_read_json('site.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Brand & Contact | Admin</title>
  <link rel="stylesheet" href="/manage/manage.css" />
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <h2>Content Admin</h2>
      <nav>
        <a href="/manage/">Dashboard</a>
        <a class="active" href="/manage/edit-site.php">Brand & Contact</a>
        <a href="/manage/edit-homepage.php">Homepage</a>
        <a href="/manage/edit-about.php">About</a>
        <a href="/manage/edit-contact.php">Contact page</a>
        <a href="/manage/edit-services.php">Services</a>
        <a href="/manage/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/manage/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>Brand & Contact</h1>
      <p class="help">Phone/WhatsApp power the call & WhatsApp buttons. WhatsApp format: 9198XXXXXXXX (country code, no +).</p>
      <form class="card" method="post" action="/manage/save.php">
        <input type="hidden" name="type" value="site" />
        <label>Site name <input name="siteName" type="text" value="<?= manage_h($site['siteName'] ?? '') ?>" required /></label>
        <label>Tagline <input name="tagline" type="text" value="<?= manage_h($site['tagline'] ?? '') ?>" /></label>
        <label>Logo URL <input name="logo" type="text" value="<?= manage_h($site['logo'] ?? '') ?>" placeholder="/images/logo.png" /></label>
        <div class="grid-2">
          <label>Email <input name="email" type="email" value="<?= manage_h($site['email'] ?? '') ?>" /></label>
          <label>Phone <input name="phone" type="text" value="<?= manage_h($site['phone'] ?? '') ?>" /></label>
        </div>
        <label>WhatsApp number <input name="whatsapp" type="text" value="<?= manage_h($site['whatsapp'] ?? '') ?>" /></label>
        <div class="grid-2">
          <label>Primary button <input name="ctaPrimary" type="text" value="<?= manage_h($site['ctaPrimary'] ?? '') ?>" /></label>
          <label>Secondary button <input name="ctaSecondary" type="text" value="<?= manage_h($site['ctaSecondary'] ?? '') ?>" /></label>
        </div>
        <label>SEO title <input name="seoDefaultTitle" type="text" value="<?= manage_h($site['seoDefaultTitle'] ?? '') ?>" /></label>
        <label>SEO description <textarea name="seoDefaultDescription"><?= manage_h($site['seoDefaultDescription'] ?? '') ?></textarea></label>
        <button type="submit">Save changes</button>
      </form>
    </main>
  </div>
</body>
</html>
