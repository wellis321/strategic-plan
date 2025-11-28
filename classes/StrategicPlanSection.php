<?php
class StrategicPlanSection {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $sql = "SELECT s.*,
                g.number as goal_number,
                g.title as goal_title,
                u.email as created_by_email
                FROM strategic_plan_sections s
                LEFT JOIN goals g ON s.linked_goal_id = g.id
                LEFT JOIN users u ON s.created_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['organization_id'])) {
            $sql .= " AND s.organization_id = :organization_id";
            $params['organization_id'] = $filters['organization_id'];
        }

        if (!empty($filters['plan_id'])) {
            $sql .= " AND s.plan_id = :plan_id";
            $params['plan_id'] = $filters['plan_id'];
        }

        if (!empty($filters['linked_goal_id'])) {
            $sql .= " AND s.linked_goal_id = :linked_goal_id";
            $params['linked_goal_id'] = $filters['linked_goal_id'];
        }

        $sql .= " ORDER BY s.sort_order ASC, s.created_at ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id, $organizationId = null) {
        $sql = "SELECT s.*,
                g.number as goal_number,
                g.title as goal_title
                FROM strategic_plan_sections s
                LEFT JOIN goals g ON s.linked_goal_id = g.id
                WHERE s.id = :id";

        $params = ['id' => $id];

        if ($organizationId !== null) {
            $sql .= " AND s.organization_id = :organization_id";
            $params['organization_id'] = $organizationId;
        }

        return $this->db->fetchOne($sql, $params);
    }

    public function create($data) {
        $sectionData = [
            'organization_id' => $data['organization_id'],
            'plan_id' => $data['plan_id'] ?? null,
            'title' => $data['title'],
            'content' => $data['content'],
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'linked_goal_id' => !empty($data['linked_goal_id']) ? (int)$data['linked_goal_id'] : null,
            'created_by' => $data['created_by'] ?? null
        ];

        return $this->db->insert('strategic_plan_sections', $sectionData);
    }

    public function update($id, $data, $organizationId = null) {
        $sectionData = [
            'title' => $data['title'],
            'content' => $data['content'],
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'linked_goal_id' => !empty($data['linked_goal_id']) ? (int)$data['linked_goal_id'] : null
        ];

        $where = 'id = :id';
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $where .= ' AND organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        return $this->db->update('strategic_plan_sections', $sectionData, $where, $params);
    }

    public function delete($id, $organizationId = null) {
        $where = 'id = :id';
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $where .= ' AND organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        return $this->db->delete('strategic_plan_sections', $where, $params);
    }

    public function validate($data) {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Section title is required';
        } elseif (strlen($data['title']) > 255) {
            $errors['title'] = 'Section title cannot exceed 255 characters';
        }

        if (empty($data['content'])) {
            $errors['content'] = 'Section content is required';
        }

        if (isset($data['linked_goal_id']) && !empty($data['linked_goal_id'])) {
            // Verify goal exists and belongs to same organization
            $goalModel = new Goal();
            $goal = $goalModel->getById($data['linked_goal_id']);
            if (!$goal || $goal['organization_id'] != $data['organization_id']) {
                $errors['linked_goal_id'] = 'Invalid goal selected';
            }
        }

        return $errors;
    }

    public function reorder($sections, $organizationId) {
        // $sections is an array of [id => sort_order]
        foreach ($sections as $id => $sortOrder) {
            $this->db->update(
                'strategic_plan_sections',
                ['sort_order' => (int)$sortOrder],
                'id = :id AND organization_id = :organization_id',
                ['id' => $id, 'organization_id' => $organizationId]
            );
        }
        return true;
    }
}
?>
