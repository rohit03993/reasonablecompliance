<?php
require __DIR__ . '/auth.php';
manage_require_login();
$msg = $_GET['saved'] ?? '';
$navCurrent = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Content Admin | Reasonable Compliance</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Dashboard</h1>
      <p class="help">Edit the same content visitors see on the website. If a section looks empty, use <strong>Repair / re-sync</strong> on that page.</p>
      <?php if ($msg === '1'): ?>
        <p class="success">Saved successfully. If the public site looks old, open <a href="/rc-panel/flush-cache.php">Flush cache</a> then hard-refresh (Ctrl+Shift+R).</p>
      <?php endif; ?>
      <div class="card">
        <h2>Content sections</h2>
        <ul>
          <li><a href="/rc-panel/edit-site.php">Brand & Contact</a></li>
          <li><a href="/rc-panel/edit-social.php">Social Links</a></li>
          <li><a href="/rc-panel/edit-homepage.php">Homepage</a></li>
          <li><a href="/rc-panel/edit-about.php">About</a></li>
          <li><a href="/rc-panel/edit-contact.php">Contact page</a></li>
          <li><a href="/rc-panel/edit-services.php">Services</a> — company registration, ROC, GST, etc.</li>
          <li><a href="/rc-panel/edit-blog.php">Blog</a></li>
          <li><a href="/rc-panel/edit-gallery.php">Gallery</a></li>
          <li><a href="/rc-panel/edit-testimonials.php">Testimonials</a></li>
          <li><a href="/rc-panel/edit-faqs.php">FAQs</a></li>
          <li><a href="/rc-panel/flush-cache.php">Flush cache</a> — use when public pages look outdated</li>
        </ul>
      </div>
      <p class="small">Login URL: <strong>/rc-panel/</strong> &nbsp;|&nbsp; Username: <strong>admin</strong></p>
    </main>
  </div>
</body>
</html>
