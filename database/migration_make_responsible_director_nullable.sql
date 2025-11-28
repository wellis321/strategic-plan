-- Make responsible_director nullable in goals table
-- This allows organizations to skip this field if they don't use it

START TRANSACTION;

-- Modify the column to allow NULL
ALTER TABLE goals MODIFY COLUMN responsible_director VARCHAR(255) NULL;

COMMIT;
