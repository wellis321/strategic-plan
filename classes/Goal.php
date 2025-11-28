<?php
class Goal {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $sql = "SELECT g.*,
                GROUP_CONCAT(gs.statement ORDER BY gs.sort_order SEPARATOR '|') as statements
                FROM goals g
                LEFT JOIN goal_statements gs ON g.id = gs.goal_id";

        $where = [];
        $params = [];

        if (!empty($filters['organization_id'])) {
            $where[] = "g.organization_id = :organization_id";
            $params['organization_id'] = $filters['organization_id'];
        }

        if (!empty($filters['plan_id'])) {
            $where[] = "g.plan_id = :plan_id";
            $params['plan_id'] = $filters['plan_id'];
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " GROUP BY g.id ORDER BY g.number";

        $goals = $this->db->fetchAll($sql, $params);

        // Process statements
        foreach ($goals as &$goal) {
            $goal['statements'] = $goal['statements'] ? explode('|', $goal['statements']) : [];
        }

        return $goals;
    }

    public function getById($id) {
        $sql = "SELECT * FROM goals WHERE id = :id";
        $goal = $this->db->fetchOne($sql, ['id' => $id]);

        if ($goal) {
            $goal['statements'] = $this->getStatements($id);
        }

        return $goal;
    }

    public function getByNumber($number) {
        $sql = "SELECT * FROM goals WHERE number = :number";
        $goal = $this->db->fetchOne($sql, ['number' => $number]);

        if ($goal) {
            $goal['statements'] = $this->getStatements($goal['id']);
        }

        return $goal;
    }

    public function getStatements($goalId) {
        $sql = "SELECT statement FROM goal_statements WHERE goal_id = :goal_id ORDER BY sort_order";
        $statements = $this->db->fetchAll($sql, ['goal_id' => $goalId]);

        return array_column($statements, 'statement');
    }

    public function create($data) {
        $this->db->beginTransaction();

        try {
            // Insert goal
            $goalData = [
                'organization_id' => $data['organization_id'] ?? null,
                'plan_id' => $data['plan_id'] ?? null,
                'number' => $data['number'],
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'responsible_director' => $data['responsible_director'],
                'created_by' => $data['created_by'] ?? null
            ];

            $goalId = $this->db->insert('goals', $goalData);

            // Insert statements if provided
            if (!empty($data['statements']) && is_array($data['statements'])) {
                foreach ($data['statements'] as $index => $statement) {
                    if (!empty(trim($statement))) {
                        $this->db->insert('goal_statements', [
                            'goal_id' => $goalId,
                            'statement' => trim($statement),
                            'sort_order' => $index + 1
                        ]);
                    }
                }
            }

            $this->db->commit();
            return $goalId;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();

        try {
            // Update goal
            $goalData = [
                'number' => $data['number'],
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'responsible_director' => $data['responsible_director']
            ];

            $this->db->update('goals', $goalData, 'id = :id', ['id' => $id]);

            // Update statements
            if (isset($data['statements']) && is_array($data['statements'])) {
                // Delete existing statements
                $this->db->delete('goal_statements', 'goal_id = :goal_id', ['goal_id' => $id]);

                // Insert new statements
                foreach ($data['statements'] as $index => $statement) {
                    if (!empty(trim($statement))) {
                        $this->db->insert('goal_statements', [
                            'goal_id' => $id,
                            'statement' => trim($statement),
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
        // Check if goal has associated projects
        $sql = "SELECT COUNT(*) as count FROM projects WHERE goal_id = :goal_id";
        $result = $this->db->fetchOne($sql, ['goal_id' => $id]);

        if ($result['count'] > 0) {
            throw new Exception('Cannot delete goal with associated projects');
        }

        return $this->db->delete('goals', 'id = :id', ['id' => $id]);
    }

    public function getProjectCount($goalId) {
        $sql = "SELECT COUNT(*) as count FROM projects WHERE goal_id = :goal_id";
        $result = $this->db->fetchOne($sql, ['goal_id' => $goalId]);
        return $result['count'];
    }

    public function validate($data) {
        $errors = [];

        if (empty($data['number'])) {
            $errors['number'] = 'Goal number is required';
        }

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        }

        if (empty($data['responsible_director'])) {
            $errors['responsible_director'] = 'Responsible Senior manager is required';
        }

        // Check for duplicate number (if creating new or changing number)
        if (!empty($data['number'])) {
            $sql = "SELECT id FROM goals WHERE number = :number";
            $params = ['number' => $data['number']];

            if (isset($data['id'])) {
                $sql .= " AND id != :id";
                $params['id'] = $data['id'];
            }

            $existing = $this->db->fetchOne($sql, $params);
            if ($existing) {
                $errors['number'] = 'Goal number already exists';
            }
        }

        return $errors;
    }
}
?>
