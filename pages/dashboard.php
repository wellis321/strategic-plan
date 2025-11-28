<?php
// Dashboard page
requireLogin();

$goalModel = new Goal();
$projectModel = new Project();

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

// Get summary data (filtered by organization)
$goals = $goalModel->getAll(['organization_id' => $organizationId]);
$projects = $projectModel->getAll(['organization_id' => $organizationId]);
$progressSummary = $projectModel->getProgressSummary(['organization_id' => $organizationId]);


$title = 'Dashboard - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Strategic Plan Dashboard</h1>
    </header>

    <!-- Summary Cards -->
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8' ?>">
        <!-- Total Projects Card -->
        <?php
        $projectsContent = "
            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-center') . "'>
                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-3xl font-bold text-blue-600' : '') . "'>" . count($projects) . "</dt>
                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : '') . "'>Total Projects</dd>
                </div>
            </div>
        ";
        echo DesignSystem::card($projectsContent, null, ['class' => DesignSystem::getCurrentSystem() === 'tailwind' ? 'p-6' : '']);
        ?>

        <!-- Total Goals Card -->
        <?php
        $goalsContent = "
            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-center') . "'>
                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-3xl font-bold text-green-600' : '') . "'>" . count($goals) . "</dt>
                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : '') . "'>Strategic Goals</dd>
                </div>
            </div>
        ";
        echo DesignSystem::card($goalsContent, null, ['class' => DesignSystem::getCurrentSystem() === 'tailwind' ? 'p-6' : '']);
        ?>

        <!-- Average Progress Card -->
        <?php
        $avgProgress = $progressSummary['avg_progress'] ?? 0;
        $progressContent = "
            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-center') . "'>
                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-3xl font-bold text-purple-600' : '') . "'>" . number_format($avgProgress, 1) . "%</dt>
                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : '') . "'>Average Progress</dd>
                </div>
            </div>
        ";
        echo DesignSystem::card($progressContent, null, ['class' => DesignSystem::getCurrentSystem() === 'tailwind' ? 'p-6' : '']);
        ?>

        <!-- Projects at Risk Card -->
        <?php
        $atRisk = ($progressSummary['at_risk_projects'] ?? 0) + ($progressSummary['delayed_projects'] ?? 0);
        $riskContent = "
            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-center') . "'>
                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-3xl font-bold text-red-600' : '') . "'>{$atRisk}</dt>
                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : '') . "'>At Risk/Delayed</dd>
                </div>
            </div>
        ";
        echo DesignSystem::card($riskContent, null, ['class' => DesignSystem::getCurrentSystem() === 'tailwind' ? 'p-6' : '']);
        ?>
    </div>

    <!-- Recent Projects -->
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--6' : 'mb-8' ?>">
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--3' : 'flex justify-between items-center mb-4' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium' : 'text-xl font-semibold text-gray-900' ?>">Recent Projects</h2>
            <?= DesignSystem::button('View All Projects', '/projects', 'secondary') ?>
        </div>

        <?php if (empty($projects)): ?>
            <?= DesignSystem::alert('No projects found. <a href="/projects/new">Create your first project</a>.', 'info') ?>
        <?php else: ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-4' ?>">
                <?php foreach (array_slice($projects, 0, 5) as $project): ?>
                    <?php
                    $progress = calculateProgress($project['milestones']);
                    $projectContent = "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'flex justify-between items-start mb-3') . "'>
                            <div>
                                <h3 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'font-medium text-gray-900') . "'>
                                    <span class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_tag' : 'inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2') . "'>
                                        " . h($project['project_number']) . "
                                    </span>
                                    " . h($project['title']) . "
                                </h3>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500' : '') . "'>
                                {$progress}% Complete
                            </div>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600') . "'>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                <dt>Goal</dt>
                                <dd>" . h($project['goal_number'] . ': ' . $project['goal_title']) . "</dd>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                <dt>Leads</dt>
                                <dd>" . h(implode(', ', $project['leads'])) . "</dd>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt>Timeline</dt>
                                <dd>" . formatDateForDisplay($project['start_date']) . " - " . formatDateForDisplay($project['end_date']) . "</dd>
                            </div>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'mt-4') . "'>
                            " . DesignSystem::button('View Details', '/projects/' . $project['slug'], 'primary') . "
                        </div>
                    ";
                    echo DesignSystem::card($projectContent);
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Strategic Goals Overview -->
    <div>
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--3' : 'flex justify-between items-center mb-4' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium' : 'text-xl font-semibold text-gray-900' ?>">Strategic Goals</h2>
            <?= DesignSystem::button('View All Goals', '/goals', 'secondary') ?>
        </div>

        <?php if (empty($goals)): ?>
            <?= DesignSystem::alert('No goals found. <a href="/goals/new">Create your first goal</a>.', 'info') ?>
        <?php else: ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-4' ?>">
                <?php foreach ($goals as $goal): ?>
                    <?php
                    $projectCount = $goalModel->getProjectCount($goal['id']);
                    $goalContent = "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'mb-3') . "'>
                            <h3 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'font-medium text-gray-900') . "'>
                                Goal " . h($goal['number']) . ": " . h($goal['title']) . "
                            </h3>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                            <p class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600 mb-3' : 'mb-3') . "'>" . h($goal['description']) . "</p>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600') . "'>
                                " . (!empty($goal['responsible_director']) ? "
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                    <dt>Responsible Senior manager</dt>
                                    <dd>" . h($goal['responsible_director']) . "</dd>
                                </div>
                                " : "") . "
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                    <dt>Projects</dt>
                                    <dd>{$projectCount} " . pluralize($projectCount, 'project') . "</dd>
                                </div>
                            </div>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'mt-4') . "'>
                            " . DesignSystem::button('View Goal', '/goals/' . $goal['id'], 'primary') . "
                        </div>
                    ";
                    echo DesignSystem::card($goalContent);
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
