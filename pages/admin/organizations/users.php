<?php
// View organization users page
requireSuperAdmin();

$orgModel = new Organization();
$userModel = new User();

$orgId = getQueryParam('id');

if (!$orgId) {
    setFlashMessage('error', 'Organization ID is required');
    redirect('/admin');
}

$organization = $orgModel->getById($orgId);
if (!$organization) {
    setFlashMessage('error', 'Organization not found');
    redirect('/admin');
}

$users = $userModel->getAllByOrganization($orgId);
$usage = $orgModel->getSeatUsage($orgId);

$title = 'Users - ' . h($organization['name']) . ' - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Users: <?= h($organization['name']) ?>
            </h1>
            <p class="mt-1 text-gray-600">
                Domain: <?= h($organization['domain']) ?>
            </p>
        </div>
        <?= DesignSystem::button('Back to Organizations', '/admin', 'secondary') ?>
    </header>

    <!-- Seat Usage Summary -->
    <div class="bg-blue-50 border border-blue-200 rounded px-3 py-2 mb-4 text-sm">
        <span class="font-medium text-blue-900">Seats: </span>
        <span class="text-blue-800"><?= h($usage['seats_used']) ?>/<?= h($usage['seat_allocation']) ?> used</span>
        <span class="text-blue-600 mx-2">•</span>
        <span class="text-blue-800"><?= h($usage['seats_available']) ?> available</span>
    </div>

    <!-- Users List -->
    <?php if (empty($users)): ?>
        <?= DesignSystem::alert('No users found for this organization.', 'info') ?>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $db = Database::getInstance();
                        $adminCount = $db->fetchOne(
                            "SELECT COUNT(*) as count FROM users WHERE organization_id = :org_id AND role = 'admin' AND status = 'active'",
                            ['org_id' => $orgId]
                        );
                        foreach ($users as $user):
                            $isLastAdmin = ($user['role'] === 'admin' && $adminCount['count'] <= 1);
                            $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['email'];
                        ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= h($userName) ?></div>
                                    <div class="text-sm text-gray-500"><?= h($user['email']) ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $user['role'] === 'admin' ? 'bg-blue-100 text-blue-800' : ($user['role'] === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800') ?>">
                                        <?= formatStatus($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="<?= getStatusBadgeClass($user['status']) ?>"><?= formatStatus($user['status']) ?></span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php if ($user['email_verified']): ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">✓ Verified</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDateForDisplay($user['created_at']) ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <?= $user['last_login'] ? formatDateForDisplay($user['last_login']) : 'Never' ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if ($user['role'] !== 'super_admin'): ?>
                                        <div class="flex justify-end space-x-1">
                                            <?php if ($user['status'] === 'inactive'): ?>
                                                <?php
                                                // Check seat availability
                                                $canActivate = $usage['seats_available'] > 0 || ($user['email_verified'] && $user['status'] === 'inactive');
                                                ?>
                                                <form method="POST" action="/admin/users/activate" class="inline" onsubmit="return confirm('Are you sure you want to activate this user?<?= !$canActivate ? ' WARNING: No seats available - you may need to increase seat allocation first.' : '' ?>');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="user_id" value="<?= h($user['id']) ?>">
                                                    <input type="hidden" name="organization_id" value="<?= h($orgId) ?>">
                                                    <button type="submit" class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none <?= !$canActivate ? 'opacity-50 cursor-not-allowed' : '' ?>" title="Activate User" <?= !$canActivate ? 'disabled' : '' ?>>
                                                        Activate
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <?php
                                                    $confirmMessage = $isLastAdmin
                                                        ? 'WARNING: This is the last admin in the organization. Demoting them will leave the organization without any admins. Are you absolutely sure you want to proceed?'
                                                        : 'Are you sure you want to demote this user from admin to regular user?';
                                                    ?>
                                                    <form method="POST" action="/admin/users/demote" class="inline" onsubmit="return confirm('<?= h($confirmMessage) ?>');">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="user_id" value="<?= h($user['id']) ?>">
                                                        <input type="hidden" name="organization_id" value="<?= h($orgId) ?>">
                                                        <button type="submit" class="px-2 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 focus:outline-none <?= $isLastAdmin ? 'ring-2 ring-yellow-300' : '' ?>" title="Demote to User">
                                                            Demote
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="/admin/users/promote" class="inline" onsubmit="return confirm('Are you sure you want to promote this user to admin?');">
                                                        <?= csrfField() ?>
                                                        <input type="hidden" name="user_id" value="<?= h($user['id']) ?>">
                                                        <input type="hidden" name="organization_id" value="<?= h($orgId) ?>">
                                                        <button type="submit" class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none" title="Promote to Admin">
                                                            Promote
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" action="/admin/users/deactivate" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this user? This will free up a seat.');">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="user_id" value="<?= h($user['id']) ?>">
                                                    <input type="hidden" name="organization_id" value="<?= h($orgId) ?>">
                                                    <button type="submit" class="px-2 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none" title="Deactivate User">
                                                        Deactivate
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../templates/layout.php';
?>
