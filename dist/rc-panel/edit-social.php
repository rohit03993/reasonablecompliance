<?php
require __DIR__ . '/auth.php';
manage_require_login();
$social = manage_read_json('social.json');
$navCurrent = 'social';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Social Links | Admin</title>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Social media links</h1>
      <p class="help">Icons always show in the footer. Paste a full URL to make that icon clickable; leave blank to keep it visible but not linked.</p>
      <form class="card" method="post" action="/rc-panel/save.php">
        <input type="hidden" name="type" value="social" />
        <label>Facebook URL <input name="facebook" type="url" value="<?= manage_h($social['facebook'] ?? '') ?>" placeholder="https://facebook.com/yourpage" /></label>
        <label>Instagram URL <input name="instagram" type="url" value="<?= manage_h($social['instagram'] ?? '') ?>" placeholder="https://instagram.com/yourpage" /></label>
        <label>LinkedIn URL <input name="linkedin" type="url" value="<?= manage_h($social['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/company/yourpage" /></label>
        <label>Twitter / X URL <input name="twitter" type="url" value="<?= manage_h($social['twitter'] ?? '') ?>" placeholder="https://x.com/yourpage" /></label>
        <button type="submit">Save social links</button>
      </form>
    </main>
  </div>
</body>
</html>
