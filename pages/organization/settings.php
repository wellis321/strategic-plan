<?php
// Organization settings page (for organization admins)
requireOrganizationAdmin();

if (!function_exists('processOrganizationImageUpload')) {
    function processOrganizationImageUpload(string $field, int $organizationId, ?string $existingPath, array &$errors, string $label) {
        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }

        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[$field] = "{$label} upload failed. Please try again.";
            return $existingPath;
        }

        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = mime_content_type($file['tmp_name']);
        if ($mime === false || !in_array($mime, $allowedMime, true)) {
            $errors[$field] = "{$label} must be a JPG, PNG, or WebP image.";
            return $existingPath;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $errors[$field] = "{$label} must have a JPG, PNG, or WebP extension.";
            return $existingPath;
        }

        $uploadDir = __DIR__ . '/../../static/uploads/organizations/' . $organizationId;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = $field . '-' . time() . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $errors[$field] = "Unable to save {$label}. Please try again.";
            return $existingPath;
        }

        // Set secure file permissions (non-executable)
        chmod($destination, 0644);

        // Delete previous image if provided and different
        if (!empty($existingPath)) {
            $existingFullPath = __DIR__ . '/../../' . ltrim($existingPath, '/');
            if (file_exists($existingFullPath)) {
                @unlink($existingFullPath);
            }
        }

        $relativePath = 'static/uploads/organizations/' . $organizationId . '/' . $filename;
        return $relativePath;
    }
}

if (!function_exists('deleteOrganizationImage')) {
    function deleteOrganizationImage(?string $relativePath) {
        if (!$relativePath) {
            return;
        }
        $fullPath = __DIR__ . '/../../' . ltrim($relativePath, '/');
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}

$orgModel = new Organization();
$currentUser = getCurrentUser();
$organizationId = $currentUser['organization_id'];

$organization = $orgModel->getByIdWithValues($organizationId);
if (!$organization) {
    setFlashMessage('error', 'Organization not found');
    redirect('/dashboard');
}

$errors = [];
$formData = $organization;

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();
        $formData = sanitizeInput($postData);
        $formData['id'] = $organizationId;
        $formData['name'] = $organization['name']; // Preserve name
        $formData['domain'] = $organization['domain']; // Preserve domain
        $formData['seat_allocation'] = $organization['seat_allocation']; // Preserve seat allocation
        $formData['status'] = $organization['status']; // Preserve status
        // Handle values array
        $formData['values'] = $postData['values'] ?? [];
        $formData['show_hero'] = !empty($postData['show_hero']) ? 1 : 0;
        $formData['show_about'] = !empty($postData['show_about']) ? 1 : 0;
        $formData['show_vision'] = !empty($postData['show_vision']) ? 1 : 0;
        $formData['show_mission'] = !empty($postData['show_mission']) ? 1 : 0;
        $formData['show_values'] = !empty($postData['show_values']) ? 1 : 0;
        $formData['hero_title'] = trim($postData['hero_title'] ?? '') ?: null;
        $formData['hero_subtitle'] = trim($postData['hero_subtitle'] ?? '') ?: null;
        $formData['hero_image_height'] = in_array($postData['hero_image_height'] ?? 'medium', ['short','medium','tall'], true)
            ? $postData['hero_image_height']
            : 'medium';
        $defaultStart = $organization['hero_bg_start'] ?? '#1d4ed8';
        $defaultEnd = $organization['hero_bg_end'] ?? '#9333ea';
        $colorPattern = '/^#([A-Fa-f0-9]{6})$/';
        $heroBgStart = strtoupper(trim($postData['hero_bg_start'] ?? $defaultStart));
        $heroBgEnd = strtoupper(trim($postData['hero_bg_end'] ?? $defaultEnd));
        if (!preg_match($colorPattern, $heroBgStart)) {
            $errors['hero_bg_start'] = 'Hero start colour must be a valid hex value (e.g., #1D4ED8).';
            $heroBgStart = $defaultStart;
        }
        if (!preg_match($colorPattern, $heroBgEnd)) {
            $errors['hero_bg_end'] = 'Hero end colour must be a valid hex value (e.g., #9333EA).';
            $heroBgEnd = $defaultEnd;
        }
        $formData['hero_bg_start'] = $heroBgStart;
        $formData['hero_bg_end'] = $heroBgEnd;
        // Sanitize rich text fields
        if (isset($postData['about_us'])) {
            $formData['about_us'] = sanitizeRichText($postData['about_us']);
        }
        if (isset($postData['vision'])) {
            $formData['vision'] = sanitizeRichText($postData['vision']);
        }
        if (isset($postData['mission'])) {
            $formData['mission'] = sanitizeRichText($postData['mission']);
        }

        // Handle hero image upload/removal
        $heroImagePath = $organization['hero_image_path'] ?? null;
        if (!empty($postData['remove_hero_image'])) {
            deleteOrganizationImage($heroImagePath);
            $heroImagePath = null;
        }
        $heroImagePath = processOrganizationImageUpload('hero_image', $organizationId, $heroImagePath, $errors, 'Hero image');
        $formData['hero_image_path'] = $heroImagePath;

        // Handle About Us image upload/removal
        $aboutImagePath = $organization['about_image_path'] ?? null;
        if (!empty($postData['remove_about_image'])) {
            deleteOrganizationImage($aboutImagePath);
            $aboutImagePath = null;
        }
        $aboutImagePath = processOrganizationImageUpload('about_image', $organizationId, $aboutImagePath, $errors, 'About Us image');
        $formData['about_image_path'] = $aboutImagePath;

        if (empty(array_filter($errors))) {
            try {
                $orgModel->update($organizationId, $formData);
                setFlashMessage('success', 'Strategic plan foundation updated successfully!');
                redirect('/strategic-plan');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to update: ' . $e->getMessage();
            }
        }
    }
}

