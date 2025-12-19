<?php
// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env file - try multiple possible locations
$envPaths = [
    __DIR__ . '/../.env',
    dirname(__DIR__) . '/.env',
    $_SERVER['DOCUMENT_ROOT'] . '/.env',
    getcwd() . '/.env'
];

$envLoaded = false;
foreach ($envPaths as $envPath) {
    if (file_exists($envPath) && is_readable($envPath)) {
        loadEnv($envPath);
        $envLoaded = true;
        break;
    }
}

// Log if .env file wasn't found (for debugging)
if (!$envLoaded && function_exists('error_log')) {
    error_log("Warning: .env file not found. Tried paths: " . implode(', ', $envPaths));
}
?>
