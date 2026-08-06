<?php
require __DIR__ . '/auth.php';
manage_require_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$file = $_FILES['file'] ?? $_FILES['image'] ?? null;
if (!$file || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No image uploaded']);
    exit;
}

$maxBytes = 4 * 1024 * 1024; // 4MB
if (($file['size'] ?? 0) > $maxBytes) {
    http_response_code(400);
    echo json_encode(['error' => 'Image too large (max 4MB)']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']) ?: '';
$allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];
if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(['error' => 'Only JPG, PNG, WEBP or GIF allowed']);
    exit;
}

$uploadDir = dirname(__DIR__) . '/uploads/blog';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not create upload folder']);
    exit;
}

$base = preg_replace('/[^a-z0-9\-]+/i', '-', pathinfo((string) $file['name'], PATHINFO_FILENAME));
$base = trim((string) $base, '-') ?: 'blog-image';
$filename = strtolower($base) . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.' . $allowed[$mime];
$dest = $uploadDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed']);
    exit;
}

$url = '/uploads/blog/' . $filename;
echo json_encode(['location' => $url, 'url' => $url]);
