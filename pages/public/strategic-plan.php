<?php
// Public strategic plan page - accessible via /org-slug/plan-slug
// No login required for published plans

$planModel = new StrategicPlan();
$goalModel = new Goal();
$projectModel = new Project();
$sectionModel = new StrategicPlanSection();
$topSectionModel = new OrganizationTopSection();
$orgModel = new Organization();

$orgSlug = getQueryParam('org_slug');
$planSlug = getQueryParam('plan_slug');

if (!$orgSlug || !$planSlug) {
    http_response_code(404);
    require __DIR__ . '/../404.php';
    exit;
}

// Get the plan (allow draft plans to be viewed publicly for now)
// TODO: Add proper access control - maybe require login for draft plans
$plan = $planModel->getBySlug($orgSlug, $planSlug, false);

if (!$plan) {
    http_response_code(404);
    require __DIR__ . '/../404.php';
    exit;
}

// Get organization details
$organization = $orgModel->getByIdWithValues($plan['organization_id']);

// Get plan data
$goals = $goalModel->getAll(['plan_id' => $plan['id']]);
$projects = $projectModel->getAll(['plan_id' => $plan['id']]);
$sections = $sectionModel->getAll(['plan_id' => $plan['id']]);
// Get top sections for this plan (plan-specific + organization-wide)
$topSections = $topSectionModel->getAll(['organization_id' => $plan['organization_id'], 'plan_id' => $plan['id'], 'is_active' => true]);

// Group projects by goal
$projectsByGoal = [];
foreach ($projects as $project) {
    $goalId = $project['goal_id'];
    if (!isset($projectsByGoal[$goalId])) {
        $projectsByGoal[$goalId] = [];
    }
    $projectsByGoal[$goalId][] = $project;
}

$title = $plan['title'] . ' - ' . ($organization['name'] ?? 'Strategic Plan');
ob_start();
?>

