<?php
// Strategic Plans listing page
requireOrganizationAdmin();

$planModel = new StrategicPlan();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];
$organization = (new Organization())->getById($organizationId);

$plans = $planModel->getAll(['organization_id' => $organizationId]);

$title = 'Strategic Plans - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Strategic Plans</h1>
                <p class="mt-2 text-gray-600">
                    Manage multiple strategic plans for your organisation. Each plan can have its own URL (e.g., /<?= h($organization['slug'] ?? 'your-org') ?>/2025-2030plan).
                </p>
            </div>
            <div>
                <?= DesignSystem::button('Create New Plan', '/plans/new', 'primary') ?>
            </div>
        </div>
    </header>

    <?php if (empty($plans)): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <p class="text-blue-800 mb-4">You haven't created any strategic plans yet.</p>
            <p class="text-sm text-blue-700 mb-4">
                Create multiple strategic plans for different time periods. Each plan gets its own URL that you can share publicly or keep private.
            </p>
            <?= DesignSystem::button('Create Your First Plan', '/plans/new', 'primary') ?>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($plans as $plan): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= h($plan['title']) ?></div>
                                    <?php if ($plan['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                            Active
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">
                                        /<?= h($organization['slug'] ?? 'org') ?>/<?= h($plan['slug']) ?>
                                    </div>
                                    <a href="/<?= h($organization['slug'] ?? 'org') ?>/<?= h($plan['slug']) ?>" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                                        View public page →
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($plan['start_year'] || $plan['end_year']): ?>
                                        <?php if ($plan['start_year'] && $plan['end_year']): ?>
                                            <?= h($plan['start_year']) ?> - <?= h($plan['end_year']) ?>
                                        <?php elseif ($plan['start_year']): ?>
                                            <?= h($plan['start_year']) ?> onwards
                                        <?php elseif ($plan['end_year']): ?>
                                            until <?= h($plan['end_year']) ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'published' => 'bg-green-100 text-green-800',
                                        'draft' => 'bg-yellow-100 text-yellow-800',
                                        'archived' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusColor = $statusColors[$plan['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                        <?= ucfirst($plan['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= h($plan['goals_count']) ?> goals, <?= h($plan['projects_count']) ?> projects
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="/plans/<?= $plan['id'] ?>/edit" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <a href="/plans/<?= $plan['id'] ?>/view" class="text-green-600 hover:text-green-900">View</a>
                                        <a href="/plans/<?= $plan['id'] ?>/delete" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this plan? This cannot be undone.')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Tip:</strong> Only one plan can be "active" at a time. The active plan is used as the default when viewing your strategic plan from within the system.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
