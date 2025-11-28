<?php
// Edit strategic plan
requireOrganizationAdmin();

$planModel = new StrategicPlan();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];
$organization = (new Organization())->getById($organizationId);

$id = getQueryParam('id');
if (!$id) {
    setFlashMessage('error', 'Plan ID is required');
    redirect('/plans');
}

$plan = $planModel->getById($id, $organizationId);
if (!$plan) {
    setFlashMessage('error', 'Plan not found');
    redirect('/plans');
}

$errors = [];
$formData = $plan;

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();
        $formData = sanitizeInput($postData);
        $formData['organization_id'] = $organizationId;
        $formData['id'] = $id;

        $errors = $planModel->validate($formData);

        if (empty($errors)) {
            try {
                $planModel->update($id, $formData, $organizationId);
                setFlashMessage('success', 'Strategic plan updated successfully!');
                redirect('/plans');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to update plan: ' . $e->getMessage();
            }
        }
    }
}

$title = 'Edit Strategic Plan - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Strategic Plan</h1>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="max-w-4xl">
        <?= csrfField() ?>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="title">Plan Title *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="slug">URL Slug *</label>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500 mr-2">/<?= h($organization['slug'] ?? 'org') ?>/</span>
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="<?= h($formData['slug'] ?? '') ?>"
                            class="flex-1 block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?= !empty($errors['slug']) ? 'border-red-500' : '' ?>"
                            required
                            pattern="[a-z0-9-]+"
                            title="Only lowercase letters, numbers, and hyphens allowed"
                        >
                    </div>
                    <?php if (!empty($errors['slug'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['slug']) ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-sm text-gray-500">The URL slug for this plan. Changing this will change the public URL.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="start_year">Start Year (Optional)</label>
                        <input
                            type="number"
                            id="start_year"
                            name="start_year"
                            value="<?= h($formData['start_year'] ?? '') ?>"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            min="2000"
                            max="2100"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="end_year">End Year (Optional)</label>
                        <input
                            type="number"
                            id="end_year"
                            name="end_year"
                            value="<?= h($formData['end_year'] ?? '') ?>"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 <?= !empty($errors['end_year']) ? 'border-red-500' : '' ?>"
                            min="2000"
                            max="2100"
                        >
                        <?php if (!empty($errors['end_year'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= h($errors['end_year']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="status">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="draft" <?= ($formData['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($formData['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= ($formData['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                <?= ($formData['is_active'] ?? false) ? 'checked' : '' ?>
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Set as active plan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <?= DesignSystem::button('Cancel', '/plans', 'cancel') ?>
            <?= DesignSystem::button('Update Plan', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
