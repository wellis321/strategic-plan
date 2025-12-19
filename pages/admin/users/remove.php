<?php
// Remove user from organisation
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

// Verify user belongs to the organisation
if ($user['organization_id'] != $organizationId) {
    setFlashMessage('error', 'User does not belong to this organisation');
    redirect('/admin/organizations/' . $organizationId . '/users');
}

// Cannot remove super admins
if ($user['role'] === 'super_admin') {
    setFlashMessage('error', 'Cannot remove super admin accounts');
    redirect('/admin/organizations/' . $organizationId . '/users');
}

// Check if this is the last admin in the organisation - warn but allow super admin to proceed
if ($user['role'] === 'admin') {
    $db = Database::getInstance();
    $adminCount = $db->fetchOne(
        "SELECT COUNT(*) as count FROM users WHERE organization_id = :org_id AND role = 'admin' AND status = 'active'",
        ['org_id' => $organizationId]
    );

    if ($adminCount['count'] <= 1) {
        // Super admin can override, but we'll show a warning
        // The confirmation dialog in the UI should have already warned them
        // Log this action for audit purposes
        error_log("WARNING: Super admin removed the last admin (user_id: {$userId}) from organisation {$organizationId}");
    }
}

// Remove user (deactivate and free up seat)
// Note: This is the same as deactivate, but kept for backwards compatibility
if ($userModel->update($userId, ['status' => 'inactive'])) {
    setFlashMessage('success', 'User removed from organisation successfully. Seat has been freed.');
} else {
    setFlashMessage('error', 'Failed to remove user');
}

redirect('/admin/organizations/' . $organizationId . '/users');
?>
