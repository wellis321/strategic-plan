-- Verification Script for Phase 2 Migration
-- Run this after migration_phase2_multitenancy.sql to verify everything worked

USE strategic_plan;

-- ============================================
-- CHECK NEW TABLES EXIST
-- ============================================

SELECT 'Checking new tables...' as status;

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ organizations table exists'
        ELSE '✗ organizations table missing'
    END as organizations_table
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'strategic_plan' AND TABLE_NAME = 'organizations';

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ users table exists'
        ELSE '✗ users table missing'
    END as users_table
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'strategic_plan' AND TABLE_NAME = 'users';

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ sessions table exists'
        ELSE '✗ sessions table missing'
    END as sessions_table
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'strategic_plan' AND TABLE_NAME = 'sessions';

-- ============================================
-- CHECK COLUMNS WERE ADDED
-- ============================================

SELECT 'Checking modified tables...' as status;

SELECT
    CASE
        WHEN COUNT(*) > 0 THEN '✓ organization_id column exists in goals'
        ELSE '✗ organization_id column missing in goals'
    END as goals_organization_id
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND COLUMN_NAME = 'organization_id';

SELECT
    CASE
        WHEN COUNT(*) > 0 THEN '✓ created_by column exists in goals'
        ELSE '✗ created_by column missing in goals'
    END as goals_created_by
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND COLUMN_NAME = 'created_by';

SELECT
    CASE
        WHEN COUNT(*) > 0 THEN '✓ organization_id column exists in projects'
        ELSE '✗ organization_id column missing in projects'
    END as projects_organization_id
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'organization_id';

SELECT
    CASE
        WHEN COUNT(*) > 0 THEN '✓ created_by column exists in projects'
        ELSE '✗ created_by column missing in projects'
    END as projects_created_by
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'created_by';

-- ============================================
-- CHECK DEFAULT DATA
-- ============================================

SELECT 'Checking default data...' as status;

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN CONCAT('✓ System organization created (ID: ', id, ')')
        ELSE '✗ System organization missing'
    END as system_organization
FROM organizations
WHERE domain = 'system.local';

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ Super admin user created'
        ELSE '✗ Super admin user missing'
    END as super_admin
FROM users
WHERE role = 'super_admin' AND email = 'admin@system.local';

-- ============================================
-- CHECK DATA MIGRATION
-- ============================================

SELECT 'Checking data migration...' as status;

SELECT
    COUNT(*) as total_goals,
    SUM(CASE WHEN organization_id = 1 THEN 1 ELSE 0 END) as goals_assigned_to_system,
    SUM(CASE WHEN organization_id IS NULL THEN 1 ELSE 0 END) as goals_without_organization
FROM goals;

SELECT
    COUNT(*) as total_projects,
    SUM(CASE WHEN organization_id = 1 THEN 1 ELSE 0 END) as projects_assigned_to_system,
    SUM(CASE WHEN organization_id IS NULL THEN 1 ELSE 0 END) as projects_without_organization
FROM projects;

-- ============================================
-- CHECK FOREIGN KEYS
-- ============================================

SELECT 'Checking foreign keys...' as status;

SELECT
    CASE
        WHEN COUNT(*) > 0 THEN '✓ Foreign key fk_goals_organization exists'
        ELSE '✗ Foreign key fk_goals_organization missing'
    END as fk_goals_org
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND CONSTRAINT_NAME = 'fk_goals_organization';

SELECT
    CASE
        WHEN COUNT(*) > 0 THEN '✓ Foreign key fk_projects_organization exists'
        ELSE '✗ Foreign key fk_projects_organization missing'
    END as fk_projects_org
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND CONSTRAINT_NAME = 'fk_projects_organization';

-- ============================================
-- CHECK VIEWS
-- ============================================

SELECT 'Checking views...' as status;

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ organization_seat_usage view exists'
        ELSE '✗ organization_seat_usage view missing'
    END as seat_usage_view
FROM information_schema.VIEWS
WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'organization_seat_usage';

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ pending_verifications view exists'
        ELSE '✗ pending_verifications view missing'
    END as pending_verifications_view
FROM information_schema.VIEWS
WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'pending_verifications';

-- ============================================
-- CHECK STORED PROCEDURE
-- ============================================

SELECT 'Checking stored procedures...' as status;

SELECT
    CASE
        WHEN COUNT(*) = 1 THEN '✓ CheckSeatAvailability procedure exists'
        ELSE '✗ CheckSeatAvailability procedure missing'
    END as seat_check_procedure
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'strategic_plan'
    AND ROUTINE_NAME = 'CheckSeatAvailability';

-- ============================================
-- TEST VIEWS
-- ============================================

SELECT 'Testing views...' as status;

SELECT * FROM organization_seat_usage;

SELECT * FROM pending_verifications;

-- ============================================
-- SUMMARY
-- ============================================

SELECT '=== MIGRATION VERIFICATION COMPLETE ===' as summary;
SELECT 'Review the results above. All items should show ✓' as note;
SELECT 'If any items show ✗, check the migration script output for errors' as troubleshooting;
