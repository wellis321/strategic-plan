# Migration Phase 9: Reusable Top Sections

This migration adds support for custom top sections that organizations can create and manage. These sections appear at the top of strategic plan pages, before goals.

## What This Migration Does

- Creates the `organization_top_sections` table
- Allows organizations to create custom sections with:
  - Title (for custom sections)
  - Rich text content
  - Optional images (with configurable positions: left, right, top, bottom, background)
  - Display order (sortable)
  - Active/inactive toggle

## How to Run

1. **Backup your database** (recommended):
   ```bash
   mysqldump -u root -p strategic_plan > backup_before_phase9.sql
   ```

2. **Run the migration**:
   ```bash
   mysql -u root -p strategic_plan < database/migration_phase9_top_sections.sql
   ```

   Or via phpMyAdmin:
   - Open phpMyAdmin
   - Select the `strategic_plan` database
   - Go to the "SQL" tab
   - Copy and paste the contents of `database/migration_phase9_top_sections.sql`
   - Click "Go"

## Verification

After running the migration, verify it worked:

```sql
-- Check that the table exists
SHOW TABLES LIKE 'organization_top_sections';

-- Check the table structure
DESCRIBE organization_top_sections;
```

You should see a table with columns:
- `id` (primary key)
- `organization_id` (foreign key to organizations)
- `section_type` (enum: hero, about, vision, mission, values, custom)
- `title` (nullable, for custom sections)
- `content` (text, rich text content)
- `image_path` (nullable, path to uploaded image)
- `image_position` (enum: left, right, top, bottom, background)
- `sort_order` (integer, for ordering)
- `is_active` (boolean, show/hide)
- `created_at`, `updated_at` (timestamps)

## What's New

After this migration, organization admins can:

1. **Access Top Sections Management**:
   - Navigate to "Strategic Plan" → "Top Sections" in the navbar
   - Or go directly to `/top-sections`

2. **Create Custom Sections**:
   - Click "Add New Section"
   - Choose section type (custom, hero, about, vision, mission, values)
   - Add title (required for custom sections)
   - Add rich text content with formatting
   - Upload an optional image
   - Choose image position (left, right, top, bottom, background)
   - Set display order
   - Toggle active/inactive

3. **Reorder Sections**:
   - On the list page, drag and drop rows to reorder
   - Or edit individual sections to change their order number

4. **View on Strategic Plan**:
   - Top sections appear on both `/strategic-plan` (internal) and public plan pages
   - They render before strategic plan sections and goals
   - Image positions are respected (left/right/top/bottom/background layouts)

## Notes

- Top sections are organization-level (not plan-specific)
- They appear on all plans for the organization
- Images are stored in `static/uploads/organizations/{org_id}/top-sections/`
- The drag-and-drop reordering uses SortableJS (loaded from CDN)
