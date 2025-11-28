<?php
// Authentication helper functions

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    $userModel = new User();
    return $userModel->getById($_SESSION['user_id']);
}

function getCurrentOrganizationId() {
    $user = getCurrentUser();
    return $user ? $user['organization_id'] : null;
}

function isSuperAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'super_admin';
}

function isOrganizationAdmin() {
    $user = getCurrentUser();
    return $user && ($user['role'] === 'admin' || $user['role'] === 'super_admin');
}

function requireLogin($redirectTo = '/login') {
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Please log in to access this page');
        redirect($redirectTo);
    }
}

function requireSuperAdmin($redirectTo = '/') {
    requireLogin();
    if (!isSuperAdmin()) {
        setFlashMessage('error', 'Access denied. Super admin privileges required.');
        redirect($redirectTo);
    }
}

function requireOrganizationAdmin($redirectTo = '/') {
    requireLogin();
    if (!isOrganizationAdmin()) {
        setFlashMessage('error', 'Access denied. Admin privileges required.');
        redirect($redirectTo);
    }
}

function login($user) {
    // Regenerate session ID for security
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['organization_id'] = $user['organization_id'];
    $_SESSION['logged_in_at'] = time();
}

function logout() {
    // Destroy session
    $_SESSION = [];

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    session_destroy();
}

function generateVerificationToken() {
    return bin2hex(random_bytes(32));
}

function generatePasswordResetToken() {
    return bin2hex(random_bytes(32));
}

function sendVerificationEmail($user, $token) {
    require_once __DIR__ . '/email.php';

    $verificationUrl = APP_URL . '/verify-email?token=' . urlencode($token);

    // Get email template
    $emailBody = getEmailTemplate('verification', [
        'user' => $user,
        'verificationUrl' => $verificationUrl
    ]);

    if (!$emailBody) {
        // Fallback to simple text email if template not found
        $emailBody = "Hello " . ($user['first_name'] ?? 'there') . ",\n\n";
        $emailBody .= "Thank you for registering with Simple Strategic Plans!\n\n";
        $emailBody .= "Please verify your email address by clicking this link:\n";
        $emailBody .= $verificationUrl . "\n\n";
        $emailBody .= "This link will expire in 24 hours.\n\n";
        $emailBody .= "If you didn't create an account, please ignore this email.\n\n";
        $emailBody .= "Best regards,\n";
        $emailBody .= "The Simple Strategic Plans Team";
    }

    $subject = 'Verify Your Email Address - Simple Strategic Plans';
    $sent = sendEmail($user['email'], $subject, $emailBody, true);

    if (!$sent && APP_ENV === 'development') {
        error_log("Verification email for {$user['email']}: {$verificationUrl}");
    }

    return $verificationUrl;
}

function sendPasswordResetEmail($user, $token) {
    require_once __DIR__ . '/email.php';

    $resetUrl = APP_URL . '/reset-password?token=' . urlencode($token);

    // Simple password reset email
    $emailBody = "Hello " . ($user['first_name'] ?? 'there') . ",\n\n";
    $emailBody .= "You requested to reset your password for your Simple Strategic Plans account.\n\n";
    $emailBody .= "Click this link to reset your password:\n";
    $emailBody .= $resetUrl . "\n\n";
    $emailBody .= "This link will expire in 1 hour.\n\n";
    $emailBody .= "If you didn't request a password reset, please ignore this email.\n\n";
    $emailBody .= "Best regards,\n";
    $emailBody .= "The Simple Strategic Plans Team";

    $subject = 'Reset Your Password - Simple Strategic Plans';
    $sent = sendEmail($user['email'], $subject, $emailBody, false);

    if (!$sent && APP_ENV === 'development') {
        error_log("Password reset email for {$user['email']}: {$resetUrl}");
    }

    return $resetUrl;
}

function checkSeatAvailability($domain) {
    $orgModel = new Organization();
    return $orgModel->hasAvailableSeats($domain);
}

function extractDomainFromEmail($email) {
    $orgModel = new Organization();
    return $orgModel->extractDomainFromEmail($email);
}

function getOrganizationByEmailDomain($email) {
    $domain = extractDomainFromEmail($email);
    if (!$domain) {
        return null;
    }

    $orgModel = new Organization();
    return $orgModel->getByDomain($domain);
}
?>
