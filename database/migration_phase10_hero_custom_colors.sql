-- Phase 10: Add custom hero colors to top sections
-- This allows each hero section to have its own gradient colors

START TRANSACTION;

-- Add hero_bg_start column if it doesn't exist
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'organization_top_sections'
    AND COLUMN_NAME = 'hero_bg_start'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organization_top_sections ADD COLUMN hero_bg_start VARCHAR(7) NULL AFTER size;',
    'SELECT "hero_bg_start already exists";'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add hero_bg_end column if it doesn't exist
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'organization_top_sections'
    AND COLUMN_NAME = 'hero_bg_end'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE organization_top_sections ADD COLUMN hero_bg_end VARCHAR(7) NULL AFTER hero_bg_start;',
    'SELECT "hero_bg_end already exists";'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
