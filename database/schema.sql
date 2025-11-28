-- Strategic Plan Manager Database Schema

CREATE DATABASE IF NOT EXISTS strategic_plan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE strategic_plan;

-- Goals table
CREATE TABLE IF NOT EXISTS goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(10) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    responsible_director VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_number (number)
);

-- Goal statements table (for multiple statements per goal)
CREATE TABLE IF NOT EXISTS goal_statements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    goal_id INT NOT NULL,
    statement TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES goals(id) ON DELETE CASCADE,
    INDEX idx_goal_id (goal_id)
);

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    project_number VARCHAR(50) NOT NULL UNIQUE,
    goal_id INT NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    project_group VARCHAR(255),
    start_date DATE,
    end_date DATE,
    meeting_frequency VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (goal_id) REFERENCES goals(id) ON DELETE RESTRICT,
    INDEX idx_project_number (project_number),
    INDEX idx_goal_id (goal_id),
    INDEX idx_slug (slug)
);

-- Project leads table (many-to-many relationship)
CREATE TABLE IF NOT EXISTS project_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    lead_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Project working group members table
CREATE TABLE IF NOT EXISTS project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    member_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Project purposes table (multiple purposes per project)
CREATE TABLE IF NOT EXISTS project_purposes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    purpose TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Project milestones table
CREATE TABLE IF NOT EXISTS project_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    target_date DATE,
    completed_date DATE,
    status ENUM('pending', 'in_progress', 'completed', 'overdue') DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_status (status),
    INDEX idx_target_date (target_date)
);

-- Project progress reports table
CREATE TABLE IF NOT EXISTS project_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    report_date DATE NOT NULL,
    progress_percentage DECIMAL(5,2) DEFAULT 0,
    status ENUM('on_track', 'at_risk', 'delayed', 'completed') DEFAULT 'on_track',
    summary TEXT,
    challenges TEXT,
    next_steps TEXT,
    created_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_report_date (report_date),
    INDEX idx_status (status)
);

-- Insert sample data (only if it doesn't already exist)
-- Insert goals and capture their IDs
INSERT IGNORE INTO goals (number, title, description, responsible_director) VALUES
('1', 'To support people to live their best lives', 'The People We Support must have the opportunity to be adventurous in their lives', 'Senior manager of Care Services'),
('5', 'Increase sustainability', 'Focus on environmental and operational sustainability across all services', 'Senior manager of Operations'),
('6', 'Reduce environmental impact', 'Minimize our carbon footprint and environmental impact', 'Senior manager of Sustainability');

-- Get the actual IDs that were inserted or already exist
SET @goal1_id = (SELECT id FROM goals WHERE number = '1');
SET @goal5_id = (SELECT id FROM goals WHERE number = '5');
SET @goal6_id = (SELECT id FROM goals WHERE number = '6');

-- Insert goal statements (only if they don't exist)
INSERT IGNORE INTO goal_statements (goal_id, statement, sort_order) VALUES
(@goal1_id, 'The People We Support must have the opportunity to be adventurous in their lives', 1),
(@goal1_id, 'Support should be person-centered and empowering', 2),
(@goal5_id, 'Implement sustainable practices across all operations', 1),
(@goal6_id, 'Achieve carbon neutrality by 2030', 1);

-- Insert project using the actual goal ID (only if it doesn't exist)
INSERT IGNORE INTO projects (title, project_number, goal_id, slug, project_group, start_date, end_date) VALUES
('Explore how technology, digital resources and AI can be used to achieve people''s goals', '1.4', @goal1_id, 'technology-digital-ai-goals', 'Digital Development Group', '2025-04-01', '2027-03-31');

-- Get the project ID (either newly inserted or existing)
SET @project_id = (SELECT id FROM projects WHERE project_number = '1.4');

-- Insert project leads (only if they don't exist)
INSERT IGNORE INTO project_leads (project_id, lead_name) VALUES
(@project_id, 'William Ellis');

-- Insert project purposes (only if they don't exist)
INSERT IGNORE INTO project_purposes (project_id, purpose, sort_order) VALUES
(@project_id, 'Research and evaluate emerging technologies for person-centered care', 1),
(@project_id, 'Develop digital literacy programs for service users', 2),
(@project_id, 'Implement AI-assisted care planning tools', 3);

-- Insert project milestones (only if they don't exist)
INSERT IGNORE INTO project_milestones (project_id, title, target_date, status) VALUES
(@project_id, 'Complete technology assessment', '2025-06-30', 'pending'),
(@project_id, 'Pilot digital literacy program', '2025-09-30', 'pending'),
(@project_id, 'Launch AI care planning tool', '2026-03-31', 'pending');
