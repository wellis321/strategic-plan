<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getById($id) {
        $sql = "SELECT u.*, o.name as organization_name, o.domain as organization_domain
                FROM users u
                LEFT JOIN organizations o ON u.organization_id = o.id
                WHERE u.id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    public function getByEmail($email) {
        $sql = "SELECT u.*, o.name as organization_name, o.domain as organization_domain
                FROM users u
                LEFT JOIN organizations o ON u.organization_id = o.id
                WHERE u.email = :email";
        return $this->db->fetchOne($sql, ['email' => strtolower(trim($email))]);
    }

    public function getByVerificationToken($token) {
        $sql = "SELECT u.*, o.name as organization_name, o.domain as organization_domain
                FROM users u
                LEFT JOIN organizations o ON u.organization_id = o.id
                WHERE u.verification_token = :token
                AND (u.verification_token_expires IS NULL OR u.verification_token_expires > NOW())";
        return $this->db->fetchOne($sql, ['token' => $token]);
    }

    public function getByPasswordResetToken($token) {
        $sql = "SELECT u.*, o.name as organization_name, o.domain as organization_domain
                FROM users u
                LEFT JOIN organizations o ON u.organization_id = o.id
                WHERE u.password_reset_token = :token
                AND (u.password_reset_expires IS NULL OR u.password_reset_expires > NOW())";
        return $this->db->fetchOne($sql, ['token' => $token]);
    }

    public function getAllByOrganization($organizationId) {
        $sql = "SELECT u.*, o.name as organization_name
                FROM users u
                LEFT JOIN organizations o ON u.organization_id = o.id
                WHERE u.organization_id = :organization_id
                ORDER BY u.created_at DESC";
        return $this->db->fetchAll($sql, ['organization_id' => $organizationId]);
    }

    public function create($data) {
        $emailVerified = isset($data['email_verified']) ? (bool)$data['email_verified'] : false;

        $userData = [
            'organization_id' => $data['organization_id'],
            'email' => strtolower(trim($data['email'])),
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'role' => $data['role'] ?? 'user',
            'email_verified' => $emailVerified ? 1 : 0, // Convert to integer for MySQL
            'status' => $data['status'] ?? 'pending_verification'
        ];

        // Generate verification token if email not verified
        if (!$emailVerified) {
            $userData['verification_token'] = bin2hex(random_bytes(32));
            $userData['verification_token_expires'] = date('Y-m-d H:i:s', strtotime('+7 days'));
        }

        return $this->db->insert('users', $userData);
    }

    public function update($id, $data) {
        $userData = [];

        $allowedFields = ['first_name', 'last_name', 'role', 'status'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $userData[$field] = $data[$field];
            }
        }

        // Handle email_verified separately to convert to integer
        if (isset($data['email_verified'])) {
            $userData['email_verified'] = (bool)$data['email_verified'] ? 1 : 0;
        }

        if (isset($data['password'])) {
            $userData['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }

        if (!empty($userData)) {
            return $this->db->update('users', $userData, 'id = :id', ['id' => $id]);
        }

        return false;
    }

    public function verifyEmail($token) {
        $user = $this->getByVerificationToken($token);

        if (!$user) {
            return false;
        }

        $this->db->update('users', [
            'email_verified' => 1, // Convert to integer for MySQL
            'verification_token' => null,
            'verification_token_expires' => null,
            'status' => 'active'
        ], 'id = :id', ['id' => $user['id']]);

        return true;
    }

    public function setPasswordResetToken($email, $token) {
        $user = $this->getByEmail($email);

        if (!$user) {
            return false;
        }

        $this->db->update('users', [
            'password_reset_token' => $token,
            'password_reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ], 'email = :email', ['email' => strtolower(trim($email))]);

        return true;
    }

    public function resetPassword($token, $newPassword) {
        $user = $this->getByPasswordResetToken($token);

        if (!$user) {
            return false;
        }

        $this->db->update('users', [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            'password_reset_token' => null,
            'password_reset_expires' => null
        ], 'id = :id', ['id' => $user['id']]);

        return true;
    }

    public function updateLastLogin($id) {
        $this->db->update('users', [
            'last_login' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete('users', 'id = :id', ['id' => $id]);
    }

    public function validate($data, $isUpdate = false) {
        $errors = [];

        if (!$isUpdate || isset($data['email'])) {
            if (empty($data['email'])) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            } else {
                // Check for duplicate email
                $sql = "SELECT id FROM users WHERE email = :email";
                $params = ['email' => strtolower(trim($data['email']))];

                if ($isUpdate && isset($data['id'])) {
                    $sql .= " AND id != :id";
                    $params['id'] = $data['id'];
                }

                $existing = $this->db->fetchOne($sql, $params);
                if ($existing) {
                    $errors['email'] = 'Email already registered';
                }
            }
        }

        if (!$isUpdate || isset($data['password'])) {
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
        }

        if (isset($data['role']) && !in_array($data['role'], ['admin', 'user', 'super_admin'])) {
            $errors['role'] = 'Invalid role';
        }

        return $errors;
    }

    public function authenticate($email, $password) {
        $user = $this->getByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        // Check email_verified (can be 0/1 from database)
        $emailVerified = (bool)$user['email_verified'] || $user['email_verified'] === 1 || $user['email_verified'] === '1';
        if (!$emailVerified) {
            return false;
        }

        // If email is verified but status is still pending_verification, update status to active
        if ($emailVerified && $user['status'] === 'pending_verification') {
            $this->update($user['id'], ['status' => 'active']);
            $user['status'] = 'active'; // Update local copy
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        // Update last login
        $this->updateLastLogin($user['id']);

        return $user;
    }
}
?>
