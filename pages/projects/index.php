<?php
// Projects listing page
requireLogin();

$projectModel = new Project();
$goalModel = new Goal();

// Get current user's organization
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

// Get filters from query parameters
$filters = [
    'goal_id' => getQueryParam('goal_id'),
    'search' => getQueryParam('search')
];

// Get projects and goals (filtered by organization)
$filters['organization_id'] = $organizationId;
$projects = $projectModel->getAll($filters);
$goals = $goalModel->getAll(['organization_id' => $organizationId]);

$title = 'Projects - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'flex justify-between items-center mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Strategic Plan Projects</h1>
        <?php if (isOrganizationAdmin()): ?>
            <?= DesignSystem::button('New Project', '/projects/new', 'primary') ?>
        <?php endif; ?>
    </header>

    <!-- Search and Filter -->
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_search-box ds_!_margin-bottom--4' : 'bg-white p-6 rounded-lg shadow mb-6' ?>">
        <form method="GET" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'space-y-4' : '' ?>">
            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : '' ?>">
                <div>
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="search">Search projects</label>
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input__wrapper' : 'relative' ?>">
                        <input
                            name="search"
                            value="<?= h($filters['search'] ?? '') ?>"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                            type="text"
                            id="search"
                            placeholder="Search by title or reference"
                        >
                        <button type="submit" class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button' : 'absolute inset-y-0 right-0 px-3 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700' ?>">
                            <?php if (DesignSystem::getCurrentSystem() === 'sgds'): ?>
                                <span class="visually-hidden">Search</span>
                                <svg class="ds_icon" aria-hidden="true" role="img"><use href="/static/images/icons/icons.stack.svg#search"></use></svg>
                            <?php else: ?>
                                Search
                            <?php endif; ?>
                        </button>
                    </div>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select-wrapper ds_!_margin-top--3' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="goal_id">Filter by goal</label>
                    <select
                        name="goal_id"
                        id="goal_id"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        onchange="this.form.submit()"
                    >
                        <option value="">All Goals</option>
                        <?php foreach ($goals as $goal): ?>
                            <option value="<?= h($goal['id']) ?>" <?= $filters['goal_id'] == $goal['id'] ? 'selected' : '' ?>>
                                Goal <?= h($goal['number']) ?>: <?= h($goal['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Projects List -->
    <?php if (empty($projects)): ?>
        <?php if (!empty($filters['search']) || !empty($filters['goal_id'])): ?>
            <?= DesignSystem::alert('No projects found matching your criteria. <a href="/projects">Clear filters</a> or <a href="/projects/new">create a new project</a>.', 'info') ?>
        <?php else: ?>
            <?= DesignSystem::alert('No projects found. <a href="/projects/new">Create your first project</a>.', 'info') ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-6' ?>">
            <?php foreach ($projects as $project): ?>
                <?php
                $progress = calculateProgress($project['milestones']);
                $projectContent = "
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'flex justify-between items-start mb-4') . "'>
                        <div>
                            <h2 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-lg font-semibold text-gray-900') . "'>
                                <span class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_tag' : 'inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-2') . "'>
                                    " . h($project['project_number']) . "
                                </span>
                                " . h($project['title']) . "
                            </h2>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-right' : '') . "'>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500 mb-1' : '') . "'>Progress</div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-lg font-semibold text-blue-600' : '') . "'>{$progress}%</div>
                        </div>
                    </div>

                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'grid grid-cols-1 md:grid-cols-2 gap-4 text-sm') . "'>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Goal</dt>
                                <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($project['goal_number'] . ': ' . $project['goal_title']) . "</dd>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Lead</dt>
                                <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h(implode(', ', $project['leads'])) . "</dd>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Group</dt>
                                <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($project['project_group'] ?? 'Not specified') . "</dd>
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Timeline</dt>
                                <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . formatDateForDisplay($project['start_date']) . " - " . formatDateForDisplay($project['end_date']) . "</dd>
                            </div>
                        </div>

                        " . (!empty($project['purposes']) ? "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-4' : 'mt-3') . "'>
                            <h4 class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900 mb-2' : 'font-weight-bold mb-2') . "'>Purpose:</h4>
                            <ul class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'list-disc list-inside text-sm text-gray-600 space-y-1' : 'list-disc list-inside') . "'>
                                " . implode('', array_map(function($purpose) {
                                    return "<li>" . h($purpose) . "</li>";
                                }, array_slice($project['purposes'], 0, 3))) . "
                                " . (count($project['purposes']) > 3 ? "<li class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-500' : '') . "'>... and " . (count($project['purposes']) - 3) . " more</li>" : "") . "
                            </ul>
                        </div>
                        " : "") . "
                    </div>

                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'flex justify-between items-center pt-4 border-t') . "'>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'flex space-x-2' : '') . "'>
                            " . DesignSystem::button('View Details', '/projects/' . $project['slug'], 'primary') . "
                            " . (isOrganizationAdmin() ? DesignSystem::button('Edit', '/projects/edit?id=' . $project['id'], 'secondary') : '') . "
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500' : '') . "'>
                            " . count($project['milestones']) . " " . pluralize(count($project['milestones']), 'milestone') . "
                        </div>
                    </div>
                ";
                echo DesignSystem::card($projectContent);
                ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