$title = 'Organization Settings - ' . APP_NAME;
ob_start();
?>

<div class="mb-8">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Strategic Plan Foundation</h1>
        <p class="mt-2 text-gray-600">
            Set your hero banner, About Us, Vision, Mission, and Values. These appear at the top of your strategic plan and on public plan pages.
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="max-w-4xl" enctype="multipart/form-data">
        <?= csrfField() ?>

        <div class="bg-white shadow rounded-lg p-6 mb-6 space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Hero Section</h2>
                <p class="text-sm text-gray-500 mb-4">
                    This content appears at the very top of your strategic plan. Use it to create a strong first impression.
                </p>
            </div>
            <label class="flex items-center space-x-2 text-sm text-gray-700">
                <input type="checkbox" name="show_hero" value="1" <?= !empty($formData['show_hero']) ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Show hero section on plan pages</span>
            </label>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="hero_title">Hero Title</label>
                <input
                    type="text"
                    id="hero_title"
                    name="hero_title"
                    value="<?= h($formData['hero_title'] ?? '') ?>"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Our Strategy for 2025-2028"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="hero_subtitle">Hero Subtitle</label>
                <textarea
                    id="hero_subtitle"
                    name="hero_subtitle"
                    rows="3"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Add a short introduction or key message for your strategic plan..."
                ><?= h($formData['hero_subtitle'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="hero_image">Hero Image</label>
                <?php if (!empty($formData['hero_image_path'])): ?>
                    <div class="mb-3">
                        <img src="<?= asset($formData['hero_image_path']) ?>" alt="Hero preview" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                        <label class="flex items-center mt-2 text-sm text-gray-600">
                            <input type="checkbox" name="remove_hero_image" value="1" class="mr-2">
                            Remove current image
                        </label>
                    </div>
                <?php endif; ?>
                <input
                    type="file"
                    id="hero_image"
                    name="hero_image"
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-gray-600"
                >
                <p class="mt-1 text-sm text-gray-500">Recommended 1600 x 500px JPG, PNG, or WebP.</p>
                <?php if (!empty($errors['hero_image'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= h($errors['hero_image']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="hero_image_height">Hero Image Height</label>
                <?php $currentHeight = $formData['hero_image_height'] ?? 'medium'; ?>
                <select
                    id="hero_image_height"
                    name="hero_image_height"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="short" <?= $currentHeight === 'short' ? 'selected' : '' ?>>Short (200px)</option>
                    <option value="medium" <?= $currentHeight === 'medium' ? 'selected' : '' ?>>Medium (256px)</option>
                    <option value="tall" <?= $currentHeight === 'tall' ? 'selected' : '' ?>>Tall (320px)</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Choose how tall the hero image appears on the plan pages.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="hero_bg_start">Hero Gradient Start Colour</label>
                    <input
                        type="color"
                        id="hero_bg_start"
                        name="hero_bg_start"
                        value="<?= h($formData['hero_bg_start'] ?? '#1d4ed8') ?>"
                        class="w-24 h-10 border border-gray-300 rounded-md cursor-pointer"
                    >
                    <p class="mt-1 text-sm text-gray-500">Select the first colour in the gradient.</p>
                    <?php if (!empty($errors['hero_bg_start'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['hero_bg_start']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="hero_bg_end">Hero Gradient End Colour</label>
                    <input
                        type="color"
                        id="hero_bg_end"
                        name="hero_bg_end"
                        value="<?= h($formData['hero_bg_end'] ?? '#9333ea') ?>"
                        class="w-24 h-10 border border-gray-300 rounded-md cursor-pointer"
                    >
                    <p class="mt-1 text-sm text-gray-500">Select the second colour in the gradient.</p>
                    <?php if (!empty($errors['hero_bg_end'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['hero_bg_end']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="space-y-6">
                <div>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 mb-2">
                        <input type="checkbox" name="show_about" value="1" <?= !empty($formData['show_about']) ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Show About Us section</span>
                    </label>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="about_us">About Us</label>
                    <textarea
                        id="about_us"
                        name="about_us"
                        rows="8"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Describe your organisation, its history, what makes it unique, and who you serve..."
                    ><?= h($formData['about_us'] ?? '') ?></textarea>
                    <?php initRichTextEditor('about_us', ['placeholder' => 'Describe your organisation, its history, what makes it unique, and who you serve...']); ?>
                    <p class="mt-1 text-sm text-gray-500">An introduction to your organisation. Use the formatting toolbar to add bold, italic, lists, and links. Press Enter to create new paragraphs with spacing.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="about_image">About Us Image</label>
                    <?php if (!empty($formData['about_image_path'])): ?>
                        <div class="mb-3">
                            <img src="<?= asset($formData['about_image_path']) ?>" alt="About us preview" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                            <label class="flex items-center mt-2 text-sm text-gray-600">
                                <input type="checkbox" name="remove_about_image" value="1" class="mr-2">
                                Remove current image
                            </label>
                        </div>
                    <?php endif; ?>
                    <input
                        type="file"
                        id="about_image"
                        name="about_image"
                        accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-600"
                    >
                    <p class="mt-1 text-sm text-gray-500">Optional supporting image (e.g., team photo). Recommended 800 x 600px.</p>
                    <?php if (!empty($errors['about_image'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= h($errors['about_image']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 mb-2">
                        <input type="checkbox" name="show_vision" value="1" <?= !empty($formData['show_vision']) ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Show Vision section</span>
                    </label>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="vision">Vision Statement</label>
                    <textarea
                        id="vision"
                        name="vision"
                        rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Your organisation's vision for the future..."
                    ><?= h($formData['vision'] ?? '') ?></textarea>
                    <?php initRichTextEditor('vision', ['placeholder' => 'Your organisation\'s vision for the future...']); ?>
                    <p class="mt-1 text-sm text-gray-500">A clear, inspiring statement of what your organisation aims to achieve in the long term. Use formatting tools to emphasise key points.</p>
                </div>

                <div>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 mb-2">
                        <input type="checkbox" name="show_mission" value="1" <?= !empty($formData['show_mission']) ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Show Mission section</span>
                    </label>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="mission">Mission Statement</label>
                    <textarea
                        id="mission"
                        name="mission"
                        rows="4"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Your organisation's core purpose and reason for existence..."
                    ><?= h($formData['mission'] ?? '') ?></textarea>
                    <?php initRichTextEditor('mission', ['placeholder' => 'Your organisation\'s core purpose and reason for existence...']); ?>
                    <p class="mt-1 text-sm text-gray-500">A statement of your organisation's fundamental purpose and what it does. Use formatting tools to emphasise key points.</p>
                </div>

                <div>
                    <label class="flex items-center space-x-2 text-sm text-gray-700 mb-2">
                        <input type="checkbox" name="show_values" value="1" <?= !empty($formData['show_values']) ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span>Show Values section</span>
                    </label>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="values">Values</label>
                    <div id="values-container" class="space-y-2">
                        <?php
                        $values = $formData['values'] ?? [];
                        if (empty($values)) {
                            $values = [['value_text' => '']];
                        }
                        foreach ($values as $index => $value): ?>
                            <div class="flex gap-2 value-item">
                                <input
                                    type="text"
                                    name="values[]"
                                    value="<?= h($value['value_text'] ?? '') ?>"
                                    class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter a value (e.g., Respect, Integrity, Innovation...)"
                                >
                                <?php if ($index > 0 || count($values) > 1): ?>
                                    <button
                                        type="button"
                                        onclick="removeValue(this)"
                                        class="px-3 py-2 text-red-600 hover:text-red-800"
                                    >Remove</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button
                        type="button"
                        onclick="addValue()"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800"
                    >+ Add Another Value</button>
                    <p class="mt-1 text-sm text-gray-500">The core principles and beliefs that guide your organisation's actions and decisions.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <?= DesignSystem::button('Cancel', '/strategic-plan', 'cancel') ?>
            <?= DesignSystem::button('Save Changes', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<script>
function addValue() {
    const container = document.getElementById('values-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 value-item';
    div.innerHTML = `
        <input
            type="text"
            name="values[]"
            class="flex-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter a value (e.g., Respect, Integrity, Innovation...)"
        >
        <button
            type="button"
            onclick="removeValue(this)"
            class="px-3 py-2 text-red-600 hover:text-red-800"
        >Remove</button>
    `;
    container.appendChild(div);
}

function removeValue(button) {
    const container = document.getElementById('values-container');
    if (container.children.length > 1) {
        button.closest('.value-item').remove();
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
