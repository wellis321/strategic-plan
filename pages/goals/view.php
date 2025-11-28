<?php
// View single goal page
requireLogin();

$goalModel = new Goal();
$projectModel = new Project();

$id = getQueryParam('id');

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];
if (!$id) {
    setFlashMessage('error', 'Goal ID is required');
    redirect('/goals');
}

$goal = $goalModel->getById($id);
if (!$goal) {
    setFlashMessage('error', 'Goal not found');
    redirect('/goals');
}

// Verify goal belongs to user's organization
if ($goal['organization_id'] != $organizationId) {
    setFlashMessage('error', 'Access denied');
    redirect('/goals');
}

$projects = $projectModel->getAll(['goal_id' => $id, 'organization_id' => $organizationId]);

$title = 'Goal ' . h($goal['number']) . ' - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'flex justify-between items-center' : '' ?>">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">
                Goal <?= h($goal['number']) ?>: <?= h($goal['title']) ?>
            </h1>
            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'space-x-2' : 'ds_button-group' ?>">
                <?php if (isOrganizationAdmin()): ?>
                    <?= DesignSystem::button('Edit', '/goals/edit?id=' . $goal['id'], 'secondary') ?>
                <?php endif; ?>
                <?= DesignSystem::button('Back to Goals', '/goals', 'cancel') ?>
            </div>
        </div>
    </header>

    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-6' ?>">
        <!-- Goal Details -->
        <?php
        $goalContent = "
            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                <h2 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium' : 'text-xl font-semibold text-gray-900 mb-4') . "'>Goal Details</h2>
                <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600 mb-4' : 'mb-4') . "'>" . displayRichText($goal['description'] ?? '') . "</div>

                " . (!empty($goal['statements']) ? "
                <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mb-4' : 'mb-4') . "'>
                    <h3 class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900 mb-2' : 'font-weight-bold mb-2') . "'>Goal Statements:</h3>
                    <ul class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'list-disc list-inside text-gray-600 space-y-1' : 'list-disc list-inside') . "'>
                        " . implode('', array_map(function($stmt) {
                            return "<li>" . displayRichText($stmt) . "</li>";
                        }, $goal['statements'])) . "
                    </ul>
                </div>
                " : "") . "

                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600') . "'>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                            <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Responsible Senior manager</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($goal['responsible_director']) . "</dd>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Created</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . formatDateForDisplay($goal['created_at']) . "</dd>
                    </div>
                </div>
            </div>
        ";
        echo DesignSystem::card($goalContent, 'Goal Information');
        ?>

        <!-- Associated Projects -->
        <div>
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-xl font-semibold text-gray-900 mb-4' ?>">
                Associated Projects (<?= count($projects) ?>)
            </h2>

            <?php if (empty($projects)): ?>
                <?= DesignSystem::alert('No projects associated with this goal. <a href="/projects/new">Create a new project</a>.', 'info') ?>
            <?php else: ?>
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-4' ?>">
                    <?php foreach ($projects as $project): ?>
                        <?php
                        $progress = calculateProgress($project['milestones']);
                        $projectContent = "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'flex justify-between items-start mb-4') . "'>
                                <div>
                                    <h3 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-lg font-semibold text-gray-900') . "'>
                                        <span class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_tag' : 'inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-2') . "'>
                                            " . h($project['project_number']) . "
                                        </span>
                                        " . h($project['title']) . "
                                    </h3>
                                </div>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-right' : '') . "'>
                                    <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500 mb-1' : '') . "'>Progress</div>
                                    <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-lg font-semibold text-blue-600' : '') . "'>{$progress}%</div>
                                </div>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600') . "'>
                                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                        <dt>Timeline</dt>
                                        <dd>" . formatDateForDisplay($project['start_date']) . " - " . formatDateForDisplay($project['end_date']) . "</dd>
                                    </div>
                                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                        <dt>Leads</dt>
                                        <dd>" . h(implode(', ', $project['leads'])) . "</dd>
                                    </div>
                                </div>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'flex justify-end pt-4 border-t') . "'>
                                " . DesignSystem::button('View Project', '/projects/' . $project['slug'], 'primary') . "
                            </div>
                        ";
                        echo DesignSystem::card($projectContent);
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
