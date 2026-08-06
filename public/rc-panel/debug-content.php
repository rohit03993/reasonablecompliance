<?php
require __DIR__ . '/auth.php';
manage_require_login();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$keys = ['blog', 'services', 'faqs', 'testimonials'];
$out = [
    'dataDir' => manage_data_dir(),
    'dataDirWritable' => is_writable(manage_data_dir()),
    'files' => [],
];

foreach ($keys as $key) {
    $file = $key . '.json';
    $path = manage_data_dir() . DIRECTORY_SEPARATOR . $file;
    $seed = manage_seed_file($key);
    $data = manage_read_json($file);
    $items = is_array($data['items'] ?? null) ? $data['items'] : [];
    $sample = null;
    foreach ($items as $item) {
        if (is_array($item)) {
            $sample = [
                'title' => $item['title'] ?? null,
                'slug' => $item['slug'] ?? null,
                'question' => $item['question'] ?? null,
                'quote' => isset($item['quote']) ? substr((string) $item['quote'], 0, 40) : null,
            ];
            break;
        }
    }
    $out['files'][$key] = [
        'path' => $path,
        'exists' => is_file($path),
        'bytes' => is_file($path) ? filesize($path) : 0,
        'seedExists' => is_file($seed),
        'itemCount' => count($items),
        'validBlogCount' => $key === 'blog' ? count(manage_blog_valid_items($items)) : null,
        'firstItem' => $sample,
    ];
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
