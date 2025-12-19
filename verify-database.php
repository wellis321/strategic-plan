<?php
/**
 * Database Verification Script
 * Run this after importing the schema to verify everything is set up correctly
 */

// Load environment variables
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/config.php';

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Database Verification</h1>
    
    <h2>1. Required Tables</h2>
    <table>
        <tr><th>Table Name</th><th>Status</th><th>Row Count</th></tr>
        <?php
        $requiredTables = ['organizations', 'users', 'goals', 'projects', 'sessions'];
        foreach ($requiredTables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $result = $stmt->fetch();
                $count = $result['count'];
                $status = $count > 0 ? '<span class="success">✓ Exists</span>' : '<span class="warning">⚠ Empty</span>';
                echo "<tr><td>$table</td><td>$status</td><td>$count</td></tr>";
            } catch (PDOException $e) {
                echo "<tr><td>$table</td><td><span class=\"error\">✗ Missing</span></td><td>-</td></tr>";
            }
        }
        ?>
    </table>
    
    <h2>2. System Organisation</h2>
    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM organizations WHERE id = 1");
        $org = $stmt->fetch();
        if ($org) {
            echo "<p class=\"success\">✓ System Organisation exists:</p>";
            echo "<ul>";
            echo "<li>Name: " . htmlspecialchars($org['name']) . "</li>";
            echo "<li>Domain: " . htmlspecialchars($org['domain']) . "</li>";
            echo "<li>Status: " . htmlspecialchars($org['status']) . "</li>";
            echo "</ul>";
        } else {
            echo "<p class=\"error\">✗ System Organisation (id=1) not found</p>";
        }
    } catch (PDOException $e) {
        echo "<p class=\"error\">✗ Error checking organizations table: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
    
    <h2>3. Super Admin User</h2>
    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'super_admin' AND email = 'admin@system.local'");
        $admin = $stmt->fetch();
        if ($admin) {
            echo "<p class=\"success\">✓ Super Admin user exists:</p>";
            echo "<ul>";
            echo "<li>Email: " . htmlspecialchars($admin['email']) . "</li>";
            echo "<li>Name: " . htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) . "</li>";
            echo "<li>Role: " . htmlspecialchars($admin['role']) . "</li>";
            echo "<li>Status: " . htmlspecialchars($admin['status']) . "</li>";
            echo "<li>Email Verified: " . ($admin['email_verified'] ? 'Yes' : 'No') . "</li>";
            echo "</ul>";
            echo "<p><strong>Default credentials:</strong></p>";
            echo "<ul>";
            echo "<li>Email: <code>admin@system.local</code></li>";
            echo "<li>Password: <code>admin123</code></li>";
            echo "</ul>";
            echo "<p class=\"warning\">⚠ <strong>IMPORTANT:</strong> Change this password immediately after first login!</p>";
        } else {
            echo "<p class=\"error\">✗ Super Admin user not found</p>";
            echo "<p>Expected: admin@system.local with role 'super_admin'</p>";
        }
    } catch (PDOException $e) {
        echo "<p class=\"error\">✗ Error checking users table: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
    
    <h2>4. Next Steps</h2>
    <?php
    $allTablesExist = true;
    $systemOrgExists = false;
    $superAdminExists = false;
    
    // Check tables
    foreach ($requiredTables as $table) {
        try {
            $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        } catch (PDOException $e) {
            $allTablesExist = false;
            break;
        }
    }
    
    // Check system org
    try {
        $stmt = $pdo->query("SELECT 1 FROM organizations WHERE id = 1");
        $systemOrgExists = $stmt->fetch() !== false;
    } catch (PDOException $e) {
        $systemOrgExists = false;
    }
    
    // Check super admin
    try {
        $stmt = $pdo->query("SELECT 1 FROM users WHERE role = 'super_admin' AND email = 'admin@system.local'");
        $superAdminExists = $stmt->fetch() !== false;
    } catch (PDOException $e) {
        $superAdminExists = false;
    }
    
    if ($allTablesExist && $systemOrgExists && $superAdminExists) {
        echo "<p class=\"success\">✓ Database is ready!</p>";
        echo "<ol>";
        echo "<li>Go to <a href=\"/login\">/login</a> and log in with:</li>";
        echo "<ul>";
        echo "<li>Email: <code>admin@system.local</code></li>";
        echo "<li>Password: <code>admin123</code></li>";
        echo "</ul>";
        echo "<li>After logging in, go to <a href=\"/admin/organizations/new\">/admin/organizations/new</a> to create organizations</li>";
        echo "<li>Users can then register at <a href=\"/register\">/register</a> using their organization email addresses</li>";
        echo "</ol>";
    } else {
        echo "<p class=\"error\">✗ Database setup incomplete. Please:</p>";
        echo "<ol>";
        if (!$allTablesExist) {
            echo "<li>Import the database schema file: <code>database/hostinger-complete-schema.sql</code></li>";
        }
        if (!$systemOrgExists) {
            echo "<li>Verify the System Organisation was created</li>";
        }
        if (!$superAdminExists) {
            echo "<li>Verify the Super Admin user was created</li>";
        }
        echo "</ol>";
    }
    ?>
    
    <hr>
    <p><small>Run this script after importing the database schema to verify everything is set up correctly.</small></p>
</body>
</html>

