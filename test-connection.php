<?php
/**
 * Diagnostic test file - Remove this after fixing issues
 * Access via: https://rosybrown-cod-114553.hostingersite.com/test-connection.php
 */

// Test 1: PHP Version
echo "<h2>PHP Version Test</h2>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Test 2: Environment Variables
echo "<h2>Environment Variables Test</h2>";
require_once 'config/env.php';
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT SET') . "<br>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT SET') . "<br>";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT SET') . "<br>";
echo "APP_ENV: " . (defined('APP_ENV') ? APP_ENV : 'NOT SET') . "<br><br>";

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

