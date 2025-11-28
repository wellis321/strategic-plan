<?php
// View single project page
requireLogin();

$projectModel = new Project();
$goalModel = new Goal();

$slug = getQueryParam('slug');

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];
if (!$slug) {
    setFlashMessage('error', 'Project slug is required');
    redirect('/projects');
}

$project = $projectModel->getBySlug($slug);
if (!$project) {
    setFlashMessage('error', 'Project not found');
    redirect('/projects');
}

// Verify project belongs to user's organization
if ($project['organization_id'] != $organizationId) {
    setFlashMessage('error', 'Access denied');
    redirect('/projects');
}

$goal = $goalModel->getById($project['goal_id']);

$title = h($project['title']) . ' - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'flex justify-between items-center' : '' ?>">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">
                <span class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_tag' : 'inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-2' ?>">
                    <?= h($project['project_number']) ?>
                </span>
                <?= h($project['title']) ?>
            </h1>
            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'space-x-2' : 'ds_button-group' ?>">
                <?php if (isOrganizationAdmin()): ?>
                    <?= DesignSystem::button('Edit', '/projects/edit?id=' . $project['id'], 'secondary') ?>
                <?php endif; ?>
                <?= DesignSystem::button('Back to Projects', '/projects', 'cancel') ?>
            </div>
        </div>
    </header>

    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-6' ?>">
        <!-- Project Details -->
        <?php
        $projectContent = "
            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                <h2 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium' : 'text-xl font-semibold text-gray-900 mb-4') . "'>Project Details</h2>

                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4') . "'>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Strategic Goal</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>
                            <a href='/goals/" . $goal['id'] . "' class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:underline' : '') . "'>
                                Goal " . h($goal['number']) . ": " . h($goal['title']) . "
                            </a>
                        </dd>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Project Group</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($project['project_group'] ?? 'Not specified') . "</dd>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Timeline</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . formatDateForDisplay($project['start_date']) . " - " . formatDateForDisplay($project['end_date']) . "</dd>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Meeting Frequency</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($project['meeting_frequency'] ?? 'Not specified') . "</dd>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Project Leads</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h(implode(', ', $project['leads'])) . "</dd>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Working Group Members</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h(implode(', ', $project['members'])) . "</dd>
                    </div>
                </div>

                " . (!empty($project['purposes']) ? "
                <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-4' : 'mt-4') . "'>
                    <h3 class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900 mb-2' : 'font-weight-bold mb-2') . "'>Project Purposes:</h3>
                    <ul class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'list-disc list-inside text-gray-600 space-y-1' : 'list-disc list-inside') . "'>
                        " . implode('', array_map(function($purpose) {
                            return "<li>" . h($purpose) . "</li>";
                        }, $project['purposes'])) . "
                    </ul>
                </div>
                " : "") . "
            </div>
        ";
        echo DesignSystem::card($projectContent, 'Project Information');
        ?>

        <!-- Milestones -->
        <?php if (!empty($project['milestones'])): ?>
            <div>
                <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-xl font-semibold text-gray-900 mb-4' ?>">
                    Project Milestones
                </h2>
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-4' ?>">
                    <?php foreach ($project['milestones'] as $milestone): ?>
                        <?php
                        $statusClass = getStatusBadgeClass($milestone['status']);
                        $milestoneContent = "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'flex justify-between items-start mb-3') . "'>
                                <h3 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-lg font-semibold text-gray-900') . "'>" . h($milestone['title']) . "</h3>
                                <span class='{$statusClass}'>" . formatStatus($milestone['status']) . "</span>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-3') . "'>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600') . "'>
                                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                        <dt>Target Date</dt>
                                        <dd>" . formatDateForDisplay($milestone['target_date']) . "</dd>
                                    </div>
                                    " . ($milestone['completed_date'] ? "
                                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                        <dt>Completed Date</dt>
                                        <dd>" . formatDateForDisplay($milestone['completed_date']) . "</dd>
                                    </div>
                                    " : "") . "
                                </div>
                                " . ($milestone['description'] ? "<p class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-2 text-gray-600' : 'mt-2') . "'>" . h($milestone['description']) . "</p>" : "") . "
                            </div>
                        ";
                        echo DesignSystem::card($milestoneContent);
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
