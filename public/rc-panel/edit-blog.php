<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'blog';

// Load posts BEFORE including nav (nav must never overwrite this)
$blog = manage_ensure_content('blog', 'blog.json');
$posts = manage_blog_valid_items($blog['items'] ?? []);
if (count($posts) === 0) {
    manage_restore_seed('blog', 'blog.json');
    $blog = manage_read_json('blog.json');
    $posts = manage_blog_valid_items($blog['items'] ?? []);
}
usort($posts, fn($a, $b) => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? '')));

$saved = ($_GET['saved'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$repaired = ($_GET['repaired'] ?? '') === '1';
$error = (string) ($_GET['error'] ?? '');
$dataPath = manage_data_dir() . DIRECTORY_SEPARATOR . 'blog.json';
$seedPath = manage_seed_file('blog');
$rawCount = is_array($blog['items'] ?? null) ? count($blog['items']) : 0;
$postTitles = [];
foreach ($posts as $p) {
    $t = trim((string) ($p['title'] ?? ''));
    if ($t !== '') {
        $postTitles[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Blog | Admin</title>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main blog-admin">
      <div class="page-head">
        <div>
          <h1>Blog</h1>
          <p class="help" style="margin:0">Same posts as the live website. Edit one at a time.</p>
        </div>
        <div class="actions">
          <a class="btn secondary" href="/rc-panel/flush-cache.php">Flush cache</a>
          <a class="btn" href="/rc-panel/edit-blog-post.php">+ New post</a>
        </div>
      </div>

      <?php if ($saved): ?><p class="success">Saved.</p><?php endif; ?>
      <?php if ($deleted): ?><p class="success">Post deleted.</p><?php endif; ?>
      <?php if ($repaired): ?><p class="success">Blog restored (<?= count($posts) ?> posts).</p><?php endif; ?>
      <?php if ($error === 'restore'): ?><p class="error">Restore failed — seed missing or data folder not writable.</p><?php endif; ?>

      <div class="card">
        <h2>Blog page settings</h2>
        <form method="post" action="/rc-panel/save.php">
          <input type="hidden" name="type" value="blog_settings" />
          <label>Section title <input name="title" type="text" value="<?= manage_h($blog['title'] ?? 'Our Blog') ?>" /></label>
          <label>Intro <textarea name="intro"><?= manage_h($blog['intro'] ?? '') ?></textarea></label>
          <button type="submit">Save settings</button>
        </form>
      </div>

      <div class="card">
        <div class="page-head" style="margin-bottom:14px">
          <h2 style="margin:0">All posts (<?= count($posts) ?>)</h2>
          <div class="actions">
            <a class="btn secondary" href="/blog" target="_blank" rel="noopener">View blog ↗</a>
            <form class="inline-form" method="post" action="/rc-panel/save.php">
              <input type="hidden" name="type" value="content_reload" />
              <input type="hidden" name="key" value="blog" />
              <button type="submit" class="btn secondary">Repair / re-sync</button>
            </form>
          </div>
        </div>
        <p class="small" style="margin-top:0">
          Data: <code><?= manage_h($dataPath) ?></code><br />
          Seed: <code><?= manage_h($seedPath) ?></code> <?= is_file($seedPath) ? '(found)' : '(MISSING)' ?><br />
          Rows in JSON: <?= (int) $rawCount ?> · Showing: <?= count($posts) ?><br />
          Titles: <?= manage_h(count($postTitles) ? implode(' · ', $postTitles) : '(none)') ?>
        </p>

        <?php if (count($posts) === 0): ?>
          <p class="error">No valid posts. Click <strong>Repair / re-sync</strong>.</p>
        <?php else: ?>
          <div class="table-wrap">
            <table class="posts-table">
              <thead>
                <tr>
                  <th>Cover</th>
                  <th>Post</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($posts as $post):
                  $slug = (string) ($post['slug'] ?? '');
                  $title = trim((string) ($post['title'] ?? ''));
                  $status = ($post['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
                  $image = trim((string) ($post['image'] ?? ''));
                ?>
                  <tr>
                    <td>
                      <div class="thumb">
                        <?php if ($image !== ''): ?>
                          <img src="<?= manage_h($image) ?>" alt="" width="72" height="54" />
                        <?php else: ?>
                          <span>No image</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <strong><?= manage_h($title) ?></strong>
                      <div class="small">/blog/<?= manage_h($slug) ?></div>
                    </td>
                    <td><span class="badge <?= $status === 'draft' ? 'badge-new' : '' ?>"><?= $status === 'draft' ? 'Draft' : 'Published' ?></span></td>
                    <td class="small"><?= manage_h($post['date'] ?? '') ?></td>
                    <td class="actions">
                      <a class="btn-link" href="/rc-panel/edit-blog-post.php?slug=<?= urlencode($slug) ?>">Edit</a>
                      <a class="btn-link" href="/blog/read?slug=<?= urlencode($slug) ?>" target="_blank" rel="noopener">View</a>
                      <form class="inline-form" method="post" action="/rc-panel/save.php" onsubmit="return confirm('Delete this post?');">
                        <input type="hidden" name="type" value="blog_delete" />
                        <input type="hidden" name="slug" value="<?= manage_h($slug) ?>" />
                        <button type="submit" class="btn-link danger">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
