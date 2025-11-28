<?php
// Delete strategic plan
requireOrganizationAdmin();

$planModel = new StrategicPlan();

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

// Count content
$goalModel = new Goal();
$projectModel = new Project();
$sectionModel = new StrategicPlanSection();

$goalsCount = count($goalModel->getAll(['plan_id' => $plan['id']]));
$projectsCount = count($projectModel->getAll(['plan_id' => $plan['id']]));
$sectionsCount = count($sectionModel->getAll(['plan_id' => $plan['id']]));

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid security token');
        redirect('/plans');
    } else {
        try {
            $planModel->delete($id, $organizationId);
            setFlashMessage('success', 'Strategic plan deleted successfully!');
            redirect('/plans');
        } catch (Exception $e) {
            setFlashMessage('error', 'Failed to delete plan: ' . $e->getMessage());
            redirect('/plans');
        }
    }
}

$title = 'Delete Strategic Plan - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Delete Strategic Plan</h1>
    </header>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Are you sure you want to delete this strategic plan?</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>This action cannot be undone. The plan "<strong><?= h($plan['title']) ?></strong>" and all its content will be permanently deleted.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Plan Details:</h4>
            <p class="text-sm text-gray-700"><strong>Title:</strong> <?= h($plan['title']) ?></p>
            <p class="text-sm text-gray-700"><strong>URL:</strong> /<?= h($plan['organization_slug'] ?? 'org') ?>/<?= h($plan['slug']) ?></p>
            <p class="text-sm text-gray-700"><strong>Status:</strong> <?= ucfirst($plan['status']) ?></p>
            <p class="text-sm text-gray-700"><strong>Content:</strong> <?= $goalsCount ?> goals, <?= $projectsCount ?> projects, <?= $sectionsCount ?> sections</p>
        </div>

        <?php if ($goalsCount > 0 || $projectsCount > 0 || $sectionsCount > 0): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Warning: This plan contains content</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Deleting this plan will also delete:</p>
                            <ul class="list-disc list-inside mt-1">
                                <?php if ($goalsCount > 0): ?>
                                    <li><?= $goalsCount ?> strategic goal(s) and all their statements</li>
                                <?php endif; ?>
                                <?php if ($projectsCount > 0): ?>
                                    <li><?= $projectsCount ?> project(s) and all their milestones, reports, and related data</li>
                                <?php endif; ?>
                                <?php if ($sectionsCount > 0): ?>
                                    <li><?= $sectionsCount ?> section(s)</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?= csrfField() ?>
            <div class="flex justify-end space-x-3">
                <a href="/plans" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    Delete Plan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
