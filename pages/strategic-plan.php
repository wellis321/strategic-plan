<?php
// Strategic Plan overview page
// #region agent log
$logPath = __DIR__ . '/../.cursor/debug.log';
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'A','location'=>'strategic-plan.php:4','message'=>'Page loading','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

requireLogin();

// #region agent log
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'A','location'=>'strategic-plan.php:8','message'=>'After requireLogin','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

$goalModel = new Goal();
$projectModel = new Project();
$sectionModel = new StrategicPlanSection();
$planModel = new StrategicPlan();
$topSectionModel = new OrganizationTopSection();

// #region agent log
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'B','location'=>'strategic-plan.php:16','message'=>'Models created','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Get current user's organization
$currentUser = getCurrentUser();

// #region agent log
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'B','location'=>'strategic-plan.php:21','message'=>'Current user retrieved','data'=>['user_id'=>$currentUser['id']??null,'org_id'=>$currentUser['organization_id']??null],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

$organizationId = $currentUser['organization_id'];

$orgModel = new Organization();

// #region agent log
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'C','location'=>'strategic-plan.php:27','message'=>'Before getByIdWithValues','data'=>['org_id'=>$organizationId],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

try {
    $organization = $orgModel->getByIdWithValues($organizationId);
    
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'C','location'=>'strategic-plan.php:32','message'=>'getByIdWithValues successful','data'=>['org_exists'=>!empty($organization)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
} catch (Exception $e) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'C','location'=>'strategic-plan.php:36','message'=>'Exception in getByIdWithValues','data'=>['error'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    error_log('Error getting organization: ' . $e->getMessage());
    throw $e;
}

// #region agent log
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'D','location'=>'strategic-plan.php:40','message'=>'Before getting active plan','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Get active plan or create default if none exists
try {
    $activePlan = $planModel->getAll(['organization_id' => $organizationId, 'is_active' => true]);
    
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'D','location'=>'strategic-plan.php:45','message'=>'getAll active plans result','data'=>['count'=>count($activePlan)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
    $activePlan = !empty($activePlan) ? $activePlan[0] : null;
} catch (Exception $e) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'D','location'=>'strategic-plan.php:50','message'=>'Exception getting active plan','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    $activePlan = null;
}

// If no active plan, get the most recent published plan or create a default
if (!$activePlan) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'E','location'=>'strategic-plan.php:57','message'=>'No active plan, getting all plans','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    
    try {
        $allPlans = $planModel->getAll(['organization_id' => $organizationId]);
        
        // #region agent log
        @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'E','location'=>'strategic-plan.php:62','message'=>'All plans retrieved','data'=>['count'=>count($allPlans)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        
        if (!empty($allPlans)) {
            $activePlan = $allPlans[0];
        } else {
            // #region agent log
            @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'F','location'=>'strategic-plan.php:68','message'=>'No plans exist, creating default','data'=>[],'timestamp'=>time()*1000])."\n", FILE_APPEND);
            // #endregion
            
            // Create a default plan for this organization
            try {
                $defaultPlanId = $planModel->create([
                    'organization_id' => $organizationId,
                    'title' => 'Strategic Plan ' . date('Y'),
                    'slug' => 'plan-' . date('Y'),
                    'status' => 'published',
                    'is_active' => true,
                    'created_by' => $currentUser['id']
                ]);
                
                // #region agent log
                @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'F','location'=>'strategic-plan.php:81','message'=>'Default plan created','data'=>['plan_id'=>$defaultPlanId],'timestamp'=>time()*1000])."\n", FILE_APPEND);
                // #endregion
                
                $activePlan = $planModel->getById($defaultPlanId);
            } catch (Exception $e) {
                // #region agent log
                @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'F','location'=>'strategic-plan.php:87','message'=>'Exception creating default plan','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
                // #endregion
                // If creation fails, continue without plan
                $activePlan = null;
            }
        }
    } catch (Exception $e) {
        // #region agent log
        @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'E','location'=>'strategic-plan.php:94','message'=>'Exception getting all plans','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
        // #endregion
        $activePlan = null;
    }
}

// #region agent log
@file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:100','message'=>'Before getting plan data','data'=>['plan_id'=>$activePlan['id']??null],'timestamp'=>time()*1000])."\n", FILE_APPEND);
// #endregion

// Get plan-specific data
$planId = $activePlan ? $activePlan['id'] : null;

try {
    $sections = $planId ? $sectionModel->getAll(['plan_id' => $planId]) : [];
    
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:107','message'=>'Sections retrieved','data'=>['count'=>count($sections)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
} catch (Exception $e) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:111','message'=>'Exception getting sections','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    $sections = [];
}

