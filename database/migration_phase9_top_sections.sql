-- Phase 9: Reusable Top Sections (custom hero/about variants)
-- This allows organizations to create custom sections with title, content, and images
-- that appear before the goals on their strategic plan pages.

START TRANSACTION;

-- Create organization_top_sections table
CREATE TABLE IF NOT EXISTS organization_top_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    section_type ENUM('hero', 'about', 'vision', 'mission', 'values', 'custom') NOT NULL DEFAULT 'custom',
    title VARCHAR(255) NULL,
    content TEXT NULL,
    image_path VARCHAR(255) NULL,
    image_position ENUM('left', 'right', 'top', 'bottom', 'background') DEFAULT 'left',
    size ENUM('small', 'medium', 'large') DEFAULT 'medium',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    INDEX idx_organization_id (organization_id),
    INDEX idx_sort_order (organization_id, sort_order),
    INDEX idx_is_active (organization_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
