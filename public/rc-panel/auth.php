<?php
$config = require __DIR__ . '/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($config['session_name']);
    session_start();
}

function manage_data_dir(): string
{
    $candidates = [];
    $candidates[] = dirname(__DIR__) . '/data';
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $candidates[] = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/data';
    }
    foreach ($candidates as $dir) {
        if ($dir && is_dir($dir) && is_writable($dir)) {
            return $dir;
        }
    }
    foreach ($candidates as $dir) {
        if ($dir && is_dir($dir)) {
            return $dir;
        }
    }
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
    if ($raw === '') {
        return [];
    }
    if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
        $raw = substr($raw, 3);
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return [];
    }
    return $data;
}

function manage_write_json(string $file, array $data): bool
{
    $dir = manage_data_dir();
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $path = $dir . '/' . $file;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    $ok = file_put_contents($path, $json . "\n", LOCK_EX) !== false;
    @chmod($path, 0644);
    return $ok;
}

function manage_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function manage_seed_all(): array
{
    static $seed = null;
    if ($seed === null) {
        $path = __DIR__ . '/seed-data.php';
        $seed = is_file($path) ? require $path : [];
        if (!is_array($seed)) {
            $seed = [];
        }
    }
    return $seed;
}

function manage_seed(string $key): array
{
    $all = manage_seed_all();
    return is_array($all[$key] ?? null) ? $all[$key] : [];
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

/** If live JSON is empty/corrupt, restore packaged seed for that key. */
function manage_ensure_content(string $key, string $file): array
{
    $data = manage_read_json($file);
    $seed = manage_seed($key);
    if ($seed === []) {
        return $data;
    }

    $liveItems = $data['items'] ?? null;
    $seedItems = $seed['items'] ?? null;

    if (is_array($seedItems)) {
        $validLive = [];
        if ($key === 'blog') {
            $validLive = manage_blog_valid_items(is_array($liveItems) ? $liveItems : []);
        } elseif (is_array($liveItems)) {
            foreach ($liveItems as $item) {
                if (!is_array($item)) {
                    continue;
                }
                // services need title; faqs need question; testimonials need quote
                if ($key === 'services' && trim((string) ($item['title'] ?? '')) !== '') {
                    $validLive[] = $item;
                } elseif ($key === 'faqs' && trim((string) ($item['question'] ?? '')) !== '') {
                    $validLive[] = $item;
                } elseif ($key === 'testimonials' && trim((string) ($item['quote'] ?? '')) !== '') {
                    $validLive[] = $item;
                } elseif ($key === 'gallery' && (trim((string) ($item['title'] ?? '')) !== '' || trim((string) ($item['image'] ?? '')) !== '')) {
                    $validLive[] = $item;
                }
            }
        }

        if (count($validLive) === 0 && count($seedItems) > 0) {
            manage_write_json($file, $seed);
            manage_bump_cache();
            return $seed;
        }

        if ($key === 'blog' && count($validLive) > 0 && count($validLive) !== count(is_array($liveItems) ? $liveItems : [])) {
            $data['items'] = $validLive;
            manage_write_json($file, $data);
            return $data;
        }
    }

    return $data;
}

function manage_cache_version_path(): string
{
    return manage_data_dir() . '/cache-version.json';
}

function manage_bump_cache(): string
{
    $version = (string) time();
    $payload = [
        'version' => $version,
        'updatedAt' => date('c'),
    ];
    manage_write_json('cache-version.json', $payload);
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
    return $version;
}

function manage_flush_cache(): array
{
    $notes = [];
    $version = manage_bump_cache();
    $notes[] = 'Cache version bumped to ' . $version;

    if (function_exists('opcache_reset')) {
        $notes[] = opcache_reset() ? 'PHP OPcache cleared' : 'PHP OPcache reset attempted';
    } else {
        $notes[] = 'PHP OPcache not available on this host';
    }

    // Touch JSON files so CDNs/proxies see fresh mtime
    $dir = manage_data_dir();
    foreach (glob($dir . '/*.json') ?: [] as $file) {
        @touch($file);
    }
    $notes[] = 'Touched data JSON files';

    // LiteSpeed / common cache folders under public_html if present
    $root = dirname(manage_data_dir());
    foreach (['/cache', '/lscache', '/tmp/cache'] as $rel) {
        $path = $root . $rel;
        if (is_dir($path)) {
            $notes[] = 'Found cache folder: ' . $path . ' (clear via Hostinger if needed)';
        }
    }

    return ['version' => $version, 'notes' => $notes];
}
