<?php
// Utility functions

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

function formatDate($date, $format = 'Y-m-d') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateForDisplay($date) {
    if (empty($date)) return '';
    return date('j M Y', strtotime($date));
}

function getCurrentUrl() {
    return $_SERVER['REQUEST_URI'];
}

function isCurrentPage($path) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $currentPath === $path;
}

function getFlashMessage($key = null) {
    if (!isset($_SESSION['flash_messages'])) {
        return null;
    }

    if ($key === null) {
        $messages = $_SESSION['flash_messages'];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    if (isset($_SESSION['flash_messages'][$key])) {
        $message = $_SESSION['flash_messages'][$key];
        unset($_SESSION['flash_messages'][$key]);
        return $message;
    }

    return null;
}

function setFlashMessage($key, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][$key] = $message;
}

function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    $token = generateCsrfToken();
    return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">";
}

function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        return "{$fieldName} is required";
    }
    return null;
}

function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    return null;
}

function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return "Invalid date format";
    }
    return null;
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

function isPost() {
    return getRequestMethod() === 'POST';
}

function isGet() {
    return getRequestMethod() === 'GET';
}

function getPostData() {
    return $_POST;
}

function getQueryParam($key, $default = null) {
    return $_GET[$key] ?? $default;
}

function buildUrl($path, $params = []) {
    $url = APP_URL . $path;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

function includeTemplate($template, $data = []) {
    extract($data);
    include __DIR__ . "/../templates/{$template}.php";
}

function renderTemplate($template, $data = []) {
    ob_start();
    includeTemplate($template, $data);
    return ob_get_clean();
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function calculateProgress($milestones) {
    if (empty($milestones)) return 0;

    $completed = 0;
    foreach ($milestones as $milestone) {
        if ($milestone['status'] === 'completed') {
            $completed++;
        }
    }

    return round(($completed / count($milestones)) * 100, 1);
}

function getStatusBadgeClass($status) {
    $system = DesignSystem::getCurrentSystem();

    switch ($system) {
        case 'sgds':
            switch ($status) {
                case 'completed': return 'ds_tag ds_tag--positive';
                case 'on_track': return 'ds_tag ds_tag--neutral';
                case 'at_risk': return 'ds_tag ds_tag--caution';
                case 'delayed': return 'ds_tag ds_tag--negative';
                default: return 'ds_tag';
            }
        case 'tailwind':
            switch ($status) {
                case 'completed': return 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
                case 'on_track': return 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800';
                case 'at_risk': return 'px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800';
                case 'delayed': return 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800';
                default: return 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800';
            }
        default:
            return "badge badge-{$status}";
    }
}

function formatStatus($status) {
    return ucwords(str_replace('_', ' ', $status));
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function pluralize($count, $singular, $plural = null) {
    if ($plural === null) {
        $plural = $singular . 's';
    }
    return $count == 1 ? $singular : $plural;
}

function asset($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');

    // Get base path from script name
    $scriptName = $_SERVER['SCRIPT_NAME'];

    // If using router.php, get the directory
    if (basename($scriptName) === 'router.php') {
        $base = dirname($scriptName);
    } else {
        $base = dirname($scriptName);
    }

    // Normalize base path
    if ($base === '/' || $base === '\\' || $base === '.') {
        $base = '';
    } else {
        $base = rtrim($base, '/');
    }

    // Return the full path
    return $base . '/' . $path;
}
?>
