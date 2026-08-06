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
    $data = json_decode((string) file_get_contents($path), true);
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
