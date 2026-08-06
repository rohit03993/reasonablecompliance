<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'blog';
$blog = manage_read_json('blog.json');
$published = [];
foreach (($blog['items'] ?? []) as $item) {
    if (trim((string) ($item['title'] ?? '')) !== '') {
        $published[] = $item;
    }
}
$items = $published;
// One empty slot for adding a new post
$items[] = [
    'slug' => '',
    'title' => '',
    'excerpt' => '',
    'date' => date('Y-m-d'),
    'author' => 'Reasonable Compliance',
    'image' => '',
    'seoTitle' => '',
    'seoDescription' => '',
    'body' => '',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Blog | Admin</title>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
  <script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.0/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main blog-admin">
      <h1>Blog</h1>
      <p class="help">
        Edit published posts below, or fill the last empty card to add a new one.
        Use the editor toolbar for <strong>Bold</strong>, <em>Italic</em>, headings, lists, links and images.
        Leave a post title blank to delete it on save.
      </p>

      <?php if (count($published) === 0): ?>
        <p class="error">No published posts found in <code>data/blog.json</code> yet. Add a new post below, or deploy the latest site files so starter posts appear here.</p>
      <?php else: ?>
        <div class="card">
          <h2>Published posts (<?= count($published) ?>)</h2>
          <ul class="post-index">
            <?php foreach ($published as $i => $item): ?>
              <li>
                <a href="#post-<?= $i ?>"><?= manage_h($item['title']) ?></a>
                <span class="small"><?= manage_h($item['date'] ?? '') ?> · /blog/<?= manage_h($item['slug'] ?? '') ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="/rc-panel/save.php" id="blog-form">
        <input type="hidden" name="type" value="blog" />
        <input type="hidden" name="count" value="<?= count($items) ?>" />

        <div class="card">
          <h2>Blog page settings</h2>
          <label>Section title <input name="title" type="text" value="<?= manage_h($blog['title'] ?? 'Our Blog') ?>" /></label>
          <label>Intro <textarea name="intro"><?= manage_h($blog['intro'] ?? '') ?></textarea></label>
        </div>

        <?php foreach ($items as $i => $item):
          $isNew = trim((string) ($item['title'] ?? '')) === '';
          $heading = $isNew ? 'Add new post' : ('Edit: ' . ($item['title'] ?? ('Post ' . ($i + 1))));
        ?>
          <div class="card post-card" id="post-<?= $i ?>">
            <div class="post-card-head">
              <h2><?= manage_h($heading) ?></h2>
              <?php if (!$isNew): ?>
                <span class="badge">Published</span>
              <?php else: ?>
                <span class="badge badge-new">New</span>
              <?php endif; ?>
            </div>

            <div class="grid-2">
              <label>Title <input name="title_<?= $i ?>" type="text" value="<?= manage_h($item['title'] ?? '') ?>" placeholder="Post title" /></label>
              <label>Slug (URL) <input name="slug_<?= $i ?>" type="text" value="<?= manage_h($item['slug'] ?? '') ?>" placeholder="my-post-url" /></label>
            </div>
            <div class="grid-2">
              <label>Date (YYYY-MM-DD) <input name="date_<?= $i ?>" type="text" value="<?= manage_h($item['date'] ?? '') ?>" /></label>
              <label>Author <input name="author_<?= $i ?>" type="text" value="<?= manage_h($item['author'] ?? '') ?>" /></label>
            </div>

            <div class="image-field">
              <label>Cover image
                <input class="image-url-input" name="image_<?= $i ?>" id="image_<?= $i ?>" type="text" value="<?= manage_h($item['image'] ?? '') ?>" placeholder="/uploads/blog/photo.jpg" />
              </label>
              <div class="image-actions">
                <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="cover-file" data-target="image_<?= $i ?>" data-preview="preview_<?= $i ?>" />
                <button type="button" class="secondary cover-upload-btn" data-file="image_<?= $i ?>">Upload image</button>
              </div>
              <div class="image-preview-wrap">
                <?php if (!empty($item['image'])): ?>
                  <img id="preview_<?= $i ?>" class="image-preview" src="<?= manage_h($item['image']) ?>" alt="" />
                <?php else: ?>
                  <img id="preview_<?= $i ?>" class="image-preview" src="" alt="" hidden />
                <?php endif; ?>
              </div>
              <p class="small">Upload a JPG/PNG/WEBP (max 4MB), or paste an image path/URL.</p>
            </div>

            <label>Short excerpt <textarea name="excerpt_<?= $i ?>"><?= manage_h($item['excerpt'] ?? '') ?></textarea></label>

            <label>Article content
              <textarea class="rich-editor" name="body_<?= $i ?>" id="body_<?= $i ?>" rows="12"><?= manage_h($item['body'] ?? '') ?></textarea>
            </label>

            <label>SEO title <input name="seoTitle_<?= $i ?>" type="text" value="<?= manage_h($item['seoTitle'] ?? '') ?>" /></label>
            <label>SEO description <textarea name="seoDescription_<?= $i ?>"><?= manage_h($item['seoDescription'] ?? '') ?></textarea></label>
          </div>
        <?php endforeach; ?>

        <button type="submit">Save blog</button>
      </form>
    </main>
  </div>

  <script>
    function uploadImage(file) {
      var data = new FormData();
      data.append('file', file);
      return fetch('/rc-panel/upload.php', {
        method: 'POST',
        body: data,
        credentials: 'same-origin',
      }).then(function (res) {
        return res.json().then(function (json) {
          if (!res.ok) throw new Error(json.error || 'Upload failed');
          return json.location || json.url;
        });
      });
    }

    tinymce.init({
      selector: 'textarea.rich-editor',
      base_url: 'https://cdn.jsdelivr.net/npm/tinymce@7.6.0',
      suffix: '.min',
      height: 420,
      menubar: false,
      plugins: 'lists link image table code autoresize',
      toolbar:
        'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | removeformat | code',
      branding: false,
      convert_urls: false,
      content_style:
        'body { font-family: Segoe UI, system-ui, sans-serif; font-size: 15px; line-height: 1.6; color: #1e293b; }',
      images_upload_handler: function (blobInfo) {
        return uploadImage(blobInfo.blob());
      },
    });

    document.getElementById('blog-form').addEventListener('submit', function () {
      if (window.tinymce) tinymce.triggerSave();
    });

    document.querySelectorAll('.cover-upload-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var inputId = btn.getAttribute('data-file');
        var fileInput = document.querySelector('.cover-file[data-target="' + inputId + '"]');
        if (fileInput) fileInput.click();
      });
    });

    document.querySelectorAll('.cover-file').forEach(function (fileInput) {
      fileInput.addEventListener('change', function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        var targetId = fileInput.getAttribute('data-target');
        var previewId = fileInput.getAttribute('data-preview');
        var urlInput = document.getElementById(targetId);
        var preview = document.getElementById(previewId);
        uploadImage(file)
          .then(function (url) {
            if (urlInput) urlInput.value = url;
            if (preview) {
              preview.src = url;
              preview.hidden = false;
            }
          })
          .catch(function (err) {
            alert(err.message || 'Upload failed');
          });
      });
    });

    document.querySelectorAll('.image-url-input').forEach(function (input) {
      input.addEventListener('change', function () {
        var preview = input.closest('.image-field').querySelector('.image-preview');
        if (!preview) return;
        if (input.value) {
          preview.src = input.value;
          preview.hidden = false;
        } else {
          preview.hidden = true;
        }
      });
    });
  </script>
</body>
</html>
