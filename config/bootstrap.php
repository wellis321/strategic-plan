<?php
// Load environment variables first
require_once __DIR__ . '/env.php';

// Load configuration (this sets session settings)
require_once __DIR__ . '/config.php';

// Start session after configuration is loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database connection
require_once __DIR__ . '/database.php';

// Load core classes
require_once __DIR__ . '/../classes/Database.php';
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
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
