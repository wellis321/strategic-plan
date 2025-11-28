-- Phase 8: Hero background colors

START TRANSACTION;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'hero_bg_start'
        ),
        'SELECT "hero_bg_start already exists";',
        'ALTER TABLE organizations ADD COLUMN hero_bg_start VARCHAR(7) NOT NULL DEFAULT "#1d4ed8" AFTER hero_image_height;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'hero_bg_end'
        ),
        'SELECT "hero_bg_end already exists";',
        'ALTER TABLE organizations ADD COLUMN hero_bg_end VARCHAR(7) NOT NULL DEFAULT "#9333ea" AFTER hero_bg_start;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'show_hero'
        ),
        'SELECT "show_hero already exists";',
        'ALTER TABLE organizations ADD COLUMN show_hero TINYINT(1) NOT NULL DEFAULT 0 AFTER hero_bg_end;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'show_about'
        ),
        'SELECT "show_about already exists";',
        'ALTER TABLE organizations ADD COLUMN show_about TINYINT(1) NOT NULL DEFAULT 0 AFTER show_hero;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'show_vision'
        ),
        'SELECT "show_vision already exists";',
        'ALTER TABLE organizations ADD COLUMN show_vision TINYINT(1) NOT NULL DEFAULT 0 AFTER show_about;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'show_mission'
        ),
        'SELECT "show_mission already exists";',
        'ALTER TABLE organizations ADD COLUMN show_mission TINYINT(1) NOT NULL DEFAULT 0 AFTER show_vision;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

SET @stmt := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'organizations'
              AND COLUMN_NAME = 'show_values'
        ),
        'SELECT "show_values already exists";',
        'ALTER TABLE organizations ADD COLUMN show_values TINYINT(1) NOT NULL DEFAULT 0 AFTER show_mission;'
    )
);
PREPARE add_col FROM @stmt; EXECUTE add_col; DEALLOCATE PREPARE add_col;

COMMIT;
