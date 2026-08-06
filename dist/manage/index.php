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
  <link rel="stylesheet" href="/manage/manage.css" />
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <h2>Content Admin</h2>
      <nav>
        <a class="active" href="/manage/">Dashboard</a>
        <a href="/manage/edit-site.php">Brand & Contact</a>
        <a href="/manage/edit-homepage.php">Homepage</a>
        <a href="/manage/edit-about.php">About</a>
        <a href="/manage/edit-contact.php">Contact page</a>
        <a href="/manage/edit-services.php">Services</a>
        <a href="/manage/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/manage/logout.php">Log out</a>
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
          <li><a href="/manage/edit-site.php">Brand & Contact</a> — logo path, phone, WhatsApp, email, CTAs</li>
          <li><a href="/manage/edit-homepage.php">Homepage</a> — hero, trust, industries, process</li>
          <li><a href="/manage/edit-about.php">About</a></li>
          <li><a href="/manage/edit-contact.php">Contact page</a></li>
          <li><a href="/manage/edit-services.php">Services</a></li>
          <li><a href="/manage/edit-faqs.php">FAQs</a></li>
        </ul>
      </div>
      <p class="small">Login URL: <strong>/manage/</strong> &nbsp;|&nbsp; Username: <strong>admin</strong></p>
    </main>
  </div>
</body>
</html>
