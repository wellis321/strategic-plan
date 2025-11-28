# Migration Phase 3: Vision, Mission, Values

This migration adds organisation-level Vision, Mission, and Values fields to support complete strategic plan documents.

## What This Migration Does

1. **Adds `about_us` field** to `organizations` table (TEXT, nullable) - Organisation description/introduction
2. **Adds `vision` field** to `organizations` table (TEXT, nullable)
3. **Adds `mission` field** to `organizations` table (TEXT, nullable)
4. **Creates `organization_values` table** for storing multiple values per organisation

## Files Changed

- `database/migration_phase3_vision_mission_values.sql` - Migration script
- `classes/Organization.php` - Updated to handle vision, mission, values
- `pages/admin/organizations/new.php` - Added fields to create form
- `pages/admin/organizations/edit.php` - Added fields to edit form
- `pages/organization/settings.php` - New page for org admins to manage these fields
- `pages/strategic-plan.php` - Displays vision/mission/values at top of plan

## How to Run

### Option 1: Via phpMyAdmin or MySQL Client

1. Open `database/migration_phase3_vision_mission_values.sql`
2. Copy the entire contents
3. Run it in phpMyAdmin SQL tab or via MySQL command line

### Option 2: Via Command Line

```bash
mysql -u root -p strategic_plan < database/migration_phase3_vision_mission_values.sql
```

### Option 3: Via PHP Script

```bash
php -r "require 'config/bootstrap.php'; \$db = Database::getInstance(); \$sql = file_get_contents('database/migration_phase3_vision_mission_values.sql'); \$db->exec(\$sql);"
```

## Verification

After running the migration, verify it worked:

```sql
-- Check columns exist
DESCRIBE organizations;

-- Should show: vision, mission columns

-- Check table exists
SHOW TABLES LIKE 'organization_values';

-- Should show: organization_values table
```

## Features Added

### For Super Admins
- Can set About Us, Vision, Mission, and Values when creating organisations
- Can edit About Us, Vision, Mission, and Values when editing organisations

### For Organisation Admins
- Can manage their organisation's About Us, Vision, Mission, and Values via `/organization/settings`
- These appear at the top of the strategic plan page (About Us first, then Vision/Mission/Values)

### For All Users
- About Us, Vision, Mission, and Values are displayed at the top of `/strategic-plan` page
- About Us appears first, providing an introduction to the organisation
- Vision, Mission, and Values follow, providing context and foundation for the strategic goals and projects

## Rollback (if needed)

If you need to rollback this migration:

```sql
USE strategic_plan;

-- Remove values table
DROP TABLE IF EXISTS organization_values;

-- Remove columns (MySQL doesn't support IF EXISTS for columns, so check first)
ALTER TABLE organizations DROP COLUMN about_us;
ALTER TABLE organizations DROP COLUMN vision;
ALTER TABLE organizations DROP COLUMN mission;
```

## Notes

- Vision and Mission are stored as TEXT fields (can be long)
- Values are stored in a separate table to allow multiple values per organisation
- Values have a `sort_order` field to maintain order
- All fields are optional - organisations can add them later
- UK English spelling is used throughout (Vision, Mission, Values)
