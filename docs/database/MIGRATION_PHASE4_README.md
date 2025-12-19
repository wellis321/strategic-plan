# Migration Phase 4: Strategic Plan Sections

This migration adds support for custom sections/chapters in strategic plans, allowing organisations to add contextual content, background information, or sections that relate to specific goals.

## What This Migration Does

1. **Creates `strategic_plan_sections` table** with:
   - Section title and content
   - Custom sort order for display
   - Optional link to specific goals
   - Organisation-level isolation

## Files Changed

- `database/migration_phase4_strategic_plan_sections.sql` - Migration script
- `classes/StrategicPlanSection.php` - New model class for managing sections
- `pages/sections/index.php` - List all sections
- `pages/sections/new.php` - Create new section
- `pages/sections/edit.php` - Edit existing section
- `pages/sections/delete.php` - Delete section
- `pages/strategic-plan.php` - Displays sections in strategic plan
- `pages/template.php` - Documents sections feature
- `templates/layout.php` - Added "Sections" link to navigation (for org admins)
- `config/bootstrap.php` - Added StrategicPlanSection class
- `index.php` - Added routes for section management

## How to Run

### Option 1: Via phpMyAdmin or MySQL Client

1. Open `database/migration_phase4_strategic_plan_sections.sql`
2. Copy the entire contents
3. Run it in phpMyAdmin SQL tab or via MySQL command line

### Option 2: Via Command Line

```bash
mysql -u root -p strategic_plan < database/migration_phase4_strategic_plan_sections.sql
```

### Option 3: Via PHP Script

```bash
php -r "require 'config/bootstrap.php'; \$db = Database::getInstance(); \$sql = file_get_contents('database/migration_phase4_strategic_plan_sections.sql'); \$db->exec(\$sql);"
```

## Verification

After running the migration, verify it worked:

```sql
-- Check table exists
SHOW TABLES LIKE 'strategic_plan_sections';

-- Should show: strategic_plan_sections table

-- Check table structure
DESCRIBE strategic_plan_sections;
```

## Features Added

### For Organisation Admins
- Create custom sections/chapters for their strategic plan
- Edit section titles and content
- Set display order (sort_order)
- Optionally link sections to specific goals
- Delete sections
- Manage sections via `/sections` page

### For All Users
- Sections are displayed in the strategic plan page
- Sections appear between the foundation (About Us/Vision/Mission/Values) and Strategic Goals
- Sections can provide context, background information, or relate to specific goals
- Sections are displayed in order (by sort_order)

## Use Cases

Sections can be used for:
- **Context and Background** - Explain the environment, challenges, opportunities
- **Strategic Priorities** - Outline key focus areas
- **Implementation Approach** - Describe how the plan will be executed
- **Governance** - Explain oversight and accountability structures
- **Service Delivery Approach** - Describe how services are delivered
- **Any other contextual information** that helps readers understand the strategic plan

## Section Structure

Each section has:
- **Title** - The heading (e.g., "Context and Background")
- **Content** - The main text content (can include paragraphs, lists, etc.)
- **Sort Order** - Controls display order (lower numbers appear first)
- **Linked Goal** (Optional) - Can be linked to a specific strategic goal

## Navigation

- Organisation admins will see a "Sections" link in the main navigation
- All users can view sections on the Strategic Plan page
- Sections are managed at `/sections`

## Rollback (if needed)

If you need to rollback this migration:

```sql
USE strategic_plan;

-- Remove sections table
DROP TABLE IF EXISTS strategic_plan_sections;
```

## Notes

- Sections are organisation-specific (isolated by organization_id)
- Sections can be linked to goals or stand alone
- Content is stored as TEXT (supports long content)
- Sections are displayed in order by sort_order, then by creation date
- UK English spelling is used throughout
