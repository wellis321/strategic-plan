<?php
// Edit project page
requireOrganizationAdmin();

$projectModel = new Project();
$goalModel = new Goal();
$id = getQueryParam('id');

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

if (!$id) {
    setFlashMessage('error', 'Project ID is required');
    redirect('/projects');
}

$project = $projectModel->getById($id);
if (!$project) {
    setFlashMessage('error', 'Project not found');
    redirect('/projects');
}

// Verify project belongs to user's organization
if ($project['organization_id'] != $organizationId) {
    setFlashMessage('error', 'Access denied');
    redirect('/projects');
}

$errors = [];
$formData = $project;
$formData['leads'] = is_array($project['leads']) ? implode(', ', $project['leads']) : '';
$formData['members'] = is_array($project['members']) ? implode(', ', $project['members']) : '';
$formData['purposes'] = is_array($project['purposes']) ? implode("\n", $project['purposes']) : '';

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $formData = sanitizeInput(getPostData());
        $formData['id'] = $id;

        if (!empty($formData['leads'])) {
            $formData['leads'] = array_map('trim', explode(',', $formData['leads']));
        }
        if (!empty($formData['members'])) {
            $formData['members'] = array_map('trim', explode(',', $formData['members']));
        }
        if (!empty($formData['purposes'])) {
            $formData['purposes'] = array_filter(array_map('trim', explode("\n", $formData['purposes'])));
        }

        $errors = $projectModel->validate($formData);

        if (empty($errors)) {
            try {
                $projectModel->update($id, $formData);
                setFlashMessage('success', 'Project updated successfully!');
                redirect('/projects');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to update project: ' . $e->getMessage();
            }
        }
    }
}

$goals = $goalModel->getAll(['organization_id' => $organizationId]);

$title = 'Edit Project - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Edit Project</h1>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-4xl' : '' ?>">
        <?= csrfField() ?>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-lg font-medium text-gray-900 mb-4' ?>">Basic Information</h2>

            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'ds_field-group' ?>">
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="title">Project Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= h($formData['title'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        required
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="project_number">Project Number *</label>
                    <input
                        type="text"
                        id="project_number"
                        name="project_number"
                        value="<?= h($formData['project_number'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        required
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="goal_id">Strategic Goal *</label>
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select-wrapper' : '' ?>">
                        <select
                            id="goal_id"
                            name="goal_id"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                            required
                        >
                            <option value="">Select a goal</option>
                            <?php foreach ($goals as $goal): ?>
                                <option value="<?= h($goal['id']) ?>" <?= ($formData['goal_id'] ?? '') == $goal['id'] ? 'selected' : '' ?>>
                                    Goal <?= h($goal['number']) ?>: <?= h($goal['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="project_group">Project Group</label>
                    <input
                        type="text"
                        id="project_group"
                        name="project_group"
                        value="<?= h($formData['project_group'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="start_date">Start Date</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="<?= h($formData['start_date'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="end_date">End Date</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="<?= h($formData['end_date'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgdb' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="leads">Project Leads</label>
                    <input
                        type="text"
                        id="leads"
                        name="leads"
                        value="<?= h($formData['leads'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Separate multiple leads with commas"
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="purposes">Project Purposes</label>
                    <textarea
                        id="purposes"
                        name="purposes"
                        rows="4"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Enter each purpose on a new line"
                    ><?= h($formData['purposes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'flex justify-end space-x-3' ?>">
            <?= DesignSystem::button('Cancel', '/projects', 'cancel') ?>
            <?= DesignSystem::button('Update Project', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
