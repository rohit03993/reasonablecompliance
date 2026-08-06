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
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main">
      <h1>Dashboard</h1>
      <p class="help">Edit website content on the live server. Changes show after you save and refresh the public page.</p>
      <?php if ($msg === '1'): ?>
        <p class="success">Saved successfully. Open the website and hard-refresh (Ctrl+Shift+R) to see updates.</p>
      <?php endif; ?>
      <div class="card">
        <h2>What you can edit</h2>
        <ul>
          <li><a href="/rc-panel/edit-site.php">Brand & Contact</a></li>
          <li><a href="/rc-panel/edit-social.php">Social Links</a> — Facebook, Instagram, LinkedIn, Twitter</li>
          <li><a href="/rc-panel/edit-homepage.php">Homepage</a></li>
          <li><a href="/rc-panel/edit-about.php">About</a></li>
          <li><a href="/rc-panel/edit-contact.php">Contact page</a></li>
          <li><a href="/rc-panel/edit-services.php">Services</a></li>
          <li><a href="/rc-panel/edit-blog.php">Blog</a> — posts &amp; articles</li>
          <li><a href="/rc-panel/edit-gallery.php">Gallery</a> — photos only</li>
          <li><a href="/rc-panel/edit-testimonials.php">Testimonials</a></li>
          <li><a href="/rc-panel/edit-faqs.php">FAQs</a></li>
        </ul>
        <p class="help" style="margin-top:1rem">
          Almost all text you see on the website is editable here. Layout, colours and animations are fixed in the design.
          After saving, hard-refresh the public page (Ctrl+Shift+R). New blog posts show on the Blog list immediately;
          clean URLs like <code>/blog/my-post/</code> appear after the next site deploy.
        </p>
      </div>
      <p class="small">Login URL: <strong>/rc-panel/</strong> &nbsp;|&nbsp; Username: <strong>admin</strong></p>
    </main>
  </div>
</body>
</html>
