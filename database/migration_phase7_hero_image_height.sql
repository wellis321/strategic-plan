-- Phase 7: Hero image height preset

START TRANSACTION;

SET @sql := IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'organizations'
       AND COLUMN_NAME = 'hero_image_height') = 0,
    'ALTER TABLE organizations ADD COLUMN hero_image_height ENUM("short","medium","tall") NOT NULL DEFAULT "medium" AFTER hero_image_path;',
    'SELECT "hero_image_height already exists";'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
