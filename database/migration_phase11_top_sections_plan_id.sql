-- Phase 11: Add plan_id to organization_top_sections
-- This allows top sections to be assigned to specific plans or be organization-wide (plan_id = NULL)

START TRANSACTION;

-- Add plan_id column if it doesn't exist
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'organization_top_sections'
    AND COLUMN_NAME = 'plan_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organization_top_sections ADD COLUMN plan_id INT NULL AFTER organization_id, ADD FOREIGN KEY (plan_id) REFERENCES strategic_plans(id) ON DELETE CASCADE, ADD INDEX idx_plan_id (plan_id, organization_id)',
    'SELECT "plan_id already exists in organization_top_sections";'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
