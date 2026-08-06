<?php
$config = require __DIR__ . '/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($config['session_name']);
    session_start();
}

function manage_data_dir(): string
{
    return dirname(__DIR__) . '/data';
}

function manage_require_login(): void
{
    if (empty($_SESSION['rc_logged_in'])) {
        header('Location: /rc-panel/login.php');
        exit;
    }
}

function manage_read_json(string $file): array
{
    $path = manage_data_dir() . '/' . $file;
    if (!is_file($path)) {
        return [];
    }
    $raw = (string) file_get_contents($path);
    // Strip UTF-8 BOM if present
    if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
        $raw = substr($raw, 3);
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function manage_write_json(string $file, array $data): bool
{
    $path = manage_data_dir() . '/' . $file;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json . "\n") !== false;
}

function manage_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Keep only real blog posts (must have title or slug). */
function manage_blog_valid_items(array $items): array
{
    $out = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $title = trim((string) ($item['title'] ?? ''));
        $slug = trim((string) ($item['slug'] ?? ''));
        if ($title === '' && $slug === '') {
            continue;
        }
        if ($slug === '' && $title !== '') {
            $slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $title));
            $slug = trim($slug, '-');
            $item['slug'] = $slug;
        }
        if (!isset($item['status']) || $item['status'] === '') {
            $item['status'] = 'published';
        }
        $out[] = $item;
    }
    return array_values($out);
}

function manage_blog_normalize_and_save(array $blog): array
{
    $blog['title'] = trim((string) ($blog['title'] ?? 'Our Blog')) ?: 'Our Blog';
    $blog['intro'] = trim((string) ($blog['intro'] ?? ''));
    $blog['items'] = manage_blog_valid_items($blog['items'] ?? []);
    usort($blog['items'], fn($a, $b) => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? '')));
    manage_write_json('blog.json', $blog);
    return $blog;
}
