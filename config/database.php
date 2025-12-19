<?php
// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-init','hypothesisId'=>'C','location'=>'database.php:3','message'=>'database.php starting','data'=>['db_host'=>defined('DB_HOST')?DB_HOST:'not defined','db_name'=>defined('DB_NAME')?DB_NAME:'not defined'],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Database connection
// Ensure $pdo is in global scope
global $pdo;

try {
    // #region agent log
    @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-init','hypothesisId'=>'C','location'=>'database.php:10','message'=>'Before PDO creation','data'=>['dsn'=>"mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    // Ensure it's also in global scope
    $GLOBALS['pdo'] = $pdo;
    
    // #region agent log
    @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-init','hypothesisId'=>'C','location'=>'database.php:19','message'=>'PDO created successfully','data'=>['pdo_set'=>isset($pdo),'globals_pdo_set'=>isset($GLOBALS['pdo'])],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
} catch (PDOException $e) {
    // #region agent log
    @file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'database-init','hypothesisId'=>'C','location'=>'database.php:24','message'=>'PDO exception caught','data'=>['error'=>$e->getMessage(),'code'=>$e->getCode()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();

    if (APP_ENV === 'development') {
        // Provide helpful error messages in development
        if (strpos($errorMessage, 'Unknown database') !== false) {
            die('
                <h2>Database Not Found</h2>
                <p>The database "' . DB_NAME . '" does not exist.</p>
                <p><strong>To fix this:</strong></p>
                <ol>
                    <li>Create the database by running: <code>mysql -u ' . DB_USER . ' -p &lt; database/schema.sql</code></li>
                    <li>Or manually create it: <code>CREATE DATABASE ' . DB_NAME . ';</code></li>
                    <li>Then import the schema: <code>mysql -u ' . DB_USER . ' -p ' . DB_NAME . ' &lt; database/schema.sql</code></li>
                </ol>
                <p><strong>Error details:</strong> ' . htmlspecialchars($errorMessage) . '</p>
            ');
        } else {
            die('
                <h2>Database Connection Failed</h2>
                <p><strong>Error:</strong> ' . htmlspecialchars($errorMessage) . '</p>
                <p><strong>Configuration:</strong></p>
                <ul>
                    <li>Host: ' . DB_HOST . '</li>
                    <li>Database: ' . DB_NAME . '</li>
                    <li>User: ' . DB_USER . '</li>
                    <li>Port: ' . DB_PORT . '</li>
                </ul>
                <p>Please check your database credentials in the <code>.env</code> file.</p>
            ');
        }
    } else {
        die('Database connection failed. Please contact the administrator.');
    }
}
?>
