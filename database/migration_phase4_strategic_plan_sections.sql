-- Migration Script: Phase 4 - Strategic Plan Sections
-- This script adds support for custom sections/chapters in strategic plans
-- Run this after migration_phase3_vision_mission_values.sql has been executed

USE strategic_plan;

-- ============================================
-- CREATE STRATEGIC PLAN SECTIONS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS strategic_plan_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    linked_goal_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (linked_goal_id) REFERENCES goals(id) ON DELETE SET NULL,
    INDEX idx_organization_id (organization_id),
    INDEX idx_sort_order (sort_order),
    INDEX idx_linked_goal_id (linked_goal_id)
);

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Migration completed successfully!' as status;
SELECT 'New table: strategic_plan_sections' as new_table;
SELECT 'Features: custom sections, optional goal linking, custom ordering' as features;
