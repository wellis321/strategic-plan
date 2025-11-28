<?php
// Application configuration
define('APP_NAME', getenv('APP_NAME') ?: 'Simple Strategic Plans');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'strategic_plan');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

// Design system configuration
define('DESIGN_SYSTEM', getenv('DESIGN_SYSTEM') ?: 'tailwind');

// Session configuration
define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: 7200);

// Email configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-mail.outlook.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: 'simplestrategicplanning@outlook.com');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'simplestrategicplanning@outlook.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Simple Strategic Plans');
define('CONTACT_EMAIL', getenv('CONTACT_EMAIL') ?: 'simplestrategicplanning@outlook.com');

// Set session lifetime
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
?>
