<?php
class StrategicPlan {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $sql = "SELECT sp.*,
                o.name as organization_name,
                o.domain as organization_domain,
                u.email as created_by_email,
                (SELECT COUNT(*) FROM goals g WHERE g.plan_id = sp.id) as goals_count,
                (SELECT COUNT(*) FROM projects p WHERE p.plan_id = sp.id) as projects_count
                FROM strategic_plans sp
                LEFT JOIN organizations o ON sp.organization_id = o.id
                LEFT JOIN users u ON sp.created_by = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['organization_id'])) {
            $sql .= " AND sp.organization_id = :organization_id";
            $params['organization_id'] = $filters['organization_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND sp.status = :status";
            $params['status'] = $filters['status'];
        }

        if (isset($filters['is_active'])) {
            $sql .= " AND sp.is_active = :is_active";
            $params['is_active'] = $filters['is_active'] ? 1 : 0;
        }

        $sql .= " ORDER BY sp.start_year DESC, sp.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id, $organizationId = null) {
        $sql = "SELECT sp.*,
                o.name as organization_name,
                o.domain as organization_domain,
                o.slug as organization_slug
                FROM strategic_plans sp
                LEFT JOIN organizations o ON sp.organization_id = o.id
                WHERE sp.id = :id";

        $params = ['id' => $id];

        if ($organizationId !== null) {
            $sql .= " AND sp.organization_id = :organization_id";
            $params['organization_id'] = $organizationId;
        }

        return $this->db->fetchOne($sql, $params);
    }

    public function getBySlug($organizationSlug, $planSlug) {
        $sql = "SELECT sp.*,
                o.name as organization_name,
                o.domain as organization_domain,
                o.slug as organization_slug
                FROM strategic_plans sp
                INNER JOIN organizations o ON sp.organization_id = o.id
                WHERE o.slug = :organization_slug AND sp.slug = :plan_slug AND sp.status = 'published'";

        return $this->db->fetchOne($sql, [
            'organization_slug' => $organizationSlug,
            'plan_slug' => $planSlug
        ]);
    }

    public function create($data) {
        $planData = [
            'organization_id' => $data['organization_id'],
            'slug' => $this->generateSlug($data['slug'] ?? $data['title'], $data['organization_id']),
            'title' => $data['title'],
            'start_year' => !empty($data['start_year']) ? (int)$data['start_year'] : null,
            'end_year' => !empty($data['end_year']) ? (int)$data['end_year'] : null,
            'status' => $data['status'] ?? 'draft',
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 0,
            'created_by' => $data['created_by'] ?? null
        ];

        // If this is set as active, deactivate other plans for this organization
        if ($planData['is_active']) {
            $this->deactivateOtherPlans($planData['organization_id']);
        }

        return $this->db->insert('strategic_plans', $planData);
    }

    public function update($id, $data, $organizationId = null) {
        $planData = [
            'title' => $data['title'],
            'start_year' => !empty($data['start_year']) ? (int)$data['start_year'] : null,
            'end_year' => !empty($data['end_year']) ? (int)$data['end_year'] : null,
            'status' => $data['status'] ?? 'draft'
        ];

        // Handle slug update if provided
        if (!empty($data['slug'])) {
            $planData['slug'] = $this->generateSlug($data['slug'], $data['organization_id'] ?? null, $id);
        }

        // Handle is_active
        if (isset($data['is_active'])) {
            $planData['is_active'] = (int)$data['is_active'];
            // If setting as active, deactivate others
            if ($planData['is_active']) {
                $currentPlan = $this->getById($id, $organizationId);
                if ($currentPlan) {
                    $this->deactivateOtherPlans($currentPlan['organization_id'], $id);
                }
            }
        }

        $where = 'id = :id';
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $where .= ' AND organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        return $this->db->update('strategic_plans', $planData, $where, $params);
    }

    public function delete($id, $organizationId = null) {
        $where = 'id = :id';
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $where .= ' AND organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        return $this->db->delete('strategic_plans', $where, $params);
    }

    public function validate($data) {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Plan title is required';
        }

        if (empty($data['slug']) && empty($data['title'])) {
            $errors['slug'] = 'Plan slug is required';
        }

        if (!empty($data['slug'])) {
            $slug = $this->generateSlug($data['slug'], $data['organization_id'] ?? null);
            // Check for duplicate slug within organization
            $sql = "SELECT id FROM strategic_plans WHERE organization_id = :organization_id AND slug = :slug";
            $params = [
                'organization_id' => $data['organization_id'],
                'slug' => $slug
            ];
            if (!empty($data['id'])) {
                $sql .= " AND id != :id";
                $params['id'] = $data['id'];
            }
            $existing = $this->db->fetchOne($sql, $params);
            if ($existing) {
                $errors['slug'] = 'This slug is already in use for another plan in your organization';
            }
        }

        if (!empty($data['start_year']) && !empty($data['end_year'])) {
            if ($data['start_year'] > $data['end_year']) {
                $errors['end_year'] = 'End year must be after start year';
            }
        }

        return $errors;
    }

    private function generateSlug($input, $organizationId = null, $excludeId = null) {
        // Convert to lowercase, replace spaces and special chars with hyphens
        $slug = strtolower(trim($input));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure uniqueness within organization
        if ($organizationId) {
            $baseSlug = $slug;
            $counter = 1;
            while (true) {
                $sql = "SELECT id FROM strategic_plans WHERE organization_id = :organization_id AND slug = :slug";
                $params = [
                    'organization_id' => $organizationId,
                    'slug' => $slug
                ];
                if ($excludeId) {
                    $sql .= " AND id != :id";
                    $params['id'] = $excludeId;
                }
                $existing = $this->db->fetchOne($sql, $params);
                if (!$existing) {
                    break;
                }
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        return $slug;
    }

    private function deactivateOtherPlans($organizationId, $excludeId = null) {
        $where = "organization_id = :organization_id AND is_active = 1";
        $params = ['organization_id' => $organizationId];

        if ($excludeId) {
            $where .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        return $this->db->update('strategic_plans', ['is_active' => 0], $where, $params);
    }
}
?>
