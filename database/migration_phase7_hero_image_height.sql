-- Phase 7: Hero image height preset

START TRANSACTION;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'hero_image_height'
        ),
        'SELECT "hero_image_height already exists"',
        'ALTER TABLE organizations ADD COLUMN hero_image_height ENUM("short","medium","tall") NOT NULL DEFAULT "medium" AFTER hero_image_path;'
    )
);
PREPARE add_col FROM @stmt;
EXECUTE add_col;
DEALLOCATE PREPARE add_col;

COMMIT;
