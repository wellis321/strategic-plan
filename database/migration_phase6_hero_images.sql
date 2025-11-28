-- Phase 6: Hero & About Image support
-- Adds optional hero/about image fields to organizations

START TRANSACTION;

-- Hero content
ALTER TABLE organizations
    ADD COLUMN IF NOT EXISTS hero_title VARCHAR(255) NULL AFTER mission,
    ADD COLUMN IF NOT EXISTS hero_subtitle TEXT NULL AFTER hero_title,
    ADD COLUMN IF NOT EXISTS hero_image_path VARCHAR(255) NULL AFTER hero_subtitle,
    ADD COLUMN IF NOT EXISTS about_image_path VARCHAR(255) NULL AFTER hero_image_path;

COMMIT;
