<?php
// Goals listing page
requireLogin();

$goalModel = new Goal();
$projectModel = new Project();

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

// Get goals (filtered by organization)
$goals = $goalModel->getAll(['organization_id' => $organizationId]);

$title = 'Goals - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'flex justify-between items-center mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Strategic Goals</h1>
        <?php if (isOrganizationAdmin()): ?>
            <?= DesignSystem::button('New Goal', '/goals/new', 'primary') ?>
        <?php endif; ?>
    </header>

    <?php if (empty($goals)): ?>
        <?php if (isOrganizationAdmin()): ?>
            <?= DesignSystem::alert('No goals found. <a href="/goals/new">Create your first goal</a>.', 'info') ?>
        <?php else: ?>
            <?= DesignSystem::alert('No goals found.', 'info') ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-6' ?>">
            <?php foreach ($goals as $goal): ?>
                <?php
                $projectCount = $goalModel->getProjectCount($goal['id']);
                $goalContent = "
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'mb-3') . "'>
                        <h2 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-lg font-semibold text-gray-900') . "'>
                            Goal " . h($goal['number']) . ": " . h($goal['title']) . "
                        </h2>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600 mb-3' : 'mb-3') . "'>" . displayRichText($goal['description'] ?? '') . "</div>
                        " . (!empty($goal['statements']) ? "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mb-3' : 'mb-3') . "'>
                            <h4 class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900 mb-2' : 'font-weight-bold mb-2') . "'>Goal Statements:</h4>
                            <ul class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'list-disc list-inside text-sm text-gray-600 space-y-1' : 'list-disc list-inside') . "'>
                                " . implode('', array_map(function($stmt) {
                                    return "<li>" . displayRichText($stmt) . "</li>";
                                }, $goal['statements'])) . "
                            </ul>
                        </div>
                        " : "") . "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600') . "'>
                            " . (!empty($goal['responsible_director']) ? "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Responsible Senior manager</dt>
                                <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($goal['responsible_director']) . "</dd>
                            </div>
                            " : "") . "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Projects</dt>
                                <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>{$projectCount} " . pluralize($projectCount, 'project') . "</dd>
                            </div>
                        </div>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'flex justify-end space-x-2 pt-4 border-t') . "'>
                        " . DesignSystem::button('View Details', '/goals/' . $goal['id'], 'primary') . "
                        " . (isOrganizationAdmin() ? DesignSystem::button('Edit', '/goals/edit?id=' . $goal['id'], 'secondary') : '') . "
                    </div>
                ";
                echo DesignSystem::card($goalContent);
                ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
