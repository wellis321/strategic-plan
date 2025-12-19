-- Complete Database Schema for Hostinger
-- Database: u248320297_strategic_plan
-- This file combines the base schema and multi-tenancy migration

-- ============================================
-- BASE TABLES
-- ============================================

-- Goals table
CREATE TABLE IF NOT EXISTS goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(10) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    responsible_director VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_number (number)
);

-- Goal statements table (for multiple statements per goal)
CREATE TABLE IF NOT EXISTS goal_statements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    goal_id INT NOT NULL,
    statement TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES goals(id) ON DELETE CASCADE,
    INDEX idx_goal_id (goal_id)
);

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    project_number VARCHAR(50) NOT NULL UNIQUE,
    goal_id INT NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    project_group VARCHAR(255),
    start_date DATE,
    end_date DATE,
    meeting_frequency VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES goals(id) ON DELETE RESTRICT,
    INDEX idx_project_number (project_number),
    INDEX idx_goal_id (goal_id),
    INDEX idx_slug (slug)
);

-- Project leads table (many-to-many relationship)
CREATE TABLE IF NOT EXISTS project_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    lead_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Project working group members table
CREATE TABLE IF NOT EXISTS project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    member_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Project purposes table (multiple purposes per project)
CREATE TABLE IF NOT EXISTS project_purposes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    purpose TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Project milestones table
CREATE TABLE IF NOT EXISTS project_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    target_date DATE,
    completed_date DATE,
    status ENUM('pending', 'in_progress', 'completed', 'overdue') DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_status (status),
    INDEX idx_target_date (target_date)
);

-- Project progress reports table
CREATE TABLE IF NOT EXISTS project_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    report_date DATE NOT NULL,
    progress_percentage DECIMAL(5,2) DEFAULT 0,
    status ENUM('on_track', 'at_risk', 'delayed', 'completed') DEFAULT 'on_track',
    summary TEXT,
    challenges TEXT,
    next_steps TEXT,
    created_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_report_date (report_date),
    INDEX idx_status (status)
);

-- ============================================
-- MULTI-TENANCY TABLES
-- ============================================

-- Organizations table
CREATE TABLE IF NOT EXISTS organizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    seat_allocation INT NOT NULL DEFAULT 10,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    contact_email VARCHAR(255),
    contact_name VARCHAR(255),
    about_us TEXT,
    vision TEXT,
    mission TEXT,
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    hero_image_path VARCHAR(255),
    hero_image_height ENUM('small', 'medium', 'large', 'full') DEFAULT 'medium',
    hero_bg_start VARCHAR(7) DEFAULT '#1d4ed8',
    hero_bg_end VARCHAR(7) DEFAULT '#9333ea',
    show_hero BOOLEAN DEFAULT TRUE,
    show_about BOOLEAN DEFAULT TRUE,
    show_vision BOOLEAN DEFAULT TRUE,
    show_mission BOOLEAN DEFAULT TRUE,
    show_values BOOLEAN DEFAULT TRUE,
    about_image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NULL,
    INDEX idx_domain (domain),
    INDEX idx_slug (slug),
    INDEX idx_status (status)
);

-- Organization values table
CREATE TABLE IF NOT EXISTS organization_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    value TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_organization_id (organization_id)
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
    INDEX idx_email_verified (email_verified),
    INDEX idx_users_org_verified (organization_id, email_verified, status)
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

-- Strategic Plans table
CREATE TABLE IF NOT EXISTS strategic_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    start_year INT,
    end_year INT,
    is_active BOOLEAN DEFAULT TRUE,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_org_slug (organization_id, slug),
    INDEX idx_organization_id (organization_id),
    INDEX idx_slug (slug)
);

-- Strategic Plan Sections table
CREATE TABLE IF NOT EXISTS strategic_plan_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    strategic_plan_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (strategic_plan_id) REFERENCES strategic_plans(id) ON DELETE CASCADE,
    INDEX idx_strategic_plan_id (strategic_plan_id)
);

-- Organization Top Sections table
CREATE TABLE IF NOT EXISTS organization_top_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    strategic_plan_id INT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image_path VARCHAR(255),
    size ENUM('small', 'medium', 'large') DEFAULT 'medium',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (strategic_plan_id) REFERENCES strategic_plans(id) ON DELETE SET NULL,
    INDEX idx_organization_id (organization_id),
    INDEX idx_strategic_plan_id (strategic_plan_id)
);

-- ============================================
-- MODIFY EXISTING TABLES
-- ============================================

-- Add organization_id to goals table (if not exists)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
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
    WHERE TABLE_SCHEMA = DATABASE()
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
    WHERE TABLE_SCHEMA = DATABASE()
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
    WHERE TABLE_SCHEMA = DATABASE()
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

-- Make responsible_director nullable in goals (if not already)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'goals'
    AND COLUMN_NAME = 'responsible_director'
    AND IS_NULLABLE = 'NO'
);

SET @sql = IF(@col_exists > 0,
    'ALTER TABLE goals MODIFY COLUMN responsible_director VARCHAR(255) NULL',
    'SELECT "Column responsible_director is already nullable"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- ADD FOREIGN KEY CONSTRAINTS
-- ============================================

-- For goals table
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
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
    WHERE CONSTRAINT_SCHEMA = DATABASE()
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
    WHERE CONSTRAINT_SCHEMA = DATABASE()
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
    WHERE CONSTRAINT_SCHEMA = DATABASE()
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
-- CREATE DEFAULT DATA
-- ============================================

-- Create default "System" organization for existing data
INSERT IGNORE INTO organizations (id, name, domain, slug, seat_allocation, status, created_by)
VALUES (1, 'System Organisation', 'system.local', 'system', 999, 'active', NULL);

-- Create a default super admin user
-- Password: 'admin123' (CHANGE THIS IMMEDIATELY IN PRODUCTION!)
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
-- CREATE VIEWS
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

-- ============================================
-- MIGRATION COMPLETE
-- ============================================

SELECT 'Database schema created successfully!' as status;
SELECT 'Default super admin created: admin@system.local / admin123' as admin_info;
SELECT 'IMPORTANT: Change the super admin password immediately!' as warning;

