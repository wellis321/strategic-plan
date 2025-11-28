<?php
// Deactivate user in organization
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

// Check if this is the last admin in the organization - warn but allow super admin to proceed
if ($user['role'] === 'admin' && $user['status'] === 'active') {
    $db = Database::getInstance();
    $adminCount = $db->fetchOne(
        "SELECT COUNT(*) as count FROM users WHERE organization_id = :org_id AND role = 'admin' AND status = 'active'",
        ['org_id' => $organizationId]
    );

    if ($adminCount['count'] <= 1) {
        error_log("WARNING: Super admin deactivating the last admin (user_id: {$userId}) from organization {$organizationId}");
    }
}

// Deactivate user
if ($userModel->update($userId, ['status' => 'inactive'])) {
    setFlashMessage('success', 'User deactivated successfully. Seat has been freed.');
} else {
    setFlashMessage('error', 'Failed to deactivate user');
}

redirect('/admin/organizations/' . $organizationId . '/users');
?>
