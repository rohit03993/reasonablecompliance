<?php
$navCurrent = $navCurrent ?? '';
$items = [
    '' => ['Dashboard', '/rc-panel/'],
    'site' => ['Brand & Contact', '/rc-panel/edit-site.php'],
    'social' => ['Social Links', '/rc-panel/edit-social.php'],
    'homepage' => ['Homepage', '/rc-panel/edit-homepage.php'],
    'about' => ['About', '/rc-panel/edit-about.php'],
    'contact' => ['Contact page', '/rc-panel/edit-contact.php'],
    'services' => ['Services', '/rc-panel/edit-services.php'],
    'blog' => ['Blog', '/rc-panel/edit-blog.php'],
    'gallery' => ['Gallery', '/rc-panel/edit-gallery.php'],
    'testimonials' => ['Testimonials', '/rc-panel/edit-testimonials.php'],
    'faqs' => ['FAQs', '/rc-panel/edit-faqs.php'],
    'cache' => ['Flush cache', '/rc-panel/flush-cache.php'],
];
?>
<aside class="sidebar">
  <h2>Content Admin</h2>
  <nav>
    <?php foreach ($items as $key => [$label, $href]): ?>
      <a class="<?= $navCurrent === $key ? 'active' : '' ?>" href="<?= manage_h($href) ?>"><?= manage_h($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <a class="logout" href="/rc-panel/logout.php">Log out</a>
</aside>
