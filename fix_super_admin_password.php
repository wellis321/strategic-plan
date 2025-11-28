<?php
/**
 * Fix Super Admin Password
 *
 * This script updates the super admin password to 'admin123'
 * Run this if you need to reset the super admin password
 *
 * Usage: php fix_super_admin_password.php
 */

require_once 'config/bootstrap.php';

$userModel = new User();
$user = $userModel->getByEmail('admin@system.local');

if (!$user) {
    die("ERROR: Super admin user not found!\n");
}

if ($user['role'] !== 'super_admin') {
    die("ERROR: User found but is not a super admin!\n");
}

// Generate new password hash
$newPassword = 'admin123';
$passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

// Update the password
$userModel->update($user['id'], ['password' => $newPassword]);

echo "SUCCESS: Super admin password has been reset!\n";
echo "Email: admin@system.local\n";
echo "Password: admin123\n";
echo "\n";
echo "You can now log in with these credentials.\n";
echo "\n";
echo "IMPORTANT: Change this password after first login!\n";
