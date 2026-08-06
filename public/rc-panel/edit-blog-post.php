<?php
require __DIR__ . '/auth.php';
manage_require_login();
$navCurrent = 'blog';
$blog = manage_read_json('blog.json');
$items = $blog['items'] ?? [];
$editSlug = trim((string) ($_GET['slug'] ?? ''));
$isNew = $editSlug === '';
$post = [
    'slug' => '',
    'title' => '',
    'excerpt' => '',
    'date' => date('Y-m-d'),
    'author' => 'Reasonable Compliance',
    'image' => '',
    'status' => 'published',
    'seoTitle' => '',
    'seoDescription' => '',
    'body' => '',
];
$originalSlug = '';

if (!$isNew) {
    foreach ($items as $item) {
        if (($item['slug'] ?? '') === $editSlug) {
            $post = array_merge($post, $item);
            $originalSlug = $editSlug;
            break;
        }
    }
    if ($originalSlug === '') {
        header('Location: /rc-panel/edit-blog.php');
        exit;
    }
}

$pageTitle = $isNew ? 'New post' : 'Edit post';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title><?= manage_h($pageTitle) ?> | Admin</title>
  <?php require __DIR__ . '/partials/favicon.php'; ?>
  <link rel="stylesheet" href="/rc-panel/rc-panel.css" />
  <script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.0/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
  <div class="layout">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <main class="main blog-admin">
      <div class="page-head">
        <div>
          <p class="crumb"><a href="/rc-panel/edit-blog.php">← All posts</a></p>
          <h1><?= manage_h($pageTitle) ?></h1>
        </div>
        <?php if (!$isNew): ?>
          <a class="btn secondary" href="/blog/read?slug=<?= urlencode($originalSlug) ?>" target="_blank" rel="noopener">Preview ↗</a>
        <?php endif; ?>
      </div>

      <form method="post" action="/rc-panel/save.php" id="post-form">
        <input type="hidden" name="type" value="blog_post" />
        <input type="hidden" name="original_slug" value="<?= manage_h($originalSlug) ?>" />

        <div class="editor-grid">
          <div class="editor-main">
            <div class="card">
              <label>Title
                <input id="post-title" name="title" type="text" value="<?= manage_h($post['title'] ?? '') ?>" placeholder="Write a clear, searchable title" required />
              </label>
              <label>Permalink
                <div class="slug-row">
                  <span class="slug-prefix">/blog/</span>
                  <input id="post-slug" name="slug" type="text" value="<?= manage_h($post['slug'] ?? '') ?>" placeholder="auto-from-title" />
                </div>
              </label>
              <label>Short excerpt
                <textarea name="excerpt" placeholder="1–2 lines shown on the blog cards"><?= manage_h($post['excerpt'] ?? '') ?></textarea>
              </label>
              <label>Article content
                <textarea class="rich-editor" name="body" id="post-body"><?= manage_h($post['body'] ?? '') ?></textarea>
              </label>
            </div>

            <div class="card">
              <h2>SEO</h2>
              <label>SEO title <input name="seoTitle" type="text" value="<?= manage_h($post['seoTitle'] ?? '') ?>" placeholder="Defaults to post title" /></label>
              <label>SEO description <textarea name="seoDescription" placeholder="Shown in Google search results"><?= manage_h($post['seoDescription'] ?? '') ?></textarea></label>
            </div>
          </div>

          <aside class="editor-side">
            <div class="card sticky-card">
              <h2>Publish</h2>
              <label>Status
                <select name="status">
                  <option value="published" <?= ($post['status'] ?? 'published') !== 'draft' ? 'selected' : '' ?>>Published</option>
                  <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft (hidden on site)</option>
                </select>
              </label>
              <label>Publish date
                <input name="date" type="date" value="<?= manage_h($post['date'] ?? date('Y-m-d')) ?>" />
              </label>
              <label>Author
                <input name="author" type="text" value="<?= manage_h($post['author'] ?? 'Reasonable Compliance') ?>" />
              </label>
              <button type="submit" class="btn-block"><?= $isNew ? 'Publish post' : 'Update post' ?></button>
              <a class="btn secondary btn-block" href="/rc-panel/edit-blog.php">Cancel</a>
            </div>

            <div class="card">
              <h2>Cover image</h2>
              <div id="cover-drop" class="dropzone <?= trim((string) ($post['image'] ?? '')) !== '' ? 'has-image' : '' ?>">
                <img id="cover-preview" src="<?= manage_h($post['image'] ?? '') ?>" alt="" <?= trim((string) ($post['image'] ?? '')) === '' ? 'hidden' : '' ?> />
                <div id="cover-empty" class="dropzone-empty" <?= trim((string) ($post['image'] ?? '')) !== '' ? 'hidden' : '' ?>>
                  <strong>Drop image here</strong>
                  <span>or click to upload (JPG/PNG/WEBP, max 4MB)</span>
                </div>
                <input id="cover-file" type="file" accept="image/jpeg,image/png,image/webp,image/gif" hidden />
              </div>
              <input id="cover-url" name="image" type="hidden" value="<?= manage_h($post['image'] ?? '') ?>" />
              <div class="image-actions" style="margin-top:10px">
                <button type="button" class="secondary" id="cover-pick">Choose image</button>
                <button type="button" class="btn-link danger" id="cover-remove" <?= trim((string) ($post['image'] ?? '')) === '' ? 'hidden' : '' ?>>Remove</button>
              </div>
            </div>
          </aside>
        </div>
      </form>
    </main>
  </div>

  <script>
    (function () {
      var titleInput = document.getElementById('post-title');
      var slugInput = document.getElementById('post-slug');
      var slugTouched = <?= $isNew ? 'false' : 'true' ?>;
      var coverDrop = document.getElementById('cover-drop');
      var coverFile = document.getElementById('cover-file');
      var coverUrl = document.getElementById('cover-url');
      var coverPreview = document.getElementById('cover-preview');
      var coverEmpty = document.getElementById('cover-empty');
      var coverRemove = document.getElementById('cover-remove');

      function slugify(text) {
        return String(text || '')
          .toLowerCase()
          .normalize('NFKD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/^-+|-+$/g, '')
          .slice(0, 80);
      }

      if (titleInput && slugInput) {
        slugInput.addEventListener('input', function () { slugTouched = true; });
        titleInput.addEventListener('input', function () {
          if (!slugTouched) slugInput.value = slugify(titleInput.value);
        });
      }

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

      function setCover(url) {
        coverUrl.value = url || '';
        if (url) {
          coverPreview.src = url;
          coverPreview.hidden = false;
          coverEmpty.hidden = true;
          coverDrop.classList.add('has-image');
          coverRemove.hidden = false;
        } else {
          coverPreview.removeAttribute('src');
          coverPreview.hidden = true;
          coverEmpty.hidden = false;
          coverDrop.classList.remove('has-image');
          coverRemove.hidden = true;
        }
      }

      function handleFile(file) {
        if (!file) return;
        uploadImage(file)
          .then(setCover)
          .catch(function (err) { alert(err.message || 'Upload failed'); });
      }

      document.getElementById('cover-pick').addEventListener('click', function () { coverFile.click(); });
      coverDrop.addEventListener('click', function () { coverFile.click(); });
      coverFile.addEventListener('change', function () { handleFile(coverFile.files && coverFile.files[0]); });
      coverRemove.addEventListener('click', function (e) {
        e.stopPropagation();
        setCover('');
      });

      ;['dragenter', 'dragover'].forEach(function (evt) {
        coverDrop.addEventListener(evt, function (e) {
          e.preventDefault();
          coverDrop.classList.add('dragover');
        });
      });
      ;['dragleave', 'drop'].forEach(function (evt) {
        coverDrop.addEventListener(evt, function (e) {
          e.preventDefault();
          coverDrop.classList.remove('dragover');
        });
      });
      coverDrop.addEventListener('drop', function (e) {
        var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
        handleFile(file);
      });

      tinymce.init({
        selector: '#post-body',
        base_url: 'https://cdn.jsdelivr.net/npm/tinymce@7.6.0',
        suffix: '.min',
        height: 480,
        menubar: false,
        plugins: 'lists link image table code autoresize',
        toolbar:
          'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | removeformat | code',
        branding: false,
        convert_urls: false,
        content_style:
          'body { font-family: Segoe UI, system-ui, sans-serif; font-size: 15px; line-height: 1.65; color: #1e293b; } img { max-width: 100%; height: auto; border-radius: 8px; }',
        images_upload_handler: function (blobInfo) {
          return uploadImage(blobInfo.blob());
        },
      });

      document.getElementById('post-form').addEventListener('submit', function () {
        if (window.tinymce) tinymce.triggerSave();
        if (!slugInput.value.trim() && titleInput.value.trim()) {
          slugInput.value = slugify(titleInput.value);
        }
      });
    })();
  </script>
</body>
</html>
