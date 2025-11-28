<?php
// Top Sections listing page
requireOrganizationAdmin();

$topSectionModel = new OrganizationTopSection();
$orgModel = new Organization();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];
$organization = $orgModel->getById($organizationId);

$sections = $topSectionModel->getAll(['organization_id' => $organizationId]);

$title = 'Top Sections - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">Top Sections</h1>
                <p class="mt-1 text-sm text-gray-500">
                    For: <?= h($organization['name'] ?? 'Your Organisation') ?>
                </p>
                <p class="mt-2 text-gray-600">
                    Create custom sections that appear at the top of your strategic plan, before your goals. These can include hero banners, about sections, or any custom content with images.
                </p>
            </div>
            <div class="flex-shrink-0">
                <?= DesignSystem::button('Add New Section', '/top-sections/new', 'primary') ?>
            </div>
        </div>
    </header>

    <?php if (empty($sections)): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
            <p class="text-blue-800 mb-4">You haven't created any top sections yet.</p>
            <p class="text-sm text-blue-700 mb-4">
                Top sections appear at the very beginning of your strategic plan, before your goals. You can create hero banners, about sections, or any custom content with images and rich text.
            </p>
            <?= DesignSystem::button('Create Your First Top Section', '/top-sections/new', 'primary') ?>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="sections-list">
                        <?php foreach ($sections as $section): ?>
                            <tr data-id="<?= $section['id'] ?>" class="cursor-move hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?= h($section['sort_order']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= ucfirst(h($section['section_type'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= h($section['title'] ?: 'Untitled Section') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (!empty($section['plan_id'])): ?>
                                        <?php
                                        $planModel = new StrategicPlan();
                                        $plan = $planModel->getById($section['plan_id']);
                                        ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <?= h($plan['title'] ?? 'Plan #' . $section['plan_id']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            All Plans
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($section['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($section['image_path']): ?>
                                        <span class="text-green-600">✓</span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="/top-sections/<?= $section['id'] ?>/edit" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <a href="/top-sections/<?= $section['id'] ?>/delete" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this section?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Tip:</strong> Sections are displayed in your strategic plan in the order shown above. Drag rows to reorder, or edit a section to change its order number.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const list = document.getElementById('sections-list');
    if (list) {
        const sortable = Sortable.create(list, {
            handle: 'tr',
            animation: 150,
            filter: 'a, button',
            onEnd: function(evt) {
                const items = Array.from(list.querySelectorAll('tr[data-id]'));
                const order = {};
                items.forEach((item, index) => {
                    const id = item.getAttribute('data-id');
                    if (!id) {
                        console.error('Missing data-id on row:', item);
                        return;
                    }
                    order[id] = index;
                });

                console.log('Sending reorder request:', order);

                fetch('/api/top-sections/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update order numbers in UI
                        items.forEach((item, index) => {
                            const orderCell = item.querySelector('td:first-child span');
                            if (orderCell) {
                                orderCell.textContent = index;
                            }
                        });
                    } else {
                        alert('Failed to update order: ' + (data.message || 'Unknown error'));
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update order: ' + error.message + '. Please refresh the page.');
                    location.reload();
                });
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
