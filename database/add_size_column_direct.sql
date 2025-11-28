-- Direct SQL to add size column (run this if migration didn't work)
ALTER TABLE organization_top_sections
ADD COLUMN size ENUM('small', 'medium', 'large') NOT NULL DEFAULT 'medium'
AFTER image_position;
