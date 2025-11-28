# Database Migration Guide

## Phase 2: Multi-Tenancy Migration

This migration adds multi-tenancy support to the Strategic Plan Manager application.

### What This Migration Does

1. **Creates New Tables:**
   - `organizations` - Stores organization information and seat allocations
   - `users` - Stores user accounts with email verification
   - `sessions` - Database-backed session storage

2. **Modifies Existing Tables:**
   - Adds `organization_id` to `goals` table
   - Adds `created_by` to `goals` table
   - Adds `organization_id` to `projects` table
   - Adds `created_by` to `projects` table

3. **Data Migration:**
   - Creates a default "System Organization" for existing data
   - Assigns all existing goals and projects to the system organization

4. **Creates Helper Objects:**
   - Views for seat usage and pending verifications
   - Stored procedure for checking seat availability
   - Indexes for performance

### Before Running Migration

1. **Backup Your Database:**
   ```bash
   mysqldump -u root -p strategic_plan > backup_before_phase2_$(date +%Y%m%d).sql
   ```

2. **Verify Current Schema:**
   - Make sure you've run the initial `schema.sql` first
   - Check that `goals` and `projects` tables exist

3. **Check MySQL Version:**
   - Requires MySQL 5.7+ or MariaDB 10.2+
   - The script uses features like `CREATE OR REPLACE VIEW`

### Running the Migration

#### Option 1: Using MySQL Command Line
```bash
mysql -u root -p strategic_plan < database/migration_phase2_multitenancy.sql
```

#### Option 2: Using phpMyAdmin
1. Select the `strategic_plan` database
2. Go to the "SQL" tab
3. Copy and paste the contents of `migration_phase2_multitenancy.sql`
4. Click "Go"

#### Option 3: Using MySQL Workbench
1. Open MySQL Workbench
2. Connect to your database
3. Open `migration_phase2_multitenancy.sql`
4. Execute the script

### After Migration

1. **Verify Migration Success:**
   ```sql
   -- Check new tables exist
   SHOW TABLES LIKE '%organizations%';
   SHOW TABLES LIKE '%users%';
   SHOW TABLES LIKE '%sessions%';

   -- Check columns were added
   DESCRIBE goals;
   DESCRIBE projects;

   -- Check default organization was created
   SELECT * FROM organizations WHERE domain = 'system.local';

   -- Check super admin was created
   SELECT * FROM users WHERE role = 'super_admin';
   ```

2. **Change Default Super Admin Password:**
   - Default credentials: `admin@system.local` / `admin123`
   - **CHANGE THIS IMMEDIATELY** in production!
   - You can change it via the application once login is implemented

3. **Test the Views:**
   ```sql
   -- Check seat usage view
   SELECT * FROM organization_seat_usage;

   -- Check pending verifications view
   SELECT * FROM pending_verifications;
   ```

### Default Super Admin Account

After migration, a default super admin account is created:
- **Email:** `admin@system.local`
- **Password:** `admin123` (hashed with bcrypt)
- **Role:** `super_admin`
- **Status:** Active and verified

**⚠️ IMPORTANT:** Change this password immediately after first login!

### Troubleshooting

#### Error: "Table already exists"
- This is normal if you've run the migration before
- The script uses `CREATE TABLE IF NOT EXISTS` so it's safe to run multiple times

#### Error: "Column already exists"
- The script checks for existing columns before adding them
- If you see this, the column was already added in a previous run

#### Error: "Constraint already exists"
- The script checks for existing constraints before adding them
- This is safe to ignore

#### Error: "Unknown database 'strategic_plan'"
- Make sure you've run the initial `schema.sql` first
- Or create the database manually: `CREATE DATABASE strategic_plan;`

### Rolling Back (If Needed)

If you need to rollback the migration:

```sql
-- WARNING: This will delete all user and organization data!
-- Only use if you're sure you want to remove multi-tenancy

-- Remove foreign key constraints first
ALTER TABLE goals DROP FOREIGN KEY IF EXISTS fk_goals_organization;
ALTER TABLE goals DROP FOREIGN KEY IF EXISTS fk_goals_created_by;
ALTER TABLE projects DROP FOREIGN KEY IF EXISTS fk_projects_organization;
ALTER TABLE projects DROP FOREIGN KEY IF EXISTS fk_projects_created_by;

-- Drop new tables
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS organizations;

-- Remove columns from existing tables
ALTER TABLE goals DROP COLUMN IF EXISTS organization_id;
ALTER TABLE goals DROP COLUMN IF EXISTS created_by;
ALTER TABLE projects DROP COLUMN IF EXISTS organization_id;
ALTER TABLE projects DROP COLUMN IF EXISTS created_by;

-- Drop views
DROP VIEW IF EXISTS organization_seat_usage;
DROP VIEW IF EXISTS pending_verifications;

-- Drop stored procedure
DROP PROCEDURE IF EXISTS CheckSeatAvailability;
```

**Note:** The `IF EXISTS` syntax may not work in all MySQL versions. You may need to check manually or use a script.

### Next Steps

After running the migration:

1. Update your PHP code to use the new schema
2. Implement authentication system
3. Add organization context to all queries
4. Test user registration and email verification
5. Create your first organization via super admin

### Support

If you encounter issues:
1. Check the error message carefully
2. Verify your MySQL version
3. Check that the initial schema was applied correctly
4. Review the migration script for any custom modifications needed

---

**Migration Version:** Phase 2 - Multi-Tenancy
**Date Created:** 2025-11-27
**Compatible With:** MySQL 5.7+, MariaDB 10.2+
