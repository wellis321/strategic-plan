<?php
// Router for PHP built-in server
// This ensures static files are served correctly

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove query string
$path = strtok($path, '?');

// If it's a static file (CSS, JS, images, fonts), serve it directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $path)) {
    // Build file path - path starts with / so we need to remove it
    $normalizedPath = ltrim($path, '/');
    $filePath = __DIR__ . '/' . $normalizedPath;

    // Normalize path separators
    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

    // Security check: ensure file is within project directory
    $projectRoot = realpath(__DIR__);
    $resolvedPath = realpath($filePath);

    if ($resolvedPath && strpos($resolvedPath, $projectRoot) === 0 && file_exists($resolvedPath) && is_file($resolvedPath)) {
        $filePath = $resolvedPath;
        // Set appropriate content type
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

        // Set headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));

        // Cache headers
        header('Cache-Control: public, max-age=3600');

        // For JS files, allow CORS if needed
        if ($ext === 'js') {
            header('Access-Control-Allow-Origin: *');
        }

        // Output the file
        readfile($filePath);
        exit;
    } else {
        // File not found - return 404
        http_response_code(404);
        header('Content-Type: text/plain');
        if (defined('APP_ENV') && APP_ENV === 'development') {
            echo "404 - File not found: " . htmlspecialchars($path) . "\n";
            echo "Looking for: " . htmlspecialchars($filePath) . "\n";
            echo "Resolved: " . htmlspecialchars($resolvedPath ?? 'null') . "\n";
            echo "Project root: " . htmlspecialchars($projectRoot) . "\n";
        } else {
            echo "404 - File not found";
        }
        exit;
    }
}

// Otherwise, route to index.php
require __DIR__ . '/index.php';
?>
