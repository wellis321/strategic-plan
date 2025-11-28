-- Migration Script: Phase 5 - Multiple Strategic Plans per Organisation
-- This script adds support for organisations to have multiple strategic plans with custom slugs
-- Run this after migration_phase4_strategic_plan_sections.sql has been executed

USE strategic_plan;

-- ============================================
-- CREATE STRATEGIC PLANS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS strategic_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    slug VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    start_year INT,
    end_year INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_org_slug (organization_id, slug),
    INDEX idx_organization_id (organization_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_is_active (is_active)
);

-- Add slug to organizations table for URL routing
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'organizations'
    AND COLUMN_NAME = 'slug'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organizations ADD COLUMN slug VARCHAR(255) NULL AFTER domain, ADD UNIQUE KEY unique_slug (slug), ADD INDEX idx_slug (slug)',
    'SELECT "Column slug already exists in organizations"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Generate slugs for existing organizations
UPDATE organizations SET slug = LOWER(REPLACE(REPLACE(REPLACE(name, ' ', '-'), '.', ''), '_', '-')) WHERE slug IS NULL;

-- ============================================
-- ADD PLAN_ID TO EXISTING TABLES
-- ============================================

-- Add plan_id to goals
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'goals'
    AND COLUMN_NAME = 'plan_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE goals ADD COLUMN plan_id INT NULL AFTER organization_id, ADD FOREIGN KEY (plan_id) REFERENCES strategic_plans(id) ON DELETE CASCADE, ADD INDEX idx_plan_id (plan_id)',
    'SELECT "Column plan_id already exists in goals"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add plan_id to projects
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'plan_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE projects ADD COLUMN plan_id INT NULL AFTER organization_id, ADD FOREIGN KEY (plan_id) REFERENCES strategic_plans(id) ON DELETE CASCADE, ADD INDEX idx_plan_id (plan_id)',
    'SELECT "Column plan_id already exists in projects"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add plan_id to strategic_plan_sections
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'strategic_plan_sections'
    AND COLUMN_NAME = 'plan_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE strategic_plan_sections ADD COLUMN plan_id INT NULL AFTER organization_id, ADD FOREIGN KEY (plan_id) REFERENCES strategic_plans(id) ON DELETE CASCADE, ADD INDEX idx_plan_id (plan_id)',
    'SELECT "Column plan_id already exists in strategic_plan_sections"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- MIGRATE EXISTING DATA
-- ============================================

-- Create a default plan for each organization with existing data
INSERT IGNORE INTO strategic_plans (organization_id, slug, title, status, is_active, created_at)
SELECT
    o.id,
    CONCAT('plan-', YEAR(NOW())),
    CONCAT('Strategic Plan ', YEAR(NOW())),
    'published',
    TRUE,
    NOW()
FROM organizations o
WHERE NOT EXISTS (
    SELECT 1 FROM strategic_plans sp WHERE sp.organization_id = o.id
);

-- Update existing goals to link to their organization's default plan
UPDATE goals g
INNER JOIN strategic_plans sp ON g.organization_id = sp.organization_id AND sp.is_active = TRUE
SET g.plan_id = sp.id
WHERE g.plan_id IS NULL;

-- Update existing projects to link to their organization's default plan
UPDATE projects p
INNER JOIN strategic_plans sp ON p.organization_id = sp.organization_id AND sp.is_active = TRUE
SET p.plan_id = sp.id
WHERE p.plan_id IS NULL;

-- Update existing sections to link to their organization's default plan
UPDATE strategic_plan_sections s
INNER JOIN strategic_plans sp ON s.organization_id = sp.organization_id AND sp.is_active = TRUE
SET s.plan_id = sp.id
WHERE s.plan_id IS NULL;

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Migration completed successfully!' as status;
SELECT 'New table: strategic_plans' as new_table;
SELECT 'plan_id added to: goals, projects, strategic_plan_sections' as updated_tables;
SELECT 'Default plans created for existing organizations' as migration_note;
