<?php
// Login page
$userModel = new User();

$errors = [];
$email = '';

if (isLoggedIn()) {
    redirect('/');
}

if (isPost()) {
    $postData = getPostData();
    $email = sanitizeInput($postData['email'] ?? '');
    $password = $postData['password'] ?? '';

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        $user = $userModel->authenticate($email, $password);

        if ($user) {
            // Check email_verified (can be 0/1 from database or boolean)
            $emailVerified = (bool)$user['email_verified'] || $user['email_verified'] === 1 || $user['email_verified'] === '1';

            if (!$emailVerified) {
                $errors['general'] = 'Please verify your email address before logging in. Check your inbox for the verification link.';
            } elseif ($user['status'] !== 'active') {
                $errors['general'] = 'Your account is not active. Please contact your administrator.';
            } else {
                login($user);
                setFlashMessage('success', 'Welcome back, ' . ($user['first_name'] ?? $user['email']) . '!');

                // Redirect to intended page or dashboard
                $redirectTo = $_SESSION['redirect_after_login'] ?? '/';
                unset($_SESSION['redirect_after_login']);
                redirect($redirectTo);
            }
        } else {
            $errors['general'] = 'Invalid email or password';
        }
    }
}

$title = 'Login - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-md mx-auto' : '' ?>">
        <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6 text-center' ?>">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Login</h1>
            <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-2 text-gray-600' : 'mt-2' ?>">
                Sign in to access your strategic plan
            </p>
        </header>

        <?php if (!empty($errors['general'])): ?>
            <?= DesignSystem::alert($errors['general'], 'error') ?>
        <?php endif; ?>

        <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question-group' : 'bg-white shadow rounded-lg p-6' ?>">
            <form method="POST">
                <?= csrfField() ?>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= h($email) ?>"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['email']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                        autofocus
                    >
                    <?php if (!empty($errors['email'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question' : 'mb-4' ?>">
                    <label class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_label' : 'block text-sm font-medium text-gray-700 mb-1' ?>" for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input' : 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500' ?> <?= !empty($errors['password']) ? (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_input--error' : 'border-red-500') : '' ?>"
                        required
                    >
                    <?php if (!empty($errors['password'])): ?>
                        <p class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__error-message' : 'mt-1 text-sm text-red-600' ?>"><?= h($errors['password']) ?></p>
                    <?php endif; ?>
                    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_question__hint' : 'mt-1 text-right' ?>">
                        <a href="/forgot-password" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-blue-600 hover:text-blue-800' : '' ?>">Forgot password?</a>
                    </div>
                </div>

                <?php
                $signInButtonClass = DesignSystem::getCurrentSystem() === 'tailwind'
                    ? 'w-full px-4 py-3 rounded-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 border-0 transition-colors'
                    : '';
                ?>
                <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'space-y-3' ?>">
                    <?= DesignSystem::button('Sign In', null, 'primary', ['type' => 'submit', 'class' => $signInButtonClass]) ?>

                    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-center text-sm text-gray-600' : 'text-center' ?>">
                        Don't have an account? <a href="/register" class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-blue-600 hover:text-blue-800 font-medium' : '' ?>">Register here</a>
                    </div>
                </div>
            </form>
        </div>

        <?php if (APP_ENV === 'development' && isSuperAdmin()): ?>
        <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded' : 'mt-4' ?>">
            <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-yellow-800' : '' ?>">
                <strong>Development Mode:</strong> Super admin login: admin@system.local / admin123
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
