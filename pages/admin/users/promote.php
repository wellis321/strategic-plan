<?php
// Promote user to admin
requireSuperAdmin();

$userModel = new User();

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

// Cannot promote if already admin
if ($user['role'] === 'admin') {
    setFlashMessage('error', 'User is already an admin');
    redirect('/admin/organizations/' . $organizationId . '/users');
}

// Promote user to admin
if ($userModel->update($userId, ['role' => 'admin'])) {
    setFlashMessage('success', 'User promoted to admin successfully');
} else {
    setFlashMessage('error', 'Failed to promote user');
}

redirect('/admin/organizations/' . $organizationId . '/users');
?>
