<?php
require __DIR__ . '/auth.php';
manage_require_login();
$msg = $_GET['saved'] ?? '';
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
    <aside class="sidebar">
      <h2>Content Admin</h2>
      <nav>
        <a class="active" href="/rc-panel/">Dashboard</a>
        <a href="/rc-panel/edit-site.php">Brand & Contact</a>
        <a href="/rc-panel/edit-homepage.php">Homepage</a>
        <a href="/rc-panel/edit-about.php">About</a>
        <a href="/rc-panel/edit-contact.php">Contact page</a>
        <a href="/rc-panel/edit-services.php">Services</a>
        <a href="/rc-panel/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/rc-panel/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>Dashboard</h1>
      <p class="help">Edit website content on the live server. Changes show after you save and refresh the public page.</p>
      <?php if ($msg === '1'): ?>
        <p class="success">Saved successfully. Open the website and hard-refresh (Ctrl+Shift+R) to see updates.</p>
      <?php endif; ?>
      <div class="card">
        <h2>What you can edit</h2>
        <ul>
          <li><a href="/rc-panel/edit-site.php">Brand & Contact</a> — logo path, phone, WhatsApp, email, CTAs</li>
          <li><a href="/rc-panel/edit-homepage.php">Homepage</a> — hero, trust, industries, process</li>
          <li><a href="/rc-panel/edit-about.php">About</a></li>
          <li><a href="/rc-panel/edit-contact.php">Contact page</a></li>
          <li><a href="/rc-panel/edit-services.php">Services</a></li>
          <li><a href="/rc-panel/edit-faqs.php">FAQs</a></li>
        </ul>
      </div>
      <p class="small">Login URL: <strong>/rc-panel/</strong> &nbsp;|&nbsp; Username: <strong>admin</strong></p>
    </main>
  </div>
</body>
</html>
