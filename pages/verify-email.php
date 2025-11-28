<?php
// Email verification page
$userModel = new User();

$token = getQueryParam('token');
$success = false;
$error = null;

if ($token) {
    $success = $userModel->verifyEmail($token);

    if (!$success) {
        $error = 'Invalid or expired verification token. Please request a new verification email.';
    } else {
        setFlashMessage('success', 'Email verified successfully! You can now log in.');
    }
} else {
    $error = 'Verification token is required';
}

$title = 'Verify Email - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'max-w-md mx-auto' : '' ?>">
        <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-6 text-center' ?>">
            <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Email Verification</h1>
        </header>

        <?php if ($success): ?>
            <?= DesignSystem::alert('Your email has been verified successfully! You can now log in to your account.', 'success') ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'mt-6 text-center' ?>">
                <?= DesignSystem::button('Go to Login', '/login', 'primary') ?>
            </div>
        <?php else: ?>
            <?= DesignSystem::alert($error ?? 'Verification failed. Please check your verification link or request a new one.', 'error') ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_button-group' : 'mt-6 space-y-3' ?>">
                <?= DesignSystem::button('Go to Login', '/login', 'primary') ?>
                <?= DesignSystem::button('Request New Verification Email', '/resend-verification', 'secondary') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
