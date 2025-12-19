<?php
// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'A','location'=>'bootstrap.php:3','message'=>'Bootstrap starting','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Load environment variables first
require_once __DIR__ . '/env.php';

// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'A','location'=>'bootstrap.php:8','message'=>'env.php loaded','data'=>['app_env'=>defined('APP_ENV')?APP_ENV:'not defined'],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Load configuration (this sets session settings)
require_once __DIR__ . '/config.php';

// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'A','location'=>'bootstrap.php:13','message'=>'config.php loaded','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Start session after configuration is loaded
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookies before starting session
    // Check if HTTPS is actually available before setting secure flag
    $isSecure = (APP_ENV === 'production' && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'));
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '', // Set to your domain in production if needed
        'secure' => $isSecure, // Only use secure cookies if HTTPS is actually available
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // CSRF protection
    ]);
    session_start();
}

// Load database connection
// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'B','location'=>'bootstrap.php:28','message'=>'Before loading database.php','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

require_once __DIR__ . '/database.php';

// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'B','location'=>'bootstrap.php:33','message'=>'database.php loaded','data'=>['pdo_exists'=>isset($GLOBALS['pdo']),'pdo_is_null'=>$GLOBALS['pdo']??null===null],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Load core classes
// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'B','location'=>'bootstrap.php:37','message'=>'Before loading Database class','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

require_once __DIR__ . '/../classes/Database.php';

// #region agent log
@file_put_contents('/Users/wellis/Desktop/Cursor/strategic-plan/.cursor/debug.log', json_encode(['sessionId'=>'debug-session','runId'=>'bootstrap','hypothesisId'=>'B','location'=>'bootstrap.php:42','message'=>'Database class loaded','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion
require_once __DIR__ . '/../classes/Goal.php';
require_once __DIR__ . '/../classes/Project.php';
require_once __DIR__ . '/../classes/DesignSystem.php';
require_once __DIR__ . '/../classes/Organization.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/StrategicPlanSection.php';
require_once __DIR__ . '/../classes/StrategicPlan.php';
require_once __DIR__ . '/../classes/OrganizationTopSection.php';

// Load utility functions
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rich-text-editor.php';
require_once __DIR__ . '/../includes/email.php';

// Load authentication functions
require_once __DIR__ . '/../includes/auth.php';

// Set error reporting based on environment
if (getenv('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // In production, log errors but don't display them
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../error_log');
}
?>
