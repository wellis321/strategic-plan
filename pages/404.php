<?php
$title = 'Page Not Found - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'text-center py-12' ?>">
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'mb-8' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-4xl font-bold text-gray-900 mb-4' ?>">Page Not Found</h1>
        <p class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-lg text-gray-600 mb-8' : 'mb-4' ?>">
            The page you're looking for doesn't exist or has been moved.
        </p>
    </div>

    <div class="<?= DesignSystem::getCurrentSystem() === 'tailwind' ? 'space-x-4' : 'ds_button-group' ?>">
        <?= DesignSystem::button('Go Home', '/', 'primary') ?>
        <?= DesignSystem::button('View Projects', '/projects', 'secondary') ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
