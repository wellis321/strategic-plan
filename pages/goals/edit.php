<?php
// Edit goal page (placeholder - similar to create but with update logic)
requireOrganizationAdmin();

$goalModel = new Goal();
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

$errors = [];
$formData = $goal;

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();

        // Get raw data for rich text fields (don't HTML encode them)
        $formData = sanitizeInput($postData);
        $formData['id'] = $id;

        // Handle description - preserve HTML from rich text editor
        if (!empty($postData['description'])) {
            $formData['description'] = sanitizeRichText($postData['description']);
        }

        // Process statements - handle both HTML from rich text editor and plain text
        if (!empty($postData['statements'])) {
            $statements = $postData['statements'];

            // If it's HTML (from rich text editor), extract list items or paragraphs
            if (strip_tags($statements) !== $statements) {
                // It's HTML - extract list items or paragraphs
                $dom = new DOMDocument();
                @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $statements);
                $xpath = new DOMXPath($dom);

                $extractedStatements = [];

                // First, try to get list items
                $listItems = $xpath->query('//li');
                if ($listItems->length > 0) {
                    foreach ($listItems as $item) {
                        $text = trim($item->textContent);
                        if (!empty($text)) {
                            // Sanitize HTML but keep basic formatting
                            $html = $dom->saveHTML($item);
                            $extractedStatements[] = sanitizeRichText($html);
                        }
                    }
                } else {
                    // No list items, extract paragraphs
                    $paragraphs = $xpath->query('//p');
                    foreach ($paragraphs as $para) {
                        $text = trim($para->textContent);
                        if (!empty($text)) {
                            $html = $dom->saveHTML($para);
                            $extractedStatements[] = sanitizeRichText($html);
                        }
                    }
                }

                // If we didn't find any structured content, fall back to plain text extraction
                if (empty($extractedStatements)) {
                    $plainText = strip_tags($statements);
                    $extractedStatements = array_filter(array_map('trim', explode("\n", $plainText)));
                }

                $formData['statements'] = $extractedStatements;
            } else {
                // Plain text - split by newlines
                $formData['statements'] = array_filter(array_map('trim', explode("\n", $statements)));
            }
        }

        $errors = $goalModel->validate($formData);

        if (empty($errors)) {
            try {
                $goalModel->update($id, $formData);
                setFlashMessage('success', 'Goal updated successfully!');
                redirect('/goals');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to update goal: ' . $e->getMessage();
            }
        }
    }
}

$title = 'Edit Goal - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Edit Goal</h1>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-4xl' : '' ?>">
        <?= csrfField() ?>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-lg font-medium text-gray-900 mb-4' ?>">Goal Information</h2>

            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'ds_field-group' ?>">
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="number">Goal Number *</label>
                    <input
                        type="text"
                        id="number"
                        name="number"
                        value="<?= h($formData['number'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['number']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                    >
                    <?php if (!empty($errors['number'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['number']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="title">Goal Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= h($formData['title'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['title']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="description">Description</label>
                    <div class="mb-2">
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        ><?= h($formData['description'] ?? '') ?></textarea>
                        <?php initRichTextEditor('description', ['placeholder' => 'Enter a detailed description of this goal...']); ?>
                    </div>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="responsible_director">Responsible Senior manager *</label>
                    <input
                        type="text"
                        id="responsible_director"
                        name="responsible_director"
                        value="<?= h($formData['responsible_director'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        required
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="statements">Goal Statements</label>
                    <div class="mb-2">
                        <textarea
                            id="statements"
                            name="statements"
                            rows="4"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                            placeholder="Enter each statement on a new line or as a list"
                        ><?php
                            // Convert existing statements array to HTML list for rich text editor
                            if (!empty($formData['statements']) && is_array($formData['statements'])) {
                                $htmlStatements = '<ul>';
                                foreach ($formData['statements'] as $stmt) {
                                    // Check if statement already contains HTML
                                    if (strip_tags($stmt) !== $stmt) {
                                        $htmlStatements .= '<li>' . $stmt . '</li>';
                                    } else {
                                        $htmlStatements .= '<li>' . h($stmt) . '</li>';
                                    }
                                }
                                $htmlStatements .= '</ul>';
                                echo $htmlStatements;
                            } else {
                                echo h($formData['statements'] ?? '');
                            }
                        ?></textarea>
                        <?php initRichTextEditor('statements', ['placeholder' => 'Enter goal statements. You can format them as a list or separate paragraphs...']); ?>
                    </div>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">You can format statements with bold, italic, or lists. Each list item or paragraph will be treated as a separate statement.</p>
                </div>
            </div>
        </div>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'flex justify-end space-x-3' ?>">
            <?= DesignSystem::button('Cancel', '/goals', 'cancel') ?>
            <?= DesignSystem::button('Update Goal', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
