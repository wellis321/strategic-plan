<?php
// Super Admin Dashboard
requireSuperAdmin();

$orgModel = new Organization();
$userModel = new User();

// Get statistics
$organizations = $orgModel->getAll();
$totalOrganizations = count($organizations);
$activeOrganizations = count(array_filter($organizations, fn($org) => $org['status'] === 'active'));

// Get total users across all organizations
$sql = "SELECT
            COUNT(*) as total_users,
            SUM(CASE WHEN email_verified = TRUE AND status = 'active' THEN 1 ELSE 0 END) as verified_users,
            SUM(CASE WHEN email_verified = FALSE THEN 1 ELSE 0 END) as pending_verifications
        FROM users";
$userStats = Database::getInstance()->fetchOne($sql);

// Get seat usage summary
$sql = "SELECT
            SUM(seat_allocation) as total_seats_allocated,
            COUNT(DISTINCT u.id) as total_seats_used
        FROM organizations o
        LEFT JOIN users u ON o.id = u.organization_id
            AND u.email_verified = TRUE
            AND u.status = 'active'";
$seatStats = Database::getInstance()->fetchOne($sql);

$title = 'Super Admin Dashboard - ' . APP_NAME;
ob_start();
?>

<div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_!_margin-bottom--4' : 'mb-8' ?>">
    <header class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header' : 'flex justify-between items-center mb-6' ?>">
        <h1 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_page-header__title' : 'text-3xl font-bold text-gray-900' ?>">Super Admin Dashboard</h1>
        <?= DesignSystem::button('New Organization', '/admin/organizations/new', 'primary') ?>
    </header>

    <!-- Statistics Cards -->
    <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8' ?>">
        <?php
        $stats = [
            ['label' => 'Total Organizations', 'value' => $totalOrganizations, 'color' => 'blue'],
            ['label' => 'Active Organizations', 'value' => $activeOrganizations, 'color' => 'green'],
            ['label' => 'Total Users', 'value' => $userStats['total_users'] ?? 0, 'color' => 'purple'],
            ['label' => 'Seats Used', 'value' => ($seatStats['total_seats_used'] ?? 0) . ' / ' . ($seatStats['total_seats_allocated'] ?? 0), 'color' => 'orange'],
        ];

        foreach ($stats as $stat):
            $statContent = "
                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'text-center') . "'>
                    <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                        <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-3xl font-bold text-' . $stat['color'] . '-600' : '') . "'>" . h($stat['value']) . "</dt>
                        <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : '') . "'>" . h($stat['label']) . "</dd>
                    </div>
                </div>
            ";
            echo DesignSystem::card($statContent, null, ['class' => DesignSystem::getCurrentSystem() === 'tailwind' ? 'p-6' : '']);
        endforeach;
        ?>
    </div>

    <!-- Organizations List -->
    <div>
        <h2 class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_heading--medium ds_!_margin-bottom--3' : 'text-xl font-semibold text-gray-900 mb-4' ?>">Organizations</h2>

        <?php if (empty($organizations)): ?>
            <?= DesignSystem::alert('No organizations found. <a href="/admin/organizations/new">Create your first organization</a>.', 'info') ?>
        <?php else: ?>
            <div class="<?= DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card-list' : 'space-y-4' ?>">
                <?php foreach ($organizations as $org): ?>
                    <?php
                    $usage = $orgModel->getSeatUsage($org['id']);
                    $seatsAvailable = $usage['seats_available'] ?? 0;
                    $seatsUsed = $usage['seats_used'] ?? 0;
                    $seatPercentage = $usage['seat_allocation'] > 0 ? round(($seatsUsed / $usage['seat_allocation']) * 100, 1) : 0;

                    $orgContent = "
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__header' : 'flex justify-between items-start mb-4') . "'>
                            <div>
                                <h3 class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__title' : 'text-lg font-semibold text-gray-900') . "'>" . h($org['name']) . "</h3>
                                <p class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-600 mt-1' : 'mt-1') . "'>" . h($org['domain']) . "</p>
                            </div>
                            <span class='" . getStatusBadgeClass($org['status']) . "'>" . formatStatus($org['status']) . "</span>
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__body' : 'mb-4') . "'>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata' : 'grid grid-cols-1 md:grid-cols-2 gap-4 text-sm') . "'>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Seat Allocation</dt>
                                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($usage['seat_allocation']) . " seats</dd>
                                </div>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Seats Used</dt>
                                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($seatsUsed) . " / " . h($usage['seat_allocation']) . " (" . h($seatPercentage) . "%)</dd>
                                </div>
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Seats Available</dt>
                                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($seatsAvailable) . "</dd>
                                </div>
                                " . ($org['contact_name'] ? "
                                <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_metadata__item' : '') . "'>
                                    <dt class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'font-medium text-gray-900' : '') . "'>Contact</dt>
                                    <dd class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-gray-600' : '') . "'>" . h($org['contact_name']) . "</dd>
                                </div>
                                " : "") . "
                            </div>

                            " . ($seatPercentage >= 80 ? "
                            <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded' : 'mt-4') . "'>
                                <p class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-yellow-800' : '') . "'>
                                    <strong>Warning:</strong> " . h($seatPercentage) . "% of seats are in use. Consider increasing allocation.
                                </p>
                            </div>
                            " : "") . "
                        </div>
                        <div class='" . (DesignSystem::getCurrentSystem() === 'sgds' ? 'ds_card__footer' : 'flex justify-between items-center pt-4 border-t') . "'>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'flex space-x-2' : '') . "'>
                                " . DesignSystem::button('View Users', '/admin/organizations/' . $org['id'] . '/users', 'secondary') . "
                                " . DesignSystem::button('Edit', '/admin/organizations/' . $org['id'] . '/edit', 'secondary') . "
                            </div>
                            <div class='" . (DesignSystem::getCurrentSystem() === 'tailwind' ? 'text-sm text-gray-500' : '') . "'>
                                Created: " . formatDateForDisplay($org['created_at']) . "
                            </div>
                        </div>
                    ";
                    echo DesignSystem::card($orgContent);
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../templates/layout.php';
?>
