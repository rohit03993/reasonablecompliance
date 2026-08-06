<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'blog';
$blog = manage_read_json('blog.json');
$items = $blog['items'] ?? [];
usort($items, fn($a, $b) => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? '')));
$saved = ($_GET['saved'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
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
          <p class="help" style="margin:0">Manage posts like a normal CMS — edit one at a time, upload covers, format with Bold/Italic.</p>
        </div>
        <a class="btn" href="/rc-panel/edit-blog-post.php">+ New post</a>
      </div>

      <?php if ($saved): ?><p class="success">Saved successfully.</p><?php endif; ?>
      <?php if ($deleted): ?><p class="success">Post deleted.</p><?php endif; ?>

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
          <h2 style="margin:0">All posts (<?= count($items) ?>)</h2>
          <a class="btn secondary" href="/blog" target="_blank" rel="noopener">View blog ↗</a>
        </div>

        <?php if (count($items) === 0): ?>
          <p class="muted">No posts yet. Click <strong>New post</strong> to create your first article.</p>
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
                <?php foreach ($items as $item):
                  $slug = (string) ($item['slug'] ?? '');
                  $status = ($item['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
                  $image = trim((string) ($item['image'] ?? ''));
                ?>
                  <tr>
                    <td>
                      <div class="thumb">
                        <?php if ($image !== ''): ?>
                          <img src="<?= manage_h($image) ?>" alt="" />
                        <?php else: ?>
                          <span>No image</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <strong><?= manage_h($item['title'] ?? 'Untitled') ?></strong>
                      <div class="small">/blog/<?= manage_h($slug) ?></div>
                    </td>
                    <td>
                      <span class="badge <?= $status === 'draft' ? 'badge-new' : '' ?>">
                        <?= $status === 'draft' ? 'Draft' : 'Published' ?>
                      </span>
                    </td>
                    <td class="small"><?= manage_h($item['date'] ?? '') ?></td>
                    <td class="actions">
                      <a class="btn-link" href="/rc-panel/edit-blog-post.php?slug=<?= urlencode($slug) ?>">Edit</a>
                      <a class="btn-link" href="/blog/read?slug=<?= urlencode($slug) ?>" target="_blank" rel="noopener">View</a>
                      <form class="inline-form" method="post" action="/rc-panel/save.php" onsubmit="return confirm('Delete this post permanently?');">
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
