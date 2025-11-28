<?php
// Activate user in organization
requireSuperAdmin();

$userModel = new User();
$orgModel = new Organization();

if (!isPost()) {
    setFlashMessage('error', 'Invalid request method');
    redirect('/admin');
}

$userId = getPostData()['user_id'] ?? null;
$organizationId = getPostData()['organization_id'] ?? null;

if (!$userId || !$organizationId) {
    setFlashMessage('error', 'Missing required parameters');
    redirect('/admin');
}

// Validate CSRF token
if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
    setFlashMessage('error', 'Invalid security token');
    redirect('/admin');
}

// Get user to verify they exist and belong to the organization
$user = $userModel->getById($userId);
if (!$user) {
    setFlashMessage('error', 'User not found');
    redirect('/admin/organizations/' . $organizationId . '/users');
}

// Verify user belongs to the organization
if ($user['organization_id'] != $organizationId) {
    setFlashMessage('error', 'User does not belong to this organization');
    redirect('/admin/organizations/' . $organizationId . '/users');
}

// Cannot modify super admins
if ($user['role'] === 'super_admin') {
    setFlashMessage('error', 'Cannot modify super admin accounts');
    redirect('/admin/organizations/' . $organizationId . '/users');
}

// Check seat availability (only if user is verified - unverified users don't count toward seats)
if ($user['email_verified'] && $user['status'] === 'inactive') {
    $usage = $orgModel->getSeatUsage($organizationId);
    if ($usage['seats_available'] <= 0) {
        setFlashMessage('error', 'No seats available. Cannot activate user. Please increase seat allocation or deactivate another user.');
        redirect('/admin/organizations/' . $organizationId . '/users');
    }
}

// Activate user
if ($userModel->update($userId, ['status' => 'active'])) {
    setFlashMessage('success', 'User activated successfully');
} else {
    setFlashMessage('error', 'Failed to activate user');
}

redirect('/admin/organizations/' . $organizationId . '/users');
?>