<div class="mb-8">
    <?php if ($plan['status'] === 'draft'): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-800">
                        <strong>Draft Plan:</strong> This strategic plan is currently in draft status and may be incomplete or subject to change.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?= h($plan['title']) ?></h1>
        <p class="mt-2 text-gray-600">
            <?= h($organization['name'] ?? '') ?>
            <?php if ($plan['start_year'] || $plan['end_year']): ?>
                <?php if ($plan['start_year'] && $plan['end_year']): ?>
                    (<?= h($plan['start_year']) ?> - <?= h($plan['end_year']) ?>)
                <?php elseif ($plan['start_year']): ?>
                    (<?= h($plan['start_year']) ?> onwards)
                <?php elseif ($plan['end_year']): ?>
                    (until <?= h($plan['end_year']) ?>)
                <?php endif; ?>
            <?php endif; ?>
        </p>
    </header>

    <?php
    $showHero = !empty($organization['show_hero']);
    $showAbout = !empty($organization['show_about']);
    $showVision = !empty($organization['show_vision']);
    $showMission = !empty($organization['show_mission']);
    $showValues = !empty($organization['show_values']);
    $hasHeroContent = $showHero && (!empty($organization['hero_title']) || !empty($organization['hero_subtitle']) || !empty($organization['hero_image_path']));

    // Only show organization hero if there are no custom top sections for this plan
    // This allows plans to have their own custom sections without the org hero appearing
    $hasCustomSections = !empty($topSections);
    $shouldShowOrgHero = $hasHeroContent && !$hasCustomSections;

    $heroHeight = $organization['hero_image_height'] ?? 'medium';
    $heroHeightClass = [
        'short' => 'h-52',
        'tall' => 'h-80'
    ][$heroHeight] ?? 'h-64';
    $heroBgStart = $organization['hero_bg_start'] ?? '#1D4ED8';
    $heroBgEnd = $organization['hero_bg_end'] ?? '#9333EA';
    $heroGradientStyle = "background: linear-gradient(to right, {$heroBgStart}, {$heroBgEnd});";
    ?>
    <?php if ($shouldShowOrgHero): ?>
        <div class="relative rounded-lg overflow-hidden mb-8 shadow-lg" style="<?= h($heroGradientStyle) ?>">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="relative p-8 md:p-12 text-white">
                <h2 class="text-3xl font-bold mb-3"><?= h($organization['hero_title'] ?? ($plan['title'] ?? 'Strategic Plan')) ?></h2>
                <?php if (!empty($organization['hero_subtitle'])): ?>
                    <p class="text-lg text-blue-100 max-w-3xl"><?= h($organization['hero_subtitle']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($organization['hero_image_path'])): ?>
                <div class="relative <?= $heroHeightClass ?>">
                    <img src="<?= asset($organization['hero_image_path']) ?>" alt="Organisation hero banner" class="w-full <?= $heroHeightClass ?> object-cover">
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (($showAbout && !empty($organization['about_us'])) || ($showVision && !empty($organization['vision'])) || ($showMission && !empty($organization['mission'])) || ($showValues && !empty($organization['values']))): ?>
        <!-- About Us, Vision, Mission, Values Section -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
            <div class="md:flex">
                <?php if ($showAbout && !empty($organization['about_image_path'])): ?>
                    <div class="md:w-1/3">
                        <img src="<?= asset($organization['about_image_path']) ?>" alt="About our organisation" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>
                <div class="<?= ($showAbout && !empty($organization['about_image_path'])) ? 'md:w-2/3' : 'w-full' ?> p-6 md:p-8 space-y-6">
                    <?php if ($showAbout && !empty($organization['about_us'])): ?>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">About Us</h2>
                            <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($organization['about_us']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($showVision && !empty($organization['vision'])): ?>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">Our Vision</h2>
                            <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($organization['vision']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($showMission && !empty($organization['mission'])): ?>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">Our Mission</h2>
                            <div class="text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($organization['mission']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($showValues && !empty($organization['values'])): ?>
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
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($topSections)): ?>
        <!-- Custom Top Sections -->
        <?php foreach ($topSections as $topSection): ?>
            <?php
            $sectionType = $topSection['section_type'] ?? 'custom';
            $hasImage = !empty($topSection['image_path']);
            $imagePosition = $topSection['image_position'] ?? 'left';
            $content = $topSection['content'] ?? '';
            $title = $topSection['title'] ?? '';
            $size = $topSection['size'] ?? 'medium';

            // Size-based styling (padding and fonts, but height stays flexible)
            $paddingClasses = [
                'small' => 'p-4 md:p-5',
                'medium' => 'p-6 md:p-8',
                'large' => 'p-8 md:p-12'
            ];
            $titleSizes = [
                'small' => 'text-xl',
                'medium' => 'text-2xl',
                'large' => 'text-3xl'
            ];
            $contentSizes = [
                'small' => 'text-sm',
                'medium' => 'text-base',
                'large' => 'text-lg'
            ];
            $imageHeights = [
                'small' => 'h-52',
                'medium' => 'h-64',
                'large' => 'h-80'
            ];
            $backgroundHeights = [
                'small' => 'h-64',
                'medium' => 'h-80',
                'large' => 'h-96'
            ];

            $padding = $paddingClasses[$size] ?? $paddingClasses['medium'];
            $titleSize = $titleSizes[$size] ?? $titleSizes['medium'];
            $contentSize = $contentSizes[$size] ?? $contentSizes['medium'];
            $imageHeight = $imageHeights[$size] ?? $imageHeights['medium'];
            $backgroundHeight = $backgroundHeights[$size] ?? $backgroundHeights['medium'];

                // Hero section type gets special treatment with gradient background
                if ($sectionType === 'hero'):
                    // Use custom colors if provided, otherwise fall back to organization defaults
                    $heroBgStart = !empty($topSection['hero_bg_start'])
                        ? $topSection['hero_bg_start']
                        : ($organization['hero_bg_start'] ?? '#1D4ED8');
                    $heroBgEnd = !empty($topSection['hero_bg_end'])
                        ? $topSection['hero_bg_end']
                        : ($organization['hero_bg_end'] ?? '#9333EA');
                    $heroGradientStyle = "background: linear-gradient(to right, {$heroBgStart}, {$heroBgEnd});";
                ?>
                <div class="relative rounded-lg overflow-hidden mb-8 shadow-lg" style="<?= h($heroGradientStyle) ?>">
                    <div class="absolute inset-0 bg-black/30"></div>
                    <div class="relative <?= $padding ?> text-white">
                        <?php if ($title): ?>
                            <h2 class="<?= $titleSize ?> font-bold mb-3"><?= h($title) ?></h2>
                        <?php endif; ?>
                        <?php if ($content): ?>
                            <div class="<?= $contentSize ?> text-blue-100 max-w-3xl rich-text-content"><?= displayRichText($content) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($hasImage): ?>
                        <div class="relative <?= $imageHeight ?>">
                            <img src="<?= asset($topSection['image_path']) ?>" alt="<?= h($title) ?>" class="w-full <?= $imageHeight ?> object-cover">
                        </div>
                    <?php endif; ?>
                </div>
            <?php
            // Regular sections render based on image position - wrap in container
            else:
            ?>
                <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                <?php
                if ($imagePosition === 'background' && $hasImage):
                ?>
                    <div class="relative <?= $backgroundHeight ?>">
                        <img src="<?= asset($topSection['image_path']) ?>" alt="<?= h($title) ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40"></div>
                        <div class="absolute inset-0 <?= $padding ?> text-white">
                            <?php if ($title): ?>
                                <h2 class="<?= $titleSize ?> font-bold mb-3"><?= h($title) ?></h2>
                            <?php endif; ?>
                            <?php if ($content): ?>
                                <div class="<?= $contentSize ?> text-white/90 max-w-3xl rich-text-content"><?= displayRichText($content) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($imagePosition === 'top' && $hasImage): ?>
                    <div>
                        <img src="<?= asset($topSection['image_path']) ?>" alt="<?= h($title) ?>" class="w-full <?= $imageHeight ?> object-cover">
                        <div class="<?= $padding ?>">
                            <?php if ($title): ?>
                                <h2 class="<?= $titleSize ?> font-semibold text-gray-900 mb-3"><?= h($title) ?></h2>
                            <?php endif; ?>
                            <?php if ($content): ?>
                                <div class="<?= $contentSize ?> text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($content) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($imagePosition === 'bottom' && $hasImage): ?>
                    <div>
                        <div class="<?= $padding ?>">
                            <?php if ($title): ?>
                                <h2 class="<?= $titleSize ?> font-semibold text-gray-900 mb-3"><?= h($title) ?></h2>
                            <?php endif; ?>
                            <?php if ($content): ?>
                                <div class="<?= $contentSize ?> text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($content) ?></div>
                            <?php endif; ?>
                        </div>
                        <img src="<?= asset($topSection['image_path']) ?>" alt="<?= h($title) ?>" class="w-full <?= $imageHeight ?> object-cover">
                    </div>
                <?php elseif (($imagePosition === 'left' || $imagePosition === 'right') && $hasImage): ?>
                    <div class="md:flex">
                        <div class="<?= $imagePosition === 'left' ? 'md:w-1/3' : 'md:w-1/3 md:order-2' ?>">
                            <img src="<?= asset($topSection['image_path']) ?>" alt="<?= h($title) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="<?= ($imagePosition === 'left' ? 'md:w-2/3' : 'md:w-2/3 md:order-1') ?> <?= $padding ?>">
                            <?php if ($title): ?>
                                <h2 class="<?= $titleSize ?> font-semibold text-gray-900 mb-3"><?= h($title) ?></h2>
                            <?php endif; ?>
                            <?php if ($content): ?>
                                <div class="<?= $contentSize ?> text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($content) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="<?= $padding ?>">
                        <?php if ($title): ?>
                            <h2 class="<?= $titleSize ?> font-semibold text-gray-900 mb-3"><?= h($title) ?></h2>
                        <?php endif; ?>
                        <?php if ($content): ?>
                            <div class="<?= $contentSize ?> text-gray-700 leading-relaxed rich-text-content"><?= displayRichText($content) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
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
                        <?php $description = displayRichText($goal['description'] ?? ''); ?>
                        <?php if (!empty($description)): ?>
                            <div class="text-gray-600 mb-4"><?= $description ?></div>
                        <?php endif; ?>
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
