<?php
// Organisation registration request page
$errors = [];
$formData = [];
$success = false;

if (isPost()) {
    $postData = getPostData();
    $formData = sanitizeInput($postData);
    
    // Validate CSRF token
    if (!validateCsrfToken($postData['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Invalid security token';
    }
    
    // Validate required fields
    if (empty($formData['organization_name'])) {
        $errors['organization_name'] = 'Organisation name is required';
    }
    
    if (empty($formData['domain'])) {
        $errors['domain'] = 'Email domain is required';
    } elseif (!preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?)*$/i', $formData['domain'])) {
        $errors['domain'] = 'Invalid domain format (e.g., example.org.uk)';
    } else {
        // Check if organisation already exists
        $orgModel = new Organization();
        $existing = $orgModel->getByDomain(strtolower(trim($formData['domain'])));
        if ($existing) {
            $errors['domain'] = 'An organisation with this domain already exists. Please register as a user instead.';
        }
    }
    
    if (empty($formData['contact_email'])) {
        $errors['contact_email'] = 'Contact email is required';
    } elseif (!filter_var($formData['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['contact_email'] = 'Invalid email address';
    }
    
    if (empty($formData['contact_name'])) {
        $errors['contact_name'] = 'Contact name is required';
    }
    
    if (empty($formData['estimated_seats'])) {
        $errors['estimated_seats'] = 'Estimated number of seats is required';
    } elseif (!is_numeric($formData['estimated_seats']) || $formData['estimated_seats'] < 1) {
        $errors['estimated_seats'] = 'Please enter a valid number (minimum 1)';
    }
    
    if (empty($errors)) {
        // Get super admin emails
        $superAdminEmails = getSuperAdminEmails();
        
        // If no super admins found, use CONTACT_EMAIL as fallback
        if (empty($superAdminEmails)) {
            $superAdminEmails = [CONTACT_EMAIL];
        }
        
        // Prepare email content
        $emailSubject = 'New Organisation Registration Request: ' . h($formData['organization_name']);
        
        $emailBody = getEmailTemplate('organization-request', [
            'organization_name' => $formData['organization_name'],
            'domain' => $formData['domain'],
            'contact_name' => $formData['contact_name'],
            'contact_email' => $formData['contact_email'],
            'estimated_seats' => $formData['estimated_seats'],
            'additional_info' => $formData['additional_info'] ?? '',
            'admin_url' => APP_URL . '/admin/organizations/new'
        ]);
        
        // Send email to all super admins
        $emailsSent = 0;
        foreach ($superAdminEmails as $email) {
            if (sendEmail($email, $emailSubject, $emailBody)) {
                $emailsSent++;
            }
        }
        
        if ($emailsSent > 0) {
            $success = true;
            $formData = []; // Clear form on success
        } else {
            $errors['general'] = 'Failed to send request. Please try again or contact us directly at ' . CONTACT_EMAIL;
        }
    }
}

$title = 'Request Organisation Registration - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-2xl mx-auto' : '' ?>">
        <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6 text-center' ?>">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Request Organisation Registration</h1>
            <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-2 text-gray-600' : 'mt-2' ?>">
                Request to register your organisation for Simple Strategic Plans
            </p>
        </header>

        <?php if ($success): ?>
            <?= DesignSystem::alert('Thank you! Your organisation registration request has been submitted. We will review your request and contact you shortly.', 'success') ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-6 text-center' : '' ?>">
                <a href="/" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:text-blue-800 font-medium' : '' ?>">Return to Home</a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors['general'])): ?>
                <?= DesignSystem::alert($errors['general'], 'error') ?>
            <?php endif; ?>

            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6' ?>">
                <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mb-6 text-gray-600' : '' ?>">
                    To use Simple Strategic Plans, your organisation needs to be registered first. 
                    Please fill out the form below and we'll review your request. Once approved, 
                    staff from your organisation will be able to register using their organisation email addresses.
                </p>

                <form method="POST">
                    <?= csrfField() ?>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                        <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="organization_name">Organisation Name *</label>
                        <input
                            type="text"
                            id="organization_name"
                            name="organization_name"
                            value="<?= h($formData['organization_name'] ?? '') ?>"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['organization_name']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                            required
                            autofocus
                        >
                        <?php if (!empty($errors['organization_name'])): ?>
                            <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['organization_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                        <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="domain">Email Domain *</label>
                        <input
                            type="text"
                            id="domain"
                            name="domain"
                            value="<?= h($formData['domain'] ?? '') ?>"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['domain']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                            placeholder="example.org.uk"
                            required
                        >
                        <?php if (!empty($errors['domain'])): ?>
                            <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['domain']) ?></p>
                        <?php else: ?>
                            <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">The email domain your organisation uses (e.g., ramh.org.uk)</p>
                        <?php endif; ?>
                    </div>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-2 gap-4 mb-4' : 'ds_field-group' ?>">
                        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                            <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="contact_name">Contact Name *</label>
                            <input
                                type="text"
                                id="contact_name"
                                name="contact_name"
                                value="<?= h($formData['contact_name'] ?? '') ?>"
                                class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['contact_name']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                                required
                            >
                            <?php if (!empty($errors['contact_name'])): ?>
                                <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['contact_name']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                            <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="contact_email">Contact Email *</label>
                            <input
                                type="email"
                                id="contact_email"
                                name="contact_email"
                                value="<?= h($formData['contact_email'] ?? '') ?>"
                                class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['contact_email']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                                required
                            >
                            <?php if (!empty($errors['contact_email'])): ?>
                                <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['contact_email']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                        <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="estimated_seats">Estimated Number of Users *</label>
                        <input
                            type="number"
                            id="estimated_seats"
                            name="estimated_seats"
                            value="<?= h($formData['estimated_seats'] ?? '') ?>"
                            min="1"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['estimated_seats']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                            required
                        >
                        <?php if (!empty($errors['estimated_seats'])): ?>
                            <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['estimated_seats']) ?></p>
                        <?php else: ?>
                            <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">How many staff members will need access?</p>
                        <?php endif; ?>
                    </div>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                        <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="additional_info">Additional Information</label>
                        <textarea
                            id="additional_info"
                            name="additional_info"
                            rows="4"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_textarea' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                            placeholder="Any additional information about your organisation or requirements..."
                        ><?= h($formData['additional_info'] ?? '') ?></textarea>
                    </div>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'space-y-3' ?>">
                        <?= DesignSystem::button('Submit Request', null, 'primary', ['type' => 'submit']) ?>
                        
                        <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-center text-sm text-gray-600' : 'text-center' ?>">
                            Already registered? <a href="/register" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:text-blue-800 font-medium' : '' ?>">Register as a user</a>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

