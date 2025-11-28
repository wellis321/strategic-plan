-- Fix goal number uniqueness to be per plan instead of globally unique
-- This allows the same goal number to be used in different plans

START TRANSACTION;

-- Drop the existing UNIQUE constraint on number (if it exists)
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'goals'
    AND INDEX_NAME = 'number'
);

SET @sql = IF(@index_exists > 0,
    'ALTER TABLE goals DROP INDEX number',
    'SELECT "Index number does not exist"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop idx_number if it exists (might have been created separately)
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'goals'
    AND INDEX_NAME = 'idx_number'
);

SET @sql = IF(@index_exists > 0,
    'ALTER TABLE goals DROP INDEX idx_number',
    'SELECT "Index idx_number does not exist"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add a composite unique index on (plan_id, number) for plan-specific uniqueness
-- Note: This allows NULL plan_id values (for backwards compatibility), but multiple NULLs with same number are allowed
-- For new goals, plan_id should always be set, so this works correctly
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'goals'
    AND INDEX_NAME = 'idx_plan_number'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE goals ADD UNIQUE INDEX idx_plan_number (plan_id, number)',
    'SELECT "Index idx_plan_number already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add regular index for faster lookups (if it doesn't exist)
SET @index_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'goals'
    AND INDEX_NAME = 'idx_number'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE goals ADD INDEX idx_number (number)',
    'SELECT "Index idx_number already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
