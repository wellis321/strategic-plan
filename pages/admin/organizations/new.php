<?php
// Create new organization page
requireSuperAdmin();

$orgModel = new Organization();
$errors = [];
$formData = [];

if (isPost()) {
    if (!validateCsrfToken(getPostData()['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    } else {
        $postData = getPostData();
        $formData = sanitizeInput($postData);
        $formData['created_by'] = getCurrentUser()['id'];
        // Handle values array (preserve as array, don't sanitize as string)
        $formData['values'] = $postData['values'] ?? [];
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

        $errors = $orgModel->validate($formData);

        if (empty($errors)) {
            try {
                $orgId = $orgModel->create($formData);
                setFlashMessage('success', 'Organization created successfully!');
                redirect('/admin');
            } catch (Exception $e) {
                $errors['general'] = 'Failed to create organization: ' . $e->getMessage();
            }
        }
    }
}

$title = 'Create Organization - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Create New Organization</h1>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <?= DesignSystem::alert($errors['general'], 'error') ?>
    <?php endif; ?>

    <form method="POST" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-4xl' : '' ?>">
        <?= csrfField() ?>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-lg font-medium text-gray-900 mb-4' ?>">Organization Information</h2>

            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'ds_field-group' ?>">
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'md:col-span-2' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="name">Organization Name *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= h($formData['name'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['name']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        placeholder="e.g., RAMH"
                        required
                        autofocus
                    >
                    <?php if (!empty($errors['name'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="domain">Domain *</label>
                    <input
                        type="text"
                        id="domain"
                        name="domain"
                        value="<?= h($formData['domain'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['domain']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        placeholder="e.g., ramh.org.uk"
                        required
                    >
                    <?php if (!empty($errors['domain'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['domain']) ?></p>
                    <?php else: ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Users with emails from this domain can register</p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="seat_allocation">Seat Allocation *</label>
                    <input
                        type="number"
                        id="seat_allocation"
                        name="seat_allocation"
                        value="<?= h($formData['seat_allocation'] ?? '10') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['seat_allocation']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        min="1"
                        max="1000"
                        required
                    >
                    <?php if (!empty($errors['seat_allocation'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['seat_allocation']) ?></p>
                    <?php else: ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Number of verified users allowed</p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="status">Status</label>
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select-wrapper' : '' ?>">
                        <select
                            id="status"
                            name="status"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_select' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        >
                            <option value="active" <?= ($formData['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($formData['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= ($formData['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="contact_name">Contact Name</label>
                    <input
                        type="text"
                        id="contact_name"
                        name="contact_name"
                        value="<?= h($formData['contact_name'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Primary contact person"
                    >
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="contact_email">Contact Email</label>
                    <input
                        type="email"
                        id="contact_email"
                        name="contact_email"
                        value="<?= h($formData['contact_email'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="contact@example.com"
                    >
                </div>
            </div>
        </div>

        <!-- About Us, Vision, Mission, Values Section -->
        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6 mb-6' ?>">
            <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-lg font-medium text-gray-900 mb-4' ?>">Strategic Plan Foundation (Optional)</h2>
            <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mb-4' : 'mb-4' ?>">These can be added now or later. They will appear at the top of the organisation's strategic plan.</p>

            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'space-y-6' : 'ds_field-group' ?>">
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="about_us">About Us</label>
                    <textarea
                        id="about_us"
                        name="about_us"
                        rows="8"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_textarea' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Describe your organisation, its history, what makes it unique, and who you serve..."
                    ><?= h($formData['about_us'] ?? '') ?></textarea>
                    <?php initRichTextEditor('about_us', ['placeholder' => 'Describe your organisation, its history, what makes it unique, and who you serve...']); ?>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-1 text-sm text-gray-500' : 'ds_hint-text' ?>">Use the formatting toolbar to add bold, italic, lists, and links. Press Enter to create new paragraphs with spacing.</p>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="vision">Vision Statement</label>
                    <textarea
                        id="vision"
                        name="vision"
                        rows="4"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_textarea' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Your organisation's vision for the future..."
                    ><?= h($formData['vision'] ?? '') ?></textarea>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-1 text-sm text-gray-500' : 'ds_hint-text' ?>">A clear, inspiring statement of what your organisation aims to achieve in the long term.</p>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="mission">Mission Statement</label>
                    <textarea
                        id="mission"
                        name="mission"
                        rows="4"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_textarea' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        placeholder="Your organisation's core purpose and reason for existence..."
                    ><?= h($formData['mission'] ?? '') ?></textarea>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-1 text-sm text-gray-500' : 'ds_hint-text' ?>">A statement of your organisation's fundamental purpose and what it does.</p>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="values">Values</label>
                    <div id="values-container" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'space-y-2' : '' ?>">
                        <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'flex gap-2' : '' ?> value-item">
                            <input
                                type="text"
                                name="values[]"
                                value=""
                                class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'flex-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                                placeholder="Enter a value (e.g., Respect, Integrity, Innovation...)"
                            >
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="addValue()"
                        class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-2 text-sm text-blue-600 hover:text-blue-800' : 'ds_button ds_button--secondary' ?>"
                    >+ Add Another Value</button>
                    <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-1 text-sm text-gray-500' : 'ds_hint-text' ?>">The core principles and beliefs that guide your organisation's actions and decisions.</p>
                </div>
            </div>
        </div>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'flex justify-end space-x-3' ?>">
            <?= DesignSystem::button('Cancel', '/admin', 'cancel') ?>
            <?= DesignSystem::button('Create Organization', null, 'primary', ['type' => 'submit']) ?>
        </div>
    </form>
</div>

<script>
function addValue() {
    const container = document.getElementById('values-container');
    const div = document.createElement('div');
    div.className = '<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'flex gap-2' : '' ?> value-item';
    div.innerHTML = `
        <input
            type="text"
            name="values[]"
            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'flex-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
            placeholder="Enter a value (e.g., Respect, Integrity, Innovation...)"
        >
        <button
            type="button"
            onclick="removeValue(this)"
            class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'px-3 py-2 text-red-600 hover:text-red-800' : '' ?>"
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
include __DIR__ . '/../../../templates/layout.php';
?>
