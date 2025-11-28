-- Migration Script: Phase 2 - Multi-Tenancy & User Management
-- This script adds multi-tenancy support to the Strategic Plan Manager
-- Run this after the initial schema.sql has been executed

USE strategic_plan;

-- ============================================
-- NEW TABLES
-- ============================================

-- Organizations table
CREATE TABLE IF NOT EXISTS organizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) NOT NULL UNIQUE,
    seat_allocation INT NOT NULL DEFAULT 10,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    contact_email VARCHAR(255),
    contact_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NULL,
    INDEX idx_domain (domain),
    INDEX idx_status (status)
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role ENUM('admin', 'user', 'super_admin') DEFAULT 'user',
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255) NULL,
    verification_token_expires TIMESTAMP NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_expires TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'pending_verification') DEFAULT 'pending_verification',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_email (email),
    INDEX idx_organization_id (organization_id),
    INDEX idx_email (email),
    INDEX idx_verification_token (verification_token),
    INDEX idx_password_reset_token (password_reset_token),
    INDEX idx_status (status),
    INDEX idx_email_verified (email_verified)
);

-- Sessions table (for database-backed sessions)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

-- ============================================
-- MODIFY EXISTING TABLES
-- ============================================

-- Add organization_id to goals table (if not exists)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND COLUMN_NAME = 'organization_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE goals ADD COLUMN organization_id INT NULL AFTER id',
    'SELECT "Column organization_id already exists in goals"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add created_by to goals table (if not exists)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND COLUMN_NAME = 'created_by'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE goals ADD COLUMN created_by INT NULL AFTER responsible_director',
    'SELECT "Column created_by already exists in goals"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add organization_id to projects table (if not exists)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'organization_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE projects ADD COLUMN organization_id INT NULL AFTER id',
    'SELECT "Column organization_id already exists in projects"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add created_by to projects table (if not exists)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'created_by'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE projects ADD COLUMN created_by INT NULL AFTER meeting_frequency',
    'SELECT "Column created_by already exists in projects"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- DATA MIGRATION
-- ============================================

-- Create default "System" organization for existing data
INSERT IGNORE INTO organizations (id, name, domain, seat_allocation, status, created_by)
VALUES (1, 'System Organization', 'system.local', 999, 'active', NULL);

-- Update existing goals to belong to system organization
UPDATE goals SET organization_id = 1 WHERE organization_id IS NULL;

-- Update existing projects to belong to system organization
UPDATE projects SET organization_id = 1 WHERE organization_id IS NULL;

-- ============================================
-- ADD FOREIGN KEY CONSTRAINTS
-- ============================================

-- Add foreign key constraints now that data is migrated
-- Check if constraints don't already exist before adding

-- For goals table
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND CONSTRAINT_NAME = 'fk_goals_organization'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE goals ADD CONSTRAINT fk_goals_organization FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE RESTRICT',
    'SELECT "Constraint fk_goals_organization already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND CONSTRAINT_NAME = 'fk_goals_created_by'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE goals ADD CONSTRAINT fk_goals_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT "Constraint fk_goals_created_by already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- For projects table
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND CONSTRAINT_NAME = 'fk_projects_organization'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE projects ADD CONSTRAINT fk_projects_organization FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE RESTRICT',
    'SELECT "Constraint fk_projects_organization already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND CONSTRAINT_NAME = 'fk_projects_created_by'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE projects ADD CONSTRAINT fk_projects_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT "Constraint fk_projects_created_by already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- CREATE SUPER ADMIN USER
-- ============================================

-- Create a default super admin user
-- Password: 'admin123' (CHANGE THIS IMMEDIATELY IN PRODUCTION!)
-- This will be hashed with bcrypt
INSERT IGNORE INTO users (
    organization_id,
    email,
    password_hash,
    first_name,
    last_name,
    role,
    email_verified,
    status
) VALUES (
    1, -- System organization
    'admin@system.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- 'admin123' hashed
    'Super',
    'Administrator',
    'super_admin',
    TRUE,
    'active'
);

-- ============================================
-- HELPER VIEWS (Optional but useful)
-- ============================================

-- View: Organization seat usage
CREATE OR REPLACE VIEW organization_seat_usage AS
SELECT
    o.id,
    o.name,
    o.domain,
    o.seat_allocation,
    COUNT(u.id) as seats_used,
    (o.seat_allocation - COUNT(u.id)) as seats_available,
    o.status
FROM organizations o
LEFT JOIN users u ON o.id = u.organization_id
    AND u.email_verified = TRUE
    AND u.status = 'active'
GROUP BY o.id, o.name, o.domain, o.seat_allocation, o.status;

-- View: Pending verifications
CREATE OR REPLACE VIEW pending_verifications AS
SELECT
    u.id,
    u.email,
    u.first_name,
    u.last_name,
    u.verification_token_expires,
    o.name as organization_name,
    o.domain
FROM users u
JOIN organizations o ON u.organization_id = o.id
WHERE u.email_verified = FALSE
    AND u.status = 'pending_verification'
    AND (u.verification_token_expires IS NULL OR u.verification_token_expires > NOW())
ORDER BY u.created_at DESC;

-- ============================================
-- STORED PROCEDURES (Optional but helpful)
-- ============================================

-- Procedure: Check if domain has available seats
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS CheckSeatAvailability(
    IN p_domain VARCHAR(255),
    OUT p_available BOOLEAN,
    OUT p_used INT,
    OUT p_total INT
)
BEGIN
    DECLARE v_seat_allocation INT;
    DECLARE v_seats_used INT;

    SELECT seat_allocation INTO v_seat_allocation
    FROM organizations
    WHERE domain = p_domain AND status = 'active';

    IF v_seat_allocation IS NULL THEN
        SET p_available = FALSE;
        SET p_used = 0;
        SET p_total = 0;
    ELSE
        SELECT COUNT(*) INTO v_seats_used
        FROM users
        WHERE organization_id = (SELECT id FROM organizations WHERE domain = p_domain)
            AND email_verified = TRUE
            AND status = 'active';

        SET p_used = v_seats_used;
        SET p_total = v_seat_allocation;
        SET p_available = (v_seats_used < v_seat_allocation);
    END IF;
END //
DELIMITER ;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================

-- Additional indexes for common queries (check if they exist first)
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'users'
    AND INDEX_NAME = 'idx_users_org_verified'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_users_org_verified ON users(organization_id, email_verified, status)',
    'SELECT "Index idx_users_org_verified already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND INDEX_NAME = 'idx_goals_org'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_goals_org ON goals(organization_id)',
    'SELECT "Index idx_goals_org already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND INDEX_NAME = 'idx_projects_org'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_projects_org ON projects(organization_id)',
    'SELECT "Index idx_projects_org already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- MIGRATION COMPLETE
-- ============================================

SELECT 'Migration Phase 2 completed successfully!' as status;
SELECT 'Remember to:' as reminder;
SELECT '1. Change the default super admin password' as step1;
SELECT '2. Create your first organization' as step2;
SELECT '3. Test user registration and email verification' as step3;
