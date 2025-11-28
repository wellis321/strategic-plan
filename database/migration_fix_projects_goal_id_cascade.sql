-- Fix projects.goal_id foreign key constraint to allow CASCADE deletion
-- This allows plans to be deleted, which cascades to goals, which then cascades to projects

START TRANSACTION;

-- First, find and drop the existing foreign key constraint
SET @constraint_name = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'goal_id'
    AND REFERENCED_TABLE_NAME = 'goals'
    LIMIT 1
);

-- Drop the constraint if it exists
SET @sql = IF(@constraint_name IS NOT NULL,
    CONCAT('ALTER TABLE projects DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT "No foreign key constraint found on projects.goal_id"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now add the constraint back with CASCADE
-- Check if constraint already exists (it shouldn't after dropping, but just in case)
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'projects'
    AND COLUMN_NAME = 'goal_id'
    AND REFERENCED_TABLE_NAME = 'goals'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE projects ADD CONSTRAINT fk_projects_goal_id FOREIGN KEY (goal_id) REFERENCES goals(id) ON DELETE CASCADE',
    'SELECT "Foreign key constraint already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
