# Strategic Plan Tool Capability Analysis

## What Our Tool Currently Supports

### ✅ Fully Supported Components

1. **Strategic Goals**
   - Goal number/identifier
   - Goal title
   - Description
- Responsible Senior manager (customisable name)
   - Goal statements/objectives (multiple per goal)

2. **Projects/Workstreams**
   - Project title and number
   - Linked to strategic goals
   - Project group/team
   - Timeline (start/end dates)
   - Project leads
   - Working group members
   - Project purposes/objectives
   - Milestones with target dates and status
   - Progress tracking

3. **Progress & Reporting**
   - Progress percentages
   - Status tracking (on track, at risk, delayed, completed)
   - Reports and analytics
   - Dashboard summaries

### ⚠️ Partially Supported / Workarounds Available

1. **Aims**
   - Could be created as Strategic Goals
   - Or could use Goal Statements to represent aims
   - **Gap**: No dedicated "Aims" section separate from goals

2. **Values**
   - Could be added as a special Goal (e.g., "Goal 0: Our Values")
   - Or could use Goal Statements
   - **Gap**: No dedicated organisation-wide Values section

### ❌ Currently Missing Components

1. **Vision Statement**
   - Organisation-wide vision statement
   - Typically appears at the start of strategic plans
   - **Gap**: No dedicated field for organisation vision

2. **Mission Statement**
   - Organisation-wide mission statement
   - Core purpose of the organisation
   - **Gap**: No dedicated field for organisation mission

3. **Organisation-Level Content**
   - No way to store organisation-wide introductory content
   - Vision, Mission, Values are typically organisation-level, not goal-level

## How Penumbra Could Use Our Tool (Current State)

### What They CAN Do:

1. **Create Strategic Goals**
   - Each major goal from their plan becomes a "Goal" in our system
   - Add descriptions and goal statements

2. **Create Projects/Workstreams**
   - Each project/workstream becomes a "Project"
   - Link to relevant goals
   - Add milestones, timelines, leads

3. **Track Progress**
   - Update milestone status
   - Track project completion
   - Generate progress reports

### What They CANNOT Do (Workarounds Needed):

1. **Vision Statement**
   - **Workaround**: Create a special "Goal 0: Our Vision" with the vision as the description
   - **Better Solution**: Add organisation-level vision field

2. **Mission Statement**
   - **Workaround**: Create a special "Goal 0: Our Mission" or add to organisation description
   - **Better Solution**: Add organisation-level mission field

3. **Values**
   - **Workaround**: Create a special "Goal 0: Our Values" with each value as a goal statement
   - **Better Solution**: Add organisation-level values section

4. **Aims**
   - **Workaround**: Use Strategic Goals (if aims are the same as goals)
   - Or create a separate "Aims" goal with aims as statements
   - **Better Solution**: Add dedicated Aims section or make Goals/Aims interchangeable

## Recommended Enhancements

To fully support strategic plans like Penumbra's, we should add:

1. **Organisation Settings/Profile**
   - Vision statement (text field)
   - Mission statement (text field)
   - Values (multiple text fields or list)
   - Aims (could be goals, or separate section)

2. **Strategic Plan Introduction Page**
   - Display Vision, Mission, Values at the top
   - Then show Goals and Projects below
   - Makes it feel like a complete strategic plan document

3. **Field Customisation** (Already Planned)
   - Allow organisations to rename "Goals" to "Aims" if preferred
   - Customise field labels to match their terminology

## Conclusion

**Current Capability**: ~80% of a typical strategic plan
- ✅ Goals and Projects: Fully supported
- ✅ Progress tracking: Fully supported
- ⚠️ Vision/Mission/Values: Workarounds available but not ideal
- ❌ Organisation-level introductory content: Missing

**With Workarounds**: Penumbra could create most of their plan, but Vision/Mission/Values would need creative use of Goals or be stored separately.

**With Recommended Enhancements**: 100% capability for typical strategic plans.

---

## ✅ IMPLEMENTED: Phase 3 - Vision, Mission, Values

**Status**: Complete (as of latest update)

### What Was Added

1. **Database Schema**
   - `vision` field added to `organizations` table (TEXT, nullable)
   - `mission` field added to `organizations` table (TEXT, nullable)
   - `organization_values` table created for multiple values per organisation

2. **Backend**
   - `Organization` class updated with `getValues()`, `setValues()`, `getByIdWithValues()` methods
   - Values stored in separate table with sort order

3. **Super Admin Interface**
   - Create organisation form includes Vision, Mission, Values fields
   - Edit organisation form includes Vision, Mission, Values fields
   - Dynamic add/remove values functionality

4. **Organisation Admin Interface**
   - New `/organization/settings` page for managing Vision, Mission, Values
   - Organisation admins can update these without super admin access

5. **Strategic Plan Display**
   - `/strategic-plan` page now displays Vision, Mission, Values at the top
   - Provides context and foundation for strategic goals
   - Edit link shown to organisation admins

### Current Capability: 100% ✅

- ✅ Goals and Projects: Fully supported
- ✅ Progress tracking: Fully supported
- ✅ Vision/Mission/Values: Fully supported at organisation level
- ✅ Organisation-level introductory content: Fully supported

**Result**: Organisations like Penumbra can now create complete strategic plans with Vision, Mission, Values, Goals, and Projects all in one place!
