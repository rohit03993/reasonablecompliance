<?php
$config = require __DIR__ . '/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($config['session_name']);
    session_start();
}

function manage_data_dir(): string
{
    // Always use the data folder next to rc-panel (public_html/data)
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
}

function manage_require_login(): void
{
    if (empty($_SESSION['rc_logged_in'])) {
        header('Location: /rc-panel/login.php');
        exit;
    }
}

function manage_decode_json_string(string $raw): array
{
    if ($raw === '') {
        return [];
    }
    if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
        $raw = substr($raw, 3);
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function manage_read_json(string $file): array
{
    $path = manage_data_dir() . DIRECTORY_SEPARATOR . $file;
    if (!is_file($path)) {
        return [];
    }
    $raw = (string) @file_get_contents($path);
    return manage_decode_json_string($raw);
}

function manage_write_json(string $file, array $data): bool
{
    $dir = manage_data_dir();
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $path = $dir . DIRECTORY_SEPARATOR . $file;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    $tmp = $path . '.tmp.' . getmypid();
    $written = @file_put_contents($tmp, $json . "\n", LOCK_EX);
    if ($written === false) {
        return false;
    }
    if (!@rename($tmp, $path)) {
        @unlink($path);
        $ok = @rename($tmp, $path);
        @unlink($tmp);
        if (!$ok) {
            return false;
        }
    }
    @chmod($path, 0644);
    return true;
}

function manage_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function manage_seed_file(string $key): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'seed' . DIRECTORY_SEPARATOR . $key . '.json';
}

function manage_seed(string $key): array
{
    $jsonPath = manage_seed_file($key);
    if (is_file($jsonPath)) {
        $raw = (string) @file_get_contents($jsonPath);
        $data = manage_decode_json_string($raw);
        if ($data !== []) {
            return $data;
        }
    }
    // Fallback to embedded PHP seed
    $phpPath = __DIR__ . DIRECTORY_SEPARATOR . 'seed-data.php';
    if (is_file($phpPath)) {
        $all = require $phpPath;
        if (is_array($all) && isset($all[$key]) && is_array($all[$key])) {
            return $all[$key];
        }
    }
    return [];
}

/** Copy packaged seed JSON over live data file. Returns true only if read-back looks valid. */
function manage_restore_seed(string $key, string $file): bool
{
    $seedPath = manage_seed_file($key);
    $dest = manage_data_dir() . DIRECTORY_SEPARATOR . $file;
    $seed = manage_seed($key);
    if ($seed === []) {
        return false;
    }

    // Prefer binary copy of JSON seed when available
    if (is_file($seedPath)) {
        $dir = manage_data_dir();
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!@copy($seedPath, $dest)) {
            // Fall back to encoded write
            if (!manage_write_json($file, $seed)) {
                return false;
            }
        } else {
            @chmod($dest, 0644);
        }
    } elseif (!manage_write_json($file, $seed)) {
        return false;
    }

    // Preserve web3forms key for site.json
    if ($key === 'site') {
        $live = manage_read_json('site.json');
        $seedSite = $seed;
        if (!empty($live['web3formsAccessKey'])) {
            $seedSite['web3formsAccessKey'] = $live['web3formsAccessKey'];
            manage_write_json('site.json', $seedSite);
        }
    }

    $verify = manage_read_json($file);
    if ($key === 'blog' || $key === 'services' || $key === 'faqs' || $key === 'testimonials') {
        $items = $verify['items'] ?? null;
        if (!is_array($items) || count($items) === 0) {
            return false;
        }
        // Ensure at least one item has a real title/question/quote
        $ok = false;
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            if ($key === 'blog' && trim((string) ($item['title'] ?? '')) !== '') {
                $ok = true;
                break;
            }
            if ($key === 'services' && trim((string) ($item['title'] ?? '')) !== '') {
                $ok = true;
                break;
            }
            if ($key === 'faqs' && trim((string) ($item['question'] ?? '')) !== '') {
                $ok = true;
                break;
            }
            if ($key === 'testimonials' && trim((string) ($item['quote'] ?? '')) !== '') {
                $ok = true;
                break;
            }
        }
        if (!$ok) {
            return false;
        }
    }

    manage_bump_cache();
    return true;
}

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
        if ($title === '') {
            continue; // slug-only junk
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

function manage_ensure_content(string $key, string $file): array
{
    $data = manage_read_json($file);
    $needsRestore = false;

    if ($key === 'blog') {
        $valid = manage_blog_valid_items($data['items'] ?? []);
        $rawCount = is_array($data['items'] ?? null) ? count($data['items']) : 0;
        if (count($valid) === 0 || ($rawCount > 0 && count($valid) === 0)) {
            $needsRestore = true;
        } elseif ($rawCount !== count($valid)) {
            // Strip junk empty rows and save cleaned version
            $data['items'] = $valid;
            manage_write_json($file, $data);
            return $data;
        }
    } elseif (in_array($key, ['services', 'faqs', 'testimonials'], true)) {
        $items = is_array($data['items'] ?? null) ? $data['items'] : [];
        $valid = 0;
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            if ($key === 'services' && trim((string) ($item['title'] ?? '')) !== '') {
                $valid++;
            }
            if ($key === 'faqs' && trim((string) ($item['question'] ?? '')) !== '') {
                $valid++;
            }
            if ($key === 'testimonials' && trim((string) ($item['quote'] ?? '')) !== '') {
                $valid++;
            }
        }
        if ($valid === 0) {
            $needsRestore = true;
        }
    }

    if ($needsRestore) {
        manage_restore_seed($key, $file);
        return manage_read_json($file);
    }

    return $data;
}

function manage_bump_cache(): string
{
    $version = (string) time();
    manage_write_json('cache-version.json', [
        'version' => $version,
        'updatedAt' => date('c'),
    ]);
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
    clearstatcache(true);
    return $version;
}

function manage_flush_cache(): array
{
    $notes = [];
    $version = manage_bump_cache();
    $notes[] = 'Cache version bumped to ' . $version;

    if (function_exists('opcache_reset')) {
        $notes[] = 'PHP OPcache cleared';
    } else {
        $notes[] = 'PHP OPcache not available on this host';
    }

    $dir = manage_data_dir();
    foreach (glob($dir . DIRECTORY_SEPARATOR . '*.json') ?: [] as $file) {
        @touch($file);
    }
    $notes[] = 'Touched data JSON files in ' . $dir;

    return ['version' => $version, 'notes' => $notes];
}
