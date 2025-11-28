<?php
// Create new project page
requireOrganizationAdmin();

$projectModel = new Project();
$goalModel = new Goal();

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$errors = [];
$formData = [];

if (isPost()) {
    // Validate CSRF token
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $formData = sanitizeInput(getPostData());

        // Convert comma-separated strings to arrays
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
                // Set organization and creator
                $currentUser = getCurrentUser();
                $formData['organization_id'] = $currentUser['organization_id'];
                $formData['created_by'] = $currentUser['id'];

                // Get active plan for this organization
                $planModel = new StrategicPlan();
                $activePlans = $planModel->getAll(['organization_id' => $currentUser['organization_id'], 'is_active' => true]);
                if (!empty($activePlans)) {
                    $formData['plan_id'] = $activePlans[0]['id'];
                } elseif (!empty(getQueryParam('plan_id'))) {
                    $formData['plan_id'] = getQueryParam('plan_id');
                }

                $projectId = $projectModel->create($formData);
                setFlashMessage('success', 'Project created successfully!');
                redirect('/projects');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to create project: ' . $e->getMessage();
            }
        }
    }
}

// Get goals for dropdown (filtered by organization)
$goals = $goalModel->getAll(['organization_id' => $organizationId]);

$title = 'New Project - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <div class="flex justify-between items-center">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Create New Project</h1>
            <div>
                <?= DesignSystem::button('View Template', '/template', 'secondary', ['class' => 'text-sm']) ?>
            </div>
        </div>
        <p class="mt-2 text-sm text-gray-600">
            Not sure what these fields mean? <a href="/template" class="text-blue-600 hover:text-blue-800 underline">View the template and field guide</a> to see examples and understand each field.
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-4xl' : '' ?>">
        <?= csrfField() ?>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-lg font-medium text-gray-900 mb-4' ?>">Basic Information</h2>

            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'ds_field-group' ?>">
                <!-- Project Title -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="title">Project Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= h($formData['title'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['title']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                    >
                    <?php if (!empty($errors['title'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['title']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Project Number -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="project_number">Project Number *</label>
                    <input
                        type="text"
                        id="project_number"
                        name="project_number"
                        value="<?= h($formData['project_number'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['project_number']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        placeholder="e.g., 1.4"
                        required
                    >
                    <?php if (!empty($errors['project_number'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['project_number']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Goal -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="goal_id">Strategic Goal *</label>
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select-wrapper' : '' ?>">
                        <select
                            id="goal_id"
                            name="goal_id"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['goal_id']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select--error' : 'border-red-500') : '' ?>"
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
                    <?php if (!empty($errors['goal_id'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['goal_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-lg font-medium text-gray-900 mb-4' ?>">Project Details</h2>

            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'ds_field-group' ?>">
                <!-- Project Group -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="project_group">Project Group</label>
                    <input
                        type="text"
                        id="project_group"
                        name="project_group"
                        value="<?= h($formData['project_group'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="e.g., Digital Development Group"
                    >
                </div>

                <!-- Meeting Frequency -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="meeting_frequency">Meeting Frequency</label>
                    <input
                        type="text"
                        id="meeting_frequency"
                        name="meeting_frequency"
                        value="<?= h($formData['meeting_frequency'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="e.g., Monthly, Quarterly"
                    >
                </div>

                <!-- Start Date -->
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

                <!-- End Date -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="end_date">End Date</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="<?= h($formData['end_date'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                    >
                </div>

                <!-- Project Leads -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="leads">Project Leads</label>
                    <input
                        type="text"
                        id="leads"
                        name="leads"
                        value="<?= h(is_array($formData['leads'] ?? '') ? implode(', ', $formData['leads']) : ($formData['leads'] ?? '')) ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Separate multiple leads with commas"
                    >
                    <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Separate multiple leads with commas</p>
                </div>

                <!-- Working Group Members -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="members">Working Group Members</label>
                    <input
                        type="text"
                        id="members"
                        name="members"
                        value="<?= h(is_array($formData['members'] ?? '') ? implode(', ', $formData['members']) : ($formData['members'] ?? '')) ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Separate multiple members with commas"
                    >
                    <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Separate multiple members with commas</p>
                </div>

                <!-- Project Purposes -->
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="purposes">Project Purposes</label>
                    <textarea
                        id="purposes"
                        name="purposes"
                        rows="4"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Enter each purpose on a new line"
                    ><?= h(is_array($formData['purposes'] ?? '') ? implode("\n", $formData['purposes']) : ($formData['purposes'] ?? '')) ?></textarea>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Enter each purpose on a new line</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'flex justify-end space-x-3' ?>">
            <?= DesignSystem::button('Cancel', '/projects', 'cancel') ?>
            <?= DesignSystem::button('Create Project', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