try {
    $goals = $planId ? $goalModel->getAll(['plan_id' => $planId]) : [];
    
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:117','message'=>'Goals retrieved','data'=>['count'=>count($goals)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
} catch (Exception $e) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:121','message'=>'Exception getting goals','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    $goals = [];
}

try {
    $projects = $planId ? $projectModel->getAll(['plan_id' => $planId]) : [];
    
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:127','message'=>'Projects retrieved','data'=>['count'=>count($projects)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
} catch (Exception $e) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'G','location'=>'strategic-plan.php:131','message'=>'Exception getting projects','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    $projects = [];
}

// Get top sections for this plan (plan-specific + organization-wide)
try {
    $topSections = $planId ? $topSectionModel->getAll(['organization_id' => $organizationId, 'plan_id' => $planId, 'is_active' => true]) : [];
    
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'H','location'=>'strategic-plan.php:139','message'=>'Top sections retrieved','data'=>['count'=>count($topSections)],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
} catch (Exception $e) {
    // #region agent log
    @file_put_contents($logPath, json_encode(['sessionId'=>'debug-session','runId'=>'strategic-plan','hypothesisId'=>'H','location'=>'strategic-plan.php:143','message'=>'Exception getting top sections','data'=>['error'=>$e->getMessage()],'timestamp'=>time()*1000])."\n", FILE_APPEND);
    // #endregion
    $topSections = [];
}

// Group projects by goal
$projectsByGoal = [];
foreach ($projects as $project) {
    $goalId = $project['goal_id'];
    if (!isset($projectsByGoal[$goalId])) {
        $projectsByGoal[$goalId] = [];
    }
    $projectsByGoal[$goalId][] = $project;
}

