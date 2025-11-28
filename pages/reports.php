<?php
// Reports page
requireLogin();

$projectModel = new Project();
$goalModel = new Goal();

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$projects = $projectModel->getAll(['organization_id' => $organizationId]);
$goals = $goalModel->getAll(['organization_id' => $organizationId]);
$progressSummary = $projectModel->getProgressSummary(['organization_id' => $organizationId]);

$title = 'Reports - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Project Reports</h1>
    </header>

    <!-- Summary Statistics -->
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'grid grid-cols-1 md:grid-cols-4 gap-6 mb-8' ?>">
        <?php
        $stats = [
            ['label' => 'Total Projects', 'value' => count($projects), 'color' => 'blue'],
            ['label' => 'On Track', 'value' => $progressSummary['on_track_projects'] ?? 0, 'color' => 'green'],
            ['label' => 'At Risk', 'value' => ($progressSummary['at_risk_projects'] ?? 0) + ($progressSummary['delayed_projects'] ?? 0), 'color' => 'red'],
            ['label' => 'Avg Progress', 'value' => number_format($progressSummary['avg_progress'] ?? 0, 1) . '%', 'color' => 'purple'],
        ];

        foreach ($stats as $stat):
            $statContent = "
                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-center') . "'>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-3xl font-bold text-' . $stat['color'] . '-600' : '') . "'>" . h($stat['value']) . "</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : '') . "'>" . h($stat['label']) . "</dd>
                    </div>
                </div>
            ";
            echo DesignSystem::card($statContent, null, ['class' => DesignSystem::getCurrentSystem() === 'tailwind' ? 'p-6' : '']);
        endforeach;
        ?>
    </div>

    <!-- Projects by Status -->
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--6' : 'mb-8' ?>">
        <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium' : 'text-xl font-semibold text-gray-900 mb-4' ?>">Projects Overview</h2>

        <?php if (empty($projects)): ?>
            <?= DesignSystem::alert('No projects found.', 'info') ?>
        <?php else: ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-4' ?>">
                <?php foreach ($projects as $project): ?>
                    <?php
                    $progress = calculateProgress($project['milestones']);
                    $reportContent = "
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
                                    <dt>Goal</dt>
                                    <dd>" . h($project['goal_number'] . ': ' . $project['goal_title']) . "</dd>
                                </div>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                    <dt>Timeline</dt>
                                    <dd>" . formatDateForDisplay($project['start_date']) . " - " . formatDateForDisplay($project['end_date']) . "</dd>
                                </div>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                    <dt>Milestones</dt>
                                    <dd>" . count($project['milestones']) . " " . pluralize(count($project['milestones']), 'milestone') . "</dd>
                                </div>
                            </div>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'flex justify-end pt-4 border-t') . "'>
                            " . DesignSystem::button('View Details', '/projects/' . $project['slug'], 'primary') . "
                        </div>
                    ";
                    echo DesignSystem::card($reportContent);
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
