<?php
class Organization {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT o.*,
                (SELECT COUNT(*) FROM users u WHERE u.organization_id = o.id AND u.email_verified = TRUE AND u.status = 'active') as seats_used
                FROM organizations o
                ORDER BY o.name";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT * FROM organizations WHERE id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    public function getByDomain($domain) {
        $sql = "SELECT * FROM organizations WHERE domain = :domain AND status = 'active'";
        return $this->db->fetchOne($sql, ['domain' => $domain]);
    }

    public function getBySlug($slug) {
        $sql = "SELECT * FROM organizations WHERE slug = :slug AND status = 'active'";
        return $this->db->fetchOne($sql, ['slug' => $slug]);
    }

    public function create($data) {
        // Generate slug from name if not provided
        $slug = !empty($data['slug']) ? $data['slug'] : $this->generateSlug($data['name']);

        $orgData = [
            'name' => $data['name'],
            'domain' => strtolower(trim($data['domain'])),
            'slug' => $slug,
            'seat_allocation' => (int)$data['seat_allocation'],
            'status' => $data['status'] ?? 'active',
            'contact_email' => $data['contact_email'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'about_us' => $data['about_us'] ?? null,
            'vision' => $data['vision'] ?? null,
            'mission' => $data['mission'] ?? null,
            'hero_title' => $data['hero_title'] ?? null,
            'hero_subtitle' => $data['hero_subtitle'] ?? null,
            'hero_image_path' => $data['hero_image_path'] ?? null,
            'hero_image_height' => $data['hero_image_height'] ?? 'medium',
            'hero_bg_start' => $data['hero_bg_start'] ?? '#1d4ed8',
            'hero_bg_end' => $data['hero_bg_end'] ?? '#9333ea',
            'show_hero' => !empty($data['show_hero']) ? 1 : 0,
            'show_about' => !empty($data['show_about']) ? 1 : 0,
            'show_vision' => !empty($data['show_vision']) ? 1 : 0,
            'show_mission' => !empty($data['show_mission']) ? 1 : 0,
            'show_values' => !empty($data['show_values']) ? 1 : 0,
            'about_image_path' => $data['about_image_path'] ?? null,
            'created_by' => $data['created_by'] ?? null
        ];

        $orgId = $this->db->insert('organizations', $orgData);

        // Handle values if provided
        if ($orgId && !empty($data['values']) && is_array($data['values'])) {
            $this->setValues($orgId, $data['values']);
        }

        return $orgId;
    }

    public function update($id, $data) {
        $current = $this->getById($id);
        if (!$current) {
            throw new Exception('Organization not found');
        }

        $orgData = [
            'name' => $data['name'] ?? $current['name'],
            'domain' => strtolower(trim($data['domain'] ?? $current['domain'])),
            'seat_allocation' => isset($data['seat_allocation']) ? (int)$data['seat_allocation'] : (int)$current['seat_allocation'],
            'status' => $data['status'] ?? $current['status'],
            'slug' => !empty($data['slug']) ? $this->generateSlug($data['slug'], $id) : $current['slug'],
            'contact_email' => array_key_exists('contact_email', $data) ? $data['contact_email'] : $current['contact_email'],
            'contact_name' => array_key_exists('contact_name', $data) ? $data['contact_name'] : $current['contact_name'],
            'about_us' => array_key_exists('about_us', $data) ? $data['about_us'] : $current['about_us'],
            'vision' => array_key_exists('vision', $data) ? $data['vision'] : $current['vision'],
            'mission' => array_key_exists('mission', $data) ? $data['mission'] : $current['mission'],
            'hero_title' => array_key_exists('hero_title', $data) ? $data['hero_title'] : $current['hero_title'],
            'hero_subtitle' => array_key_exists('hero_subtitle', $data) ? $data['hero_subtitle'] : $current['hero_subtitle'],
            'hero_image_path' => array_key_exists('hero_image_path', $data) ? $data['hero_image_path'] : $current['hero_image_path'],
            'hero_image_height' => array_key_exists('hero_image_height', $data) ? ($data['hero_image_height'] ?: 'medium') : ($current['hero_image_height'] ?? 'medium'),
            'hero_bg_start' => array_key_exists('hero_bg_start', $data) ? ($data['hero_bg_start'] ?: '#1d4ed8') : ($current['hero_bg_start'] ?? '#1d4ed8'),
            'hero_bg_end' => array_key_exists('hero_bg_end', $data) ? ($data['hero_bg_end'] ?: '#9333ea') : ($current['hero_bg_end'] ?? '#9333ea'),
            'show_hero' => array_key_exists('show_hero', $data) ? (int)!empty($data['show_hero']) : (int)($current['show_hero'] ?? 0),
            'show_about' => array_key_exists('show_about', $data) ? (int)!empty($data['show_about']) : (int)($current['show_about'] ?? 0),
            'show_vision' => array_key_exists('show_vision', $data) ? (int)!empty($data['show_vision']) : (int)($current['show_vision'] ?? 0),
            'show_mission' => array_key_exists('show_mission', $data) ? (int)!empty($data['show_mission']) : (int)($current['show_mission'] ?? 0),
            'show_values' => array_key_exists('show_values', $data) ? (int)!empty($data['show_values']) : (int)($current['show_values'] ?? 0),
            'about_image_path' => array_key_exists('about_image_path', $data) ? $data['about_image_path'] : $current['about_image_path']
        ];

        // Update slug if provided
        if (!empty($data['slug'])) {
            $orgData['slug'] = $this->generateSlug($data['slug'], $id);
        }

        $result = $this->db->update('organizations', $orgData, 'id = :id', ['id' => $id]);

        // Handle values if provided
        if ($result && isset($data['values']) && is_array($data['values'])) {
            $this->setValues($id, $data['values']);
        }

        return $result;
    }

    public function delete($id) {
        // Check if organization has users
        $sql = "SELECT COUNT(*) as count FROM users WHERE organization_id = :id";
        $result = $this->db->fetchOne($sql, ['id' => $id]);

        if ($result['count'] > 0) {
            throw new Exception('Cannot delete organization with existing users');
        }

        return $this->db->delete('organizations', 'id = :id', ['id' => $id]);
    }

    public function getSeatUsage($organizationId) {
        $sql = "SELECT
                    o.seat_allocation,
                    COUNT(u.id) as seats_used,
                    (o.seat_allocation - COUNT(u.id)) as seats_available
                FROM organizations o
                LEFT JOIN users u ON o.id = u.organization_id
                    AND u.email_verified = TRUE
                    AND u.status = 'active'
                WHERE o.id = :id
                GROUP BY o.id, o.seat_allocation";

        return $this->db->fetchOne($sql, ['id' => $organizationId]);
    }

    public function hasAvailableSeats($domain) {
        $organization = $this->getByDomain($domain);

        if (!$organization) {
            return false;
        }

        $usage = $this->getSeatUsage($organization['id']);
        return $usage['seats_available'] > 0;
    }

    public function validate($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Organization name is required';
        }

        if (empty($data['domain'])) {
            $errors['domain'] = 'Domain is required';
        } else {
            // Validate domain format
            $domain = strtolower(trim($data['domain']));
            if (!preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?)*$/i', $domain)) {
                $errors['domain'] = 'Invalid domain format';
            }

            // Check for duplicate domain
            $sql = "SELECT id FROM organizations WHERE domain = :domain";
            $params = ['domain' => $domain];

            if (isset($data['id'])) {
                $sql .= " AND id != :id";
                $params['id'] = $data['id'];
            }

            $existing = $this->db->fetchOne($sql, $params);
            if ($existing) {
                $errors['domain'] = 'Domain already exists';
            }
        }

        if (isset($data['seat_allocation'])) {
            $seats = (int)$data['seat_allocation'];
            if ($seats < 1) {
                $errors['seat_allocation'] = 'Seat allocation must be at least 1';
            }
            // Removed 1000 seat limit - organizations can have unlimited seats

            // If updating, check that we're not reducing below current usage
            if (isset($data['id'])) {
                $usage = $this->getSeatUsage($data['id']);
                if ($usage && $seats < $usage['seats_used']) {
                    $errors['seat_allocation'] = "Cannot reduce seats below current usage ({$usage['seats_used']} seats in use)";
                }
            }
        }

        return $errors;
    }

    public function extractDomainFromEmail($email) {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return null;
        }

        $domain = strtolower(trim($parts[1]));

        // Extract root domain (handle subdomains)
        // e.g., user@mail.ramh.org.uk -> ramh.org.uk
        $domainParts = explode('.', $domain);
        if (count($domainParts) >= 2) {
            // Take last two parts for most cases (e.g., .co.uk, .org.uk)
            // Or last two parts for standard domains
            if (count($domainParts) > 2 && in_array($domainParts[count($domainParts) - 2], ['co', 'com', 'org', 'net', 'gov'])) {
                return $domainParts[count($domainParts) - 3] . '.' . $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
            }
            return $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
        }

        return $domain;
    }

    private function generateSlug($input, $excludeId = null) {
        // Convert to lowercase, replace spaces and special chars with hyphens
        $slug = strtolower(trim($input));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $sql = "SELECT id FROM organizations WHERE slug = :slug";
            $params = ['slug' => $slug];
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

        return $slug;
    }

    public function getValues($organizationId) {
        $sql = "SELECT value_text, sort_order
                FROM organization_values
                WHERE organization_id = :id
                ORDER BY sort_order ASC, id ASC";
        return $this->db->fetchAll($sql, ['id' => $organizationId]);
    }

    public function setValues($organizationId, $values) {
        // Delete existing values
        $this->db->delete('organization_values', 'organization_id = :id', ['id' => $organizationId]);

        // Insert new values
        if (!empty($values)) {
            foreach ($values as $index => $value) {
                $valueText = trim($value);
                if (!empty($valueText)) {
                    $this->db->insert('organization_values', [
                        'organization_id' => $organizationId,
                        'value_text' => $valueText,
                        'sort_order' => $index
                    ]);
                }
            }
        }
    }

    public function getByIdWithValues($id) {
        $organization = $this->getById($id);
        if ($organization) {
            $organization['values'] = $this->getValues($id);
        }
        return $organization;
    }
}
?>