$title = ($activePlan ? h($activePlan['title']) : 'Strategic Plan') . ' - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">
                    <?= $activePlan ? h($activePlan['title']) : 'Strategic Plan' ?>
                </h1>
                <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-2 text-gray-600' : '' ?>">
                    <?= h($organization['name'] ?? 'Our Organisation') ?>
                    <?php if ($activePlan && ($activePlan['start_year'] || $activePlan['end_year'])): ?>
                        <?php if ($activePlan['start_year'] && $activePlan['end_year']): ?>
                            (<?= h($activePlan['start_year']) ?> - <?= h($activePlan['end_year']) ?>)
                        <?php elseif ($activePlan['start_year']): ?>
                            (<?= h($activePlan['start_year']) ?> onwards)
                        <?php elseif ($activePlan['end_year']): ?>
                            (until <?= h($activePlan['end_year']) ?>)
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
                <?php if ($activePlan && isOrganizationAdmin()): ?>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-1 text-sm text-gray-500' : '' ?>">
                        Public URL: <a href="/<?= h($organization['slug'] ?? 'org') ?>/<?= h($activePlan['slug']) ?>" target="_blank" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:text-blue-800' : '' ?>">/<?= h($organization['slug'] ?? 'org') ?>/<?= h($activePlan['slug']) ?></a>
                    </p>
                <?php endif; ?>
            </div>
            <?php if (isOrganizationAdmin()): ?>
                <div>
                    <?= DesignSystem::button('Manage Plans', '/plans', 'secondary', ['class' => 'text-sm']) ?>
                </div>
            <?php endif; ?>
        </div>
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
                <h2 class="text-3xl font-bold mb-3"><?= h($organization['hero_title'] ?? ($activePlan['title'] ?? 'Strategic Plan')) ?></h2>
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

                    <?php if (isOrganizationAdmin()): ?>
                        <div class="pt-4 border-t">
                            <?= DesignSystem::button('Edit Hero & About Content', '/organization/settings', 'secondary') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif (isOrganizationAdmin()): ?>
        <!-- Prompt to add About Us/Vision/Mission/Values if admin -->
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_notification ds_!_margin-bottom--6' : 'bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8' ?>">
            <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-800' : '' ?>">
                <strong>Tip:</strong> Add your organisation's hero banner, description, Vision, Mission, and Values to provide context for your strategic plan. You can toggle each section on/off in settings.
                <a href="/organization/settings" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 underline ml-1' : '' ?>">Add them now</a>.
            </p>
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
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--6' : 'mb-8' ?>">
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--3' : 'flex justify-between items-center mb-4' ?>">
                <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium' : 'text-2xl font-bold text-gray-900' ?>">Strategic Plan Sections</h2>
                <?php if (isOrganizationAdmin()): ?>
                    <?= DesignSystem::button('Manage Sections', '/sections', 'secondary', ['class' => 'text-sm']) ?>
                <?php endif; ?>
            </div>

            <?php foreach ($sections as $section): ?>
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card ds_!_margin-bottom--4' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'mb-4' ?>">
                        <h3 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-xl font-semibold text-gray-900' ?>">
                            <?= h($section['title']) ?>
                            <?php if ($section['linked_goal_id']): ?>
                                <span class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'ml-2 text-sm font-normal text-blue-600' : '' ?>">
                                    (Related to Goal <?= h($section['goal_number']) ?>)
                                </span>
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : '' ?>">
                        <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-700 leading-relaxed rich-text-content' : '' ?>"><?= displayRichText($section['content']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--4' : 'text-2xl font-bold text-gray-900 mb-6' ?>">Strategic Goals</h2>

    <?php if (empty($goals)): ?>
        <?= DesignSystem::alert('No strategic goals found. <a href="/goals/new">Create your first goal</a>.', 'info') ?>
    <?php else: ?>
        <?php foreach ($goals as $goal): ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--6' : 'mb-8' ?>">
                <?php
                $goalProjects = $projectsByGoal[$goal['id']] ?? [];
                $goalDescription = displayRichText($goal['description'] ?? '');
                // Double-check: if description is empty HTML, make sure it's truly empty
                if (!empty($goalDescription)) {
                    $testContent = strip_tags($goalDescription);
                    $testContent = html_entity_decode($testContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $testContent = preg_replace('/[\s\n\r\t]+/', '', $testContent);
                    $testContent = str_replace("\xC2\xA0", '', $testContent);
                    if (empty($testContent)) {
                        $goalDescription = '';
                    }
                }
                // Filter out empty statements
                $nonEmptyStatements = [];
                if (!empty($goal['statements'])) {
                    foreach ($goal['statements'] as $stmt) {
                        $rendered = displayRichText($stmt);
                        if (!empty($rendered)) {
                            $nonEmptyStatements[] = $stmt;
                        }
                    }
                }
                $goalContent = "
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'mb-4') . "'>
                        <h2 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-2xl font-bold text-gray-900') . "'>
                            Goal " . h($goal['number']) . ": " . h($goal['title']) . "
                        </h2>
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                        " . (!empty($goalDescription) ? "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600 mb-4' : 'mb-4') . "'>" . $goalDescription . "</div>
                        " : "") . "
                        " . (!empty($nonEmptyStatements) ? "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mb-4' : 'mb-4') . "'>
                            <h4 class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900 mb-2' : 'font-weight-bold mb-2') . "'>Goal Statements:</h4>
                            <ul class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'list-disc list-inside text-sm text-gray-600 space-y-1' : 'list-disc list-inside') . "'>
                                " . implode('', array_map(function($stmt) {
                                    return "<li>" . displayRichText($stmt) . "</li>";
                                }, $nonEmptyStatements)) . "
                            </ul>
                        </div>
                        " : "") . "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-sm text-gray-600 mb-4') . "'>
                            " . (!empty($goal['responsible_director']) ? "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : 'mb-2') . "'>
                                <dt>Responsible Senior manager</dt>
                                <dd>" . h($goal['responsible_director']) . "</dd>
                            </div>
                            " : "") . "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                <dt>Projects</dt>
                                <dd>" . count($goalProjects) . " " . pluralize(count($goalProjects), 'project') . "</dd>
                            </div>
                        </div>

                        " . (!empty($goalProjects) ? "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-4' : 'mt-4') . "'>
                            <h4 class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900 mb-3' : 'font-weight-bold mb-3') . "'>Associated Projects:</h4>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-2') . "'>
                                " . implode('', array_map(function($project) {
                                    $progress = calculateProgress($project['milestones']);
                                    return "
                                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card' : 'bg-gray-50 p-3 rounded') . "'>
                                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'flex justify-between items-center') . "'>
                                                <span class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_tag' : 'inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2') . "'>" . h($project['project_number']) . "</span>
                                                <strong>" . h($project['title']) . "</strong>
                                                <span class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500' : '') . "'>{$progress}%</span>
                                            </div>
                                        </div>
                                    ";
                                }, array_slice($goalProjects, 0, 5))) . "
                                " . (count($goalProjects) > 5 ? "<p class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500 mt-2' : 'mt-2') . "'>... and " . (count($goalProjects) - 5) . " more project(s)</p>" : "") . "
                            </div>
                        </div>
                        " : "<p class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-500 italic' : 'italic') . "'>No projects associated with this goal yet.</p>") . "
                    </div>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'flex justify-end space-x-2 pt-4 border-t') . "'>
                        " . DesignSystem::button('View Goal Details', '/goals/' . $goal['id'], 'primary') . "
                        " . DesignSystem::button('View All Projects', '/projects?goal_id=' . $goal['id'], 'secondary') . "
                    </div>
                ";
                echo DesignSystem::card($goalContent);
                ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
