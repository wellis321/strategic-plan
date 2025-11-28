-- Add size field to organization_top_sections table
-- This allows organizations to control padding and font sizes while keeping height flexible

START TRANSACTION;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organization_top_sections'
              AND COLUMN_NAME = 'size'
        ),
        'SELECT "size column already exists";',
        'ALTER TABLE organization_top_sections ADD COLUMN size ENUM(\'small\', \'medium\', \'large\') NOT NULL DEFAULT \'medium\' AFTER image_position;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

COMMIT;
