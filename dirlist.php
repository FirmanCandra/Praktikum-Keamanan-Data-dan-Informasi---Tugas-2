<?php
header('Content-Type: application/json; charset=utf-8');

$allowed = [
    'praktikum_sqlinjection-main',
    'praktikum_xss-main',
    'demo-app',
    'broken-access-control-main'
];

$rel = isset($_GET['path']) ? trim(str_replace(['..', '\\'], ['', '/'], $_GET['path']), '/') : '';
if (!in_array($rel, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Folder tidak diizinkan']);
    exit;
}

$dir = __DIR__ . DIRECTORY_SEPARATOR . $rel;
if (!is_dir($dir)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Folder tidak ditemukan']);
    exit;
}

$items = [];
foreach (scandir($dir) as $f) {
    if ($f === '.' || $f === '..') continue;
    $full = $dir . DIRECTORY_SEPARATOR . $f;
    $isDir = is_dir($full);
    $items[] = [
        'name' => $f . ($isDir ? '/' : ''),
        'is_dir' => $isDir,
        'href' => $rel . '/' . $f
    ];
}

echo json_encode(['ok' => true, 'path' => $rel . '/', 'items' => $items], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
