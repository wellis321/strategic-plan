<?php
// Strategic Plan Sections listing page
requireOrganizationAdmin();

$sectionModel = new StrategicPlanSection();
$goalModel = new Goal();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$sections = $sectionModel->getAll(['organization_id' => $organizationId]);
$goals = $goalModel->getAll(['organization_id' => $organizationId]);

$title = 'Strategic Plan Sections - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">Strategic Plan Sections</h1>
                <p class="mt-2 text-gray-600">
                    Create custom sections or chapters for your strategic plan. These can provide context, background information, or relate to specific goals.
                </p>
            </div>
            <div class="flex-shrink-0">
                <?= DesignSystem::button('Add New Section', '/sections/new', 'primary') ?>
            </div>
        </div>
    </header>

    <?php if (empty($sections)): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <p class="text-blue-800 mb-4">You haven't created any sections yet.</p>
            <p class="text-sm text-blue-700 mb-4">
                Sections allow you to add custom chapters to your strategic plan, such as "Context and Background", "Strategic Priorities", "Implementation Approach", or any other content that helps explain your plan.
            </p>
            <?= DesignSystem::button('Create Your First Section', '/sections/new', 'primary') ?>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Linked Goal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Preview</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($sections as $section): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= h($section['sort_order']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= h($section['title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($section['linked_goal_id']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Goal <?= h($section['goal_number']) ?>: <?= h($section['goal_title']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= h(substr(strip_tags($section['content']), 0, 100)) ?><?= strlen($section['content']) > 100 ? '...' : '' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="/sections/<?= $section['id'] ?>/edit" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <a href="/sections/<?= $section['id'] ?>/delete" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this section?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Tip:</strong> Sections are displayed in your strategic plan in the order shown above. You can edit a section to change its order.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
