<?php
// View strategic plan (internal view for admins)
requireOrganizationAdmin();

$planModel = new StrategicPlan();
$goalModel = new Goal();
$projectModel = new Project();
$sectionModel = new StrategicPlanSection();
$orgModel = new Organization();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

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

$organization = $orgModel->getByIdWithValues($organizationId);
$goals = $goalModel->getAll(['plan_id' => $plan['id']]);
$projects = $projectModel->getAll(['plan_id' => $plan['id']]);
$sections = $sectionModel->getAll(['plan_id' => $plan['id']]);

// Group projects by goal
$projectsByGoal = [];
foreach ($projects as $project) {
    $goalId = $project['goal_id'];
    if (!isset($projectsByGoal[$goalId])) {
        $projectsByGoal[$goalId] = [];
    }
    $projectsByGoal[$goalId][] = $project;
}

$title = $plan['title'] . ' - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= h($plan['title']) ?></h1>
                <p class="mt-2 text-gray-600">
                    <?php if ($plan['start_year'] || $plan['end_year']): ?>
                        <?php if ($plan['start_year'] && $plan['end_year']): ?>
                            <?= h($plan['start_year']) ?> - <?= h($plan['end_year']) ?>
                        <?php elseif ($plan['start_year']): ?>
                            <?= h($plan['start_year']) ?> onwards
                        <?php elseif ($plan['end_year']): ?>
                            until <?= h($plan['end_year']) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    Public URL: <a href="/<?= h($organization['slug'] ?? 'org') ?>/<?= h($plan['slug']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800">/<?= h($organization['slug'] ?? 'org') ?>/<?= h($plan['slug']) ?></a>
                </p>
            </div>
            <div>
                <?= DesignSystem::button('Edit Plan', '/plans/' . $plan['id'] . '/edit', 'secondary') ?>
            </div>
        </div>
    </header>

    <?php if (!empty($organization['about_us']) || !empty($organization['vision']) || !empty($organization['mission']) || !empty($organization['values'])): ?>
        <!-- About Us, Vision, Mission, Values Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <?php if (!empty($organization['about_us'])): ?>
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">About Us</h2>
                    <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($organization['about_us']) ?></div>
                </div>
                <?php if (!empty($organization['vision']) || !empty($organization['mission']) || !empty($organization['values'])): ?>
                    <hr class="my-6 border-gray-200">
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($organization['vision'])): ?>
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Our Vision</h2>
                    <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($organization['vision']) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($organization['mission'])): ?>
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Our Mission</h2>
                    <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($organization['mission']) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($organization['values'])): ?>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Our Values</h2>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        <?php foreach ($organization['values'] as $value): ?>
                            <li><?= h($value['value_text']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($sections)): ?>
        <!-- Strategic Plan Sections -->
        <div class="mb-8">
            <?php foreach ($sections as $section): ?>
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <?= h($section['title']) ?>
                            <?php if ($section['linked_goal_id']): ?>
                                <span class="ml-2 text-sm font-normal text-blue-600">
                                    (Related to Goal <?= h($section['goal_number']) ?>)
                                </span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div>
                        <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($section['content']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="text-2xl font-bold text-gray-900 mb-6">Strategic Goals</h2>

    <?php if (empty($goals)): ?>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
            <p class="text-gray-600">No strategic goals have been added to this plan yet.</p>
            <?php if (isOrganizationAdmin()): ?>
                <a href="/goals/new?plan_id=<?= $plan['id'] ?>" class="inline-block mt-4 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Create Your First Goal
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($goals as $goal): ?>
            <div class="mb-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">
                            Goal <?= h($goal['number']) ?>: <?= h($goal['title']) ?>
                        </h3>
                    </div>
                    <div class="mb-4">
                        <div class="text-gray-600 mb-4"><?= displayRichText($goal['description'] ?? '') ?></div>
                        <?php if (!empty($goal['statements'])): ?>
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-900 mb-2">Goal Statements:</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    <?php foreach ($goal['statements'] as $stmt): ?>
                                        <li><?= displayRichText($stmt) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="text-sm text-gray-600 mb-4">
                            <?php if (!empty($goal['responsible_director'])): ?>
                            <div class="mb-2">
                                <dt class="inline font-medium">Responsible Senior manager:</dt>
                                <dd class="inline"> <?= h($goal['responsible_director']) ?></dd>
                            </div>
                            <?php endif; ?>
                            <div>
                                <dt class="inline font-medium">Projects:</dt>
                                <dd class="inline"> <?= count($projectsByGoal[$goal['id']] ?? []) ?> <?= pluralize(count($projectsByGoal[$goal['id']] ?? []), 'project') ?></dd>
                            </div>
                        </div>

                        <?php
                        $goalProjects = $projectsByGoal[$goal['id']] ?? [];
                        if (!empty($goalProjects)):
                        ?>
                            <div class="mt-4">
                                <h4 class="font-medium text-gray-900 mb-3">Associated Projects:</h4>
                                <div class="space-y-2">
                                    <?php foreach ($goalProjects as $project): ?>
                                        <?php $progress = calculateProgress($project['milestones']); ?>
                                        <div class="bg-gray-50 p-3 rounded">
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?= h($project['project_number']) ?></span>
                                                    <strong class="text-gray-900"><?= h($project['title']) ?></strong>
                                                </div>
                                                <span class="text-sm text-gray-500"><?= $progress ?>%</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No projects associated with this goal yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
