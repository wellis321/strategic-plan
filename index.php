<?php
require_once 'config/bootstrap.php';

// Handle static files first (CSS, JS, images, fonts)
$requestUri = $_SERVER['REQUEST_URI'];
$staticPath = parse_url($requestUri, PHP_URL_PATH);
$staticPath = strtok($staticPath, '?'); // Remove query string

if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $staticPath)) {
    // Build file path - path starts with / so we need to remove it
    $normalizedPath = ltrim($staticPath, '/');
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

        // Output the file
        readfile($filePath);
        exit;
    } else {
        // File not found - return 404
        http_response_code(404);
        header('Content-Type: text/plain');
        if (defined('APP_ENV') && APP_ENV === 'development') {
            echo "404 - File not found: " . htmlspecialchars($staticPath) . "\n";
            echo "Looking for: " . htmlspecialchars($filePath) . "\n";
            echo "Resolved: " . htmlspecialchars($resolvedPath ?? 'null') . "\n";
            echo "Project root: " . htmlspecialchars($projectRoot) . "\n";
        } else {
            echo "404 - File not found";
        }
        exit;
    }
}

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove base path if app is in a subdirectory
// Don't strip anything from API routes - they should always be at root level
if (strpos($path, '/api/') === 0) {
    // Keep API routes as-is
    $route = rtrim($path, '/');
} else {
    // Remove base path if app is in a subdirectory
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptName !== '/' && $scriptName !== '\\' && $scriptName !== '.' && strpos($path, $scriptName) === 0) {
        $path = substr($path, strlen($scriptName));
    }
    $path = rtrim($path, '/');
    $route = $path ?: '/';
}

