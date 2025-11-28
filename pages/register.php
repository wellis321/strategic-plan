<?php
// Registration page
$userModel = new User();
$orgModel = new Organization();

$errors = [];
$formData = [];

if (isLoggedIn()) {
    redirect('/');
}

if (isPost()) {
    $postData = getPostData();
    $formData = sanitizeInput($postData);

    // Extract domain from email
    $domain = extractDomainFromEmail($formData['email'] ?? '');

    if (!$domain) {
        $errors['email'] = 'Invalid email format';
    } else {
        // Check if organization exists for this domain
        $organization = getOrganizationByEmailDomain($formData['email']);

        if (!$organization) {
            $errors['email'] = 'No organization found for this email domain. Please contact your administrator.';
        } elseif ($organization['status'] !== 'active') {
            $errors['email'] = 'The organization for this domain is not active. Please contact your administrator.';
        } else {
            // Check seat availability
            if (!checkSeatAvailability($domain)) {
                $usage = $orgModel->getSeatUsage($organization['id']);
                $errors['email'] = "All seats for this organization are currently allocated ({$usage['seats_used']}/{$usage['seat_allocation']}). Please contact your administrator.";
            }
        }
    }

    // Validate user data
    $userErrors = $userModel->validate($formData);
    $errors = array_merge($errors, $userErrors);

    // Check password confirmation
    if (isset($formData['password']) && isset($formData['password_confirm'])) {
        if ($formData['password'] !== $formData['password_confirm']) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
    }

    if (empty($errors)) {
        try {
            // Create user
            $userId = $userModel->create([
                'organization_id' => $organization['id'],
                'email' => $formData['email'],
                'password' => $formData['password'],
                'first_name' => $formData['first_name'] ?? null,
                'last_name' => $formData['last_name'] ?? null,
                'role' => 'user',
                'email_verified' => false,
                'status' => 'pending_verification'
            ]);

            // Get user to get verification token
            $user = $userModel->getById($userId);

            // Send verification email
            $verificationUrl = sendVerificationEmail($user, $user['verification_token']);

            // In development, show the verification URL
            if (APP_ENV === 'development') {
                setFlashMessage('info', 'Registration successful! Verification URL: ' . $verificationUrl);
            } else {
                setFlashMessage('success', 'Registration successful! Please check your email to verify your account.');
            }

            redirect('/login');

        } catch (Exception $e) {
            $errors['general'] = 'Registration failed: ' . $e->getMessage();
        }
    }
}

$title = 'Register - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-md mx-auto' : '' ?>">
        <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6 text-center' ?>">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Create Account</h1>
            <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-2 text-gray-600' : 'mt-2' ?>">
                Register with your organization email address
            </p>
        </header>

        <?php if (!empty($errors['general'])): ?>
            <?= DesignSystem::alert($errors['general'], 'error') ?>
        <?php endif; ?>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6' ?>">
            <form method="POST">
                <?= csrfField() ?>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="email">Email Address *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= h($formData['email'] ?? '') ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['email']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        placeholder="your.name@yourorganization.com"
                        required
                        autofocus
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['email']) ?></p>
                    <?php else: ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Use your organization email address</p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'grid grid-cols-2 gap-4 mb-4' : 'ds_field-group' ?>">
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                        <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="first_name">First Name</label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?= h($formData['first_name'] ?? '') ?>"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        >
                    </div>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : '' ?>">
                        <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="last_name">Last Name</label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?= h($formData['last_name'] ?? '') ?>"
                            class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?>"
                        >
                    </div>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="password">Password *</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['password']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['password']) ?></p>
                    <?php else: ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_hint-text' : 'mt-1 text-sm text-gray-500' ?>">Minimum 8 characters</p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="password_confirm">Confirm Password *</label>
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['password_confirm']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                    >
                    <?php if (!empty($errors['password_confirm'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['password_confirm']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_checkbox' : 'flex items-center' ?>">
                        <input
                            type="checkbox"
                            name="terms"
                            required
                            class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mr-2' : '' ?>"
                        >
                        <span class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-700' : '' ?>">
                            I agree to the <a href="/terms" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:text-blue-800' : '' ?>">Terms of Service</a>
                        </span>
                    </label>
                </div>

                <?php
                $createAccountButtonClass = DesignSystem::getCurrentSystem() === 'tailwind'
                    ? 'w-full px-4 py-3 rounded-lg font-semibold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 border-0 transition-colors'
                    : '';
                ?>
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'space-y-3' ?>">
                    <?= DesignSystem::button('Create Account', null, 'primary', ['type' => 'submit', 'class' => $createAccountButtonClass]) ?>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-center text-sm text-gray-600' : 'text-center' ?>">
                        Already have an account? <a href="/login" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:text-blue-800 font-medium' : '' ?>">Sign in here</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
