<?php
// Edit strategic plan section
requireOrganizationAdmin();

$sectionModel = new StrategicPlanSection();
$goalModel = new Goal();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$id = getQueryParam('id');
if (!$id) {
    setFlashMessage('error', 'Section ID is required');
    redirect('/sections');
}

$section = $sectionModel->getById($id, $organizationId);
if (!$section) {
    setFlashMessage('error', 'Section not found');
    redirect('/sections');
}

$errors = [];
$formData = $section;

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();
        $formData = sanitizeInput($postData);
        $formData['organization_id'] = $organizationId;
        // Sanitize rich text content
        if (isset($postData['content'])) {
            $formData['content'] = sanitizeRichText($postData['content']);
        }

        $errors = $sectionModel->validate($formData);

        if (empty($errors)) {
            try {
                $sectionModel->update($id, $formData, $organizationId);
                setFlashMessage('success', 'Section updated successfully!');
                redirect('/sections');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to update section: ' . $e->getMessage();
            }
        }
    }
}

$goals = $goalModel->getAll(['organization_id' => $organizationId]);

$title = 'Edit Section - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Strategic Plan Section</h1>
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
                        required
                        autofocus
                    >
                    <?php if (!empty($errors['title'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['title']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="content">Section Content *</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="12"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?= !empty($errors['content']) ? 'border-red-500' : '' ?>"
                        required
                    ><?= h($formData['content'] ?? '') ?></textarea>
                    <?php initRichTextEditor('content', ['placeholder' => 'Enter the content for this section...']); ?>
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
                            value="<?= h($formData['sort_order'] ?? 0) ?>"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            min="0"
                        >
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <?= DesignSystem::button('Cancel', '/sections', 'cancel') ?>
            <?= DesignSystem::button('Update Section', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
