<?php
class Project {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $sql = "SELECT p.*, g.number as goal_number, g.title as goal_title,
                GROUP_CONCAT(DISTINCT pl.lead_name ORDER BY pl.id SEPARATOR ', ') as leads,
                GROUP_CONCAT(DISTINCT pm.member_name ORDER BY pm.id SEPARATOR ', ') as members
                FROM projects p
                LEFT JOIN goals g ON p.goal_id = g.id
                LEFT JOIN project_leads pl ON p.id = pl.project_id
                LEFT JOIN project_members pm ON p.id = pm.project_id";

        $where = [];
        $params = [];

        if (!empty($filters['organization_id'])) {
            $where[] = "p.organization_id = :organization_id";
            $params['organization_id'] = $filters['organization_id'];
        }

        if (!empty($filters['plan_id'])) {
            $where[] = "p.plan_id = :plan_id";
            $params['plan_id'] = $filters['plan_id'];
        }

        if (!empty($filters['goal_id'])) {
            $where[] = "p.goal_id = :goal_id";
            $params['goal_id'] = $filters['goal_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.title LIKE :search OR p.project_number LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " GROUP BY p.id ORDER BY p.project_number";

        $projects = $this->db->fetchAll($sql, $params);

        // Process leads and members
        foreach ($projects as &$project) {
            $project['leads'] = $project['leads'] ? explode(', ', $project['leads']) : [];
            $project['members'] = $project['members'] ? explode(', ', $project['members']) : [];
            $project['purposes'] = $this->getPurposes($project['id']);
            $project['milestones'] = $this->getMilestones($project['id']);
        }

        return $projects;
    }

    public function getById($id) {
        $sql = "SELECT p.*, g.number as goal_number, g.title as goal_title
                FROM projects p
                LEFT JOIN goals g ON p.goal_id = g.id
                WHERE p.id = :id";

        $project = $this->db->fetchOne($sql, ['id' => $id]);

        if ($project) {
            $project['leads'] = $this->getLeads($id);
            $project['members'] = $this->getMembers($id);
            $project['purposes'] = $this->getPurposes($id);
            $project['milestones'] = $this->getMilestones($id);
        }

        return $project;
    }

    public function getBySlug($slug) {
        $sql = "SELECT p.*, g.number as goal_number, g.title as goal_title
                FROM projects p
                LEFT JOIN goals g ON p.goal_id = g.id
                WHERE p.slug = :slug";

        $project = $this->db->fetchOne($sql, ['slug' => $slug]);

        if ($project) {
            $project['leads'] = $this->getLeads($project['id']);
            $project['members'] = $this->getMembers($project['id']);
            $project['purposes'] = $this->getPurposes($project['id']);
            $project['milestones'] = $this->getMilestones($project['id']);
        }

        return $project;
    }

    public function getLeads($projectId) {
        $sql = "SELECT lead_name FROM project_leads WHERE project_id = :project_id ORDER BY id";
        $leads = $this->db->fetchAll($sql, ['project_id' => $projectId]);
        return array_column($leads, 'lead_name');
    }

    public function getMembers($projectId) {
        $sql = "SELECT member_name FROM project_members WHERE project_id = :project_id ORDER BY id";
        $members = $this->db->fetchAll($sql, ['project_id' => $projectId]);
        return array_column($members, 'member_name');
    }

    public function getPurposes($projectId) {
        $sql = "SELECT purpose FROM project_purposes WHERE project_id = :project_id ORDER BY sort_order";
        $purposes = $this->db->fetchAll($sql, ['project_id' => $projectId]);
        return array_column($purposes, 'purpose');
    }

    public function getMilestones($projectId) {
        $sql = "SELECT * FROM project_milestones WHERE project_id = :project_id ORDER BY target_date";
        return $this->db->fetchAll($sql, ['project_id' => $projectId]);
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            // Generate slug
            $slug = $this->generateSlug($data['title']);

            // Insert project
            $projectData = [
                'organization_id' => $data['organization_id'] ?? null,
                'plan_id' => $data['plan_id'] ?? null,
                'title' => $data['title'],
                'project_number' => $data['project_number'],
                'goal_id' => $data['goal_id'],
                'slug' => $slug,
                'project_group' => $data['project_group'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'meeting_frequency' => $data['meeting_frequency'] ?? null,
                'created_by' => $data['created_by'] ?? null
            ];

            $projectId = $this->db->insert('projects', $projectData);

            // Insert leads
            if (!empty($data['leads']) && is_array($data['leads'])) {
                foreach ($data['leads'] as $lead) {
                    if (!empty(trim($lead))) {
                        $this->db->insert('project_leads', [
                            'project_id' => $projectId,
                            'lead_name' => trim($lead)
                        ]);
                    }
                }
            }

            // Insert members
            if (!empty($data['members']) && is_array($data['members'])) {
                foreach ($data['members'] as $member) {
                    if (!empty(trim($member))) {
                        $this->db->insert('project_members', [
                            'project_id' => $projectId,
                            'member_name' => trim($member)
                        ]);
                    }
                }
            }

            // Insert purposes
            if (!empty($data['purposes']) && is_array($data['purposes'])) {
                foreach ($data['purposes'] as $index => $purpose) {
                    if (!empty(trim($purpose))) {
                        $this->db->insert('project_purposes', [
                            'project_id' => $projectId,
                            'purpose' => trim($purpose),
                            'sort_order' => $index + 1
                        ]);
                    }
                }
            }

            $this->db->commit();
            return $projectId;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();

        try {
            // Update project
            $projectData = [
                'title' => $data['title'],
                'project_number' => $data['project_number'],
                'goal_id' => $data['goal_id'],
                'project_group' => $data['project_group'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'meeting_frequency' => $data['meeting_frequency'] ?? null
            ];

            // Update slug if title changed
            if (isset($data['title'])) {
                $projectData['slug'] = $this->generateSlug($data['title']);
            }

            $this->db->update('projects', $projectData, 'id = :id', ['id' => $id]);

            // Update leads
            if (isset($data['leads']) && is_array($data['leads'])) {
                $this->db->delete('project_leads', 'project_id = :project_id', ['project_id' => $id]);
                foreach ($data['leads'] as $lead) {
                    if (!empty(trim($lead))) {
                        $this->db->insert('project_leads', [
                            'project_id' => $id,
                            'lead_name' => trim($lead)
                        ]);
                    }
                }
            }

            // Update members
            if (isset($data['members']) && is_array($data['members'])) {
                $this->db->delete('project_members', 'project_id = :project_id', ['project_id' => $id]);
                foreach ($data['members'] as $member) {
                    if (!empty(trim($member))) {
                        $this->db->insert('project_members', [
                            'project_id' => $id,
                            'member_name' => trim($member)
                        ]);
                    }
                }
            }

            // Update purposes
            if (isset($data['purposes']) && is_array($data['purposes'])) {
                $this->db->delete('project_purposes', 'project_id = :project_id', ['project_id' => $id]);
                foreach ($data['purposes'] as $index => $purpose) {
                    if (!empty(trim($purpose))) {
                        $this->db->insert('project_purposes', [
                            'project_id' => $id,
                            'purpose' => trim($purpose),
                            'sort_order' => $index + 1
                        ]);
                    }
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete($id) {
        return $this->db->delete('projects', 'id = :id', ['id' => $id]);
    }

    public function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists($slug) {
        $sql = "SELECT id FROM projects WHERE slug = :slug";
        $result = $this->db->fetchOne($sql, ['slug' => $slug]);
        return $result !== false;
    }

    public function validate($data) {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        }

        if (empty($data['project_number'])) {
            $errors['project_number'] = 'Project number is required';
        }

        if (empty($data['goal_id'])) {
            $errors['goal_id'] = 'Goal is required';
        }

        // Check for duplicate project number
        if (!empty($data['project_number'])) {
            $sql = "SELECT id FROM projects WHERE project_number = :project_number";
            $params = ['project_number' => $data['project_number']];

            if (isset($data['id'])) {
                $sql .= " AND id != :id";
                $params['id'] = $data['id'];
            }

            $existing = $this->db->fetchOne($sql, $params);
            if ($existing) {
                $errors['project_number'] = 'Project number already exists';
            }
        }

        return $errors;
    }

    public function getProgressSummary($filters = []) {
        $sql = "SELECT
                    COUNT(*) as total_projects,
                    SUM(CASE WHEN pr.status = 'completed' THEN 1 ELSE 0 END) as completed_projects,
                    SUM(CASE WHEN pr.status = 'on_track' THEN 1 ELSE 0 END) as on_track_projects,
                    SUM(CASE WHEN pr.status = 'at_risk' THEN 1 ELSE 0 END) as at_risk_projects,
                    SUM(CASE WHEN pr.status = 'delayed' THEN 1 ELSE 0 END) as delayed_projects,
                    AVG(pr.progress_percentage) as avg_progress
                FROM projects p
                LEFT JOIN (
                    SELECT project_id, status, progress_percentage,
                           ROW_NUMBER() OVER (PARTITION BY project_id ORDER BY report_date DESC) as rn
                    FROM project_reports
                ) pr ON p.id = pr.project_id AND pr.rn = 1";

        $where = [];
        $params = [];

        if (!empty($filters['organization_id'])) {
            $where[] = "p.organization_id = :organization_id";
            $params['organization_id'] = $filters['organization_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        return $this->db->fetchOne($sql, $params);
    }
}
?>
