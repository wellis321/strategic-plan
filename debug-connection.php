<?php
/**
 * Diagnostic test file - Remove this after fixing issues
 * Access via: https://rosybrown-cod-114553.hostingersite.com/test-connection.php
 */

// Test 1: PHP Version
echo "<h2>PHP Version Test</h2>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Test 2: .env File Check
echo "<h2>.env File Check</h2>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "✓ .env file exists<br>";
    if (is_readable($envPath)) {
        echo "✓ .env file is readable<br>";
        echo "File size: " . filesize($envPath) . " bytes<br>";
        // Show first few lines (without sensitive data)
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        echo "Number of lines: " . count($lines) . "<br>";
        echo "First few lines (sanitized):<br>";
        foreach (array_slice($lines, 0, 5) as $line) {
            if (strpos(trim($line), '#') !== 0 && strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                if (isset($parts[1]) && (strpos($parts[0], 'PASS') !== false || strpos($parts[0], 'SECRET') !== false)) {
                    echo htmlspecialchars($parts[0]) . "=***HIDDEN***<br>";
                } else {
                    echo htmlspecialchars(substr($line, 0, 50)) . "<br>";
                }
            }
        }
    } else {
        echo "✗ .env file is NOT readable (check permissions)<br>";
    }
} else {
    echo "✗ .env file does NOT exist at: " . htmlspecialchars($envPath) . "<br>";
    echo "Current directory: " . __DIR__ . "<br>";
}
echo "<br>";

// Test 2b: Environment Variables (after loading)
echo "<h2>Environment Variables Test</h2>";
require_once 'config/env.php';
require_once 'config/config.php';
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'NOT SET')) . "<br>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'NOT SET')) . "<br>";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'NOT SET')) . "<br>";
echo "APP_ENV: " . (defined('APP_ENV') ? APP_ENV : (getenv('APP_ENV') ?: 'NOT SET')) . "<br>";
echo "getenv('DB_HOST'): " . (getenv('DB_HOST') ?: 'NOT SET') . "<br>";
echo "\$_ENV['DB_HOST']: " . (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'NOT SET') . "<br><br>";

// Test 3: Database Connection
echo "<h2>Database Connection Test</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    $result = $db->fetchOne("SELECT 1 as test");
    echo "✓ Database connection successful!<br>";
    echo "Test query result: " . $result['test'] . "<br><br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br><br>";
}

// Test 4: Required Classes
echo "<h2>Required Classes Test</h2>";
$classes = ['User', 'Organization', 'Database'];
foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✓ Class {$class} loaded<br>";
    } else {
        echo "✗ Class {$class} NOT found<br>";
    }
}
echo "<br>";

// Test 5: Required Functions
echo "<h2>Required Functions Test</h2>";
$functions = ['isLoggedIn', 'getPostData', 'sanitizeInput', 'extractDomainFromEmail', 'getOrganizationByEmailDomain'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "✓ Function {$func} exists<br>";
    } else {
        echo "✗ Function {$func} NOT found<br>";
    }
}
echo "<br>";

// Test 6: File Permissions
echo "<h2>File Permissions Test</h2>";
$writableDirs = ['config', 'static/uploads'];
foreach ($writableDirs as $dir) {
    if (is_writable($dir)) {
        echo "✓ Directory {$dir} is writable<br>";
    } else {
        echo "✗ Directory {$dir} is NOT writable<br>";
    }
}
echo "<br>";

// Test 7: Session Test
echo "<h2>Session Test</h2>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "✓ Session started successfully<br>";
    $_SESSION['test'] = 'test_value';
    echo "✓ Session write test: " . ($_SESSION['test'] === 'test_value' ? 'PASSED' : 'FAILED') . "<br>";
} catch (Exception $e) {
    echo "✗ Session error: " . $e->getMessage() . "<br>";
}
echo "<br>";

echo "<h2>All Tests Complete</h2>";
echo "<p><strong>If all tests pass, the issue is likely in the registration form processing logic.</strong></p>";
echo "<p><strong>Check the error_log file in the root directory for detailed error messages.</strong></p>";
?>

