-- Migration Script: Phase 3 - Vision, Mission, Values
-- This script adds organisation-level Vision, Mission, and Values fields
-- Run this after migration_phase2_multitenancy.sql has been executed

USE strategic_plan;

-- ============================================
-- MODIFY ORGANIZATIONS TABLE
-- ============================================

-- Add about_us field (organisation description/introduction)
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'organizations'
    AND COLUMN_NAME = 'about_us'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organizations ADD COLUMN about_us TEXT NULL AFTER contact_name',
    'SELECT "Column about_us already exists in organizations"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add vision field
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'organizations'
    AND COLUMN_NAME = 'vision'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organizations ADD COLUMN vision TEXT NULL AFTER about_us',
    'SELECT "Column vision already exists in organizations"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add mission field
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'strategic_plan'
    AND TABLE_NAME = 'organizations'
    AND COLUMN_NAME = 'mission'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organizations ADD COLUMN mission TEXT NULL AFTER vision',
    'SELECT "Column mission already exists in organizations"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create organization_values table (multiple values per organization)
CREATE TABLE IF NOT EXISTS organization_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    value_text TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_organization_id (organization_id),
    INDEX idx_sort_order (sort_order)
);

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Migration completed successfully!' as status;
SELECT 'New fields: about_us, vision, mission' as organizations_table;
SELECT 'New table: organization_values' as new_table;
