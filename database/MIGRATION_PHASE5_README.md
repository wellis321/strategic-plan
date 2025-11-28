# Migration Phase 5: Multiple Strategic Plans per Organisation

This migration adds support for organisations to have multiple strategic plans with custom URL slugs (e.g., `/ramh/2025-2030plan`, `/gamh/strategy-2026`).

## What This Migration Does

1. **Creates `strategic_plans` table** - Stores multiple plans per organisation with:
   - Custom URL slugs
   - Title, start/end years
   - Status (draft, published, archived)
   - Active flag (only one active plan per organisation)

2. **Adds `plan_id` to existing tables**:
   - `goals` - Links goals to specific plans
   - `projects` - Links projects to specific plans
   - `strategic_plan_sections` - Links sections to specific plans

3. **Adds `slug` to `organizations` table** - For URL routing (e.g., `ramh`, `gamh`)

4. **Migrates existing data** - Creates a default plan for each organisation and links existing goals/projects/sections to it

## How to Run

1. **Backup your database first!**
   ```bash
   mysqldump -u root -p strategic_plan > backup_before_phase5.sql
   ```

2. **Run the migration**:
   ```bash
   mysql -u root -p strategic_plan < database/migration_phase5_strategic_plans.sql
   ```

3. **Verify the migration**:
   ```sql
   -- Check strategic_plans table exists
   DESCRIBE strategic_plans;

   -- Check plan_id columns were added
   DESCRIBE goals;
   DESCRIBE projects;
   DESCRIBE strategic_plan_sections;

   -- Check organizations have slugs
   SELECT id, name, slug FROM organizations LIMIT 5;

   -- Check default plans were created
   SELECT id, organization_id, slug, title, status, is_active FROM strategic_plans;
   ```

## What Changes for Users

### Organisation Admins

- **New "Plans" menu item** - Manage multiple strategic plans
- **Create new plans** - Each with its own URL slug and time period
- **Set active plan** - The plan used as default when viewing strategic plan internally
- **Public URLs** - Each plan gets a public URL like `/ramh/2025-2030plan`

### All Users

- Goals, projects, and sections are automatically assigned to the active plan
- When creating new content, it's added to the currently active plan
- The strategic plan page shows the active plan

## URL Structure

- **Public plan URLs**: `/{org-slug}/{plan-slug}`
  - Example: `/ramh/2025-2030plan`
  - Example: `/gamh/strategy-2026`
  - Only published plans are accessible via public URLs

- **Internal plan management**: `/plans`
  - List all plans for your organisation
  - Create, edit, view, delete plans

## Notes

- Only one plan can be "active" at a time per organisation
- The active plan is used as the default when viewing strategic plan internally
- Published plans are visible via their public URL (no login required)
- Draft plans are only visible to logged-in users
- When you create a new plan and set it as active, the previous active plan is automatically deactivated
