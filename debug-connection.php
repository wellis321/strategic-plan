<?php
/**
 * Diagnostic test file - Remove this after fixing issues
 * Access via: https://rosybrown-cod-114553.hostingersite.com/debug-connection.php
 */

// Enable error reporting and set up fatal error handler
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set up error handler to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $logData = ['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E','location'=>'debug-connection.php:shutdown','message'=>'Fatal error detected','data'=>['type'=>$error['type'],'message'=>$error['message'],'file'=>$error['file'],'line'=>$error['line']],'timestamp'=>time()*1000];
        @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode($logData)."\n", FILE_APPEND);
        echo "<br><br><strong style='color: red;'>FATAL ERROR DETECTED:</strong><br>";
        echo "Type: " . $error['type'] . "<br>";
        echo "Message: " . htmlspecialchars($error['message']) . "<br>";
        echo "File: " . htmlspecialchars($error['file']) . "<br>";
        echo "Line: " . $error['line'] . "<br>";
    }
});

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
    
    // #region agent log
    file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A','location'=>'debug-connection.php:87','message'=>'Before Database class test','data'=>['app_env'=>defined('APP_ENV')?APP_ENV:'not_set'],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
    try {
        // Temporarily set APP_ENV to development to avoid die()
        $oldEnv = defined('APP_ENV') ? APP_ENV : null;
        if (!defined('APP_ENV')) {
            define('APP_ENV', 'development');
        }
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A','location'=>'debug-connection.php:95','message'=>'Before loading database.php','data'=>['app_env'=>APP_ENV,'old_env'=>$oldEnv],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        require_once 'config/database.php';
        echo "✓ Database config loaded<br>";
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A','location'=>'debug-connection.php:122','message'=>'After loading database.php','data'=>['pdo_exists'=>isset($pdo),'globals_pdo_exists'=>isset($GLOBALS['pdo']),'db_class_exists'=>class_exists('Database')],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        // Check if global $pdo exists - this is critical for Database class
        $globalPdoExists = isset($GLOBALS['pdo']);
        $localPdoExists = isset($pdo);
        
        if (!$globalPdoExists && !$localPdoExists) {
            echo "⚠ Warning: Global \$pdo variable not set after loading database.php<br>";
            echo "This will cause Database::getInstance() to fail!<br>";
            // #region agent log
            file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B','location'=>'debug-connection.php:131','message'=>'Global pdo missing','data'=>['has_globals_pdo'=>$globalPdoExists,'has_local_pdo'=>$localPdoExists],'timestamp'=>time()*1000])."\n", FILE_APPEND);
            // #endregion
            
            // Try to set it manually
            if (isset($pdo)) {
                $GLOBALS['pdo'] = $pdo;
                echo "✓ Set global \$pdo from local variable<br>";
            }
        } else {
            echo "✓ Global \$pdo variable is set<br>";
        }
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'C','location'=>'debug-connection.php:142','message'=>'Before Database::getInstance()','data'=>['global_pdo_set'=>isset($GLOBALS['pdo'])],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        // Check if Database class exists before trying to use it
        if (!class_exists('Database')) {
            throw new Exception('Database class not found after loading database.php');
        }
        
        $db = Database::getInstance();
        echo "✓ Database instance created<br>";
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'C','location'=>'debug-connection.php:116','message'=>'After Database::getInstance()','data'=>['db_class'=>get_class($db)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D','location'=>'debug-connection.php:120','message'=>'Before fetchOne query','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        $result = $db->fetchOne("SELECT 1 as test");
        echo "✓ Database class query successful! Result: " . $result['test'] . "<br><br>";
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D','location'=>'debug-connection.php:125','message'=>'After fetchOne query','data'=>['result'=>$result],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
    } catch (Exception $e) {
        echo "✗ Database class failed!<br>";
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
        echo "Trace: <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre><br><br>";
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E','location'=>'debug-connection.php:135','message'=>'Exception caught','data'=>['error'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'trace'=>$e->getTraceAsString()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
    } catch (Error $e) {
        echo "✗ Fatal error occurred!<br>";
        echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine() . "<br>";
        echo "Trace: <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre><br><br>";
        
        // #region agent log
        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E','location'=>'debug-connection.php:145','message'=>'Fatal Error caught','data'=>['error'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'trace'=>$e->getTraceAsString()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
    }
    
    // #region agent log
    file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A','location'=>'debug-connection.php:150','message'=>'After Database class test block','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
    // Check database tables (using direct PDO connection)
    echo "<h3>Database Tables Check</h3>";
    
    // #region agent log
    file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'F','location'=>'debug-connection.php:155','message'=>'Starting table check','data'=>['pdo_exists'=>isset($pdo)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
    if (!isset($pdo)) {
        echo "✗ Cannot check tables - PDO connection not available<br><br>";
    } else {
        $requiredTables = ['users', 'organizations', 'goals', 'projects'];
        $missingTables = [];
        
        foreach ($requiredTables as $table) {
            try {
                $result = $pdo->query("SHOW TABLES LIKE '{$table}'");
                if ($result->rowCount() > 0) {
                    echo "✓ Table '{$table}' exists<br>";
                    // Count rows
                    try {
                        $count = $pdo->query("SELECT COUNT(*) as count FROM {$table}")->fetch();
                        echo "  - Rows: " . $count['count'] . "<br>";
                        
                        // #region agent log
                        file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'F','location'=>'debug-connection.php:170','message'=>'Table exists','data'=>['table'=>$table,'row_count'=>$count['count']],'timestamp'=>time()*1000])."\n", FILE_APPEND);
                        // #endregion
                    } catch (Exception $e) {
                        echo "  - Error counting rows: " . htmlspecialchars($e->getMessage()) . "<br>";
                    }
                } else {
                    echo "✗ Table '{$table}' does NOT exist - <strong>THIS IS LIKELY THE PROBLEM!</strong><br>";
                    $missingTables[] = $table;
                    
                    // #region agent log
                    file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'F','location'=>'debug-connection.php:178','message'=>'Table missing','data'=>['table'=>$table],'timestamp'=>time()*1000])."\n", FILE_APPEND);
                    // #endregion
                }
            } catch (Exception $e) {
                echo "✗ Error checking table '{$table}': " . htmlspecialchars($e->getMessage()) . "<br>";
                $missingTables[] = $table;
            }
        }
        
        if (!empty($missingTables)) {
            echo "<br><strong style='color: red;'>MISSING TABLES DETECTED!</strong><br>";
            echo "The following tables are missing: " . implode(', ', $missingTables) . "<br>";
            echo "<strong>Solution:</strong> Import the database schema file: <code>database/hostinger-complete-schema.sql</code><br>";
            echo "Go to phpMyAdmin → Select database → Import → Choose file → Go<br><br>";
            
            // #region agent log
            file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'F','location'=>'debug-connection.php:190','message'=>'Missing tables summary','data'=>['missing_tables'=>$missingTables],'timestamp'=>time()*1000])."\n", FILE_APPEND);
            // #endregion
        } else {
            echo "<br>✓ All required tables exist!<br><br>";
        }
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

