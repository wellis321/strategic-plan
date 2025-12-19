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

// Check if DB_PORT is defined
if (!defined('DB_PORT')) {
    echo "⚠ DB_PORT not defined, using default 3306<br>";
    define('DB_PORT', '3306');
}

echo "Connection details:<br>";
echo "- Host: " . DB_HOST . "<br>";
echo "- Port: " . DB_PORT . "<br>";
echo "- Database: " . DB_NAME . "<br>";
echo "- User: " . DB_USER . "<br>";
echo "- Password: " . (DB_PASS ? "***SET***" : "NOT SET") . "<br><br>";

// Try direct PDO connection first (before loading database.php which might die)
echo "<h3>Direct PDO Connection Test</h3>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    echo "DSN: " . htmlspecialchars($dsn) . "<br>";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✓ Direct PDO connection successful!<br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "✓ Test query successful! Result: " . $result['test'] . "<br><br>";
    
    // Now try through Database class
    echo "<h3>Database Class Test</h3>";
    try {
        // Temporarily set APP_ENV to development to avoid die()
        $oldEnv = defined('APP_ENV') ? APP_ENV : null;
        define('APP_ENV', 'development');
        
        require_once 'config/database.php';
        echo "✓ Database config loaded<br>";
        
        $db = Database::getInstance();
        echo "✓ Database instance created<br>";
        
        $result = $db->fetchOne("SELECT 1 as test");
        echo "✓ Database class query successful! Result: " . $result['test'] . "<br><br>";
    } catch (Exception $e) {
        echo "✗ Database class failed!<br>";
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br><br>";
    }
    
} catch (PDOException $e) {
    echo "✗ Direct PDO connection failed!<br>";
    echo "Error code: " . $e->getCode() . "<br>";
    echo "Error message: " . htmlspecialchars($e->getMessage()) . "<br><br>";
    
    // Common error messages and solutions
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<strong>Solution:</strong> The database '" . DB_NAME . "' does not exist. Create it in your Hostinger control panel.<br><br>";
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<strong>Solution:</strong> Database credentials are incorrect. Check your username and password in .env file.<br><br>";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false || strpos($e->getMessage(), 'Connection timed out') !== false) {
        echo "<strong>Solution:</strong> Cannot connect to database server. Check if DB_HOST is correct (might need to be different than 'localhost' on Hostinger).<br><br>";
    }
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

// Test 8: Registration Page Dependencies & Database Tables
echo "<h2>Registration Page Dependencies Test</h2>";
try {
    // Check if database tables exist (using the PDO connection from earlier)
    if (isset($pdo)) {
        echo "<h3>Database Tables Check</h3>";
        $tables = ['users', 'organizations'];
        foreach ($tables as $table) {
            try {
                $result = $pdo->query("SHOW TABLES LIKE '{$table}'");
                if ($result->rowCount() > 0) {
                    echo "✓ Table '{$table}' exists<br>";
                    // Count rows
                    $count = $pdo->query("SELECT COUNT(*) as count FROM {$table}")->fetch();
                    echo "  - Rows: " . $count['count'] . "<br>";
                } else {
                    echo "✗ Table '{$table}' does NOT exist - <strong>THIS IS LIKELY THE PROBLEM!</strong><br>";
                    echo "  <strong>Solution:</strong> Import the database schema from database/schema.sql<br>";
                }
            } catch (Exception $e) {
                echo "✗ Error checking table '{$table}': " . htmlspecialchars($e->getMessage()) . "<br>";
            }
        }
        echo "<br>";
    }
    
    // Try loading bootstrap to check for errors
    echo "<h3>Bootstrap & Classes Test</h3>";
    try {
        // Temporarily override APP_ENV to see errors
        $originalEnv = getenv('APP_ENV');
        putenv('APP_ENV=development');
        
        require_once 'config/bootstrap.php';
        echo "✓ Bootstrap loaded successfully<br>";
        
        // Check classes
        if (class_exists('User')) {
            echo "✓ User class loaded<br>";
        } else {
            echo "✗ User class NOT found<br>";
        }
        
        if (class_exists('Organization')) {
            echo "✓ Organization class loaded<br>";
        } else {
            echo "✗ Organization class NOT found<br>";
        }
        
        // Check critical functions
        $criticalFunctions = ['isLoggedIn', 'isPost', 'getPostData', 'sanitizeInput', 'extractDomainFromEmail', 'getOrganizationByEmailDomain', 'checkSeatAvailability'];
        foreach ($criticalFunctions as $func) {
            if (function_exists($func)) {
                echo "✓ Function {$func} exists<br>";
            } else {
                echo "✗ Function {$func} NOT found - <strong>THIS COULD CAUSE 500 ERROR!</strong><br>";
            }
        }
        
        // Restore original env
        if ($originalEnv) {
            putenv('APP_ENV=' . $originalEnv);
        }
        
    } catch (Exception $e) {
        echo "✗ Error loading bootstrap!<br>";
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre><br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error in dependency test: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<br><h2>All Tests Complete</h2>";
echo "<p><strong>Most Common Issues:</strong></p>";
echo "<ul>";
echo "<li>Missing database tables - Import database/schema.sql</li>";
echo "<li>Missing functions - Check if all files are uploaded</li>";
echo "<li>Check error_log file in root directory for detailed errors</li>";
echo "</ul>";
echo "<p><strong>To see registration page errors:</strong> Temporarily set APP_ENV=development in .env file</p>";
?>

