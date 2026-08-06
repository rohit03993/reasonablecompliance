<?php
require __DIR__ . '/auth.php';
manage_require_login();
$site = manage_read_json('site.json');
$navCurrent = 'site';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Brand & Contact | Admin</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Brand & Contact</h1>
      <p class="help">Phone/WhatsApp power the call & WhatsApp buttons. WhatsApp format: 9198XXXXXXXX (country code, no +).</p>
      <form class="card" method="post" action="/rc-panel/save.php">
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
        <label>Footer short description <textarea name="footerBlurb"><?= manage_h($site['footerBlurb'] ?? '') ?></textarea></label>
        <label>SEO title <input name="seoDefaultTitle" type="text" value="<?= manage_h($site['seoDefaultTitle'] ?? '') ?>" /></label>
        <label>SEO description <textarea name="seoDefaultDescription"><?= manage_h($site['seoDefaultDescription'] ?? '') ?></textarea></label>
        <button type="submit">Save changes</button>
      </form>
    </main>
  </div>
</body>
</html>
