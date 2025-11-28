<?php
// Create new strategic plan section
requireOrganizationAdmin();

$sectionModel = new StrategicPlanSection();
$goalModel = new Goal();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$errors = [];
$formData = [];

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();
        $formData = sanitizeInput($postData);
        $formData['organization_id'] = $organizationId;
        $formData['created_by'] = $currentUser['id'];
        // Sanitize rich text content
        if (isset($postData['content'])) {
            $formData['content'] = sanitizeRichText($postData['content']);
        }

        $errors = $sectionModel->validate($formData);

        if (empty($errors)) {
            try {
                // Get active plan for this organization
                $planModel = new StrategicPlan();
                $activePlans = $planModel->getAll(['organization_id' => $organizationId, 'is_active' => true]);
                if (!empty($activePlans)) {
                    $formData['plan_id'] = $activePlans[0]['id'];
                } elseif (!empty(getQueryParam('plan_id'))) {
                    $formData['plan_id'] = getQueryParam('plan_id');
                }

                $sectionId = $sectionModel->create($formData);
                setFlashMessage('success', 'Section created successfully!');
                redirect('/sections');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to create section: ' . $e->getMessage();
            }
        }
    }
}

$goals = $goalModel->getAll(['organization_id' => $organizationId]);
$sections = $sectionModel->getAll(['organization_id' => $organizationId]);
$nextSortOrder = count($sections);

$title = 'Create Section - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Strategic Plan Section</h1>
        <p class="mt-2 text-gray-600">
            Add a custom section or chapter to your strategic plan. Sections can provide context, background information, or relate to specific goals.
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="max-w-4xl">
        <?= csrfField() ?>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="title">Section Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= h($formData['title'] ?? '') ?>"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?= !empty($errors['title']) ? 'border-red-500' : '' ?>"
                        placeholder="e.g., Context and Background, Strategic Priorities, Implementation Approach"
                        required
                        autofocus
                    >
                    <?php if (!empty($errors['title'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['title']) ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-sm text-gray-500">The heading for this section in your strategic plan.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="content">Section Content *</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="12"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?= !empty($errors['content']) ? 'border-red-500' : '' ?>"
                        placeholder="Enter the content for this section. You can include multiple paragraphs, lists, or any other information that helps explain your strategic plan..."
                        required
                    ><?= h($formData['content'] ?? '') ?></textarea>
                    <?php initRichTextEditor('content', ['placeholder' => 'Enter the content for this section. You can include multiple paragraphs, lists, or any other information...']); ?>
                    <?php if (!empty($errors['content'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['content']) ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-sm text-gray-500">Use the formatting toolbar to add bold, italic, lists, and links. Press Enter to create new paragraphs with spacing.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="sort_order">Display Order</label>
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            value="<?= h($formData['sort_order'] ?? $nextSortOrder) ?>"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            min="0"
                        >
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first. Sections are displayed in order in your strategic plan.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="linked_goal_id">Link to Goal (Optional)</label>
                        <select
                            id="linked_goal_id"
                            name="linked_goal_id"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">— No link —</option>
                            <?php foreach ($goals as $goal): ?>
                                <option value="<?= $goal['id'] ?>" <?= ($formData['linked_goal_id'] ?? '') == $goal['id'] ? 'selected' : '' ?>>
                                    Goal <?= h($goal['number']) ?>: <?= h($goal['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['linked_goal_id'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= h($errors['linked_goal_id']) ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">Optionally link this section to a specific strategic goal. Leave blank for general sections.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <?= DesignSystem::button('Cancel', '/sections', 'cancel') ?>
            <?= DesignSystem::button('Create Section', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
