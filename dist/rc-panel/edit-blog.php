<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'blog';

$rawBlog = manage_read_json('blog.json');
$rawCount = count($rawBlog['items'] ?? []);
$validItems = manage_blog_valid_items($rawBlog['items'] ?? []);
$emptyCount = $rawCount - count($validItems);

// Auto-clean junk empty rows so admin stays in sync with real posts
if ($emptyCount > 0) {
    $rawBlog['items'] = $validItems;
    $rawBlog = manage_blog_normalize_and_save($rawBlog);
}

$blog = $rawBlog;
$items = $blog['items'] ?? [];
usort($items, fn($a, $b) => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? '')));

$saved = ($_GET['saved'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$repaired = ($_GET['repaired'] ?? '') === '1';
$cleaned = $emptyCount > 0;
$dataPath = manage_data_dir() . '/blog.json';
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
          <p class="help" style="margin:0">Posts below are the same data the website uses. Edit one post at a time to keep everything in sync.</p>
        </div>
        <a class="btn" href="/rc-panel/edit-blog-post.php">+ New post</a>
      </div>

      <?php if ($saved): ?><p class="success">Saved successfully. Hard-refresh the public blog page to see updates.</p><?php endif; ?>
      <?php if ($deleted): ?><p class="success">Post deleted.</p><?php endif; ?>
      <?php if ($repaired): ?><p class="success">Blog posts restored and synced.</p><?php endif; ?>
      <?php if ($cleaned): ?><p class="success">Removed <?= (int) $emptyCount ?> empty/broken draft rows automatically.</p><?php endif; ?>

      <?php if (count($items) === 0): ?>
        <div class="card">
          <p class="error" style="margin-bottom:12px">No valid posts found in the live data file. Restore the starter posts to sync the website again.</p>
          <form method="post" action="/rc-panel/save.php">
            <input type="hidden" name="type" value="blog_repair" />
            <input type="hidden" name="force" value="1" />
            <button type="submit">Restore &amp; sync blog posts</button>
          </form>
        </div>
      <?php endif; ?>

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
          <div class="actions">
            <a class="btn secondary" href="/blog" target="_blank" rel="noopener">View blog ↗</a>
            <form class="inline-form" method="post" action="/rc-panel/save.php" onsubmit="return confirm('Restore packaged blog posts and sync the live website data?');">
              <input type="hidden" name="type" value="blog_repair" />
              <input type="hidden" name="force" value="1" />
              <button type="submit" class="btn secondary">Repair / re-sync</button>
            </form>
          </div>
        </div>

        <p class="small" style="margin-top:0">Data file: <code><?= manage_h($dataPath) ?></code></p>

        <?php if (count($items) === 0): ?>
          <p class="muted">No posts yet. Click <strong>New post</strong> or <strong>Repair / re-sync</strong>.</p>
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
                  $title = trim((string) ($item['title'] ?? ''));
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
                      <strong><?= manage_h($title !== '' ? $title : 'Untitled') ?></strong>
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
