<?php
// Delete strategic plan section
requireOrganizationAdmin();

$sectionModel = new StrategicPlanSection();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$id = getQueryParam('id');
if (!$id) {
    setFlashMessage('error', 'Section ID is required');
    redirect('/sections');
}

$section = $sectionModel->getById($id, $organizationId);
if (!$section) {
    setFlashMessage('error', 'Section not found');
    redirect('/sections');
}

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid security token');
        redirect('/sections');
    } else {
        try {
            $sectionModel->delete($id, $organizationId);
            setFlashMessage('success', 'Section deleted successfully!');
            redirect('/sections');
        } catch (Exception $e) {
            setFlashMessage('error', 'Failed to delete section: ' . $e->getMessage());
            redirect('/sections');
        }
    }
}

$title = 'Delete Section - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Delete Strategic Plan Section</h1>
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
                    <h3 class="text-sm font-medium text-red-800">Are you sure you want to delete this section?</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>This action cannot be undone. The section "<strong><?= h($section['title']) ?></strong>" will be permanently removed from your strategic plan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Section Details:</h4>
            <p class="text-sm text-gray-700"><strong>Title:</strong> <?= h($section['title']) ?></p>
            <p class="text-sm text-gray-700"><strong>Content Preview:</strong> <?= h(substr(strip_tags($section['content']), 0, 150)) ?>...</p>
        </div>

        <form method="POST">
            <?= csrfField() ?>
            <div class="flex justify-end space-x-3">
                <a href="/sections" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    Delete Section
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