// Handle routes
// API routes should be checked first
if (strpos($route, '/api/') === 0) {

    // Route API requests
    if ($route === '/api/test-route') {
        require __DIR__ . '/api/test-route.php';
        exit;
    } elseif ($route === '/api/top-sections/reorder' || preg_match('#^/api/top-sections/reorder#', $route)) {
        // Debug: uncomment to see what's happening
        // error_log("Matched /api/top-sections/reorder route. Route value: '$route'");
        $apiFile = __DIR__ . '/api/top-sections-reorder.php';
        if (!file_exists($apiFile)) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'API file not found: ' . $apiFile]);
            exit;
        }
        require $apiFile;
        exit;
    } elseif ($route === '/api/projects' || strpos($route, '/api/projects') === 0) {
        require __DIR__ . '/api/projects.php';
        exit;
    } elseif ($route === '/api/goals' || strpos($route, '/api/goals') === 0) {
        require __DIR__ . '/api/goals.php';
        exit;
    } else {
        // Unknown API route
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
        exit;
    }
} elseif ($route === '/') {
    // Show home page for non-logged-in users, dashboard for logged-in users
    if (isLoggedIn()) {
        require 'pages/dashboard.php';
    } else {
        require 'pages/home.php';
    }
} elseif ($route === '/home') {
    require 'pages/home.php';
} elseif ($route === '/dashboard') {
    require 'pages/dashboard.php';
} elseif ($route === '/template') {
    require 'pages/template.php';
} elseif ($route === '/login') {
    require 'pages/login.php';
} elseif ($route === '/register') {
    require 'pages/register.php';
} elseif ($route === '/request-organization') {
    require 'pages/request-organization.php';
} elseif ($route === '/verify-email') {
    require 'pages/verify-email.php';
} elseif ($route === '/logout') {
    require 'pages/logout.php';
} elseif ($route === '/admin' || $route === '/admin/') {
    require 'pages/admin/index.php';
} elseif ($route === '/admin/organizations/new') {
    require 'pages/admin/organizations/new.php';
} elseif (preg_match('#^/admin/organizations/(\d+)/edit$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/admin/organizations/edit.php';
} elseif (preg_match('#^/admin/organizations/(\d+)/users$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/admin/organizations/users.php';
} elseif ($route === '/admin/users/promote') {
    require 'pages/admin/users/promote.php';
} elseif ($route === '/admin/users/demote') {
    require 'pages/admin/users/demote.php';
} elseif ($route === '/admin/users/remove') {
    require 'pages/admin/users/remove.php';
} elseif ($route === '/admin/users/activate') {
    require 'pages/admin/users/activate.php';
} elseif ($route === '/admin/users/deactivate') {
    require 'pages/admin/users/deactivate.php';
} elseif ($route === '/projects') {
    require 'pages/projects/index.php';
} elseif ($route === '/projects/new') {
    require 'pages/projects/create.php';
} elseif ($route === '/projects/edit') {
    require 'pages/projects/edit.php';
} elseif (preg_match('#^/projects/([^/]+)$#', $route, $matches)) {
    // Dynamic project route: /projects/{slug}
    $_GET['slug'] = $matches[1];
    require 'pages/projects/view.php';
} elseif ($route === '/goals') {
    require 'pages/goals/index.php';
} elseif ($route === '/goals/new') {
    require 'pages/goals/create.php';
} elseif ($route === '/goals/edit') {
    require 'pages/goals/edit.php';
} elseif (preg_match('#^/goals/(\d+)$#', $route, $matches)) {
    // Dynamic goal route: /goals/{id}
    $_GET['id'] = $matches[1];
    require 'pages/goals/view.php';
} elseif ($route === '/reports') {
    require 'pages/reports.php';
} elseif ($route === '/strategic-plan') {
    require 'pages/strategic-plan.php';
} elseif ($route === '/example-plan') {
    require 'pages/example-plan.php';
} elseif ($route === '/example-plan-in-progress') {
    require 'pages/example-plan-in-progress.php';
} elseif ($route === '/about') {
    require 'pages/about.php';
} elseif ($route === '/organization/settings') {
    require 'pages/organization/settings.php';
} elseif ($route === '/sections' || $route === '/sections/') {
    require 'pages/sections/index.php';
} elseif ($route === '/sections/new') {
    require 'pages/sections/new.php';
} elseif (preg_match('#^/sections/(\d+)/edit$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/sections/edit.php';
} elseif (preg_match('#^/sections/(\d+)/delete$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/sections/delete.php';
} elseif ($route === '/top-sections' || $route === '/top-sections/') {
    require 'pages/top-sections/index.php';
} elseif ($route === '/top-sections/new') {
    require 'pages/top-sections/new.php';
} elseif (preg_match('#^/top-sections/(\d+)/edit$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/top-sections/edit.php';
} elseif (preg_match('#^/top-sections/(\d+)/delete$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/top-sections/delete.php';
} elseif ($route === '/plans' || $route === '/plans/') {
    require 'pages/plans/index.php';
} elseif ($route === '/plans/new') {
    require 'pages/plans/new.php';
} elseif (preg_match('#^/plans/(\d+)/edit$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/plans/edit.php';
} elseif (preg_match('#^/plans/(\d+)/view$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/plans/view.php';
} elseif (preg_match('#^/plans/(\d+)/delete$#', $route, $matches)) {
    $_GET['id'] = $matches[1];
    require 'pages/plans/delete.php';
} elseif ($route === '/terms') {
    require 'pages/terms.php';
} elseif ($route === '/privacy') {
    require 'pages/privacy.php';
} elseif ($route === '/cookies') {
    require 'pages/cookies.php';
} elseif ($route === '/how-to-create') {
    require 'pages/how-to-create.php';
} elseif (preg_match('#^/([a-z0-9-]+)/([a-z0-9-]+)$#', $route, $matches)) {
    // Handle organization/plan slug routes (e.g., /ramh/2025-2030plan)
    // This must come last to avoid matching system routes like /plans/new, /admin/organizations, etc.
    $_GET['org_slug'] = $matches[1];
    $_GET['plan_slug'] = $matches[2];
    require 'pages/public/strategic-plan.php';
} else {
    http_response_code(404);
    require 'pages/404.php';
}
?>
