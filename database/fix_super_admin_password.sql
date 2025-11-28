-- Fix Super Admin Password
-- This updates the super admin password to match 'admin123'
-- NOTE: Run the PHP script instead: php fix_super_admin_password.php
-- This SQL file is kept for reference but the hash needs to be generated fresh each time

USE strategic_plan;

-- This hash was generated for 'admin123' - but bcrypt hashes are unique each time
-- So this might not work. Use the PHP script instead.
UPDATE users
SET password_hash = '$2y$12$q0z4A4BqEOUzArsWW36tVuU8rDUYmk1fNEdbLFEc.Z1CpHxuOc0ei'
WHERE email = 'admin@system.local' AND role = 'super_admin';

-- Verify the update
SELECT
    email,
    role,
    'Password updated - verify login works' as status
FROM users
WHERE email = 'admin@system.local';
