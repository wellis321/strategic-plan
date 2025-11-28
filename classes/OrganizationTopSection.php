<?php
class OrganizationTopSection {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM organization_top_sections WHERE 1=1";
        $params = [];

        if (!empty($filters['organization_id'])) {
            $sql .= " AND organization_id = :organization_id";
            $params['organization_id'] = $filters['organization_id'];
        }

        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'] ? 1 : 0;
        }

        $sql .= " ORDER BY sort_order ASC, created_at ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id, $organizationId = null) {
        $sql = "SELECT * FROM organization_top_sections WHERE id = :id";
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $sql .= " AND organization_id = :organization_id";
            $params['organization_id'] = $organizationId;
        }

        return $this->db->fetchOne($sql, $params);
    }

    public function create($data) {
        $sectionData = [
            'organization_id' => $data['organization_id'],
            'section_type' => $data['section_type'] ?? 'custom',
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'image_position' => $data['image_position'] ?? 'left',
            'size' => $data['size'] ?? 'medium',
            'hero_bg_start' => !empty($data['hero_bg_start']) ? $data['hero_bg_start'] : null,
            'hero_bg_end' => !empty($data['hero_bg_end']) ? $data['hero_bg_end'] : null,
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']) ? 1 : 0
        ];

        return $this->db->insert('organization_top_sections', $sectionData);
    }

    public function update($id, $data, $organizationId = null) {
        $sectionData = [
            'section_type' => $data['section_type'] ?? 'custom',
            'title' => $data['title'] ?? null,
            'content' => $data['content'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'image_position' => $data['image_position'] ?? 'left',
            'size' => $data['size'] ?? 'medium',
            'hero_bg_start' => !empty($data['hero_bg_start']) ? $data['hero_bg_start'] : null,
            'hero_bg_end' => !empty($data['hero_bg_end']) ? $data['hero_bg_end'] : null,
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']) ? 1 : 0
        ];

        $where = 'id = :id';
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $where .= ' AND organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        return $this->db->update('organization_top_sections', $sectionData, $where, $params);
    }

    public function delete($id, $organizationId = null) {
        $where = 'id = :id';
        $params = ['id' => $id];

        if ($organizationId !== null) {
            $where .= ' AND organization_id = :organization_id';
            $params['organization_id'] = $organizationId;
        }

        return $this->db->delete('organization_top_sections', $where, $params);
    }

    public function validate($data) {
        $errors = [];

        if (empty($data['section_type'])) {
            $errors['section_type'] = 'Section type is required';
        } elseif (!in_array($data['section_type'], ['hero', 'about', 'vision', 'mission', 'values', 'custom'], true)) {
            $errors['section_type'] = 'Invalid section type';
        }

        // Title is required for custom sections, optional for others
        if ($data['section_type'] === 'custom' && empty($data['title'])) {
            $errors['title'] = 'Title is required for custom sections';
        } elseif (!empty($data['title']) && strlen($data['title']) > 255) {
            $errors['title'] = 'Title cannot exceed 255 characters';
        }

        // Validate hero colors if provided
        if ($data['section_type'] === 'hero') {
            if (!empty($data['hero_bg_start']) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $data['hero_bg_start'])) {
                $errors['hero_bg_start'] = 'Start color must be a valid hex color (e.g., #1D4ED8)';
            }
            if (!empty($data['hero_bg_end']) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $data['hero_bg_end'])) {
                $errors['hero_bg_end'] = 'End color must be a valid hex color (e.g., #9333EA)';
            }
        }

        if (!empty($data['image_position']) && !in_array($data['image_position'], ['left', 'right', 'top', 'bottom', 'background'], true)) {
            $errors['image_position'] = 'Invalid image position';
        }

        return $errors;
    }

    public function reorder($sections, $organizationId) {
        // $sections is an array of [id => sort_order]
        // Ensure IDs are integers and validate they belong to the organization
        $this->db->beginTransaction();
        try {
            foreach ($sections as $id => $sortOrder) {
                $id = (int)$id;
                $sortOrder = (int)$sortOrder;

                // Verify the section belongs to this organization
                $section = $this->getById($id, $organizationId);
                if (!$section) {
                    throw new Exception("Section {$id} not found or doesn't belong to this organization");
                }

                $result = $this->db->update(
                    'organization_top_sections',
                    ['sort_order' => $sortOrder],
                    'id = :id AND organization_id = :organization_id',
                    ['id' => $id, 'organization_id' => $organizationId]
                );

                if (!$result) {
                    throw new Exception("Failed to update section {$id}");
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
?>
