<?php
// Edit top section
requireOrganizationAdmin();

$topSectionModel = new OrganizationTopSection();
$planModel = new StrategicPlan();

$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$id = getQueryParam('id');
if (!$id) {
    setFlashMessage('error', 'Section ID is required');
    redirect('/top-sections');
}

$section = $topSectionModel->getById($id, $organizationId);
if (!$section) {
    setFlashMessage('error', 'Section not found');
    redirect('/top-sections');
}

// Get all plans for this organization
$allPlans = $planModel->getAll(['organization_id' => $organizationId]);

$errors = [];
$formData = $section;

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();

        $formData = [
            'plan_id' => !empty($postData['plan_id']) && $postData['plan_id'] !== '' ? (int)$postData['plan_id'] : null,
            'section_type' => $postData['section_type'] ?? 'custom',
            'title' => trim($postData['title'] ?? ''),
            'content' => sanitizeRichText($postData['content'] ?? ''),
            'image_position' => $postData['image_position'] ?? 'left',
            'size' => in_array($postData['size'] ?? 'medium', ['small', 'medium', 'large'], true) ? $postData['size'] : 'medium',
            'hero_bg_start' => !empty($postData['hero_bg_start']) ? trim($postData['hero_bg_start']) : null,
            'hero_bg_end' => !empty($postData['hero_bg_end']) ? trim($postData['hero_bg_end']) : null,
            'sort_order' => (int)($postData['sort_order'] ?? 0),
            'is_active' => !empty($postData['is_active']) ? 1 : 0
        ];

        // Handle image upload/removal
        $imagePath = $section['image_path'] ?? null;
        if (!empty($postData['remove_image'])) {
            if ($imagePath) {
                $fullPath = __DIR__ . '/../../' . ltrim($imagePath, '/');
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $imagePath = null;
        } elseif (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
            $mime = mime_content_type($file['tmp_name']);
            if ($mime && in_array($mime, $allowedMime, true)) {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    $uploadDir = __DIR__ . '/../../static/uploads/organizations/' . $organizationId . '/top-sections';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    // Delete old image if exists
                    if ($imagePath) {
                        $oldFullPath = __DIR__ . '/../../' . ltrim($imagePath, '/');
                        if (file_exists($oldFullPath)) {
                            @unlink($oldFullPath);
                        }
                    }
                    $filename = 'section-' . time() . '.' . $extension;
                    $destination = $uploadDir . '/' . $filename;
                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        // Set secure file permissions (non-executable)
                        chmod($destination, 0644);
                        $imagePath = 'static/uploads/organizations/' . $organizationId . '/top-sections/' . $filename;
                    } else {
                        $errors['image'] = 'Failed to upload image. Please try again.';
                    }
                } else {
                    $errors['image'] = 'Image must be JPG, PNG, or WebP.';
                }
            } else {
                $errors['image'] = 'Invalid image file.';
            }
        }
        $formData['image_path'] = $imagePath;

        // Validate
        $validationErrors = $topSectionModel->validate($formData);
        if (!empty($validationErrors)) {
            $errors = array_merge($errors, $validationErrors);
        }

        if (empty($errors)) {
            try {
                $topSectionModel->update($id, $formData, $organizationId);
                setFlashMessage('success', 'Top section updated successfully!');
                redirect('/top-sections');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to update section: ' . $e->getMessage();
            }
        }
    }
}

