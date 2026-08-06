<?php
require __DIR__ . '/auth.php';
manage_require_login();
$h = manage_read_json('homepage.json');
$hero = $h['hero'] ?? [];
$steps = $h['processSteps'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Homepage | Admin</title>
  <link rel="stylesheet" href="/manage/manage.css" />
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <h2>Content Admin</h2>
      <nav>
        <a href="/manage/">Dashboard</a>
        <a href="/manage/edit-site.php">Brand & Contact</a>
        <a class="active" href="/manage/edit-homepage.php">Homepage</a>
        <a href="/manage/edit-about.php">About</a>
        <a href="/manage/edit-contact.php">Contact page</a>
        <a href="/manage/edit-services.php">Services</a>
        <a href="/manage/edit-faqs.php">FAQs</a>
      </nav>
      <a class="logout" href="/manage/logout.php">Log out</a>
    </aside>
    <main class="main">
      <h1>Homepage</h1>
      <p class="help">For list fields, put one item per line.</p>
      <form method="post" action="/manage/save.php">
        <input type="hidden" name="type" value="homepage" />
        <div class="card">
          <h2>Hero</h2>
          <label>Headline <input name="headline" type="text" value="<?= manage_h($hero['headline'] ?? '') ?>" /></label>
          <label>Subheadline <textarea name="subheadline"><?= manage_h($hero['subheadline'] ?? '') ?></textarea></label>
          <label>Intro <textarea name="intro"><?= manage_h($hero['intro'] ?? '') ?></textarea></label>
        </div>
        <div class="card">
          <h2>Trust & Why us</h2>
          <label>Trust title <input name="trustTitle" type="text" value="<?= manage_h($h['trustTitle'] ?? '') ?>" /></label>
          <label>Trust points (one per line) <textarea name="trustBullets"><?= manage_h(implode("\n", $h['trustBullets'] ?? [])) ?></textarea></label>
          <label>Why choose title <input name="whyChooseTitle" type="text" value="<?= manage_h($h['whyChooseTitle'] ?? '') ?>" /></label>
          <label>Why choose points (one per line) <textarea name="whyChooseBullets"><?= manage_h(implode("\n", $h['whyChooseBullets'] ?? [])) ?></textarea></label>
        </div>
        <div class="card">
          <h2>Industries</h2>
          <label>Title <input name="industriesTitle" type="text" value="<?= manage_h($h['industriesTitle'] ?? '') ?>" /></label>
          <label>Intro <input name="industriesIntro" type="text" value="<?= manage_h($h['industriesIntro'] ?? '') ?>" /></label>
          <label>Industries (one per line) <textarea name="industries"><?= manage_h(implode("\n", $h['industries'] ?? [])) ?></textarea></label>
        </div>
        <div class="card">
          <h2>Process</h2>
          <label>Process title <input name="processTitle" type="text" value="<?= manage_h($h['processTitle'] ?? '') ?>" /></label>
          <?php for ($i = 0; $i < 4; $i++): $step = $steps[$i] ?? ['title' => '', 'description' => '']; ?>
            <div class="list-block">
              <label>Step <?= $i + 1 ?> title <input name="step_title[]" type="text" value="<?= manage_h($step['title'] ?? '') ?>" /></label>
              <label>Step <?= $i + 1 ?> description <textarea name="step_description[]"><?= manage_h($step['description'] ?? '') ?></textarea></label>
            </div>
          <?php endfor; ?>
        </div>
        <div class="card">
          <h2>Final CTA</h2>
          <label>Headline <input name="finalHeadline" type="text" value="<?= manage_h($h['finalCta']['headline'] ?? '') ?>" /></label>
          <label>Text <textarea name="finalText"><?= manage_h($h['finalCta']['text'] ?? '') ?></textarea></label>
        </div>
        <button type="submit">Save homepage</button>
      </form>
    </main>
  </div>
</body>
</html>
