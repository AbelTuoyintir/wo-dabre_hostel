<?php
// Simple image proxy for shared hosts that expect a physical image.php file
// Usage: /image.php?path=hostels/covers/filename.jpg

$path = trim($_GET['path'] ?? '');
if (empty($path) || str_contains($path, '..')) {
    http_response_code(404);
    exit('Not found');
}

$fullPath = __DIR__ . '/../storage/app/public/' . ltrim($path, '/');
if (!file_exists($fullPath)) {
    http_response_code(404);
    exit('Not found');
}

$mime = mime_content_type($fullPath) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($fullPath));
// Optional: caching headers
header('Cache-Control: public, max-age=31536000, immutable');
readfile($fullPath);
exit;