$title = 'Edit Top Section - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Top Section</h1>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="max-w-4xl" enctype="multipart/form-data">
        <?= csrfField() ?>

        <div class="bg-white shadow rounded-lg p-6 mb-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="plan_id">Assign to Plan</label>
                <select
                    id="plan_id"
                    name="plan_id"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="" <?= empty($formData['plan_id']) ? 'selected' : '' ?>>All Plans (Organization-wide)</option>
                    <?php foreach ($allPlans as $plan): ?>
                        <option value="<?= $plan['id'] ?>" <?= ($formData['plan_id'] ?? null) == $plan['id'] ? 'selected' : '' ?>>
                            <?= h($plan['title']) ?> (<?= h($plan['status']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1 text-sm text-gray-500">Select a specific plan or leave as "All Plans" to show on every plan.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="section_type">Section Type</label>
                <select
                    id="section_type"
                    name="section_type"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="custom" <?= $formData['section_type'] === 'custom' ? 'selected' : '' ?>>Custom Section</option>
                    <option value="hero" <?= $formData['section_type'] === 'hero' ? 'selected' : '' ?>>Hero Banner</option>
                    <option value="about" <?= $formData['section_type'] === 'about' ? 'selected' : '' ?>>About Us</option>
                    <option value="vision" <?= $formData['section_type'] === 'vision' ? 'selected' : '' ?>>Vision</option>
                    <option value="mission" <?= $formData['section_type'] === 'mission' ? 'selected' : '' ?>>Mission</option>
                    <option value="values" <?= $formData['section_type'] === 'values' ? 'selected' : '' ?>>Values</option>
                </select>
            </div>

            <div id="title-field">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="title">
                    Title <span id="title-required" class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= h($formData['title'] ?? '') ?>"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Our Strategic Priorities"
                >
                <p id="title-hint" class="mt-1 text-sm text-gray-500">Optional for hero sections. Leave blank if you don't want a title.</p>
                <?php if (!empty($errors['title'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= h($errors['title']) ?></p>
                <?php endif; ?>
            </div>

            <div id="hero-colors-field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-3">Hero Gradient Colours</label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="hero_bg_start">Start Colour</label>
                        <div class="flex items-center space-x-2">
                            <input
                                type="color"
                                id="hero_bg_start"
                                name="hero_bg_start"
                                value="<?= h($formData['hero_bg_start'] ?: '#1D4ED8') ?>"
                                class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                            >
                            <input
                                type="text"
                                id="hero_bg_start_text"
                                value="<?= h($formData['hero_bg_start'] ?: '#1D4ED8') ?>"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="#1D4ED8"
                                pattern="^#[0-9A-Fa-f]{6}$"
                            >
                        </div>
                        <?php if (!empty($errors['hero_bg_start'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= h($errors['hero_bg_start']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="hero_bg_end">End Colour</label>
                        <div class="flex items-center space-x-2">
                            <input
                                type="color"
                                id="hero_bg_end"
                                name="hero_bg_end"
                                value="<?= h($formData['hero_bg_end'] ?: '#9333EA') ?>"
                                class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                            >
                            <input
                                type="text"
                                id="hero_bg_end_text"
                                value="<?= h($formData['hero_bg_end'] ?: '#9333EA') ?>"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="#9333EA"
                                pattern="^#[0-9A-Fa-f]{6}$"
                            >
                        </div>
                        <?php if (!empty($errors['hero_bg_end'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= h($errors['hero_bg_end']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Leave blank to use your organisation's default hero colours.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="content">Content</label>
                <textarea
                    id="content"
                    name="content"
                    rows="10"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter the content for this section..."
                ><?= h($formData['content'] ?? '') ?></textarea>
                <?php initRichTextEditor('content', ['placeholder' => 'Enter the content for this section...']); ?>
                <p class="mt-1 text-sm text-gray-500">Use the formatting toolbar to add bold, italic, lists, and links.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="image">Image</label>
                <?php if (!empty($formData['image_path'])): ?>
                    <div class="mb-3">
                        <img src="<?= asset($formData['image_path']) ?>" alt="Section preview" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                        <label class="flex items-center mt-2 text-sm text-gray-600">
                            <input type="checkbox" name="remove_image" value="1" class="mr-2">
                            Remove current image
                        </label>
                    </div>
                <?php endif; ?>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-gray-600"
                >
                <p class="mt-1 text-sm text-gray-500">Recommended 1200 x 600px JPG, PNG, or WebP.</p>
                <?php if (!empty($errors['image'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= h($errors['image']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="image_position">Image Position</label>
                <select
                    id="image_position"
                    name="image_position"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="left" <?= $formData['image_position'] === 'left' ? 'selected' : '' ?>>Left</option>
                    <option value="right" <?= $formData['image_position'] === 'right' ? 'selected' : '' ?>>Right</option>
                    <option value="top" <?= $formData['image_position'] === 'top' ? 'selected' : '' ?>>Top</option>
                    <option value="bottom" <?= $formData['image_position'] === 'bottom' ? 'selected' : '' ?>>Bottom</option>
                    <option value="background" <?= $formData['image_position'] === 'background' ? 'selected' : '' ?>>Background</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="size">Section Size</label>
                <select
                    id="size"
                    name="size"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="small" <?= ($formData['size'] ?? 'medium') === 'small' ? 'selected' : '' ?>>Small (compact padding, smaller fonts)</option>
                    <option value="medium" <?= ($formData['size'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium (standard padding, standard fonts)</option>
                    <option value="large" <?= ($formData['size'] ?? 'medium') === 'large' ? 'selected' : '' ?>>Large (generous padding, larger fonts)</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Controls padding and font sizes. The section height will adjust automatically based on your content.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="sort_order">Display Order</label>
                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    value="<?= h($formData['sort_order']) ?>"
                    min="0"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
            </div>

            <div>
                <label class="flex items-center space-x-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        <?= !empty($formData['is_active']) ? 'checked' : '' ?>
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span>Show this section on plan pages</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <?= DesignSystem::button('Cancel', '/top-sections', 'cancel') ?>
            <?= DesignSystem::button('Update Section', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<script>
document.getElementById('section_type').addEventListener('change', function() {
    const titleField = document.getElementById('title-field');
    const titleRequired = document.getElementById('title-required');
    const titleHint = document.getElementById('title-hint');
    const titleInput = document.getElementById('title');
    const heroColorsField = document.getElementById('hero-colors-field');

    if (this.value === 'custom') {
        titleField.style.display = 'block';
        titleRequired.style.display = 'inline';
        titleHint.textContent = 'Required for custom sections.';
        titleInput.required = true;
        heroColorsField.style.display = 'none';
    } else if (this.value === 'hero') {
        titleField.style.display = 'block';
        titleRequired.style.display = 'none';
        titleHint.textContent = 'Optional for hero sections. Leave blank if you don\'t want a title.';
        titleInput.required = false;
        heroColorsField.style.display = 'block';
    } else {
        titleField.style.display = 'block';
        titleRequired.style.display = 'none';
        titleHint.textContent = 'Optional. Leave blank if you don\'t want a title.';
        titleInput.required = false;
        heroColorsField.style.display = 'none';
    }
});

// Sync color picker and text input
document.getElementById('hero_bg_start').addEventListener('input', function() {
    document.getElementById('hero_bg_start_text').value = this.value;
});
document.getElementById('hero_bg_start_text').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.getElementById('hero_bg_start').value = this.value;
    }
});

document.getElementById('hero_bg_end').addEventListener('input', function() {
    document.getElementById('hero_bg_end_text').value = this.value;
});
document.getElementById('hero_bg_end_text').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.getElementById('hero_bg_end').value = this.value;
    }
});

// Initial check
document.getElementById('section_type').dispatchEvent(new Event('change'));
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
